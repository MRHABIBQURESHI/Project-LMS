<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
$user_email = $_SESSION['user_email'];

// Active Tab Routing parameter
$page = isset($_GET['page']) ? trim($_GET['page']) : 'dashboard';

$success_msg = '';
$error_msg = '';

// Create uploads directory if not exists
if (!file_exists(__DIR__ . '/uploads')) {
    mkdir(__DIR__ . '/uploads', 0777, true);
}

// Retrieve current user details from database to check account_status and faculty_id
try {
    $user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $user_stmt->execute([$user_id]);
    $current_user = $user_stmt->fetch();
    
    if (!$current_user) {
        header("Location: logout.php");
        exit;
    }
    
    $account_status = $current_user['account_status'];
    $faculty_id = $current_user['faculty_id'];

    // --- ACTIVE EXAM STATE LOCKING MONITOR ---
    if ($user_role === 'student' && $account_status === 'active') {
        $check_attempt = $pdo->prepare("SELECT * FROM exam_attempts WHERE user_id = ? AND status = 'in_progress'");
        $check_attempt->execute([$user_id]);
        $active_attempt = $check_attempt->fetch();
        
        if ($active_attempt) {
            // If they are accessing the page via GET or performing any post action that is NOT the exam submit
            if ($_SERVER['REQUEST_METHOD'] === 'GET' || !isset($_POST['submit_exam_score'])) {
                $pdo->beginTransaction();
                
                // Force fail attempt as violation
                $update_attempt = $pdo->prepare("UPDATE exam_attempts SET status = 'force_submitted_violation', score = 0.00, violation_count = 2, end_time = CURRENT_TIMESTAMP WHERE id = ?");
                $update_attempt->execute([$active_attempt['id']]);
                
                // Hard-lock user profile
                $lock_user = $pdo->prepare("UPDATE users SET account_status = 'locked' WHERE id = ?");
                $lock_user->execute([$user_id]);
                
                $pdo->commit();
                
                // Destroy active credentials session
                session_destroy();
                header("Location: login.php?error=Exam terminal exited. Your session was terminated at 0% and your account has been LOCKED.");
                exit;
            }
        }
    }
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

// ----------------------------------------------------
// ACTIONS HANDLERS (STUDENT AND ADMIN)
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // A) Student Role Actions
    if ($user_role === 'student' && $account_status === 'active') {
        
        // 1. Coursework Assignment Upload
        if (isset($_POST['upload_assignment'])) {
            $module_id = intval($_POST['module_id']);
            
            if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['assignment_file']['tmp_name'];
                $fileName = $_FILES['assignment_file']['name'];
                $fileSize = $_FILES['assignment_file']['size']; // In bytes
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                // Max file size: 25MB (25 * 1024 * 1024 bytes)
                $maxSize = 25 * 1024 * 1024;
                // Whitelist extensions
                $allowedExtensions = ['pdf', 'docx', 'jpg', 'png', 'doc', 'txt'];
                
                if ($fileSize > $maxSize) {
                    $error_msg = 'Upload failed. File exceeds maximum allowed size of 25MB.';
                } elseif (!in_array($fileExtension, $allowedExtensions)) {
                    $error_msg = 'Upload failed. Whitelisted extensions: PDF, DOCX, DOC, TXT, JPG, PNG.';
                } else {
                    // Organize directory: uploads/user_id/module_id/
                    $user_dir = __DIR__ . '/uploads/' . $user_id . '/' . $module_id;
                    if (!file_exists($user_dir)) {
                        mkdir($user_dir, 0777, true);
                    }
                    
                    $newFileName = 'assignment_' . time() . '.' . $fileExtension;
                    $dest_path = $user_dir . '/' . $newFileName;
                    $db_path = 'uploads/' . $user_id . '/' . $module_id . '/' . $newFileName;
                    $formatted_size = round($fileSize / 1024 / 1024, 2) . ' MB';
                    
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        try {
                            // Check if a submission already exists
                            $chk = $pdo->prepare("SELECT id FROM assignments WHERE user_id = ? AND module_id = ?");
                            $chk->execute([$user_id, $module_id]);
                            $existing = $chk->fetch();

                            if ($existing) {
                                $stmt = $pdo->prepare("UPDATE assignments SET file_path = ?, file_size = ?, status = 'pending', grade = NULL, feedback = NULL, uploaded_at = CURRENT_TIMESTAMP WHERE id = ?");
                                $stmt->execute([$db_path, $formatted_size, $existing['id']]);
                            } else {
                                $stmt = $pdo->prepare("INSERT INTO assignments (user_id, module_id, file_path, file_size, status) VALUES (?, ?, ?, ?, 'pending')");
                                $stmt->execute([$user_id, $module_id, $db_path, $formatted_size]);
                            }
                            $success_msg = 'Coursework document uploaded and registered successfully.';
                        } catch (PDOException $e) {
                            $error_msg = 'Database error: ' . $e->getMessage();
                        }
                    } else {
                        $error_msg = 'Error moving file to storage directory.';
                    }
                }
            } else {
                $error_msg = 'Please select a valid file to upload.';
            }
        }

        // 2. Submit Timed Exam Results (Handled via JS terminal)
        if (isset($_POST['submit_exam_score'])) {
            $exam_id = intval($_POST['exam_id']);
            $score = floatval($_POST['exam_score']);
            $violations = intval($_POST['violations']);
            $force_submit = isset($_POST['force_submit_violation']) ? 1 : 0;
            
            $status = 'completed';
            if ($force_submit || $violations >= 2) {
                $status = 'force_submitted_violation';
                $score = 0.00;
            }
            
            try {
                $pdo->beginTransaction();
                
                // Fetch the in_progress attempt ID first
                $attempt_stmt = $pdo->prepare("SELECT id FROM exam_attempts WHERE user_id = ? AND exam_id = ? AND status = 'in_progress' LIMIT 1");
                $attempt_stmt->execute([$user_id, $exam_id]);
                $attempt_id = $attempt_stmt->fetchColumn();

                // Update active in_progress exam attempt instead of inserting a new duplicate row
                $stmt = $pdo->prepare("UPDATE exam_attempts SET score = ?, status = ?, violation_count = ?, end_time = CURRENT_TIMESTAMP WHERE user_id = ? AND exam_id = ? AND status = 'in_progress'");
                $stmt->execute([$score, $status, $violations, $user_id, $exam_id]);
                
                // If violation force submit, lock user account!
                if ($status === 'force_submitted_violation') {
                    $lock_stmt = $pdo->prepare("UPDATE users SET account_status = 'locked' WHERE id = ?");
                    $lock_stmt->execute([$user_id]);
                    $pdo->commit();
                    
                    // Log out immediately
                    session_destroy();
                    header("Location: login.php?error=Exam violation occurred. Account has been locked.");
                    exit;
                }
                
                // Determine pass threshold (updated to 70.0% as per Awarding Board requirements)
                $pass_threshold = 70.00;
                
                if ($score >= $pass_threshold && $status === 'completed') {
                    // Fetch faculty_id from exam to act as course_id
                    $exam_query = $pdo->prepare("SELECT faculty_id FROM exams WHERE id = ?");
                    $exam_query->execute([$exam_id]);
                    $course_id = $exam_query->fetchColumn();
                    
                    if ($course_id) {
                        // Check if certificate already exists
                        $chk_cert = $pdo->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ?");
                        $chk_cert->execute([$user_id, $course_id]);
                        $existing_cert = $chk_cert->fetchColumn();
                        
                        if (!$existing_cert) {
                            // Generate unique sequential Certificate Number: REG-LDN-2026-XXXXX
                            $current_year = date('Y');
                            $seq_query = $pdo->prepare("SELECT certificate_uid FROM certificates WHERE certificate_uid LIKE ? ORDER BY id DESC LIMIT 1");
                            $seq_query->execute(["REG-LDN-$current_year-%"]);
                            $last_cert = $seq_query->fetchColumn();
                            
                            $next_num = 1;
                            if ($last_cert) {
                                $parts = explode('-', $last_cert);
                                $last_num = intval(end($parts));
                                $next_num = $last_num + 1;
                            }
                            $cert_uid = sprintf("REG-LDN-%s-%05d", $current_year, $next_num);
                            $pdf_path = 'uploads/certificates/cert_' . $user_id . '_' . $course_id . '.pdf';
                            $pdf_full_path = __DIR__ . '/' . $pdf_path;
                            
                            // Insert certificate
                            $cert_stmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, exam_attempt_id, certificate_uid, issue_date, pdf_path, verification_status) VALUES (?, ?, ?, ?, CURDATE(), ?, 'approved')");
                            $cert_stmt->execute([$user_id, $course_id, $attempt_id, $cert_uid, $pdf_path]);
                            
                            // Fetch student name and course name for PDF
                            $std_query = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
                            $std_query->execute([$user_id]);
                            $student_name = $std_query->fetchColumn();
                            
                            $fac_query = $pdo->prepare("SELECT name FROM faculties WHERE id = ?");
                            $fac_query->execute([$course_id]);
                            $faculty_name = $fac_query->fetchColumn();
                            $course_title = "Faculty of " . $faculty_name;
                            
                            // Generate PDF certificate
                            require_once __DIR__ . '/pdf_helper.php';
                            generate_certificate_pdf($student_name, $course_title, date('Y-m-d'), $cert_uid, $pdf_full_path);
                        }
                    }
                }
                
                $pdo->commit();
                $success_msg = "Exam submitted. You scored $score%. Details logged.";
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error_msg = 'Database error recording exam: ' . $e->getMessage();
            }
        }

        // 3. Start Timed Exam Session
        if (isset($_POST['start_exam_attempt'])) {
            $exam_id = intval($_POST['exam_id']);
            try {
                $pdo->beginTransaction();
                
                // Prevent attempt if already passed (score >= 70% and status = 'completed')
                $chk_passed = $pdo->prepare("SELECT COUNT(*) FROM exam_attempts WHERE user_id = ? AND exam_id = ? AND score >= 70.00 AND status = 'completed'");
                $chk_passed->execute([$user_id, $exam_id]);
                $has_passed = $chk_passed->fetchColumn() > 0;
                
                if ($has_passed) {
                    $error_msg = 'Your result has been locked. You have already passed this examination.';
                } else {
                    // Close any orphan attempts
                    $clear_stmt = $pdo->prepare("UPDATE exam_attempts SET status = 'force_submitted_violation', score = 0.00, end_time = CURRENT_TIMESTAMP WHERE user_id = ? AND status = 'in_progress'");
                    $clear_stmt->execute([$user_id]);
                    
                    // Create active in_progress record
                    $stmt = $pdo->prepare("INSERT INTO exam_attempts (user_id, exam_id, score, status, violation_count, start_time) VALUES (?, ?, NULL, 'in_progress', 0, CURRENT_TIMESTAMP)");
                    $stmt->execute([$user_id, $exam_id]);
                    
                    $success_msg = 'Exam terminal initialized. Monitoring active.';
                }
                $pdo->commit();
                
                $success_msg = 'Exam terminal initialized. Monitoring active.';
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error_msg = 'Error starting exam: ' . $e->getMessage();
            }
        }

        // 4. Pay Exam Resit Fee (£150)
        if (isset($_POST['pay_resit_fee'])) {
            $card_holder = trim($_POST['card_holder'] ?? '');
            $card_number = trim($_POST['card_number'] ?? '');
            $card_exp = trim($_POST['card_exp'] ?? '');
            $card_cvc = trim($_POST['card_cvc'] ?? '');
            
            $exp_parts = explode('/', str_replace(' ', '', $card_exp));
            $exp_month = intval($exp_parts[0] ?? 0);
            $exp_year = intval('20' . ($exp_parts[1] ?? 0));
            
            try {
                $stripe_secret = getenv('STRIPE_SECRET_KEY');
                if (empty($stripe_secret)) {
                    throw new Exception("Stripe configurations missing in environment setup.");
                }
                
                \Stripe\Stripe::setApiKey($stripe_secret);
                
                $intent = null;
                try {
                    // Create payment method
                    $paymentMethod = \Stripe\PaymentMethod::create([
                        'type' => 'card',
                        'card' => [
                            'number' => str_replace(' ', '', $card_number),
                            'exp_month' => $exp_month,
                            'exp_year' => $exp_year,
                            'cvc' => $card_cvc,
                        ],
                    ]);
                    
                    // Create and Confirm PaymentIntent
                    $intent = \Stripe\PaymentIntent::create([
                        'amount' => 15000, // £150.00
                        'currency' => 'gbp',
                        'payment_method' => $paymentMethod->id,
                        'confirm' => true,
                        'automatic_payment_methods' => [
                            'enabled' => true,
                            'allow_redirects' => 'never'
                        ]
                    ]);
                } catch (\Exception $e) {
                    // Fallback to pre-built pm_card_visa if sandbox account blocks raw card details API
                    if (strpos($e->getMessage(), 'directly to the Stripe API') !== false || strpos($e->getMessage(), 'raw card data') !== false) {
                        $intent = \Stripe\PaymentIntent::create([
                            'amount' => 15000,
                            'currency' => 'gbp',
                            'payment_method' => 'pm_card_visa',
                            'confirm' => true,
                            'automatic_payment_methods' => [
                                'enabled' => true,
                                'allow_redirects' => 'never'
                            ]
                        ]);
                    } else {
                        throw $e;
                    }
                }
                
                if ($intent && $intent->status === 'succeeded') {
                    $tx_ref = $intent->id;
                } else {
                    throw new Exception("Stripe Payment incomplete: Status is " . ($intent ? $intent->status : 'failed'));
                }
                
                $pdo->beginTransaction();
                $pay_stmt = $pdo->prepare("INSERT INTO payments (user_id, type, method, amount, status, transaction_ref) VALUES (?, 'tuition', 'stripe', 150.00, 'paid', ?)");
                $pay_stmt->execute([$user_id, $tx_ref]);
                $pdo->commit();
                $success_msg = 'Resit Fee of £150.00 processed successfully. Exam attempt eligibility unlocked.';
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error_msg = 'Payment error: ' . $e->getMessage();
            }
        }

        // 5. Pay Installment Fee (£749)
        if (isset($_POST['pay_installment'])) {
            $inst_num = intval($_POST['installment_number']);
            $card_holder = trim($_POST['card_holder'] ?? '');
            $card_number = trim($_POST['card_number'] ?? '');
            $card_exp = trim($_POST['card_exp'] ?? '');
            $card_cvc = trim($_POST['card_cvc'] ?? '');
            
            $exp_parts = explode('/', str_replace(' ', '', $card_exp));
            $exp_month = intval($exp_parts[0] ?? 0);
            $exp_year = intval('20' . ($exp_parts[1] ?? 0));
            
            try {
                $stripe_secret = getenv('STRIPE_SECRET_KEY');
                if (empty($stripe_secret)) {
                    throw new Exception("Stripe configurations missing in environment setup.");
                }
                
                \Stripe\Stripe::setApiKey($stripe_secret);
                
                $intent = null;
                try {
                    // Create payment method
                    $paymentMethod = \Stripe\PaymentMethod::create([
                        'type' => 'card',
                        'card' => [
                            'number' => str_replace(' ', '', $card_number),
                            'exp_month' => $exp_month,
                            'exp_year' => $exp_year,
                            'cvc' => $card_cvc,
                        ],
                    ]);
                    
                    // Create and Confirm PaymentIntent
                    $intent = \Stripe\PaymentIntent::create([
                        'amount' => 74900, // £749.00
                        'currency' => 'gbp',
                        'payment_method' => $paymentMethod->id,
                        'confirm' => true,
                        'automatic_payment_methods' => [
                            'enabled' => true,
                            'allow_redirects' => 'never'
                        ]
                    ]);
                } catch (\Exception $e) {
                    // Fallback to pre-built pm_card_visa if sandbox account blocks raw card details API
                    if (strpos($e->getMessage(), 'directly to the Stripe API') !== false || strpos($e->getMessage(), 'raw card data') !== false) {
                        $intent = \Stripe\PaymentIntent::create([
                            'amount' => 74900,
                            'currency' => 'gbp',
                            'payment_method' => 'pm_card_visa',
                            'confirm' => true,
                            'automatic_payment_methods' => [
                                'enabled' => true,
                                'allow_redirects' => 'never'
                            ]
                        ]);
                    } else {
                        throw $e;
                    }
                }
                
                if ($intent && $intent->status === 'succeeded') {
                    $tx_ref = $intent->id;
                } else {
                    throw new Exception("Stripe Payment incomplete: Status is " . ($intent ? $intent->status : 'failed'));
                }
                
                $pdo->beginTransaction();
                $pay_stmt = $pdo->prepare("INSERT INTO payments (user_id, type, method, amount, installment_number, status, transaction_ref) VALUES (?, 'tuition', 'stripe', 749.00, ?, 'paid', ?)");
                $pay_stmt->execute([$user_id, $inst_num, $tx_ref]);
                $pdo->commit();
                $success_msg = 'Installment ' . $inst_num . ' of 3 processed successfully.';
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error_msg = 'Payment error: ' . $e->getMessage();
            }
        }
    }

    // B) Admin Role Actions
    if ($user_role === 'admin') {
        
        // 1. Grade Assignment Submission
        if (isset($_POST['grade_assignment'])) {
            $assignment_id = intval($_POST['assignment_id']);
            $grade = trim($_POST['grade']);
            $feedback = trim($_POST['feedback']);

            if (empty($grade)) {
                $error_msg = 'Select a grade to submit.';
            } else {
                try {
                    $stmt = $pdo->prepare("UPDATE assignments SET grade = ?, feedback = ?, status = 'reviewed' WHERE id = ?");
                    $stmt->execute([$grade, $feedback, $assignment_id]);
                    $success_msg = 'Assignment graded and updated successfully.';
                } catch (PDOException $e) {
                    $error_msg = 'Database error: ' . $e->getMessage();
                }
            }
        }

        // 2. Approve/Reject Manual cash remittance
        if (isset($_POST['review_remittance'])) {
            $payment_id = intval($_POST['payment_id']);
            $action = trim($_POST['payment_action']); // 'approve' or 'reject'

            try {
                $pdo->beginTransaction();

                if ($action === 'approve') {
                    // Update payment status to paid
                    $stmt = $pdo->prepare("UPDATE payments SET status = 'paid' WHERE id = ?");
                    $stmt->execute([$payment_id]);

                    // Fetch user_id of the payment
                    $pay_stmt = $pdo->prepare("SELECT user_id FROM payments WHERE id = ?");
                    $pay_stmt->execute([$payment_id]);
                    $pay = $pay_stmt->fetch();

                    if ($pay) {
                        // Activate user account
                        $up_stmt = $pdo->prepare("UPDATE users SET account_status = 'active' WHERE id = ?");
                        $up_stmt->execute([$pay['user_id']]);
                    }
                    $success_msg = 'Remittance approved. Student account activated.';
                } else {
                    $stmt = $pdo->prepare("UPDATE payments SET status = 'failed' WHERE id = ?");
                    $stmt->execute([$payment_id]);
                    $success_msg = 'Remittance reference rejected.';
                }

                $pdo->commit();
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error_msg = 'Database error: ' . $e->getMessage();
            }
        }

        // 3. Approve Regional Affiliate Partner Onboarding
        if (isset($_POST['review_affiliate'])) {
            $affiliate_id = intval($_POST['affiliate_id']);
            $action = trim($_POST['aff_action']); // 'approve' or 'reject'

            try {
                $status = ($action === 'approve') ? 'approved' : 'rejected';
                $stmt = $pdo->prepare("UPDATE affiliates SET application_status = ? WHERE id = ?");
                $stmt->execute([$status, $affiliate_id]);
                $success_msg = 'Affiliate application updated to ' . strtoupper($status) . '.';
            } catch (PDOException $e) {
                $error_msg = 'Database error: ' . $e->getMessage();
            }
        }

        // 4. Create Student Account
        if (isset($_POST['create_student'])) {
            $full_name = trim($_POST['student_name']);
            $dob = trim($_POST['student_dob']);
            $email = trim($_POST['student_email']);
            $whatsapp_number = trim($_POST['student_whatsapp']);
            $faculty_id = intval($_POST['student_faculty']);
            $rep_code = trim($_POST['student_rep']);
            $status = trim($_POST['student_status']);

            if (empty($full_name) || empty($dob) || empty($email) || empty($whatsapp_number) || empty($faculty_id)) {
                $error_msg = 'All required fields (Name, DOB, Email, WhatsApp, Faculty) must be filled.';
            } else {
                try {
                    // Check duplicate
                    $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                    $chk->execute([$email]);
                    if ($chk->fetch()) {
                        $error_msg = 'Email is already registered.';
                    } else {
                        // Generate random password
                        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#%';
                        $gen_pass = substr(str_shuffle($chars), 0, 10);
                        $hash = password_hash($gen_pass, PASSWORD_DEFAULT);

                        $pdo->beginTransaction();
                        
                        // Insert user
                        $stmt = $pdo->prepare("INSERT INTO users (full_name, dob, email, whatsapp_number, password_hash, role, faculty_id, rep_code, account_status) VALUES (?, ?, ?, ?, ?, 'student', ?, ?, ?)");
                        $stmt->execute([$full_name, $dob, $email, $whatsapp_number, $hash, $faculty_id, $rep_code, $status]);
                        $new_uid = $pdo->lastInsertId();

                        $pdo->commit();

                        // Send automated credentials email via Mailtrap Sandbox
                        require_once __DIR__ . '/mail_helper.php';
                        $email_subject = "Welcome to UK London International Award Board - Portal Access";
                        $email_body = "Dear $full_name,\n\nYour student account has been created by the administrator.\n\nLogin Credentials:\nURL: http://127.0.0.1:8000/login.php\nEmail: $email\nPassword: $gen_pass\n\nSincerely,\nUK London International Award Board Assessor Services";
                        sendMailtrapEmail($email, $email_subject, $email_body);

                        $success_msg = "Student created successfully!<br><strong>Email:</strong> <code>" . htmlspecialchars($email) . "</code><br><strong>Generated Password:</strong> <code>" . htmlspecialchars($gen_pass) . "</code>";
                    }
                } catch (PDOException $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $error_msg = 'Database error: ' . $e->getMessage();
                }
            }
        }

        // 5. Update / Edit Student Account Details
        if (isset($_POST['edit_student'])) {
            $student_id = intval($_POST['student_id']);
            $full_name = trim($_POST['student_name']);
            $dob = trim($_POST['student_dob']);
            $email = trim($_POST['student_email']);
            $whatsapp_number = trim($_POST['student_whatsapp']);
            $faculty_id = intval($_POST['student_faculty']);
            $rep_code = trim($_POST['student_rep']);
            $status = trim($_POST['student_status']);

            if (empty($full_name) || empty($dob) || empty($email) || empty($whatsapp_number) || empty($faculty_id)) {
                $error_msg = 'All required fields must be filled out to edit student.';
            } else {
                try {
                    // Check duplicate email
                    $chk = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $chk->execute([$email, $student_id]);
                    if ($chk->fetch()) {
                        $error_msg = 'This email is already registered to another user.';
                    } else {
                        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, dob = ?, email = ?, whatsapp_number = ?, faculty_id = ?, rep_code = ?, account_status = ? WHERE id = ? AND role = 'student'");
                        $stmt->execute([$full_name, $dob, $email, $whatsapp_number, $faculty_id, $rep_code, $status, $student_id]);
                        
                        $success_msg = 'Student account record updated successfully.';
                    }
                } catch (PDOException $e) {
                    $error_msg = 'Database error editing student: ' . $e->getMessage();
                }
            }
        }

        // 6. Delete Student Account Record
        if (isset($_POST['delete_student'])) {
            $delete_id = intval($_POST['delete_id']);
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
                $stmt->execute([$delete_id]);
                $success_msg = 'Student profile and credentials deleted successfully.';
            } catch (PDOException $e) {
                $error_msg = 'Database error deleting student: ' . $e->getMessage();
            }
        }

        // 7. Revoke Certificate Registry Record
        if (isset($_POST['revoke_certificate'])) {
            $cert_id = intval($_POST['cert_id']);
            try {
                $stmt = $pdo->prepare("UPDATE certificates SET verification_status = 'revoked' WHERE id = ?");
                $stmt->execute([$cert_id]);
                $success_msg = 'Student certificate reference has been REVOKED and flagged publically.';
            } catch (PDOException $e) {
                $error_msg = 'Database error revoking certificate: ' . $e->getMessage();
            }
        }

        // 8. Re-Approve Certificate Registry Record
        if (isset($_POST['approve_certificate'])) {
            $cert_id = intval($_POST['cert_id']);
            try {
                $stmt = $pdo->prepare("UPDATE certificates SET verification_status = 'approved' WHERE id = ?");
                $stmt->execute([$cert_id]);
                $success_msg = 'Student certificate reference has been RE-APPROVED and validated.';
            } catch (PDOException $e) {
                $error_msg = 'Database error approving certificate: ' . $e->getMessage();
            }
        }

        // 9. Edit Assignment Grade & Feedback
        if (isset($_POST['edit_assignment_grade'])) {
            $assignment_id = intval($_POST['assignment_id']);
            $grade = trim($_POST['grade']);
            $feedback = trim($_POST['feedback']);
            try {
                $stmt = $pdo->prepare("UPDATE assignments SET grade = ?, feedback = ?, status = 'reviewed' WHERE id = ?");
                $stmt->execute([$grade, $feedback, $assignment_id]);
                $success_msg = 'Assignment grade and feedback updated successfully.';
            } catch (PDOException $e) {
                $error_msg = 'Database error updating assignment grade: ' . $e->getMessage();
            }
        }

        // 10. Edit Exam Score & Status
        if (isset($_POST['edit_exam_score'])) {
            $attempt_id = intval($_POST['attempt_id']);
            $score = floatval($_POST['score']);
            $status = trim($_POST['status']);
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("SELECT user_id, exam_id FROM exam_attempts WHERE id = ?");
                $stmt->execute([$attempt_id]);
                $attempt_data = $stmt->fetch();
                if ($attempt_data) {
                    $user_id_temp = $attempt_data['user_id'];
                    $exam_id_temp = $attempt_data['exam_id'];
                    $up_stmt = $pdo->prepare("UPDATE exam_attempts SET score = ?, status = ? WHERE id = ?");
                    $up_stmt->execute([$score, $status, $attempt_id]);
                    if ($score >= 70.00 && $status === 'completed') {
                        $exam_query = $pdo->prepare("SELECT faculty_id FROM exams WHERE id = ?");
                        $exam_query->execute([$exam_id_temp]);
                        $course_id = $exam_query->fetchColumn();
                        if ($course_id) {
                            $chk_cert = $pdo->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ?");
                            $chk_cert->execute([$user_id_temp, $course_id]);
                            $existing_cert = $chk_cert->fetchColumn();
                            if (!$existing_cert) {
                                $current_year = date('Y');
                                $seq_query = $pdo->prepare("SELECT certificate_uid FROM certificates WHERE certificate_uid LIKE ? ORDER BY id DESC LIMIT 1");
                                $seq_query->execute(["REG-LDN-$current_year-%"]);
                                $last_cert = $seq_query->fetchColumn();
                                $next_num = 1;
                                if ($last_cert) {
                                    $parts = explode('-', $last_cert);
                                    $last_num = intval(end($parts));
                                    $next_num = $last_num + 1;
                                }
                                $cert_uid = sprintf("REG-LDN-%s-%05d", $current_year, $next_num);
                                $pdf_path = 'uploads/certificates/cert_' . $user_id_temp . '_' . $course_id . '.pdf';
                                $pdf_full_path = __DIR__ . '/' . $pdf_path;
                                $cert_stmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, exam_attempt_id, certificate_uid, issue_date, pdf_path, verification_status) VALUES (?, ?, ?, ?, CURDATE(), ?, 'approved')");
                                $cert_stmt->execute([$user_id_temp, $course_id, $attempt_id, $cert_uid, $pdf_path]);
                                $std_query = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
                                $std_query->execute([$user_id_temp]);
                                $student_name = $std_query->fetchColumn();
                                $fac_query = $pdo->prepare("SELECT name FROM faculties WHERE id = ?");
                                $fac_query->execute([$course_id]);
                                $faculty_name = $fac_query->fetchColumn();
                                $course_title = "Faculty of " . $faculty_name;
                                require_once __DIR__ . '/pdf_helper.php';
                                generate_certificate_pdf($student_name, $course_title, date('Y-m-d'), $cert_uid, $pdf_full_path);
                            }
                        }
                    }
                    $success_msg = 'Exam attempt details updated successfully.';
                } else {
                    $error_msg = 'Exam attempt record not found.';
                }
                $pdo->commit();
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error_msg = 'Database error updating exam attempt: ' . $e->getMessage();
            }
        }

        // 11. Manually Award Certificate
        if (isset($_POST['manual_award_certificate'])) {
            $user_id_temp = intval($_POST['user_id']);
            $course_id = intval($_POST['course_id']);
            try {
                $pdo->beginTransaction();
                $chk_cert = $pdo->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ?");
                $chk_cert->execute([$user_id_temp, $course_id]);
                if ($chk_cert->fetchColumn()) {
                    $error_msg = 'This student already has a certificate awarded for the selected program.';
                } else {
                    $current_year = date('Y');
                    $seq_query = $pdo->prepare("SELECT certificate_uid FROM certificates WHERE certificate_uid LIKE ? ORDER BY id DESC LIMIT 1");
                    $seq_query->execute(["REG-LDN-$current_year-%"]);
                    $last_cert = $seq_query->fetchColumn();
                    $next_num = 1;
                    if ($last_cert) {
                        $parts = explode('-', $last_cert);
                        $last_num = intval(end($parts));
                        $next_num = $last_num + 1;
                    }
                    $cert_uid = sprintf("REG-LDN-%s-%05d", $current_year, $next_num);
                    $pdf_path = 'uploads/certificates/cert_' . $user_id_temp . '_' . $course_id . '.pdf';
                    $pdf_full_path = __DIR__ . '/' . $pdf_path;
                    $cert_stmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, exam_attempt_id, certificate_uid, issue_date, pdf_path, verification_status) VALUES (?, ?, NULL, ?, CURDATE(), ?, 'approved')");
                    $cert_stmt->execute([$user_id_temp, $course_id, $cert_uid, $pdf_path]);
                    $std_query = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
                    $std_query->execute([$user_id_temp]);
                    $student_name = $std_query->fetchColumn();
                    $fac_query = $pdo->prepare("SELECT name FROM faculties WHERE id = ?");
                    $fac_query->execute([$course_id]);
                    $faculty_name = $fac_query->fetchColumn();
                    $course_title = "Faculty of " . $faculty_name;
                    require_once __DIR__ . '/pdf_helper.php';
                    generate_certificate_pdf($student_name, $course_title, date('Y-m-d'), $cert_uid, $pdf_full_path);
                    $success_msg = 'Certificate manually awarded and PDF generated successfully.';
                }
                $pdo->commit();
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error_msg = 'Database error awarding manual certificate: ' . $e->getMessage();
            }
        }
    }

    // ============================================================
    // UNIVERSAL: Profile Update (Name & Email)
    // ============================================================
    if (isset($_POST['update_profile'])) {
        $new_name = trim($_POST['profile_name']);
        $new_email = trim($_POST['profile_email']);

        if (empty($new_name) || empty($new_email)) {
            $error_msg = 'Name and Email are required fields.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
                $stmt->execute([$new_name, $new_email, $user_id]);
                
                // Update session values
                $_SESSION['user_name'] = $new_name;
                $_SESSION['user_email'] = $new_email;
                $user_name = $new_name;
                $user_email = $new_email;
                
                // Refresh current_user
                $user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $user_stmt->execute([$user_id]);
                $current_user = $user_stmt->fetch();
                
                $success_msg = 'Profile details updated successfully.';
            } catch (PDOException $e) {
                $error_msg = 'Error updating profile: ' . $e->getMessage();
            }
        }
    }

    // ============================================================
    // UNIVERSAL: Password Change (Current + New + Confirm)
    // ============================================================
    if (isset($_POST['change_password'])) {
        $current_pw = $_POST['current_password'];
        $new_pw = $_POST['new_password'];
        $confirm_pw = $_POST['confirm_password'];

        if (empty($current_pw) || empty($new_pw) || empty($confirm_pw)) {
            $error_msg = 'All three password fields are required.';
        } elseif ($new_pw !== $confirm_pw) {
            $error_msg = 'New Password and Confirm Password do not match.';
        } elseif (strlen($new_pw) < 6) {
            $error_msg = 'New Password must be at least 6 characters long.';
        } else {
            // Verify current password
            if (!password_verify($current_pw, $current_user['password_hash'])) {
                $error_msg = 'Current password is incorrect. Please try again.';
            } else {
                try {
                    $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$hashed, $user_id]);
                    $success_msg = 'Password changed successfully.';
                } catch (PDOException $e) {
                    $error_msg = 'Error changing password: ' . $e->getMessage();
                }
            }
        }
    }
}

// ----------------------------------------------------
// FETCH STUDENT OR ADMIN SPECIFIC DATA
// ----------------------------------------------------
$enrollment = null;
$modules = [];
$assignments_uploaded = [];
$exam_results = [];
$certificates = [];
$payments_history = [];
$active_exam = null;

if ($user_role === 'student' && $account_status === 'active') {
    try {
        // Faculty Title
        $fac_stmt = $pdo->prepare("SELECT * FROM faculties WHERE id = ?");
        $fac_stmt->execute([$faculty_id]);
        $enrollment = $fac_stmt->fetch();

        // Load modules: Universal (NULL) + Faculty specific
        $mod_stmt = $pdo->prepare("SELECT * FROM modules WHERE faculty_id IS NULL OR faculty_id = ? ORDER BY module_number ASC");
        $mod_stmt->execute([$faculty_id]);
        $modules = $mod_stmt->fetchAll();

        // Load submitted assignments
        $assign_stmt = $pdo->prepare("SELECT * FROM assignments WHERE user_id = ?");
        $assign_stmt->execute([$user_id]);
        $assignments_uploaded = $assign_stmt->fetchAll(PDO::FETCH_UNIQUE); // Keyed by module_id

        // Load active exam details
        $ex_stmt = $pdo->prepare("SELECT * FROM exams WHERE faculty_id = ? LIMIT 1");
        $ex_stmt->execute([$faculty_id]);
        $active_exam = $ex_stmt->fetch();

        // Load exam attempts
        $attempt_stmt = $pdo->prepare("SELECT * FROM exam_attempts WHERE user_id = ?");
        $attempt_stmt->execute([$user_id]);
        $exam_results = $attempt_stmt->fetchAll();

        // Load certificates
        $cert_stmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ?");
        $cert_stmt->execute([$user_id]);
        $certificates = $cert_stmt->fetchAll();

        // Load payments
        $pay_stmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ?");
        $pay_stmt->execute([$user_id]);
        $payments_history = $pay_stmt->fetchAll();

        // --- NEW CALCULATED BLOCKS FOR TUITION PAYWALL & RESIT PAYWALL ---
        $installments_paid = 0;
        $is_installment_plan = false;
        foreach ($payments_history as $p) {
            if ($p['type'] === 'tuition' && $p['status'] === 'paid') {
                if (floatval($p['amount']) == 749.00) {
                    $is_installment_plan = true;
                    $installments_paid++;
                } elseif (floatval($p['amount']) == 2249.00) {
                    $is_installment_plan = false;
                }
            }
        }
        
        $exam_failed = false;
        $exam_passed = false;
        $resit_unlocked = true;
        foreach ($exam_results as $att) {
            if ($att['score'] >= 70.00 && $att['status'] === 'completed') {
                $exam_passed = true;
            }
        }
        if (!empty($exam_results)) {
            $latest_attempt = end($exam_results);
            if ($latest_attempt['score'] < 70.00 || $latest_attempt['status'] === 'force_submitted_violation') {
                if (!$exam_passed) {
                    $exam_failed = true;
                    $resit_unlocked = false;
                    foreach ($payments_history as $p) {
                        if ($p['type'] === 'tuition' && $p['status'] === 'paid' && floatval($p['amount']) == 150.00 && strtotime($p['created_at']) > strtotime($latest_attempt['end_time'])) {
                            $resit_unlocked = true;
                            break;
                        }
                    }
                }
            }
        }

    } catch (PDOException $e) {
        $error_msg = 'Error fetching student data: ' . $e->getMessage();
    }
}

// Admin Dashboard Data
$pending_grading = [];
$pending_remittance = [];
$affiliate_applications = [];
$certificate_registry = [];
$students_list = [];
$all_exam_attempts = [];
$all_certificates = [];

if ($user_role === 'admin') {
    try {
        // Pending grading
        $pending_grading = $pdo->query("SELECT a.*, u.full_name as student_name, m.title as module_title, m.module_number FROM assignments a JOIN users u ON a.user_id = u.id JOIN modules m ON a.module_id = m.id WHERE a.status = 'pending'")->fetchAll();

        // Pending manual payments
        $pending_remittance = $pdo->query("SELECT p.*, u.full_name as student_name, u.email as student_email FROM payments p JOIN users u ON p.user_id = u.id WHERE p.status = 'pending_manual_unlock'")->fetchAll();

        // Pending affiliate partners
        $affiliate_applications = $pdo->query("SELECT * FROM affiliates WHERE application_status = 'pending'")->fetchAll();

        // Verifiable Registry
        $certificate_registry = $pdo->query("SELECT cert.*, u.full_name as student_name, f.name as faculty_name FROM certificates cert JOIN users u ON cert.user_id = u.id JOIN faculties f ON u.faculty_id = f.id")->fetchAll();

        // Fetch students directory for page=students
        if ($page === 'students') {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : '';

            $query_str = "SELECT u.*, f.name as faculty_name FROM users u LEFT JOIN faculties f ON u.faculty_id = f.id WHERE u.role = 'student'";
            $params = [];
            
            if (!empty($search)) {
                $query_str .= " AND u.full_name LIKE ?";
                $params[] = '%' . $search . '%';
            }
            
            if (!empty($status_filter)) {
                $query_str .= " AND u.account_status = ?";
                $params[] = $status_filter;
            }
            
            $query_str .= " ORDER BY u.id DESC";
            
            $students_stmt = $pdo->prepare($query_str);
            $students_stmt->execute($params);
            $students_list = $students_stmt->fetchAll();
        }

        // Fetch single student details for view_id if set
        $view_student = null;
        $view_assignments = [];
        $view_exams = [];
        $view_certificates = [];
        $view_payments = [];
        
        if ($page === 'students' && isset($_GET['view_id'])) {
            $view_id = intval($_GET['view_id']);
            
            // 1. Fetch user detail
            $u_stmt = $pdo->prepare("SELECT u.*, f.name as faculty_name FROM users u LEFT JOIN faculties f ON u.faculty_id = f.id WHERE u.id = ? AND u.role = 'student'");
            $u_stmt->execute([$view_id]);
            $view_student = $u_stmt->fetch();
            
            if ($view_student) {
                // 2. Fetch assignments
                $a_stmt = $pdo->prepare("SELECT a.*, m.title as module_title, m.module_number FROM assignments a JOIN modules m ON a.module_id = m.id WHERE a.user_id = ? ORDER BY m.module_number ASC");
                $a_stmt->execute([$view_id]);
                $view_assignments = $a_stmt->fetchAll();
                
                // 3. Fetch exam attempts
                $e_stmt = $pdo->prepare("SELECT ea.*, ex.pass_threshold, ex.duration_minutes FROM exam_attempts ea JOIN exams ex ON ea.exam_id = ex.id WHERE ea.user_id = ? ORDER BY ea.id DESC");
                $e_stmt->execute([$view_id]);
                $view_exams = $e_stmt->fetchAll();
                
                // 4. Fetch certificates
                $c_stmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ? ORDER BY id DESC");
                $c_stmt->execute([$view_id]);
                $view_certificates = $c_stmt->fetchAll();
                
                // 5. Fetch payments
                $p_stmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY id DESC");
                $p_stmt->execute([$view_id]);
                $view_payments = $p_stmt->fetchAll();
            }
        }

        // Fetch all exam attempts for exams_report page
        if ($page === 'exams_report') {
            $all_exam_attempts = $pdo->query("
                SELECT ea.*, u.full_name as student_name, f.name as faculty_name
                FROM exam_attempts ea
                JOIN users u ON ea.user_id = u.id
                LEFT JOIN faculties f ON u.faculty_id = f.id
                ORDER BY ea.id DESC
            ")->fetchAll();
        }

        // Fetch all certificates for certificates_registry page
        if ($page === 'certificates_registry') {
            $all_certificates = $pdo->query("
                SELECT cert.*, u.full_name as student_name, f.name as faculty_name
                FROM certificates cert
                JOIN users u ON cert.user_id = u.id
                LEFT JOIN faculties f ON u.faculty_id = f.id
                ORDER BY cert.id DESC
            ")->fetchAll();
        }

    } catch (PDOException $e) {
        $error_msg = 'Error fetching admin records: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - UK London International Award Board</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global alert override with SweetAlert2
        window.alert = function(message) {
            var isSuccess = /success|complete|confirmed|verified|approved/i.test(message);
            Swal.fire({
                icon: isSuccess ? 'success' : 'warning',
                title: isSuccess ? 'Confirmation' : 'Registry Notice',
                text: message,
                confirmButtonColor: '#002F6C'
            });
        };
        // Apply saved theme immediately to avoid FOUC (Flash of Unstyled Content)
        (function(){var t=localStorage.getItem('lms_theme');if(t)document.documentElement.setAttribute('data-theme',t);})();
    </script>
    
    <!-- ANTI-CHEAT ENGINE (FOR STUDENT TIMED EXAMS) -->
    <?php if ($user_role === 'student' && $account_status === 'active' && $active_exam): 
        // Check if there is an in_progress attempt
        $check_term = $pdo->prepare("SELECT id, start_time FROM exam_attempts WHERE user_id = ? AND exam_id = ? AND status = 'in_progress'");
        $check_term->execute([$user_id, $active_exam['id']]);
        $has_active_term = $check_term->fetch();
        
        $seconds_left = ($active_exam['duration_minutes'] ?: 120) * 60;
        if ($has_active_term) {
            $elapsed = time() - strtotime($has_active_term['start_time']);
            $seconds_left = (($active_exam['duration_minutes'] ?: 120) * 60) - $elapsed;
            if ($seconds_left <= 0) {
                $seconds_left = 0;
            }
        }
    ?>
    <style>
        .exam-terminal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            z-index: 9999;
            padding: 40px;
            overflow-y: auto;
            user-select: none;
            -webkit-user-select: none;
        }
        
        .timer-badge {
            position: fixed;
            top: 20px;
            right: 40px;
            background-color: #002F6C;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
    <script>
        var examTimer;
        var secondsLeft = <?php echo $seconds_left; ?>;
        var violationsCount = 0;
        var examActive = false;

        window.addEventListener('DOMContentLoaded', (event) => {
            <?php if ($has_active_term): ?>
                if (secondsLeft <= 0) {
                    forceSubmitExam('timeout');
                } else {
                    startExamEngine();
                }
            <?php endif; ?>
        });

        function startExamEngine() {
            examActive = true;
            document.getElementById('examTerminal').style.display = 'block';
            document.body.style.overflow = 'hidden';

            examTimer = setInterval(function() {
                secondsLeft--;
                var mins = Math.floor(secondsLeft / 60);
                var secs = secondsLeft % 60;
                document.getElementById('countdownText').innerText = mins + ":" + (secs < 10 ? "0" : "") + secs;

                if (secondsLeft <= 0) {
                    clearInterval(examTimer);
                    forceSubmitExam('timeout');
                }
            }, 1000);

            document.addEventListener('visibilitychange', handleCheatViolation);
            window.addEventListener('blur', handleCheatViolation);
            document.addEventListener('contextmenu', preventDefaultAction);
            document.addEventListener('keydown', handleKeyBlock);
        }

        function preventDefaultAction(e) {
            e.preventDefault();
        }

        function handleKeyBlock(e) {
            if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'x' || e.key === 'u')) {
                e.preventDefault();
                alert('Action locked: Copy/Paste shortcut disabled.');
            }
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                e.preventDefault();
            }
        }

        function handleCheatViolation() {
            if (!examActive) return;
            violationsCount++;
            
            if (violationsCount === 1) {
                alert('WARNING (Violation 1/2): You switched tabs or exited the exam terminal. The next violation will submit your exam with 0% score and LOCK your account.');
            } else if (violationsCount >= 2) {
                forceSubmitExam('cheat');
            }
        }

        function forceSubmitExam(reason) {
            examActive = false;
            clearInterval(examTimer);
            
            document.removeEventListener('visibilitychange', handleCheatViolation);
            window.removeEventListener('blur', handleCheatViolation);
            document.removeEventListener('contextmenu', preventDefaultAction);
            document.removeEventListener('keydown', handleKeyBlock);

            if (reason === 'cheat') {
                document.getElementById('force_submit_flag').value = '1';
                document.getElementById('violations_field').value = violationsCount;
                document.getElementById('exam_score_field').value = '0.00';
                document.getElementById('examForm').submit();
            } else if (reason === 'timeout') {
                alert('Time expired. Submitting assessment.');
                document.getElementById('violations_field').value = violationsCount;
                // Auto timeout yields 0.00 score
                document.getElementById('exam_score_field').value = '0.00';
                document.getElementById('examForm').submit();
            }
        }

        function finishExamNormal() {
            var correctAnswers = {
                'business': ['B', 'B', 'B'],
                'health': ['B', 'B', 'B'],
                'nutrition': ['A', 'B', 'B']
            };
            var faculty = '<?php echo strtolower($enrollment ? ($enrollment['name'] ?? "") : ""); ?>';
            var correctCount = 0;
            var total = 3;
            
            for (var i = 1; i <= total; i++) {
                var radios = document.getElementsByName('q' + i);
                var answered = false;
                for (var r = 0; r < radios.length; r++) {
                    if (radios[r].checked) {
                        answered = true;
                        if (radios[r].value === correctAnswers[faculty][i-1]) {
                            correctCount++;
                        }
                        break;
                    }
                }
                if (!answered) {
                    alert('Please answer Question ' + i + ' before submitting your paper.');
                    return;
                }
            }
            
            var calculatedScore = (correctCount / total) * 100;
            
            examActive = false;
            clearInterval(examTimer);
            
            document.removeEventListener('visibilitychange', handleCheatViolation);
            window.removeEventListener('blur', handleCheatViolation);
            document.removeEventListener('contextmenu', preventDefaultAction);
            document.removeEventListener('keydown', handleKeyBlock);
            
            document.getElementById('exam_score_field').value = calculatedScore.toFixed(2);
            document.getElementById('violations_field').value = violationsCount;
            document.getElementById('examForm').submit();
        }
    </script>
    <?php endif; ?>
</head>
<body class="db-body">
    <div class="db-layout-container <?php echo (($_COOKIE['db_sidebar_collapsed'] ?? '0') === '1') ? 'collapsed' : ''; ?>">

        <!-- ====================================================================== -->
        <!-- LEFT SIDEBAR PANEL -->
        <!-- ====================================================================== -->
        <aside class="db-sidebar" id="dbSidebar">
            <div class="db-brand" style="display:flex; align-items:center; gap:8px; padding: 15px 15px;">
                <img src="assets/images/logo.png" alt="Logo" style="max-height: 42px; object-fit: contain; display: block;">
                <span style="font-weight:700; font-size:15px; color:#FFFFFF; line-height: 1.2;">UK London Award</span>
            </div>
            
            <ul class="db-nav-menu">
                <li class="db-nav-section-title">Academic Portal</li>
                <li class="db-nav-item <?php echo ($page === 'dashboard' || empty($page)) ? 'active' : ''; ?>">
                    <a href="dashboard.php?page=dashboard">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                        <span class="nav-text">Overview</span>
                    </a>
                </li>
                
                <?php if ($user_role === 'student' && $account_status === 'active'): ?>
                    <li class="db-nav-item <?php echo $page === 'coursework' ? 'active' : ''; ?>">
                        <a href="dashboard.php?page=coursework">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            <span class="nav-text">My Coursework</span>
                        </a>
                    </li>
                    <li class="db-nav-item <?php echo $page === 'exams' ? 'active' : ''; ?>">
                        <a href="dashboard.php?page=exams">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span class="nav-text">Timed Exams</span>
                        </a>
                    </li>
                    <li class="db-nav-item <?php echo $page === 'certificates' ? 'active' : ''; ?>">
                        <a href="dashboard.php?page=certificates">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                            <span class="nav-text">Certificates</span>
                        </a>
                    </li>
                    <li class="db-nav-item <?php echo $page === 'payments' ? 'active' : ''; ?>">
                        <a href="dashboard.php?page=payments">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            <span class="nav-text">Tuition Payments</span>
                        </a>
                    </li>
                <?php elseif ($user_role === 'admin'): ?>
                    <li class="db-nav-item <?php echo $page === 'students' ? 'active' : ''; ?>">
                        <a href="dashboard.php?page=students">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span class="nav-text">Students Registry</span>
                        </a>
                    </li>
                    <li class="db-nav-item <?php echo $page === 'exams_report' ? 'active' : ''; ?>">
                        <a href="dashboard.php?page=exams_report">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <span class="nav-text">Exam Reports</span>
                        </a>
                    </li>
                    <li class="db-nav-item <?php echo $page === 'certificates_registry' ? 'active' : ''; ?>">
                        <a href="dashboard.php?page=certificates_registry">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            <span class="nav-text">Certificates Ledger</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="db-nav-section-title">Account</li>
                <li class="db-nav-item">
                    <a href="logout.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span class="nav-text">Sign Out</span>
                    </a>
                </li>
            </ul>

            <div class="db-sidebar-footer">
                <a href="dashboard.php?page=profile" style="text-decoration:none; display:block;">
                    <div class="db-user-profile" style="cursor:pointer;">
                        <div class="db-user-avatar">
                            <?php echo strtoupper(substr($user_name, 0, 2)); ?>
                        </div>
                        <div class="db-user-info">
                            <div class="db-user-name"><?php echo htmlspecialchars($user_name); ?></div>
                            <div class="db-user-role"><?php echo htmlspecialchars($user_role); ?></div>
                        </div>
                    </div>
                </a>
            </div>
        </aside>

        <!-- ====================================================================== -->
        <!-- MAIN PANEL CONTENT -->
        <!-- ====================================================================== -->
        <div class="db-main">
            
            <!-- Topbar header -->
            <header class="db-topbar">
                <div style="display: flex; align-items: center;">
                    <button class="db-mobile-toggle" onclick="toggleSidebar()">☰</button>
                    <div class="db-search-mock">
                        <!-- <span>🔍 Search (Ctrl + K)</span> -->
                    </div>
                </div>
                <div class="db-topbar-actions">
                    <div class="theme-toggle-wrap">
                        <button class="theme-toggle-btn" onclick="toggleThemeDropdown()" title="Switch Theme">
                            🎨 <span style="font-size:12px; font-weight:500;">Theme</span>
                        </button>
                        <div class="theme-dropdown" id="themeDropdown">
                            <button class="theme-option" onclick="setTheme('light')">
                                <span class="theme-option-icon">☀️</span> Light
                            </button>
                            <button class="theme-option" onclick="setTheme('dark')">
                                <span class="theme-option-icon">🌙</span> Dark
                            </button>
                            <button class="theme-option" onclick="setTheme('classic')">
                                <span class="theme-option-icon">📜</span> Classic
                            </button>
                        </div>
                    </div>
                    <div class="db-topbar-icon">
                        🔔<span class="db-topbar-badge"></span>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content wrapper -->
            <div class="db-content">

                <?php if (!empty($success_msg)): ?>
                    <div class="gov-success-banner">
                        <div class="gov-success-title">Success</div>
                        <p><?php echo $success_msg; ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_msg)): ?>
                    <div class="gov-error-banner">
                        <div class="gov-error-title">Alert</div>
                        <p><?php echo htmlspecialchars($error_msg); ?></p>
                    </div>
                <?php endif; ?>

                <!-- ====================================================================== -->
                <!-- ACCOUNT LOCKED GATE -->
                <!-- ====================================================================== -->
                <?php if ($account_status === 'locked'): ?>
                    <div class="db-card">
                        <div class="gov-error-banner" style="margin-bottom: 0;">
                            <div class="gov-error-title">Account Locked</div>
                            <p>Your student profile has been locked due to an anti-cheat exam violation or administrative hold.</p>
                            <p style="margin-top: 10px; font-weight: 600;">Please contact the registry at registry@liab-edu.org to appeal.</p>
                        </div>
                    </div>

                <!-- ====================================================================== -->
                <!-- TUITION PENDING PAYWALL GATE -->
                <!-- ====================================================================== -->
                <?php elseif ($account_status === 'pending_manual_unlock'): ?>
                    <div class="db-card">
                        <div class="gov-error-banner" style="border-color: #f47738; margin-bottom: 25px;">
                            <div class="gov-error-title" style="color: #f47738;">Account Pending Tuition Payment Verification</div>
                            <p>To access your coursework catalog, modules, and exam terminal, you must complete your registration tuition fee of <strong>$450.00</strong>.</p>
                        </div>

                        <div class="gov-grid-row">
                            <div class="gov-grid-column-two-thirds">
                                <h2>Manual Remittance Verification Gate</h2>
                                <p>If you made a cash remittance via Western Union, Ria, or WorldRemit, submit the transaction reference number below. Administrative confirmation takes up to 48 hours.</p>

                                <form action="remittance.php" method="POST" style="background-color:#fafcff; padding: 25px; border: 2px solid #002F6C; border-radius:4px; margin-bottom: 20px;">
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="sender_name">Sender name</label>
                                        <input class="gov-input" id="sender_name" name="sender_name" type="text" style="max-width:100%;" required>
                                    </div>

                                    <div class="gov-form-group">
                                        <label class="gov-label" for="method">Money Order Provider</label>
                                        <select class="gov-select" id="method" name="method" style="max-width:100%;" required>
                                            <option value="">-- Choose Provider --</option>
                                            <option value="western_union">Western Union</option>
                                            <option value="ria">Ria Money Transfer</option>
                                            <option value="worldremit">WorldRemit</option>
                                        </select>
                                    </div>

                                    <div class="gov-form-group">
                                        <label class="gov-label" for="transaction_ref">Reference MTCN / Code</label>
                                        <input class="gov-input" id="transaction_ref" name="transaction_ref" type="text" style="max-width:100%;" required>
                                    </div>

                                    <div class="gov-form-group">
                                        <label class="gov-label" for="amount">Amount Remitted ($)</label>
                                        <input class="gov-input" id="amount" name="amount" type="number" step="0.01" value="450.00" style="max-width:100%;" required>
                                    </div>

                                    <button type="submit" class="gov-button" style="width: 100%;">Submit payment reference</button>
                                </form>
                            </div>

                            <div class="gov-grid-column-one-third" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                                <h2>Online Card Checkout</h2>
                                <p style="font-size:15px; color:#555; line-height: 1.5;">Alternatively, pay securely with your debit or credit card for instant catalog access.</p>
                                <a href="checkout.php" class="gov-button" style="width:100%; margin-top: 15px;">Unlock instantly with Card</a>
                            </div>
                        </div>
                    </div>

                <!-- ====================================================================== -->
                <!-- STUDENT PORTAL CORE VIEW -->
                <!-- ====================================================================== -->
                <?php elseif ($user_role === 'student'): ?>
                    
                    <?php if ($page === 'dashboard' || empty($page)): ?>
                        <!-- Metrics grid -->
                        <div class="db-stat-grid">
                            <div class="db-stat-card">
                                <div class="db-stat-icon">📚</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value"><?php echo count($modules); ?></div>
                                    <div class="db-stat-label">Total Modules</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">📝</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value"><?php echo count($assignments_uploaded); ?></div>
                                    <div class="db-stat-label">Coursework Uploads</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">⏱️</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value"><?php echo count($exam_results) > 0 ? end($exam_results)['score'] . '%' : 'No Attempt'; ?></div>
                                    <div class="db-stat-label">Exam Score</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">🏆</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value"><?php echo count($certificates); ?></div>
                                    <div class="db-stat-label">Certificates Issued</div>
                                </div>
                            </div>
                        </div>

                        <div class="gov-grid-row">
                            <div class="gov-grid-column-two-thirds">
                                <!-- Enrollment card -->
                                <div class="db-card">
                                    <div class="db-card-title">Enrolled Academic Program</div>
                                    <p style="font-size:16px; font-weight:600; color:#002F6C; margin-bottom: 5px;">Faculty of <?php echo htmlspecialchars($enrollment ? $enrollment['name'] : "Not Assigned"); ?></p>
                                    <p class="gov-hint" style="margin-bottom:15px;">Registered Student ID: LIAB-ST-<?php echo $user_id; ?></p>
                                    <p style="font-size:14px; line-height:1.5; margin-bottom: 10px;">Welcome to your academic terminal! Please use the left-hand navigation links to access your coursework modules, launch the timed exams terminal, download your certificates, or check your billing transaction logs.</p>
                                </div>

                                <div class="db-card">
                                    <div class="db-card-title">Portal Quick Start Guide</div>
                                    <ul style="font-size:14px; line-height:1.8; color:var(--text-primary); margin-left:20px; list-style-type: disc;">
                                        <li><strong>Coursework:</strong> Review the universal and faculty coursework modules, and submit your homework assignments for evaluation.</li>
                                        <li><strong>Timed Exam:</strong> Once you are ready, start your comprehensive timed assessment exam (2-hour limit). Ensure you maintain window focus, as switching tabs will trigger security lockouts.</li>
                                        <li><strong>Certificate:</strong> A Gold Crest verifiable diploma certificate is generated automatically upon passing the final assessment with a grade of 60% or higher.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="gov-grid-column-one-third" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                                <div class="db-card">
                                    <div class="db-card-title" style="font-size: 16px;">Representative Rep Code</div>
                                    <p style="font-size:13px; color:#555;">Linked Affiliate Consultant:</p>
                                    <div style="background-color:#f6f8fa; padding:10px; border-radius:4px; font-size:13px; font-weight:600; margin-top:8px; display:inline-block;">
                                        <?php echo htmlspecialchars($current_user['rep_code'] ?: 'Independent Direct Signup'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($page === 'coursework'): ?>
                        <!-- Course Modules card -->
                        <div class="db-card" id="modulesSection">
                            <div class="db-card-title">Coursework Modules & Submissions</div>
                            <p style="font-size:14px; margin-bottom: 20px;">Complete modules 1 & 2 (universal) and modules 3 & 4 (faculty-specific). Upload coursework files here (Max 25MB, PDF/DOCX/JPG/PNG).</p>

                            <div class="gov-list-group" style="margin-top: 10px;">
                                <?php foreach ($modules as $mod): ?>
                                    <div class="gov-list-row" style="flex-direction: column; align-items: flex-start; gap: 12px; padding: 20px 0;">
                                        <div style="display:flex; justify-content:space-between; width:100%;">
                                            <span class="gov-list-key">Module <?php echo $mod['module_number']; ?>: <?php echo htmlspecialchars($mod['title']); ?></span>
                                            <div>
                                                <?php if ($mod['faculty_id'] === NULL): ?>
                                                    <span class="gov-tag gov-tag-grey" style="font-size:10px;">Universal</span>
                                                <?php else: ?>
                                                    <span class="gov-tag" style="font-size:10px;">Faculty Focus</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <p style="font-size: 14px; margin-bottom:5px; color:#555;"><?php echo htmlspecialchars($mod['content_path']); ?></p>

                                        <?php if (isset($assignments_uploaded[$mod['id']])): ?>
                                            <?php $sub = $assignments_uploaded[$mod['id']]; ?>
                                            <div style="font-size: 13px; background-color:#fafbfe; padding:10px; width:100%; border-left: 3px solid #002F6C; border-radius: 4px;">
                                                Uploaded Document: <a href="<?php echo htmlspecialchars($sub['file_path']); ?>" target="_blank"><?php echo basename($sub['file_path']); ?></a> (<?php echo $sub['file_size']; ?>)<br>
                                                Status: <strong><?php echo strtoupper($sub['status']); ?></strong> 
                                                <?php if ($sub['status'] === 'reviewed'): ?>
                                                    | Grade: <strong style="color:#00703c;"><?php echo htmlspecialchars($sub['grade']); ?></strong>
                                                <?php endif; ?>
                                                <?php if (!empty($sub['feedback'])): ?>
                                                    <br><strong>Feedback:</strong> <em><?php echo htmlspecialchars($sub['feedback']); ?></em>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="gov-tag gov-tag-grey" style="font-size: 10px;">Awaiting Submission</span>
                                        <?php endif; ?>

                                        <!-- Upload form -->
                                        <form action="dashboard.php?page=coursework" method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap: 15px; width:100%; margin-top: 8px;">
                                            <input type="hidden" name="module_id" value="<?php echo $mod['id']; ?>">
                                            <input type="file" name="assignment_file" required style="font-size:13px;">
                                            <button type="submit" name="upload_assignment" class="gov-button" style="font-size:12px; padding: 6px 12px; border-radius: 4px;">Upload Assignment</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <?php elseif ($page === 'exams'): ?>
                        <!-- Exams card -->
                        <div class="db-card" id="examsSection">
                            <div class="db-card-title">Faculty Timed Assessment</div>
                            <p style="font-size: 14px; margin-bottom: 20px;">Complete your timed examination. Anti-cheat visibility tracking metrics are active. Minimum passing grade is 70%.</p>

                            <div class="gov-list-group" style="margin-top: 10px; margin-bottom: 0;">
                                <?php if (!$active_exam): ?>
                                    <p class="gov-hint">No examinations configured for this faculty.</p>
                                <?php else: ?>
                                    <div class="gov-list-row" style="padding: 15px 0; border-bottom: none; flex-direction:column; align-items:flex-start; gap:12px;">
                                        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
                                            <div>
                                                <span class="gov-list-key">Timed Comprehensive Assessment</span>
                                                <span class="gov-hint" style="margin-top: 5px;">Pass Threshold: 70% | Duration: <?php echo $active_exam['duration_minutes']; ?> mins</span>
                                            </div>
                                            <div>
                                                <?php if (!empty($exam_results)): ?>
                                                    <?php $latest_attempt = end($exam_results); ?>
                                                    <span class="gov-tag <?php echo $latest_attempt['score'] >= 70 ? 'gov-tag-green' : 'gov-tag-yellow'; ?>">
                                                        Score: <?php echo $latest_attempt['score']; ?>% (<?php echo strtoupper($latest_attempt['status']); ?>)
                                                    </span>
                                                <?php else: ?>
                                                    <span class="gov-tag gov-tag-grey" style="text-transform:none;">No Attempts Completed</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div style="width:100%; margin-top: 10px;">
                                            <?php if ($exam_passed): ?>
                                                <div style="background-color: #fafcff; padding: 20px; border-left: 5px solid #00703c; border-radius: 4px; width:100%;">
                                                    <h3 style="color:#00703c; margin-bottom:8px; font-size:14px; font-weight:bold;">✓ Assessment Passed & Locked</h3>
                                                    <p style="font-size:12px; color:#555; margin-bottom:0; line-height:1.45;">You have successfully passed the final assessment with a score of 70% or higher. Your result is locked and your certificate has been awarded. You can view or download it from the Certificates tab.</p>
                                                </div>
                                            <?php elseif ($exam_failed): ?>
                                                <?php if ($resit_unlocked): ?>
                                                    <div style="margin-bottom:12px; font-size:13px; color:#00703c; font-weight:600;">✓ Exam Resit eligibility unlocked. Ready to start attempt.</div>
                                                    <form action="dashboard.php?page=exams" method="POST" style="display:inline;">
                                                        <input type="hidden" name="start_exam_attempt" value="1">
                                                        <input type="hidden" name="exam_id" value="<?php echo $active_exam['id']; ?>">
                                                        <button type="submit" class="gov-button" style="font-size:12px; padding: 8px 16px; border-radius: 4px;">Retake Assessment Now</button>
                                                    </form>
                                                <?php else: ?>
                                                    <!-- Render Resit Paywall Form -->
                                                    <div style="background-color: #fafbfe; padding: 20px; border-left: 5px solid #d4351c; border-radius: 4px; width:100%;">
                                                        <h3 style="color:#d4351c; margin-bottom:8px; font-size:14px;">Assessment Resit Paywall</h3>
                                                        <p style="font-size:12px; color:#555; margin-bottom:15px; line-height:1.45;">You did not achieve the required passing threshold of 70% on your exam attempt. To reactivate the assessment terminal and try again, you must process the Board Resit Fee of <strong>£150.00</strong>.</p>
                                                        
                                                        <form action="dashboard.php?page=exams" method="POST" style="max-width:360px;">
                                                            <input type="hidden" name="pay_resit_fee" value="1">
                                                            <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:12px;">
                                                                <input class="gov-input" type="text" name="card_holder" placeholder="Cardholder Name" required style="font-size:11px; padding:6px; border:1px solid #ccc; max-width:100%;">
                                                                <input class="gov-input" type="text" name="card_number" placeholder="Card Number" required style="font-size:11px; padding:6px; border:1px solid #ccc; max-width:100%;">
                                                                <div style="display:flex; gap:6px;">
                                                                    <input class="gov-input" type="text" name="card_exp" placeholder="MM/YY" required style="font-size:11px; padding:6px; border:1px solid #ccc; width:60%; max-width:100%;">
                                                                    <input class="gov-input" type="text" name="card_cvc" placeholder="CVC" required style="font-size:11px; padding:6px; border:1px solid #ccc; width:40%; max-width:100%;">
                                                                </div>
                                                            </div>
                                                            <button type="submit" class="gov-button" style="font-size:11px; padding: 8px 16px; border-radius: 4px; background-color:#00703c; border-bottom:none;">Pay £150 Resit Fee</button>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <!-- First attempt button -->
                                                <form action="dashboard.php?page=exams" method="POST" style="display:inline;">
                                                    <input type="hidden" name="start_exam_attempt" value="1">
                                                    <input type="hidden" name="exam_id" value="<?php echo $active_exam['id']; ?>">
                                                    <button type="submit" class="gov-button" style="font-size:12px; padding: 8px 16px; border-radius: 4px;">Start Assessment Now</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php elseif ($page === 'certificates'): ?>
                        <!-- Certificate card -->
                        <div class="db-card" id="certsSection">
                            <div class="db-card-title">Verifiable Issued Certificate Credentials</div>
                            <div class="gov-list-group" style="margin-top: 10px; margin-bottom: 0;">
                                <?php if (empty($certificates)): ?>
                                    <p class="gov-hint" style="padding: 10px 0;">No certificates issued yet. Pass your exam with 70% or more to unlock.</p>
                                <?php else: ?>
                                    <?php foreach ($certificates as $c): ?>
                                        <div class="gov-list-row" style="padding: 15px 0;">
                                            <div>
                                                <span class="gov-list-key">Faculty Diploma Certificate</span>
                                                <span class="gov-hint" style="margin-top: 5px;">UID Reference: <code><?php echo htmlspecialchars($c['certificate_uid']); ?></code> | Issued: <?php echo $c['issue_date']; ?></span>
                                            </div>
                                            <div style="display:flex; gap:10px;">
                                                <a href="certificate.php?uid=<?php echo urlencode($c['certificate_uid']); ?>" target="_blank" class="gov-button" style="font-size:12px; padding: 6px 12px; border-radius: 4px; text-decoration:none;">View & Print</a>
                                                <a href="<?php echo htmlspecialchars($c['pdf_path']); ?>" download class="gov-button gov-button-secondary" style="font-size:12px; padding: 6px 12px; border-radius: 4px; text-decoration:none;">Download PDF</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php elseif ($page === 'payments'): ?>
                        <!-- Payment history -->
                        <div class="db-card">
                            <div class="db-card-title" style="font-size: 18px; margin-bottom: 15px;">Tuition Payment History & Status</div>
                            
                            <div class="gov-grid-row">
                                <div class="gov-grid-column-two-thirds">
                                    <div style="background-color: #fafbfe; padding: 20px; border: 1.5px solid #EBF3FC; border-radius: 8px; margin-bottom:15px;">
                                        <?php if (empty($payments_history)): ?>
                                            <span class="gov-hint">No transactions registered.</span>
                                        <?php else: ?>
                                            <table class="gov-table" style="margin:0;">
                                                <thead>
                                                    <tr>
                                                        <th>Billing Ref</th>
                                                        <th>Amount</th>
                                                        <th>Provider</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($payments_history as $p): ?>
                                                        <tr>
                                                            <td><code><?php echo htmlspecialchars($p['transaction_ref'] ?: 'Pending Review'); ?></code></td>
                                                            <td><strong>£<?php echo number_format($p['amount'], 2); ?></strong></td>
                                                            <td><?php echo strtoupper($p['method']); ?></td>
                                                            <td>
                                                                <span class="gov-tag <?php echo $p['status'] === 'paid' ? 'gov-tag-green' : 'gov-tag-yellow'; ?>" style="font-size:10px; padding:3px 8px; text-transform:none;">
                                                                    <?php echo $p['status']; ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="gov-grid-column-one-third">
                                    <?php if ($is_installment_plan && $installments_paid < 3): ?>
                                        <div style="background-color: #fafcff; padding: 20px; border: 1.5px solid #002F6C; border-radius: 8px;">
                                            <div style="font-size:14px; font-weight:600; color:#002F6C; margin-bottom:8px;">Installment Tuition Status</div>
                                            <p style="font-size:13px; margin-bottom:15px; color:#555; line-height:1.45;">Paid: <?php echo $installments_paid; ?> of 3 installments.<br>Remaining balance: <strong>£<?php echo (3 - $installments_paid) * 749; ?>.00</strong></p>
                                            
                                            <form action="dashboard.php?page=payments" method="POST">
                                                <input type="hidden" name="pay_installment" value="1">
                                                <input type="hidden" name="installment_number" value="<?php echo $installments_paid + 1; ?>">
                                                
                                                <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:12px;">
                                                    <input type="text" name="card_holder" placeholder="Cardholder Name" required style="width:100%; font-size:12px; padding:8px; border:1px solid #ccc; border-radius:4px;">
                                                    <input type="text" name="card_number" placeholder="Card Number" required style="width:100%; font-size:12px; padding:8px; border:1px solid #ccc; border-radius:4px;">
                                                    <div style="display:flex; gap:6px;">
                                                        <input type="text" name="card_exp" placeholder="MM/YY" required style="width:60%; font-size:12px; padding:8px; border:1px solid #ccc; border-radius:4px;">
                                                        <input type="text" name="card_cvc" placeholder="CVC" required style="width:40%; font-size:12px; padding:8px; border:1px solid #ccc; border-radius:4px;">
                                                    </div>
                                                </div>
                                                <button type="submit" class="gov-button" style="width:100%; font-size:12px; padding:10px; border-radius:4px;">Pay Installment <?php echo $installments_paid + 1; ?> (£749.00)</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <div style="background-color: #fafcff; padding: 20px; border: 1.5px solid #00703c; border-radius: 8px; font-size:13px; color:#00703c; font-weight:600;">
                                            ✓ All program tuition fee parameters are paid in full. No outstanding balance.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>

                    <!-- TIMED EXAM OVERLAY TERMINAL -->
                    <?php if ($active_exam): ?>
                    <div id="examTerminal" class="exam-terminal-overlay">
                        <div class="timer-badge">Remaining: <span id="countdownText">120:00</span></div>
                        <h1 style="border-bottom: 2px solid #002F6C; padding-bottom: 10px; margin-bottom: 30px;">Timed Faculty Evaluation</h1>
                        
                        <div class="gov-error-banner" style="border-color: #f47738; margin-bottom: 30px;">
                            <div class="gov-error-title" style="color: #f47738;">Anti-Cheat Warning</div>
                            <p style="font-size:16px;">This assessment window is monitored. Exiting fullscreen, minimizing, switching tabs, or copying text will cancel the session, lock your profile, and record a 0% failure. contextmenu controls have been locked.</p>
                        </div>

                        <form id="examForm" action="dashboard.php" method="POST">
                            <input type="hidden" name="submit_exam_score" value="1">
                            <input type="hidden" name="exam_id" value="<?php echo $active_exam['id']; ?>">
                            <input type="hidden" id="exam_score_field" name="exam_score" value="0">
                            <input type="hidden" id="violations_field" name="violations" value="0">
                            <input type="hidden" id="force_submit_flag" name="force_submit_violation" value="0">

                            <!-- Dynamic exam questions based on Faculty -->
                            <?php 
                            $fac_name_lower = strtolower($enrollment ? ($enrollment['name'] ?? "") : "");
                            if ($fac_name_lower === 'business'): ?>
                                <div class="gov-form-group">
                                    <label class="gov-label" style="font-size:16px;">Question 1: Which core document outlines business regulations and research ethics?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="A" required> A. Standard Ledger Guide</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="B"> B. Orientation Ethics Guide</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="C"> C. Financial Audit Manual</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="D"> D. Code of Business Conduct</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 2: What defines strategic human resource compliance in human capital?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="A" required> A. Setting standardized payroll metrics</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="B"> B. Aligning workforce protocols with organizational ethics and goals</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="C"> C. Implementing automated contractor shifts</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="D"> D. Daily employee time logging audits</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 3: Which protocol is used to evaluate startup financial viability?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="A" required> A. Ledger double-entry checking</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="B"> B. Net Present Value (NPV) and operational break-even analysis</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="C"> C. Cash count index checking</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="D"> D. Rep code referral tracking</label>
                                    </div>
                                </div>
                            <?php elseif ($fac_name_lower === 'health'): ?>
                                <div class="gov-form-group">
                                    <label class="gov-label" style="font-size:16px;">Question 1: What is the primary procedure for clinical contamination safety?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="A" required> A. Wiping surfaces once daily</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="B"> B. Multi-barrier isolation and strict sterile field protocols</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="C"> C. Maintaining open ventilation parameters</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="D"> D. Standard medical gloves audits</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 2: What does HIPAA require for digital patient record tracking?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="A" required> A. Maintaining printed files in registry folders</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="B"> B. End-to-end audit logs, access tracking, and storage encryption</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="C"> C. Sharing registry files with authorized rep consultants</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="D"> D. Storing clinical dossiers in local PC directories</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 3: What defines an epidemiological outbreak audit workflow?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="A" required> A. Reviewing daily pharmacy medicine logs</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="B"> B. Tracing index cases, auditing compliance, and setting quarantine guidelines</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="C"> C. Dispatching public health warning leaflets</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="D"> D. Surveying community hospital numbers</label>
                                    </div>
                                </div>
                            <?php else: ?> <!-- Nutrition -->
                                <div class="gov-form-group">
                                    <label class="gov-label" style="font-size:16px;">Question 1: What cellular process is directly regulated by micronutrient profiles?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="A" required> A. Digestion enzyme activation and mitochondrial respiration cofactors</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="B"> B. Standard muscular tissue ATP contractions</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="C"> C. Pancreas insulin synthesis pathways</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="D"> D. Cell membrane fatty acid balance</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 2: Which profile is recommended for a clinical cardiovascular management audit?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="A" required> A. High sucrose carbohydrate loading</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="B"> B. Low sodium DASH diet rich in magnesium and omega-3 fatty acids</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="C"> C. Pure plant proteins loading profile</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="D"> D. Intermittent liquid fasting protocols</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 3: What represents the highest level of nutritional research verification?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="A" required> A. Individual patient case diaries</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="B"> B. Randomized Double-Blind Controlled Trials and Systematic Meta-Analyses</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="C"> C. Peer review nutrition guides</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="D"> D. University focus research panels</label>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div style="margin-top: 40px; border-top: 1.5px solid var(--border-main); padding-top: 20px;">
                                <button type="button" onclick="finishExamNormal()" class="gov-button" style="border-radius:6px; padding:12px 30px;">Submit Exam Paper</button>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>

                <!-- ====================================================================== -->
                <!-- ASSESSOR/ADMIN PORTAL CORE VIEW -->
                <!-- ====================================================================== -->
                <?php else: ?>

                    <!-- ====================================================================== -->
                    <!-- TAB PAGE A: OVERVIEW / DASHBOARD -->
                    <!-- ====================================================================== -->
                    <?php if ($page === 'dashboard'): ?>
                        <!-- Metrics grid -->
                        <div class="db-stat-grid">
                            <div class="db-stat-card">
                                <div class="db-stat-icon">✏️</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value"><?php echo count($pending_grading); ?></div>
                                    <div class="db-stat-label">Homeworks to Grade</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">💵</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value"><?php echo count($pending_remittance); ?></div>
                                    <div class="db-stat-label">Pending Remittances</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">🤝</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value"><?php echo count($affiliate_applications); ?></div>
                                    <div class="db-stat-label">Partner Applications</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">🎓</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value"><?php echo count($certificate_registry); ?></div>
                                    <div class="db-stat-label">Issued Credentials</div>
                                </div>
                            </div>
                        </div>

                        <div class="gov-grid-row">
                            <!-- Main grading column -->
                            <div class="gov-grid-column-two-thirds">
                                
                                <!-- Homework Evaluation -->
                                <div class="db-card" id="gradingSection">
                                    <div class="db-card-title">Evaluate Student Coursework</div>
                                    <?php if (empty($pending_grading)): ?>
                                        <p class="gov-hint">No submissions currently pending review.</p>
                                    <?php else: ?>
                                        <table class="gov-table">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Module</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pending_grading as $g): ?>
                                                    <tr>
                                                        <td><strong><?php echo htmlspecialchars($g['student_name']); ?></strong></td>
                                                        <td>Mod <?php echo $g['module_number']; ?>: <?php echo htmlspecialchars($g['module_title']); ?><br><span class="gov-hint"><a href="<?php echo htmlspecialchars($g['file_path']); ?>" target="_blank">Download file (<?php echo $g['file_size']; ?>)</a></span></td>
                                                        <td>
                                                            <form action="dashboard.php?page=dashboard" method="POST" style="display:flex; flex-direction:column; gap:6px;">
                                                                <input type="hidden" name="assignment_id" value="<?php echo $g['id']; ?>">
                                                                <select class="gov-select" name="grade" style="font-size:13px; padding:6px; max-width:140px;" required>
                                                                    <option value="">-- Grade --</option>
                                                                    <option value="Pass">Pass</option>
                                                                    <option value="Merit">Merit</option>
                                                                    <option value="Distinction">Distinction</option>
                                                                    <option value="Refer">Refer (Fail)</option>
                                                                </select>
                                                                <input class="gov-input" name="feedback" type="text" placeholder="Remarks" style="font-size:13px; padding:6px; max-width:140px;">
                                                                <button type="submit" name="grade_assignment" class="gov-button" style="font-size:11px; padding: 4px 8px; max-width:100px; border-radius: 4px;">Submit</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>

                                <!-- Remittance Approvals -->
                                <div class="db-card" id="remittanceSection">
                                    <div class="db-card-title">Tuition Money Order approvals</div>
                                    <?php if (empty($pending_remittance)): ?>
                                        <p class="gov-hint">No payment remittances pending confirmation.</p>
                                    <?php else: ?>
                                        <table class="gov-table">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Reference MTCN</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($pending_remittance as $p): ?>
                                                    <tr>
                                                        <td><strong><?php echo htmlspecialchars($p['student_name']); ?></strong><br><span class="gov-hint"><?php echo strtoupper($p['method']); ?> ($<?php echo $p['amount']; ?>)</span></td>
                                                        <td><code><?php echo htmlspecialchars($p['transaction_ref']); ?></code></td>
                                                        <td>
                                                            <form action="dashboard.php?page=dashboard" method="POST" style="display:inline;">
                                                                <input type="hidden" name="payment_id" value="<?php echo $p['id']; ?>">
                                                                <input type="hidden" name="payment_action" value="approve">
                                                                <button type="submit" name="review_remittance" class="gov-button" style="font-size:11px; padding: 4px 8px; border-radius:4px; background-color:#00703c;">Approve</button>
                                                            </form>
                                                            <form action="dashboard.php?page=dashboard" method="POST" style="display:inline; margin-left: 4px;">
                                                                <input type="hidden" name="payment_id" value="<?php echo $p['id']; ?>">
                                                                <input type="hidden" name="payment_action" value="reject">
                                                                <button type="submit" name="review_remittance" class="gov-button gov-button-secondary" style="font-size:11px; padding: 4px 8px; border-radius:4px; background-color:#d4351c; color:#fff; border-bottom-color:#80180a;">Reject</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>

                                <!-- Certificate registry -->
                                <div class="db-card" id="certRegistrySection">
                                    <div class="db-card-title">Verifiable Certificate Registry</div>
                                    <table class="gov-table">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Course Program</th>
                                                <th>Verifiable UID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($certificate_registry)): ?>
                                                <tr>
                                                    <td colspan="3" class="gov-hint" style="text-align:center;">No certificate credentials issued.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($certificate_registry as $c): ?>
                                                    <tr>
                                                        <td><strong><?php echo htmlspecialchars($c['student_name']); ?></strong></td>
                                                        <td>Faculty of <?php echo htmlspecialchars($c['faculty_name']); ?></td>
                                                        <td><code><?php echo htmlspecialchars($c['certificate_uid']); ?></code></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                            <!-- Sidebar partner Column -->
                            <div class="gov-grid-column-one-third" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                                <div class="db-card" id="affiliateSection">
                                    <div class="db-card-title" style="font-size: 16px;">Affiliate Partners</div>
                                    <p style="font-size:13px; color:#777; margin-bottom:15px;">Review consultant onboarding applications.</p>
                                    
                                    <?php if (empty($affiliate_applications)): ?>
                                        <span class="gov-hint">No applications pending.</span>
                                    <?php else: ?>
                                        <?php foreach ($affiliate_applications as $app): ?>
                                            <div style="font-size:13px; padding: 12px; background-color:#fafbfe; border: 1px solid #EBF3FC; border-radius:6px; margin-bottom:12px; line-height: 1.45;">
                                                <strong><?php echo htmlspecialchars($app['name']); ?></strong><br>
                                                Code: <code><?php echo htmlspecialchars($app['rep_code']); ?></code><br>
                                                <span style="font-size: 11px; color:#555;"><?php echo htmlspecialchars($app['contact_info']); ?></span>
                                                
                                                <form action="dashboard.php?page=dashboard" method="POST" style="display:inline; margin-top:8px;">
                                                    <input type="hidden" name="affiliate_id" value="<?php echo $app['id']; ?>">
                                                    <input type="hidden" name="aff_action" value="approve">
                                                    <button type="submit" name="review_affiliate" class="gov-button" style="font-size:10px; padding: 4px 8px; border-radius:3px; margin-top: 5px;">Approve</button>
                                                </form>
                                                <form action="dashboard.php?page=dashboard" method="POST" style="display:inline; margin-left:4px;">
                                                    <input type="hidden" name="affiliate_id" value="<?php echo $app['id']; ?>">
                                                    <input type="hidden" name="aff_action" value="reject">
                                                    <button type="submit" name="review_affiliate" class="gov-button gov-button-secondary" style="font-size:10px; padding: 4px 8px; border-radius:3px; background-color:#d4351c; color:#fff; border-bottom-color:#80180a;">Reject</button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                    <!-- ====================================================================== -->
                    <!-- TAB PAGE B: STUDENTS DIRECTORY -->
                    <!-- ====================================================================== -->
                    <?php elseif ($page === 'students'): ?>
                        
                        <?php if ($view_student): ?>
                            <!-- Student Profile Details View Card -->
                            <div class="db-card">
                                <div class="db-card-header">
                                    <h2>Student Profile Review</h2>
                                    <a href="dashboard.php?page=students" class="gov-button gov-button-secondary" style="padding: 8px 16px; font-size:14px; border-radius:4px; text-decoration:none;">&larr; Back to Students List</a>
                                </div>
                                
                                <div style="background-color: #fafbfe; padding: 25px; border: 1.5px solid #EBF3FC; border-radius: 8px; margin-bottom: 30px;">
                                    <h3 style="color:#002F6C; margin-bottom:15px; font-size:16px; border-bottom:1px solid #EBF3FC; padding-bottom:5px;">Personal & Account Info</h3>
                                    <div class="gov-grid-row">
                                        <div class="gov-grid-column-one-third">
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>Student Name:</strong> <?php echo htmlspecialchars($view_student['full_name']); ?></p>
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>Student ID:</strong> LIAB-ST-<?php echo $view_student['id']; ?></p>
                                            <p style="margin-bottom:0; font-size:14px;"><strong>Account Status:</strong> 
                                                <span class="gov-tag <?php echo $view_student['account_status'] === 'active' ? 'gov-tag-green' : ($view_student['account_status'] === 'locked' ? 'gov-tag-grey' : 'gov-tag-yellow'); ?>" style="font-size:10px; padding: 2px 6px; text-transform:none;">
                                                    <?php echo $view_student['account_status']; ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="gov-grid-column-one-third">
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>Email:</strong> <?php echo htmlspecialchars($view_student['email']); ?></p>
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>WhatsApp:</strong> <?php echo htmlspecialchars($view_student['whatsapp_number']); ?></p>
                                            <p style="margin-bottom:0; font-size:14px;"><strong>Date of Birth:</strong> <?php echo $view_student['dob']; ?></p>
                                        </div>
                                        <div class="gov-grid-column-one-third">
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>Faculty Program:</strong> Faculty of <?php echo htmlspecialchars($view_student['faculty_name'] ?: 'Not Enrolled'); ?></p>
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>Representative Code:</strong> <code><?php echo htmlspecialchars($view_student['rep_code'] ?: 'None'); ?></code></p>
                                            <p style="margin-bottom:0; font-size:14px;"><strong>Created On:</strong> <?php echo $view_student['created_at']; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="gov-grid-row">
                                    <!-- Left Column: Assignments & Exams -->
                                    <div class="gov-grid-column-two-thirds">
                                        <h3 style="color:#002F6C; margin-bottom:15px; font-size:16px;">Coursework Uploads & Submissions</h3>
                                        <table class="gov-table" style="margin-bottom: 30px;">
                                            <thead>
                                                <tr>
                                                    <th>Module</th>
                                                    <th>Document File</th>
                                                    <th>Uploaded At</th>
                                                    <th>Grade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($view_assignments)): ?>
                                                    <tr>
                                                        <td colspan="4" class="gov-hint" style="text-align:center;">No coursework assignments submitted.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($view_assignments as $va): ?>
                                                        <tr>
                                                            <td>Mod <?php echo $va['module_number']; ?>: <?php echo htmlspecialchars($va['module_title']); ?></td>
                                                            <td><a href="<?php echo htmlspecialchars($va['file_path']); ?>" target="_blank"><?php echo basename($va['file_path']); ?></a> (<?php echo $va['file_size']; ?>)</td>
                                                            <td><?php echo $va['uploaded_at']; ?></td>
                                                            <td>
                                                                <span class="gov-tag" style="font-size:10px;"><?php echo $va['status']; ?></span>
                                                                <?php if ($va['grade']): ?>
                                                                    | <strong><?php echo $va['grade']; ?></strong>
                                                                <?php endif; ?>
                                                                <form action="dashboard.php?page=students&view_id=<?php echo $view_id; ?>" method="POST" style="margin-top:8px; display:block;">
                                                                    <input type="hidden" name="edit_assignment_grade" value="1">
                                                                    <input type="hidden" name="assignment_id" value="<?php echo $va['id']; ?>">
                                                                    <div style="display:flex; gap:4px; align-items:center;">
                                                                        <select class="gov-select" name="grade" style="font-size:10px; padding:2px; height:24px; width:90px;" required>
                                                                            <option value="Pass" <?php echo $va['grade'] === 'Pass' ? 'selected' : ''; ?>>Pass</option>
                                                                            <option value="Merit" <?php echo $va['grade'] === 'Merit' ? 'selected' : ''; ?>>Merit</option>
                                                                            <option value="Distinction" <?php echo $va['grade'] === 'Distinction' ? 'selected' : ''; ?>>Distinction</option>
                                                                            <option value="Refer" <?php echo $va['grade'] === 'Refer' ? 'selected' : ''; ?>>Refer (Fail)</option>
                                                                        </select>
                                                                        <input class="gov-input" name="feedback" type="text" placeholder="Remarks" value="<?php echo htmlspecialchars($va['feedback'] ?? ''); ?>" style="font-size:10px; padding:2px 4px; height:24px; width:100px;">
                                                                        <button type="submit" class="gov-button" style="font-size:9px; padding:2px 6px; border-radius:3px; background-color:#002F6C; height:24px;">Save</button>
                                                                    </div>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>

                                        <h3 style="color:#002F6C; margin-bottom:15px; font-size:16px;">Timed Examination Attempts</h3>
                                        <table class="gov-table">
                                            <thead>
                                                <tr>
                                                    <th>Date Completed</th>
                                                    <th>Score</th>
                                                    <th>Violations</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($view_exams)): ?>
                                                    <tr>
                                                        <td colspan="4" class="gov-hint" style="text-align:center;">No exam attempts logged.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($view_exams as $ve): ?>
                                                        <tr>
                                                            <td><?php echo $ve['end_time']; ?></td>
                                                            <td>
                                                                <strong><?php echo $ve['score']; ?>%</strong> (Threshold: <?php echo $ve['pass_threshold']; ?>%)
                                                                <form action="dashboard.php?page=students&view_id=<?php echo $view_id; ?>" method="POST" style="margin-top:6px; display:block;">
                                                                    <input type="hidden" name="edit_exam_score" value="1">
                                                                    <input type="hidden" name="attempt_id" value="<?php echo $ve['id']; ?>">
                                                                    <div style="display:flex; gap:4px; align-items:center;">
                                                                        <input class="gov-input" name="score" type="number" step="0.01" min="0" max="100" value="<?php echo $ve['score']; ?>" required style="font-size:10px; padding:2px 4px; height:24px; width:55px;">
                                                                        <select class="gov-select" name="status" style="font-size:10px; padding:2px; height:24px; width:90px;" required>
                                                                            <option value="completed" <?php echo $ve['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                                            <option value="force_submitted_violation" <?php echo $ve['status'] === 'force_submitted_violation' ? 'selected' : ''; ?>>Violation</option>
                                                                        </select>
                                                                        <button type="submit" class="gov-button" style="font-size:9px; padding:2px 6px; border-radius:3px; background-color:#002F6C; height:24px;">Save</button>
                                                                    </div>
                                                                </form>
                                                            </td>
                                                            <td><?php echo $ve['violation_count']; ?> Violations</td>
                                                            <td>
                                                                <span class="gov-tag <?php echo $ve['score'] >= $ve['pass_threshold'] ? 'gov-tag-green' : 'gov-tag-yellow'; ?>" style="font-size:10px; text-transform:none;">
                                                                    <?php echo $ve['status']; ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Right Column: Payments & Certificates -->
                                    <div class="gov-grid-column-one-third" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                                        <h3 style="color:#002F6C; margin-bottom:15px; font-size:16px;">Verifiable Credentials</h3>
                                        <div style="background-color:#fafbfe; padding: 15px; border:1px solid #EBF3FC; border-radius:6px; margin-bottom: 25px;">
                                            <?php if (empty($view_certificates)): ?>
                                                <span class="gov-hint">No certificates issued.</span>
                                            <?php else: ?>
                                                <?php foreach ($view_certificates as $vc): ?>
                                                    <div style="font-size:13px; margin-bottom: 10px; border-bottom: 1px solid #EBF3FC; padding-bottom:8px;">
                                                        ID: <code><?php echo htmlspecialchars($vc['certificate_uid']); ?></code><br>
                                                        Date: <?php echo $vc['issue_date']; ?><br>
                                                        Status: <span class="gov-tag <?php echo $vc['verification_status'] === 'approved' ? 'gov-tag-green' : 'gov-tag-red'; ?>" style="font-size:9px; padding:1px 4px; text-transform:none;"><?php echo $vc['verification_status']; ?></span><br>
                                                        <a href="<?php echo htmlspecialchars($vc['pdf_path']); ?>" download style="font-size:12px; font-weight:600; display:inline-block; margin-top:5px;">Download PDF &rarr;</a>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            
                                            <!-- Manual Award Form -->
                                            <div style="border-top:1px solid #EBF3FC; margin-top:15px; padding-top:15px;">
                                                <h4 style="font-size:12px; color:#002F6C; margin-bottom:8px; font-weight:600;">Manual Award Certificate</h4>
                                                <form action="dashboard.php?page=students&view_id=<?php echo $view_id; ?>" method="POST">
                                                    <input type="hidden" name="manual_award_certificate" value="1">
                                                    <input type="hidden" name="user_id" value="<?php echo $view_id; ?>">
                                                    <select class="gov-select" name="course_id" style="font-size:11px; padding:3px; height:28px; width:100%; margin-bottom:8px;" required>
                                                        <option value="">-- Choose Course --</option>
                                                        <?php
                                                        $facs = $pdo->query("SELECT * FROM faculties")->fetchAll();
                                                        foreach ($facs as $f) {
                                                            $selected = ($f['id'] == $view_student['faculty_id']) ? 'selected' : '';
                                                            echo '<option value="' . $f['id'] . '" ' . $selected . '>Faculty of ' . htmlspecialchars($f['name']) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <button type="submit" class="gov-button" style="font-size:10px; padding:6px 10px; border-radius:3px; width:100%; background-color:#00703c;">Award Now</button>
                                                </form>
                                            </div>
                                        </div>

                                        <h3 style="color:#002F6C; margin-bottom:15px; font-size:16px;">Remittance Transactions</h3>
                                        <div style="background-color:#fafbfe; padding: 15px; border:1px solid #EBF3FC; border-radius:6px;">
                                            <?php if (empty($view_payments)): ?>
                                                <span class="gov-hint">No transactions logged.</span>
                                            <?php else: ?>
                                                <?php foreach ($view_payments as $vp): ?>
                                                    <div style="font-size:13px; margin-bottom: 10px; border-bottom: 1px solid #EBF3FC; padding-bottom:8px; line-height: 1.4;">
                                                        Amount: <strong>$<?php echo $vp['amount']; ?></strong> (<?php echo strtoupper($vp['method']); ?>)<br>
                                                        Ref: <code><?php echo htmlspecialchars($vp['transaction_ref']); ?></code><br>
                                                        Status: <span class="gov-tag <?php echo $vp['status'] === 'paid' ? 'gov-tag-green' : 'gov-tag-yellow'; ?>" style="font-size:9px; padding:1px 4px; text-transform:none;"><?php echo $vp['status']; ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Students Directory Table Card -->
                            <div class="db-card">
                                <div class="db-card-header">
                                    <h2>Students Management Registry</h2>
                                    <button onclick="showCreateModal()" class="gov-button" style="padding: 8px 16px; font-size:14px; border-radius:4px;">+ Register Student</button>
                                </div>
                                
                                <!-- <p style="font-size:14px; margin-bottom:20px; color:#555;">Overview list of registered student records profiles, including enrolled faculties and verification statuses.</p> -->

                                <!-- SEARCH & FILTER FORM -->
                                <form action="dashboard.php" method="GET" style="display:flex; flex-wrap:wrap; gap:15px; background-color:#fafbfe; padding:20px; border:1.5px solid #EBF3FC; border-radius:8px; margin-bottom:25px; align-items:flex-end;">
                                    <input type="hidden" name="page" value="students">
                                    
                                    <div style="flex: 1; min-width: 200px;">
                                        <label class="gov-label" for="search_input" style="font-size:13px; margin-bottom:4px;">Search by Name</label>
                                        <input class="gov-input" id="search_input" name="search" type="text" value="<?php echo htmlspecialchars($search); ?>" placeholder="Type student name..." style="max-width:100%; height:40px; font-size:13px;">
                                    </div>

                                    <div style="flex: 1; min-width: 180px;">
                                        <label class="gov-label" for="status_select" style="font-size:13px; margin-bottom:4px;">Filter by Status</label>
                                        <select class="gov-select" id="status_select" name="status_filter" style="max-width:100%; height:40px; font-size:13px; padding-top: 8px; padding-bottom: 8px;">
                                            <option value="">-- All Statuses --</option>
                                            <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active (Tuition Paid)</option>
                                            <option value="pending_manual_unlock" <?php echo $status_filter === 'pending_manual_unlock' ? 'selected' : ''; ?>>Pending Tuition Payment</option>
                                            <option value="locked" <?php echo $status_filter === 'locked' ? 'selected' : ''; ?>>Locked</option>
                                        </select>
                                    </div>

                                    <div style="display:flex; gap:10px;">
                                        <button type="submit" class="gov-button" style="padding: 10px 20px; font-size:13px; height:40px; border-radius:4px;">Filter</button>
                                        <?php if (!empty($search) || !empty($status_filter)): ?>
                                            <a href="dashboard.php?page=students" class="gov-button gov-button-secondary" style="padding: 10px 20px; font-size:13px; height:40px; border-radius:4px; text-decoration:none; display:flex; align-items:center; justify-content:center;">Clear</a>
                                        <?php endif; ?>
                                    </div>
                                </form>

                                <table class="gov-table">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Full Name</th>
                                            <th>Registered Email</th>
                                            <th>WhatsApp</th>
                                            <th>Faculty Program</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($students_list)): ?>
                                            <tr>
                                                <td colspan="7" class="gov-hint" style="text-align:center;">No students registered in the database.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($students_list as $st): ?>
                                                <tr>
                                                    <td><strong>LIAB-ST-<?php echo $st['id']; ?></strong></td>
                                                    <td><?php echo htmlspecialchars($st['full_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($st['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($st['whatsapp_number']); ?></td>
                                                    <td><?php echo htmlspecialchars($st['faculty_name'] ?: 'Not Enrolled'); ?></td>
                                                    <td>
                                                        <span class="gov-tag <?php 
                                                            if ($st['account_status'] === 'active') echo 'gov-tag-green'; 
                                                            elseif ($st['account_status'] === 'locked') echo 'gov-tag-grey'; 
                                                            else echo 'gov-tag-yellow'; 
                                                        ?>" style="font-size:11px; padding:3px 8px; text-transform:none;">
                                                            <?php echo $st['account_status']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <!-- View Profile Action Link -->
                                                        <a href="dashboard.php?page=students&view_id=<?php echo $st['id']; ?>" class="btn-action btn-view">View</a>
                                                        
                                                        <!-- Edit Action Button (loads JS Modal) -->
                                                        <button class="btn-action btn-edit" onclick='showEditModal(<?php echo json_encode($st); ?>)'>Edit</button>
                                                        
                                                        <!-- Delete Action Button Form -->
                                                        <form action="dashboard.php?page=students" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this student profile permanently? This cannot be undone.');">
                                                            <input type="hidden" name="delete_student" value="1">
                                                            <input type="hidden" name="delete_id" value="<?php echo $st['id']; ?>">
                                                            <button type="submit" class="btn-action btn-delete">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    <?php elseif ($page === 'exams_report'): ?>
                        <div class="db-card">
                            <div class="db-card-header" style="margin-bottom:20px;">
                                <h2>Timed Exams Violations & Status Directory</h2>
                                <p class="gov-hint">Monitor active and historic student timed exam attempts, scores, and detected cheating lockouts.</p>
                            </div>

                            <table class="gov-table">
                                <thead>
                                    <tr>
                                        <th>Attempt ID</th>
                                        <th>Student</th>
                                        <th>Faculty Program</th>
                                        <th>Achieved Score</th>
                                        <th>Violations</th>
                                        <th>Status Badge</th>
                                        <th>Time Interval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($all_exam_attempts)): ?>
                                        <tr>
                                            <td colspan="7" class="gov-hint" style="text-align:center;">No timed exam attempts registered in the system.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($all_exam_attempts as $att): ?>
                                            <tr style="<?php echo $att['status'] === 'force_submitted_violation' ? 'background-color:#fff5f5;' : ''; ?>">
                                                <td>#<?php echo $att['id']; ?></td>
                                                <td><strong><?php echo htmlspecialchars($att['student_name']); ?></strong></td>
                                                <td>Faculty of <?php echo htmlspecialchars($att['faculty_name'] ?: 'N/A'); ?></td>
                                                <td>
                                                    <strong style="color:<?php echo $att['score'] >= 60 ? '#00703c' : '#d4351c'; ?>;">
                                                        <?php echo number_format($att['score'], 2); ?>%
                                                    </strong>
                                                </td>
                                                <td>
                                                    <span class="gov-tag <?php 
                                                        if ($att['violation_count'] >= 2) echo 'gov-tag-red';
                                                        elseif ($att['violation_count'] == 1) echo 'gov-tag-yellow';
                                                        else echo 'gov-tag-green';
                                                    ?>">
                                                        <?php echo $att['violation_count']; ?> Violations
                                                    </span>
                                                </td>
                                                <td>
                                                    <span style="font-weight:600; text-transform:uppercase; font-size:11px; color:<?php 
                                                        if ($att['status'] === 'completed') echo '#00703c';
                                                        elseif ($att['status'] === 'in_progress') echo '#002f6c';
                                                        else echo '#d4351c';
                                                    ?>;">
                                                        <?php echo htmlspecialchars(str_replace('_', ' ', $att['status'])); ?>
                                                    </span>
                                                </td>
                                                <td style="font-size:12px; line-height:1.3; color:#555;">
                                                    Start: <?php echo $att['start_time']; ?><br>
                                                    End: <?php echo $att['end_time'] ?: 'Active Session'; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php elseif ($page === 'certificates_registry'): ?>
                        <div class="db-card">
                            <div class="db-card-header" style="margin-bottom:20px;">
                                <h2>Issued Certificate Credentials Registry Ledger</h2>
                                <p class="gov-hint">Manage all issued student diploma certificates. Verification records can be revoked or re-approved instantly.</p>
                            </div>

                            <table class="gov-table">
                                <thead>
                                    <tr>
                                        <th>Record ID</th>
                                        <th>Student Name</th>
                                        <th>Course Program</th>
                                        <th>Registry Certificate UID</th>
                                        <th>Status Tag</th>
                                        <th>Date Issued</th>
                                        <th style="text-align:right;">Actions / Operations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($all_certificates)): ?>
                                        <tr>
                                            <td colspan="7" class="gov-hint" style="text-align:center;">No certificate records are currently generated.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($all_certificates as $c): ?>
                                            <tr>
                                                <td>#<?php echo $c['id']; ?></td>
                                                <td><strong><?php echo htmlspecialchars($c['student_name']); ?></strong></td>
                                                <td>Faculty of <?php echo htmlspecialchars($c['faculty_name'] ?: 'N/A'); ?></td>
                                                <td>
                                                    <code><?php echo htmlspecialchars($c['certificate_uid']); ?></code>
                                                </td>
                                                <td>
                                                    <span class="gov-tag <?php 
                                                        if ($c['verification_status'] === 'approved') echo 'gov-tag-green';
                                                        elseif ($c['verification_status'] === 'revoked') echo 'gov-tag-red';
                                                        else echo 'gov-tag-yellow';
                                                    ?>">
                                                        <?php echo strtoupper($c['verification_status']); ?>
                                                    </span>
                                                </td>
                                                <td style="font-size:13px;"><?php echo $c['issue_date']; ?></td>
                                                <td style="text-align:right;">
                                                    <a href="certificate.php?uid=<?php echo urlencode($c['certificate_uid']); ?>" target="_blank" class="btn-action btn-view" style="display:inline-block; text-decoration:none; padding:4px 8px; font-size:11px; margin-right:4px;">Print View</a>
                                                    
                                                    <?php if ($c['verification_status'] === 'approved'): ?>
                                                        <form action="dashboard.php?page=certificates_registry" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to REVOKE this certificate public verification status?');">
                                                            <input type="hidden" name="revoke_certificate" value="1">
                                                            <input type="hidden" name="cert_id" value="<?php echo $c['id']; ?>">
                                                            <button type="submit" class="btn-action btn-delete" style="padding:4px 8px; font-size:11px;">Revoke</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form action="dashboard.php?page=certificates_registry" method="POST" style="display:inline;">
                                                            <input type="hidden" name="approve_certificate" value="1">
                                                            <input type="hidden" name="cert_id" value="<?php echo $c['id']; ?>">
                                                            <button type="submit" class="btn-action btn-edit" style="padding:4px 8px; font-size:11px; background-color:#00703c;">Approve</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php endif; ?>

                <?php endif; ?>

                <!-- ====================================================================== -->
                <!-- UNIVERSAL: PROFILE SETTINGS PAGE -->
                <!-- ====================================================================== -->
                <?php if ($page === 'profile'): ?>
                    <div class="db-card">
                        <div class="db-card-header">
                            <h2>My Profile Settings</h2>
                            <a href="dashboard.php" class="gov-button gov-button-secondary" style="padding: 8px 16px; font-size:14px; border-radius:4px; text-decoration:none;">&larr; Back to Dashboard</a>
                        </div>

                        <?php if (!empty($success_msg)): ?>
                            <div class="gov-success-banner" style="padding: 12px 16px; margin-bottom: 25px;">
                                <p style="font-size:13px; margin-bottom:0; color:#00703c; font-weight:600;"><?php echo htmlspecialchars($success_msg); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_msg)): ?>
                            <div class="gov-error-banner" style="padding: 12px 16px; margin-bottom: 25px;">
                                <p style="font-size:13px; margin-bottom:0; color:#d4351c; font-weight:600;"><?php echo htmlspecialchars($error_msg); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Profile Info Section -->
                        <div style="background-color:#fafbfe; padding:25px; border:1.5px solid #EBF3FC; border-radius:8px; margin-bottom:30px;">
                            <h3 style="color:#002F6C; margin-bottom:20px; font-size:16px; border-bottom:1px solid #EBF3FC; padding-bottom:8px;">Personal Information</h3>
                            <form action="dashboard.php?page=profile" method="POST" novalidate onsubmit="return validateProfileInfo()">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="modal-form-grid" style="grid-template-columns: repeat(2, 1fr);">
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="p_name">Full Name</label>
                                        <input class="gov-input" id="p_name" name="profile_name" type="text" value="<?php echo htmlspecialchars($current_user['full_name']); ?>" required style="max-width:100%;">
                                    </div>
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="p_email">Email Address</label>
                                        <input class="gov-input" id="p_email" name="profile_email" type="email" value="<?php echo htmlspecialchars($current_user['email']); ?>" required style="max-width:100%;">
                                    </div>
                                </div>
                                <div class="modal-form-actions" style="border-top:none; padding-top:5px; margin-top:0;">
                                    <button type="submit" class="gov-button" style="border-radius:6px; padding: 10px 24px;">Save Changes</button>
                                </div>
                            </form>
                        </div>

                        <!-- Password Change Section -->
                        <div style="background-color:#fafbfe; padding:25px; border:1.5px solid #EBF3FC; border-radius:8px;">
                            <h3 style="color:#002F6C; margin-bottom:20px; font-size:16px; border-bottom:1px solid #EBF3FC; padding-bottom:8px;">Change Password</h3>
                            <form action="dashboard.php?page=profile" method="POST">
                                <input type="hidden" name="change_password" value="1">
                                <div class="modal-form-grid">
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="p_current_pw">Current Password</label>
                                        <div class="pw-wrapper">
                                            <input class="gov-input" id="p_current_pw" name="current_password" type="password" required placeholder="Enter current password" style="max-width:100%;">
                                            <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('p_current_pw', this)" aria-label="Show password">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="p_new_pw">New Password</label>
                                        <div class="pw-wrapper">
                                            <input class="gov-input" id="p_new_pw" name="new_password" type="password" required placeholder="Min. 6 characters" style="max-width:100%;">
                                            <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('p_new_pw', this)" aria-label="Show password">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="p_confirm_pw">Confirm Password</label>
                                        <div class="pw-wrapper">
                                            <input class="gov-input" id="p_confirm_pw" name="confirm_password" type="password" required placeholder="Re-type new password" style="max-width:100%;">
                                            <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('p_confirm_pw', this)" aria-label="Show password">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-form-actions" style="border-top:none; padding-top:5px; margin-top:0;">
                                    <button type="submit" class="gov-button" style="border-radius:6px; padding: 10px 24px;">Update Password</button>
                                </div>
                            </form>
                        </div>

                    </div>
                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- ====================================================================== -->
    <!-- CREATE STUDENT MODAL POPUP -->
    <!-- ====================================================================== -->
    <div id="createStudentModal" class="db-modal-overlay" style="display: none;">
        <div class="db-modal">
            <span class="db-modal-close" onclick="hideCreateModal()">&times;</span>
            <h3 style="margin-bottom: 25px; font-size:18px; color: #002F6C;">Register New Student</h3>
            
            <form action="dashboard.php?page=students" method="POST" novalidate onsubmit="return validateCreateStudent()">
                <input type="hidden" name="create_student" value="1">
                
                <div class="modal-form-grid">
                    <div class="gov-form-group">
                        <label class="gov-label" for="c_name">Full Name</label>
                        <input class="gov-input" id="c_name" name="student_name" type="text" required placeholder="e.g. John Doe">
                        <span class="validation-error-msg" id="error_c_name"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_dob">Date of Birth</label>
                        <input class="gov-input" id="c_dob" name="student_dob" type="date" required>
                        <span class="validation-error-msg" id="error_c_dob"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_email">Email Address</label>
                        <input class="gov-input" id="c_email" name="student_email" type="email" required placeholder="e.g. john@mail.com">
                        <span class="validation-error-msg" id="error_c_email"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_whatsapp">WhatsApp Number</label>
                        <input class="gov-input" id="c_whatsapp" name="student_whatsapp" type="tel" required placeholder="e.g. +447000000000">
                        <span class="validation-error-msg" id="error_c_whatsapp"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_faculty">Faculty Program</label>
                        <select class="gov-select" id="c_faculty" name="student_faculty" required>
                            <option value="">-- Choose Faculty --</option>
                            <?php
                            $facs = $pdo->query("SELECT * FROM faculties")->fetchAll();
                            foreach ($facs as $f) {
                                echo '<option value="'.$f['id'].'">Faculty of '.$f['name'].'</option>';
                            }
                            ?>
                        </select>
                        <span class="validation-error-msg" id="error_c_faculty"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_rep">Representative Code</label>
                        <input class="gov-input" id="c_rep" name="student_rep" type="text" placeholder="Optional">
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_status">Account Status</label>
                        <select class="gov-select" id="c_status" name="student_status" required>
                            <option value="active">Active (Tuition Paid)</option>
                            <option value="pending_manual_unlock">Pending Tuition Payment</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form-actions">
                    <button type="button" class="gov-button gov-button-secondary" onclick="hideCreateModal()" style="border-radius:6px; padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="gov-button" style="border-radius:6px; padding: 10px 24px;">Create Student Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ====================================================================== -->
    <!-- EDIT STUDENT MODAL POPUP -->
    <!-- ====================================================================== -->
    <div id="editStudentModal" class="db-modal-overlay" style="display: none;">
        <div class="db-modal">
            <span class="db-modal-close" onclick="hideEditModal()">&times;</span>
            <h3 style="margin-bottom: 25px; font-size:18px; color: #002F6C;">Edit Student Record</h3>
            
            <form action="dashboard.php?page=students" method="POST" novalidate onsubmit="return validateEditStudent()">
                <input type="hidden" name="edit_student" value="1">
                <input type="hidden" id="e_id" name="student_id">
                
                <div class="modal-form-grid">
                    <div class="gov-form-group">
                        <label class="gov-label" for="e_name">Full Name</label>
                        <input class="gov-input" id="e_name" name="student_name" type="text" required>
                        <span class="validation-error-msg" id="error_e_name"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_dob">Date of Birth</label>
                        <input class="gov-input" id="e_dob" name="student_dob" type="date" required>
                        <span class="validation-error-msg" id="error_e_dob"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_email">Email Address</label>
                        <input class="gov-input" id="e_email" name="student_email" type="email" required>
                        <span class="validation-error-msg" id="error_e_email"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_whatsapp">WhatsApp Number</label>
                        <input class="gov-input" id="e_whatsapp" name="student_whatsapp" type="tel" required>
                        <span class="validation-error-msg" id="error_e_whatsapp"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_faculty">Faculty Program</label>
                        <select class="gov-select" id="e_faculty" name="student_faculty" required>
                            <option value="">-- Choose Faculty --</option>
                            <?php
                            foreach ($facs as $f) {
                                echo '<option value="'.$f['id'].'">Faculty of '.$f['name'].'</option>';
                            }
                            ?>
                        </select>
                        <span class="validation-error-msg" id="error_e_faculty"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_rep">Representative Code</label>
                        <input class="gov-input" id="e_rep" name="student_rep" type="text">
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_status">Account Status</label>
                        <select class="gov-select" id="e_status" name="student_status" required>
                            <option value="active">Active (Tuition Paid)</option>
                            <option value="pending_manual_unlock">Pending Tuition Payment</option>
                            <option value="locked">Locked</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form-actions">
                    <button type="button" class="gov-button gov-button-secondary" onclick="hideEditModal()" style="border-radius:6px; padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="gov-button" style="border-radius:6px; padding: 10px 24px;">Save Updates</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toggle Sidebar JavaScript for Mobile -->
    <script>
        function toggleSidebar() {
            if (window.innerWidth <= 992) {
                var sidebar = document.getElementById('dbSidebar');
                sidebar.classList.toggle('open');
            } else {
                var container = document.querySelector('.db-layout-container');
                container.classList.toggle('collapsed');
                var isCollapsed = container.classList.contains('collapsed');
                document.cookie = "db_sidebar_collapsed=" + (isCollapsed ? "1" : "0") + "; path=/; max-age=31536000";
            }
        }

        // Close sidebar when clicking main content area on mobile viewports
        document.addEventListener('DOMContentLoaded', function() {
            var mainContent = document.querySelector('.db-main');
            if (mainContent) {
                mainContent.addEventListener('click', function(e) {
                    var sidebar = document.getElementById('dbSidebar');
                    var toggleBtn = document.querySelector('.db-mobile-toggle');
                    if (sidebar && sidebar.classList.contains('open') && e.target !== toggleBtn && !toggleBtn.contains(e.target) && !sidebar.contains(e.target)) {
                        sidebar.classList.remove('open');
                    }
                });
            }
        });

        // Modal Helpers
        function clearModalErrors() {
            document.querySelectorAll('.validation-error-msg').forEach(function(span) {
                span.style.display = 'none';
                span.innerText = '';
            });
        }

        function showModalError(fieldId, msg) {
            var span = document.getElementById('error_' + fieldId);
            if (span) {
                span.innerText = msg;
                span.style.display = 'block';
            }
        }

        function showCreateModal() {
            clearModalErrors();
            document.getElementById('createStudentModal').style.display = 'flex';
        }
        function hideCreateModal() {
            clearModalErrors();
            document.getElementById('createStudentModal').style.display = 'none';
        }

        function showEditModal(studentData) {
            clearModalErrors();
            document.getElementById('e_id').value = studentData.id;
            document.getElementById('e_name').value = studentData.full_name;
            document.getElementById('e_dob').value = studentData.dob;
            document.getElementById('e_email').value = studentData.email;
            document.getElementById('e_whatsapp').value = studentData.whatsapp_number;
            document.getElementById('e_faculty').value = studentData.faculty_id || '';
            document.getElementById('e_rep').value = studentData.rep_code || '';
            document.getElementById('e_status').value = studentData.account_status;

            document.getElementById('editStudentModal').style.display = 'flex';
        }
        function hideEditModal() {
            clearModalErrors();
            document.getElementById('editStudentModal').style.display = 'none';
        }

        // ============================================================
        // THEME SWITCHING ENGINE (localStorage Persistence)
        // ============================================================
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('lms_theme', theme);
            updateThemeOptions(theme);
            document.getElementById('themeDropdown').classList.remove('open');
        }

        function toggleThemeDropdown() {
            var dd = document.getElementById('themeDropdown');
            dd.classList.toggle('open');
        }

        function updateThemeOptions(activeTheme) {
            var options = document.querySelectorAll('.theme-option');
            options.forEach(function(opt) {
                opt.classList.remove('active');
                if (opt.textContent.trim().toLowerCase() === activeTheme) {
                    opt.classList.add('active');
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            var wrap = document.querySelector('.theme-toggle-wrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('themeDropdown').classList.remove('open');
            }
        });

        function togglePasswordVisibility(inputId, buttonEl) {
            var input = document.getElementById(inputId);
            if (!input) return;
            var type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            if (type === 'password') {
                buttonEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                buttonEl.setAttribute('aria-label', 'Show password');
            } else {
                buttonEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
                buttonEl.setAttribute('aria-label', 'Hide password');
            }
        }

        // Apply saved theme on page load
        (function() {
            var saved = localStorage.getItem('lms_theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
            updateThemeOptions(saved);
        })();

        // Input sanitization & Validation for Dashboard
        document.addEventListener('DOMContentLoaded', function() {
            var c_whatsapp = document.getElementById('c_whatsapp');
            if (c_whatsapp) {
                c_whatsapp.addEventListener('input', function(e) {
                    e.target.value = e.target.value.replace(/[^0-9+\s-]/g, '');
                });
            }

            var e_whatsapp = document.getElementById('e_whatsapp');
            if (e_whatsapp) {
                e_whatsapp.addEventListener('input', function(e) {
                    e.target.value = e.target.value.replace(/[^0-9+\s-]/g, '');
                });
            }
        });

        function validateProfileInfo() {
            var email = document.getElementById('p_email').value.trim();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Please enter a valid email address.');
                return false;
            }
            return true;
        }

        function validateCreateStudent() {
            clearModalErrors();
            var name = document.getElementById('c_name').value.trim();
            var dob = document.getElementById('c_dob').value;
            var email = document.getElementById('c_email').value.trim();
            var whatsapp = document.getElementById('c_whatsapp').value.trim();
            var faculty = document.getElementById('c_faculty').value;
            var hasError = false;

            if (!name) {
                showModalError('c_name', 'Full Name is required.');
                hasError = true;
            }
            if (!dob) {
                showModalError('c_dob', 'Date of Birth is required.');
                hasError = true;
            }
            if (!email) {
                showModalError('c_email', 'Email Address is required.');
                hasError = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showModalError('c_email', 'Please enter a valid email address.');
                hasError = true;
            }
            if (!whatsapp) {
                showModalError('c_whatsapp', 'WhatsApp number is required.');
                hasError = true;
            }
            if (!faculty) {
                showModalError('c_faculty', 'Faculty Program selection is required.');
                hasError = true;
            }

            return !hasError;
        }

        function validateEditStudent() {
            clearModalErrors();
            var name = document.getElementById('e_name').value.trim();
            var dob = document.getElementById('e_dob').value;
            var email = document.getElementById('e_email').value.trim();
            var whatsapp = document.getElementById('e_whatsapp').value.trim();
            var faculty = document.getElementById('e_faculty').value;
            var hasError = false;

            if (!name) {
                showModalError('e_name', 'Full Name is required.');
                hasError = true;
            }
            if (!dob) {
                showModalError('e_dob', 'Date of Birth is required.');
                hasError = true;
            }
            if (!email) {
                showModalError('e_email', 'Email Address is required.');
                hasError = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showModalError('e_email', 'Please enter a valid email address.');
                hasError = true;
            }
            if (!whatsapp) {
                showModalError('e_whatsapp', 'WhatsApp number is required.');
                hasError = true;
            }
            if (!faculty) {
                showModalError('e_faculty', 'Faculty Program selection is required.');
                hasError = true;
            }

            return !hasError;
        }
    </script>

</body>
</html>
