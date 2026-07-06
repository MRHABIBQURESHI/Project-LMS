<?php
require_once __DIR__ . '/db.php';

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
                
                // Add exam attempt
                $stmt = $pdo->prepare("INSERT INTO exam_attempts (user_id, exam_id, score, status, violation_count, end_time) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
                $stmt->execute([$user_id, $exam_id, $score, $status, $violations]);
                
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
                
                // Determine pass threshold
                $ex_stmt = $pdo->prepare("SELECT pass_threshold, faculty_id FROM exams WHERE id = ?");
                $ex_stmt->execute([$exam_id]);
                $exam_data = $ex_stmt->fetch();
                $pass_threshold = $exam_data ? intval($exam_data['pass_threshold']) : 70;
                
                if ($score >= $pass_threshold) {
                    // Generate verifiable certificate UID
                    $cert_uid = 'LIAB-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . $user_id;
                    $pdf_mock_path = 'uploads/certificates/cert_' . $user_id . '_' . $exam_data['faculty_id'] . '.pdf';
                    
                    // Create certificate folder if not exists
                    if (!file_exists(__DIR__ . '/uploads/certificates')) {
                        mkdir(__DIR__ . '/uploads/certificates', 0777, true);
                    }
                    
                    // Insert certificate
                    $cert_stmt = $pdo->prepare("INSERT INTO certificates (user_id, certificate_uid, issue_date, pdf_path, verification_status) VALUES (?, ?, CURDATE(), ?, 'approved')");
                    $cert_stmt->execute([$user_id, $cert_uid, $pdf_mock_path]);
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

                        // Create default enrollment
                        $en_stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, progress, status) VALUES (?, ?, 0, 'active')");
                        $en_stmt->execute([$new_uid, $faculty_id]);

                        $pdo->commit();

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
    <script>
        // Apply saved theme immediately to avoid FOUC (Flash of Unstyled Content)
        (function(){var t=localStorage.getItem('lms_theme');if(t)document.documentElement.setAttribute('data-theme',t);})();
    </script>
    
    <!-- ANTI-CHEAT ENGINE (FOR STUDENT TIMED EXAMS) -->
    <?php if ($user_role === 'student' && $account_status === 'active' && $active_exam): ?>
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
        var secondsLeft = <?php echo ($active_exam['duration_minutes'] ?: 120) * 60; ?>;
        var violationsCount = 0;
        var examActive = false;

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
                document.getElementById('exam_score_field').value = Math.floor(Math.random() * 40) + 30;
                document.getElementById('examForm').submit();
            }
        }

        function finishExamNormal() {
            examActive = false;
            clearInterval(examTimer);
            document.getElementById('exam_score_field').value = '85.00';
            document.getElementById('violations_field').value = violationsCount;
            document.getElementById('examForm').submit();
        }
    </script>
    <?php endif; ?>
</head>
<body>

    <div class="db-layout-container">

        <!-- ====================================================================== -->
        <!-- LEFT SIDEBAR PANEL -->
        <!-- ====================================================================== -->
        <aside class="db-sidebar" id="dbSidebar">
            <div class="db-brand" style="display:flex; align-items:center; gap:10px; padding: 15px 20px;">
                <img src="assets/images/logo.png" alt="Logo" style="max-height: 35px; object-fit: contain;">
                <span style="font-weight:700; font-size:15px; color:#FFFFFF;">UK London Award</span>
            </div>
            
            <ul class="db-nav-menu">
                <li class="db-nav-section-title">Academic Portal</li>
                <li class="db-nav-item <?php echo ($user_role === 'student' || $page === 'dashboard') ? 'active' : ''; ?>"><a href="dashboard.php">Dashboard</a></li>
                
                <?php if ($user_role === 'student' && $account_status === 'active'): ?>
                    <li class="db-nav-item"><a href="#modulesSection">My Coursework</a></li>
                    <li class="db-nav-item"><a href="#examsSection">Timed Exams</a></li>
                    <li class="db-nav-item"><a href="#certsSection">Certificates</a></li>
                <?php elseif ($user_role === 'admin'): ?>
                    <li class="db-nav-item <?php echo $page === 'students' ? 'active' : ''; ?>"><a href="dashboard.php?page=students">Students</a></li>
                <?php endif; ?>
                
                <li class="db-nav-section-title">Account</li>
                <li class="db-nav-item"><a href="logout.php">Sign Out</a></li>
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
                        <!-- Left Columns (Modules, Exams, Certificates) -->
                        <div class="gov-grid-column-two-thirds">
                            
                            <!-- Enrollment card -->
                            <div class="db-card">
                                <div class="db-card-title">Enrolled Academic Program</div>
                                <p style="font-size:16px; font-weight:600; color:#002F6C; margin-bottom: 5px;">Faculty of <?php echo htmlspecialchars($enrollment['name']); ?></p>
                                <p class="gov-hint" style="margin-bottom:0;">Registered Student ID: LIAB-ST-<?php echo $user_id; ?></p>
                            </div>

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
                                            <form action="dashboard.php" method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap: 15px; width:100%; margin-top: 8px;">
                                                <input type="hidden" name="module_id" value="<?php echo $mod['id']; ?>">
                                                <input type="file" name="assignment_file" required style="font-size:13px;">
                                                <button type="submit" name="upload_assignment" class="gov-button" style="font-size:12px; padding: 6px 12px; border-radius: 4px;">Upload Assignment</button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Exams card -->
                            <div class="db-card" id="examsSection">
                                <div class="db-card-title">Faculty Timed Assessment</div>
                                <p style="font-size: 14px; margin-bottom: 20px;">Complete your timed examination. Anti-cheat visibility tracking metrics are active.</p>

                                <div class="gov-list-group" style="margin-top: 10px; margin-bottom: 0;">
                                    <?php if (!$active_exam): ?>
                                        <p class="gov-hint">No examinations configured for this faculty.</p>
                                    <?php else: ?>
                                        <div class="gov-list-row" style="padding: 15px 0; border-bottom: none;">
                                            <div>
                                                <span class="gov-list-key">Timed Comprehensive Assessment</span>
                                                <span class="gov-hint" style="margin-top: 5px;">Pass Threshold: <?php echo $active_exam['pass_threshold']; ?>% | Duration: <?php echo $active_exam['duration_minutes']; ?> mins</span>
                                            </div>
                                            <div>
                                                <?php if (!empty($exam_results)): ?>
                                                    <?php $latest_attempt = end($exam_results); ?>
                                                    <span class="gov-tag <?php echo $latest_attempt['score'] >= $active_exam['pass_threshold'] ? 'gov-tag-green' : 'gov-tag-yellow'; ?>">
                                                        Score: <?php echo $latest_attempt['score']; ?>% (<?php echo strtoupper($latest_attempt['status']); ?>)
                                                    </span>
                                                    <?php if ($latest_attempt['score'] < $active_exam['pass_threshold']): ?>
                                                        <button onclick="startExamEngine()" class="gov-button" style="font-size:12px; padding: 6px 12px; border-radius: 4px; margin-left:10px;">Retake Exam</button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <button onclick="startExamEngine()" class="gov-button" style="font-size:12px; padding: 6px 12px; border-radius: 4px;">Start Assessment</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

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
                                                <div>
                                                    <a href="<?php echo htmlspecialchars($c['pdf_path']); ?>" download class="gov-button" style="font-size:12px; padding: 6px 12px; border-radius: 4px; text-decoration:none;">Download PDF</a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>

                        <!-- Right Sidebar Column (Payment history) -->
                        <div class="gov-grid-column-one-third" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                            <div class="db-card">
                                <div class="db-card-title" style="font-size: 16px; margin-bottom: 12px;">Tuition Payment History</div>
                                <div style="background-color: #fafbfe; padding: 15px; border: 1.5px solid #EBF3FC; border-radius: 8px;">
                                    <?php if (empty($payments_history)): ?>
                                        <span class="gov-hint">No transactions registered.</span>
                                    <?php else: ?>
                                        <?php foreach ($payments_history as $p): ?>
                                            <div style="font-size:13px; padding: 8px 0; border-bottom: 1px solid #EBF3FC; line-height: 1.4;">
                                                Amount: <strong>$<?php echo $p['amount']; ?></strong><br>
                                                Provider: <?php echo strtoupper($p['method']); ?><br>
                                                Status: 
                                                <span class="gov-tag <?php echo $p['status'] === 'paid' ? 'gov-tag-green' : 'gov-tag-yellow'; ?>" style="font-size:9px; padding:1px 4px; text-transform:none;">
                                                    <?php echo $p['status']; ?>
                                                </span><br>
                                                Ref: <code><?php echo htmlspecialchars($p['transaction_ref'] ?: 'Pending Review'); ?></code>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

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

                            <!-- Mock exam questions -->
                            <div class="gov-form-group">
                                <label class="gov-label">Question 1: Define administrative ethics in research.</label>
                                <textarea class="gov-textarea" rows="2" style="max-width:100%;" required placeholder="Type answer here..."></textarea>
                            </div>
                            <div class="gov-form-group">
                                <label class="gov-label">Question 2: Detail citation guidelines for secondary references.</label>
                                <textarea class="gov-textarea" rows="2" style="max-width:100%;" required placeholder="Type answer here..."></textarea>
                            </div>
                            <div class="gov-form-group">
                                <label class="gov-label">Question 3: Elaborate on core parameters related to Faculty focus studies.</label>
                                <textarea class="gov-textarea" rows="2" style="max-width:100%;" required placeholder="Type answer here..."></textarea>
                            </div>

                            <div style="margin-top: 30px;">
                                <button type="button" onclick="finishExamNormal()" class="gov-button">Submit Exam Paper</button>
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
                                                            <td><strong><?php echo $ve['score']; ?>%</strong> (Threshold: <?php echo $ve['pass_threshold']; ?>%)</td>
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
                                                        <a href="<?php echo htmlspecialchars($vc['pdf_path']); ?>" download style="font-size:12px; font-weight:600; display:inline-block; margin-top:5px;">Download PDF &rarr;</a>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
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
                            <form action="dashboard.php?page=profile" method="POST">
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
                                        <input class="gov-input" id="p_current_pw" name="current_password" type="password" required placeholder="Enter current password" style="max-width:100%;">
                                    </div>
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="p_new_pw">New Password</label>
                                        <input class="gov-input" id="p_new_pw" name="new_password" type="password" required placeholder="Min. 6 characters" style="max-width:100%;">
                                    </div>
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="p_confirm_pw">Confirm Password</label>
                                        <input class="gov-input" id="p_confirm_pw" name="confirm_password" type="password" required placeholder="Re-type new password" style="max-width:100%;">
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
            
            <form action="dashboard.php?page=students" method="POST">
                <input type="hidden" name="create_student" value="1">
                
                <div class="modal-form-grid">
                    <div class="gov-form-group">
                        <label class="gov-label" for="c_name">Full Name</label>
                        <input class="gov-input" id="c_name" name="student_name" type="text" required placeholder="e.g. John Doe">
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_dob">Date of Birth</label>
                        <input class="gov-input" id="c_dob" name="student_dob" type="date" required>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_email">Email Address</label>
                        <input class="gov-input" id="c_email" name="student_email" type="email" required placeholder="e.g. john@mail.com">
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_whatsapp">WhatsApp Number</label>
                        <input class="gov-input" id="c_whatsapp" name="student_whatsapp" type="tel" required placeholder="e.g. +447000000000">
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
            
            <form action="dashboard.php?page=students" method="POST">
                <input type="hidden" name="edit_student" value="1">
                <input type="hidden" id="e_id" name="student_id">
                
                <div class="modal-form-grid">
                    <div class="gov-form-group">
                        <label class="gov-label" for="e_name">Full Name</label>
                        <input class="gov-input" id="e_name" name="student_name" type="text" required>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_dob">Date of Birth</label>
                        <input class="gov-input" id="e_dob" name="student_dob" type="date" required>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_email">Email Address</label>
                        <input class="gov-input" id="e_email" name="student_email" type="email" required>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_whatsapp">WhatsApp Number</label>
                        <input class="gov-input" id="e_whatsapp" name="student_whatsapp" type="tel" required>
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
            var sidebar = document.getElementById('dbSidebar');
            sidebar.classList.toggle('open');
        }

        // Modal Helpers
        function showCreateModal() {
            document.getElementById('createStudentModal').style.display = 'flex';
        }
        function hideCreateModal() {
            document.getElementById('createStudentModal').style.display = 'none';
        }

        function showEditModal(studentData) {
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

        // Apply saved theme on page load
        (function() {
            var saved = localStorage.getItem('lms_theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
            updateThemeOptions(saved);
        })();
    </script>

</body>
</html>
