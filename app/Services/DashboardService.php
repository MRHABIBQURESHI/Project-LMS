<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class DashboardService
{
    protected $pdfService;
    protected $mailService;

    public function __construct(PdfService $pdfService, MailService $mailService)
    {
        $this->pdfService = $pdfService;
        $this->mailService = $mailService;
    }

    /**
     * Get all data required for a student dashboard.
     *
     * @param int $userId
     * @param int $facultyId
     * @param array $currentUser
     * @return array
     */
    public function getStudentData($userId, $facultyId, $currentUser)
    {
        $pdo = DB::connection()->getPdo();

        $enrollment = null;
        $modules = [];
        $assignmentsUploaded = [];
        $examResults = [];
        $certificates = [];
        $paymentsHistory = [];
        $activeExam = null;

        try {
            // Faculty Title
            $facStmt = $pdo->prepare("SELECT * FROM faculties WHERE id = ?");
            $facStmt->execute([$facultyId]);
            $enrollment = $facStmt->fetch();

            // Load modules: Universal (NULL) + Faculty specific
            $modStmt = $pdo->prepare("SELECT * FROM modules WHERE faculty_id IS NULL OR faculty_id = ? ORDER BY module_number ASC");
            $modStmt->execute([$facultyId]);
            $modules = $modStmt->fetchAll();

            // Load submitted assignments
            $assignStmt = $pdo->prepare("SELECT * FROM assignments WHERE user_id = ?");
            $assignStmt->execute([$userId]);
            $assignmentsUploaded = $assignStmt->fetchAll(\PDO::FETCH_UNIQUE); // Keyed by module_id

            // Load active exam details
            $exStmt = $pdo->prepare("SELECT * FROM exams WHERE faculty_id = ? LIMIT 1");
            $exStmt->execute([$facultyId]);
            $activeExam = $exStmt->fetch();

            // Load exam attempts
            $attemptStmt = $pdo->prepare("SELECT * FROM exam_attempts WHERE user_id = ?");
            $attemptStmt->execute([$userId]);
            $examResults = $attemptStmt->fetchAll();

            // Load certificates
            $certStmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ?");
            $certStmt->execute([$userId]);
            $certificates = $certStmt->fetchAll();

            // Load payments
            $payStmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ?");
            $payStmt->execute([$userId]);
            $paymentsHistory = $payStmt->fetchAll();

            // Calculated fields
            $installmentsPaid = 0;
            $isInstallmentPlan = false;
            foreach ($paymentsHistory as $p) {
                if ($p['type'] === 'tuition' && $p['status'] === 'paid') {
                    if (floatval($p['amount']) == 749.00) {
                        $isInstallmentPlan = true;
                        $installmentsPaid++;
                    } elseif (floatval($p['amount']) == 2249.00) {
                        $isInstallmentPlan = false;
                    }
                }
            }
            
            $examFailed = false;
            $examPassed = false;
            $resitUnlocked = false;
            foreach ($examResults as $att) {
                if ($att['score'] >= 50.00 && $att['status'] === 'completed') {
                    $examPassed = true;
                }
            }
            if (!empty($examResults)) {
                $latestAttempt = end($examResults);
                if ($latestAttempt['score'] < 50.00 || $latestAttempt['status'] === 'force_submitted_violation') {
                    if (!$examPassed) {
                        $examFailed = true;
                        $resitUnlocked = (intval($currentUser['exam_retake_unlocked'] ?? 0) === 1);
                    }
                }
            }

            // Calculate if Phase II is locked (14-day speed trap check)
            $regDate = strtotime($currentUser['created_at']);
            $daysSinceReg = floor((time() - $regDate) / 86400);
            $phase2Locked = ($daysSinceReg < 14 && intval($currentUser['phase2_expedited'] ?? 0) === 0);

            return [
                'enrollment' => $enrollment,
                'modules' => $modules,
                'assignments_uploaded' => $assignmentsUploaded,
                'active_exam' => $activeExam,
                'exam_results' => $examResults,
                'certificates' => $certificates,
                'payments_history' => $paymentsHistory,
                'installments_paid' => $installmentsPaid,
                'is_installment_plan' => $isInstallmentPlan,
                'exam_failed' => $examFailed,
                'exam_passed' => $examPassed,
                'resit_unlocked' => $resitUnlocked,
                'phase2_locked' => $phase2Locked,
            ];
        } catch (\PDOException $e) {
            error_log("Error fetching student dashboard data: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all data required for an admin dashboard.
     *
     * @param string $page
     * @param string $search
     * @param string $statusFilter
     * @param int|null $viewId
     * @return array
     */
    public function getAdminData($page, $search = '', $statusFilter = '', $viewId = null)
    {
        $pdo = DB::connection()->getPdo();

        $pendingGrading = [];
        $pendingRemittance = [];
        $affiliateApplications = [];
        $approvedAffiliates = [];
        $certificateRegistry = [];
        $studentsList = [];
        $coursesList = [];

        try {
            if ($page === 'courses' || $page === 'students') {
                $queryStr = "SELECT f.*, 
                               (SELECT COUNT(*) FROM modules m WHERE m.faculty_id = f.id OR m.faculty_id IS NULL) as modules_count,
                               (SELECT COUNT(*) FROM users u WHERE u.faculty_id = f.id AND u.role = 'student') as students_count 
                            FROM faculties f";
                $params = [];
                if ($page === 'courses' && !empty($search)) {
                    $queryStr .= " WHERE f.name LIKE ?";
                    $params[] = '%' . $search . '%';
                }
                $queryStr .= " ORDER BY f.id ASC";
                $coursesStmt = $pdo->prepare($queryStr);
                $coursesStmt->execute($params);
                $coursesList = $coursesStmt->fetchAll();
            } else {
                $coursesList = $pdo->query("SELECT * FROM faculties ORDER BY id ASC")->fetchAll();
            }

            $pendingGrading = $pdo->query("SELECT a.*, u.full_name as student_name, m.title as module_title, m.module_number FROM assignments a JOIN users u ON a.user_id = u.id JOIN modules m ON a.module_id = m.id WHERE a.status = 'pending'")->fetchAll();
            $pendingRemittance = $pdo->query("SELECT p.*, u.full_name as student_name, u.email as student_email FROM payments p JOIN users u ON p.user_id = u.id WHERE p.status = 'pending_manual_unlock'")->fetchAll();
            $affiliateApplications = $pdo->query("SELECT * FROM affiliates WHERE application_status = 'pending'")->fetchAll();
            $approvedAffiliates = $pdo->query("SELECT * FROM affiliates WHERE application_status = 'approved'")->fetchAll();
            $certificateRegistry = $pdo->query("SELECT cert.*, u.full_name as student_name, f.name as faculty_name FROM certificates cert JOIN users u ON cert.user_id = u.id JOIN faculties f ON u.faculty_id = f.id")->fetchAll();

            if ($page === 'students') {
                $queryStr = "SELECT u.*, f.name as faculty_name FROM users u LEFT JOIN faculties f ON u.faculty_id = f.id WHERE u.role = 'student'";
                $params = [];
                
                if (!empty($search)) {
                    $queryStr .= " AND u.full_name LIKE ?";
                    $params[] = '%' . $search . '%';
                }
                
                if (!empty($statusFilter)) {
                    $queryStr .= " AND u.account_status = ?";
                    $params[] = $statusFilter;
                }
                
                $queryStr .= " ORDER BY u.id DESC";
                $studentsStmt = $pdo->prepare($queryStr);
                $studentsStmt->execute($params);
                $studentsList = $studentsStmt->fetchAll();
            }

            $viewStudent = null;
            $viewAssignments = [];
            $viewExams = [];
            $viewCertificates = [];
            $viewPayments = [];

            if ($page === 'students' && $viewId) {
                $uStmt = $pdo->prepare("SELECT u.*, f.name as faculty_name FROM users u LEFT JOIN faculties f ON u.faculty_id = f.id WHERE u.id = ? AND u.role = 'student'");
                $uStmt->execute([$viewId]);
                $viewStudent = $uStmt->fetch();

                if ($viewStudent) {
                    $aStmt = $pdo->prepare("SELECT a.*, m.title as module_title, m.module_number FROM assignments a JOIN modules m ON a.module_id = m.id WHERE a.user_id = ? ORDER BY m.module_number ASC");
                    $aStmt->execute([$viewId]);
                    $viewAssignments = $aStmt->fetchAll();

                    $eStmt = $pdo->prepare("SELECT ea.*, ex.total_questions FROM exam_attempts ea JOIN exams ex ON ea.exam_id = ex.id WHERE ea.user_id = ? ORDER BY ea.id DESC");
                    $eStmt->execute([$viewId]);
                    $viewExams = $eStmt->fetchAll();

                    $cStmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ?");
                    $cStmt->execute([$viewId]);
                    $viewCertificates = $cStmt->fetchAll();

                    $pStmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY id DESC");
                    $pStmt->execute([$viewId]);
                    $viewPayments = $pStmt->fetchAll();
                }
            }

            $viewCourse = null;
            $viewCourseModules = [];
            $viewCourseStudents = [];
            $viewCourseExams = [];

            if ($page === 'courses' && $viewId) {
                $cStmt = $pdo->prepare("SELECT * FROM faculties WHERE id = ?");
                $cStmt->execute([$viewId]);
                $viewCourse = $cStmt->fetch();

                if ($viewCourse) {
                    $viewCourse = (array) $viewCourse;
                    
                    $mStmt = $pdo->prepare("SELECT * FROM modules WHERE faculty_id = ? OR faculty_id IS NULL ORDER BY module_number ASC");
                    $mStmt->execute([$viewId]);
                    $viewCourseModules = $mStmt->fetchAll();

                    $sStmt = $pdo->prepare("SELECT * FROM users WHERE faculty_id = ? AND role = 'student' ORDER BY id DESC");
                    $sStmt->execute([$viewId]);
                    $viewCourseStudents = $sStmt->fetchAll();

                    $eStmt = $pdo->prepare("SELECT * FROM exams WHERE faculty_id = ?");
                    $eStmt->execute([$viewId]);
                    $viewCourseExams = $eStmt->fetchAll();
                }
            }

            $allExamAttempts = [];
            if ($page === 'exams_report') {
                $allExamAttempts = $pdo->query("
                    SELECT ea.*, u.full_name as student_name, f.name as faculty_name
                    FROM exam_attempts ea
                    JOIN users u ON ea.user_id = u.id
                    LEFT JOIN faculties f ON u.faculty_id = f.id
                    ORDER BY ea.id DESC
                ")->fetchAll();
            }

            $allCertificates = [];
            if ($page === 'certificates_registry') {
                $allCertificates = $pdo->query("
                    SELECT cert.*, u.full_name as student_name, f.name as faculty_name
                    FROM certificates cert
                    JOIN users u ON cert.user_id = u.id
                    LEFT JOIN faculties f ON u.faculty_id = f.id
                    ORDER BY cert.id DESC
                ")->fetchAll();
            }

            return [
                'pending_grading' => $pendingGrading,
                'pending_remittance' => $pendingRemittance,
                'affiliate_applications' => $affiliateApplications,
                'approved_affiliates' => $approvedAffiliates,
                'certificate_registry' => $certificateRegistry,
                'students_list' => $studentsList,
                'view_student' => $viewStudent,
                'view_assignments' => $viewAssignments,
                'view_exams' => $viewExams,
                'view_certificates' => $viewCertificates,
                'view_payments' => $viewPayments,
                'all_exam_attempts' => $allExamAttempts,
                'all_certificates' => $allCertificates,
                'courses_list' => $coursesList,
                'view_course' => $viewCourse,
                'view_course_modules' => $viewCourseModules,
                'view_course_students' => $viewCourseStudents,
                'view_course_exams' => $viewCourseExams,
            ];
        } catch (\PDOException $e) {
            error_log("Error fetching admin dashboard data: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Upload course assignment coursework.
     *
     * @param int $userId
     * @param int $moduleId
     * @param \Illuminate\Http\UploadedFile|array $file
     * @throws Exception
     */
    public function uploadAssignment($userId, $moduleId, $file)
    {
        $pdo = DB::connection()->getPdo();

        // Enforcement of 14-day speed trap check for Phase II modules
        $modQuery = $pdo->prepare("SELECT * FROM modules WHERE id = ?");
        $modQuery->execute([$moduleId]);
        $module = $modQuery->fetch();
        if ($module) {
            $usrQuery = $pdo->prepare("SELECT created_at, phase2_expedited FROM users WHERE id = ?");
            $usrQuery->execute([$userId]);
            $user = $usrQuery->fetch();
            if ($user) {
                $regDate = strtotime($user['created_at']);
                $daysSinceReg = floor((time() - $regDate) / 86400);
                $isExpedited = intval($user['phase2_expedited'] ?? 0) === 1;

                if ($module['phase'] === 'II' && $daysSinceReg < 14 && !$isExpedited) {
                    throw new Exception('This coursework module is locked under the 14-day speed protection protocol. Access opens on Day 15.');
                }
            }
        }

        $fileTmpPath = '';
        $fileName = '';
        $fileSize = 0;
        
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileExtension = strtolower($file->getClientOriginalExtension());
        } elseif (is_array($file) && isset($file['tmp_name'])) {
            $fileTmpPath = $file['tmp_name'];
            $fileName = $file['name'];
            $fileSize = $file['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        } else {
            throw new Exception('Please select a valid file to upload.');
        }

        $maxSize = 25 * 1024 * 1024; // 25MB
        $allowedExtensions = ['pdf', 'docx', 'jpg', 'png', 'doc', 'txt'];

        if ($fileSize > $maxSize) {
            throw new Exception('Upload failed. File exceeds maximum allowed size of 25MB.');
        }

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Upload failed. Whitelisted extensions: PDF, DOCX, DOC, TXT, JPG, PNG.');
        }

        $userDir = public_path('uploads/' . $userId . '/' . $moduleId);
        if (!file_exists($userDir)) {
            mkdir($userDir, 0777, true);
        }

        $newFileName = 'assignment_' . time() . '.' . $fileExtension;
        $destPath = $userDir . '/' . $newFileName;
        $dbPath = 'uploads/' . $userId . '/' . $moduleId . '/' . $newFileName;
        $formattedSize = round($fileSize / 1024 / 1024, 2) . ' MB';

        $success = false;
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $file->move($userDir, $newFileName);
            $success = true;
        } else {
            $success = move_uploaded_file($fileTmpPath, $destPath);
        }

        if ($success) {
            try {
                $chk = $pdo->prepare("SELECT id FROM assignments WHERE user_id = ? AND module_id = ?");
                $chk->execute([$userId, $moduleId]);
                $existing = $chk->fetch();

                if ($existing) {
                    $stmt = $pdo->prepare("UPDATE assignments SET file_path = ?, file_size = ?, status = 'pending', grade = NULL, feedback = NULL, uploaded_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->execute([$dbPath, $formattedSize, $existing['id']]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO assignments (user_id, module_id, file_path, file_size, status) VALUES (?, ?, ?, ?, 'pending')");
                    $stmt->execute([$userId, $moduleId, $dbPath, $formattedSize]);
                }
            } catch (\PDOException $e) {
                throw new Exception('Database error: ' . $e->getMessage());
            }
        } else {
            throw new Exception('Error moving file to storage directory.');
        }
    }

    /**
     * Submit timed exam score metrics.
     *
     * @param int $userId
     * @param int $examId
     * @param float $score
     * @param int $violations
     * @param bool $forceSubmit
     * @param string $studentName (optional, optimizes lookup)
     * @throws Exception
     */
    public function submitExamScore($userId, $examId, $score, $violations, $forceSubmit, $studentName = '')
    {
        $pdo = DB::connection()->getPdo();

        $status = 'completed';
        if ($forceSubmit || $violations >= 2) {
            $status = 'force_submitted_violation';
            $score = 0.00;
        }

        try {
            $pdo->beginTransaction();

            $attemptStmt = $pdo->prepare("SELECT id FROM exam_attempts WHERE user_id = ? AND exam_id = ? AND status = 'in_progress' LIMIT 1");
            $attemptStmt->execute([$userId, $examId]);
            $attemptId = $attemptStmt->fetchColumn();

            $stmt = $pdo->prepare("UPDATE exam_attempts SET score = ?, status = ?, violation_count = ?, end_time = CURRENT_TIMESTAMP WHERE user_id = ? AND exam_id = ? AND status = 'in_progress'");
            $stmt->execute([$score, $status, $violations, $userId, $examId]);

            if ($status === 'force_submitted_violation') {
                $lockStmt = $pdo->prepare("UPDATE users SET account_status = 'locked' WHERE id = ?");
                $lockStmt->execute([$userId]);
                $pdo->commit();
                throw new Exception('VIOLATION_LOCK');
            }

            $passThreshold = 50.00;
            if ($score >= $passThreshold && $status === 'completed') {
                $examQuery = $pdo->prepare("SELECT faculty_id FROM exams WHERE id = ?");
                $examQuery->execute([$examId]);
                $courseId = $examQuery->fetchColumn();

                if ($courseId) {
                    $chkCert = $pdo->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ?");
                    $chkCert->execute([$userId, $courseId]);
                    $existingCert = $chkCert->fetchColumn();

                    if (!$existingCert) {
                        $currentYear = date('Y');
                        $seqQuery = $pdo->prepare("SELECT certificate_uid FROM certificates WHERE certificate_uid LIKE ? ORDER BY id DESC LIMIT 1");
                        $seqQuery->execute(["REG-LDN-$currentYear-%"]);
                        $lastCert = $seqQuery->fetchColumn();

                        $nextNum = 1;
                        if ($lastCert) {
                            $parts = explode('-', $lastCert);
                            $lastNum = intval(end($parts));
                            $nextNum = $lastNum + 1;
                        }

                        $certUid = sprintf("REG-LDN-%s-%05d", $currentYear, $nextNum);
                        $pdfPath = 'uploads/certificates/cert_' . $userId . '_' . $courseId . '.pdf';
                        $pdfFullPath = public_path($pdfPath);

                        $certStmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, exam_attempt_id, certificate_uid, issue_date, pdf_path, verification_status) VALUES (?, ?, ?, ?, CURDATE(), ?, 'approved')");
                        $certStmt->execute([$userId, $courseId, $attemptId, $certUid, $pdfPath]);

                        if (empty($studentName)) {
                            $stdQuery = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
                            $stdQuery->execute([$userId]);
                            $studentName = $stdQuery->fetchColumn();
                        }

                        $facQuery = $pdo->prepare("SELECT name FROM faculties WHERE id = ?");
                        $facQuery->execute([$courseId]);
                        $facultyName = $facQuery->fetchColumn();
                        $courseTitle = "Faculty of " . $facultyName;

                        $this->pdfService->generateCertificatePdf($studentName, $courseTitle, date('Y-m-d'), $certUid, $pdfFullPath);
                    }
                }
            } else {
                if ($status === 'completed') {
                    // Lock account
                    $lockStmt = $pdo->prepare("UPDATE users SET account_status = 'locked' WHERE id = ?");
                    $lockStmt->execute([$userId]);

                    // Fetch details & send WhatsApp Alert
                    $stdQuery = $pdo->prepare("SELECT whatsapp_number, full_name FROM users WHERE id = ?");
                    $stdQuery->execute([$userId]);
                    $usr = $stdQuery->fetch();
                    if ($usr) {
                        $whatsappMsg = "CPD UK LONDON REGISTRY Alert: Dear " . $usr['full_name'] . ", your exam score (" . $score . "%) fell below the 50% proficiency threshold. Your student account has been LOCKED. Please pay the £229 Resit Fee to reactivate your assessment terminal.";
                        $this->mailService->sendWhatsApp($usr['whatsapp_number'], $whatsappMsg);
                    }
                }
            }

            $pdo->commit();
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Initialize timed exam session attempt.
     *
     * @param int $userId
     * @param int $examId
     * @throws Exception
     */
    public function startExamAttempt($userId, $examId)
    {
        $pdo = DB::connection()->getPdo();

        try {
            $pdo->beginTransaction();

            $chkPassed = $pdo->prepare("SELECT COUNT(*) FROM exam_attempts WHERE user_id = ? AND exam_id = ? AND score >= 50.00 AND status = 'completed'");
            $chkPassed->execute([$userId, $examId]);
            if ($chkPassed->fetchColumn() > 0) {
                throw new Exception('Your result has been locked. You have already passed this examination.');
            }

            // Close orphan attempts
            $clearStmt = $pdo->prepare("UPDATE exam_attempts SET status = 'force_submitted_violation', score = 0.00, end_time = CURRENT_TIMESTAMP WHERE user_id = ? AND status = 'in_progress'");
            $clearStmt->execute([$userId]);

            // Create in_progress attempt
            $stmt = $pdo->prepare("INSERT INTO exam_attempts (user_id, exam_id, score, status, violation_count, start_time) VALUES (?, ?, NULL, 'in_progress', 0, CURRENT_TIMESTAMP)");
            $stmt->execute([$userId, $examId]);

            // Reset retake token
            $resetFlag = $pdo->prepare("UPDATE users SET exam_retake_unlocked = 0 WHERE id = ?");
            $resetFlag->execute([$userId]);

            $pdo->commit();
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Grade coursework assignment.
     *
     * @param int $assignmentId
     * @param string $grade
     * @param string $feedback
     * @throws Exception
     */
    public function gradeAssignment($assignmentId, $grade, $feedback)
    {
        $pdo = DB::connection()->getPdo();

        if (empty($grade)) {
            throw new Exception('Select a grade to submit.');
        }

        try {
            $stmt = $pdo->prepare("UPDATE assignments SET grade = ?, feedback = ?, status = 'reviewed' WHERE id = ?");
            $stmt->execute([$grade, $feedback, $assignmentId]);
        } catch (\PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Approve or reject a pending manual cash remittance.
     *
     * @param int $paymentId
     * @param string $action
     * @throws Exception
     */
    public function reviewRemittance($paymentId, $action)
    {
        $pdo = DB::connection()->getPdo();

        try {
            $pdo->beginTransaction();

            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE payments SET status = 'paid' WHERE id = ?");
                $stmt->execute([$paymentId]);

                $payStmt = $pdo->prepare("SELECT user_id FROM payments WHERE id = ?");
                $payStmt->execute([$paymentId]);
                $pay = $payStmt->fetch();

                if ($pay) {
                    $upStmt = $pdo->prepare("UPDATE users SET account_status = 'active' WHERE id = ?");
                    $upStmt->execute([$pay['user_id']]);
                }
            } else {
                $stmt = $pdo->prepare("UPDATE payments SET status = 'failed' WHERE id = ?");
                $stmt->execute([$paymentId]);
            }

            $pdo->commit();
        } catch (\PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Approve or reject regional partner affiliate applications.
     *
     * @param int $affiliateId
     * @param string $action
     * @throws Exception
     */
    public function reviewAffiliate($affiliateId, $action)
    {
        $pdo = DB::connection()->getPdo();
        $status = ($action === 'approve') ? 'approved' : 'rejected';

        try {
            $stmt = $pdo->prepare("UPDATE affiliates SET application_status = ? WHERE id = ?");
            $stmt->execute([$status, $affiliateId]);
        } catch (\PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Create student account (Admin operation).
     *
     * @param array $data
     * @return string Generated Password
     * @throws Exception
     */
    public function createStudent($data)
    {
        $pdo = DB::connection()->getPdo();

        $fullName = trim($data['student_name'] ?? '');
        $dob = trim($data['student_dob'] ?? '');
        $email = trim($data['student_email'] ?? '');
        $whatsappNumber = trim($data['student_whatsapp'] ?? '');
        $streetAddress = trim($data['student_street_address'] ?? '');
        $city = trim($data['student_city'] ?? '');
        $country = trim($data['student_country'] ?? '');
        $zipCode = trim($data['student_zip_code'] ?? '');
        $facultyId = intval($data['student_faculty'] ?? 0);
        $repCode = trim($data['student_rep'] ?? '');
        $status = trim($data['student_status'] ?? 'active');

        if (empty($fullName) || empty($dob) || empty($email) || empty($whatsappNumber) || empty($streetAddress) || empty($city) || empty($country) || empty($facultyId)) {
            throw new Exception('All required fields (Name, DOB, Email, WhatsApp, Street Address, City, Country, Faculty) must be filled.');
        }

        // Check duplicate
        $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $chk->execute([$email]);
        if ($chk->fetch()) {
            throw new Exception('Email is already registered.');
        }

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#%';
        $genPass = substr(str_shuffle($chars), 0, 10);
        $hash = password_hash($genPass, PASSWORD_DEFAULT);

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO users (full_name, dob, email, whatsapp_number, street_address, city, country, zip_code, password_hash, role, faculty_id, rep_code, account_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'student', ?, ?, ?)");
            $stmt->execute([$fullName, $dob, $email, $whatsappNumber, $streetAddress, $city, $country, $zipCode ? $zipCode : null, $hash, $facultyId, $repCode ? $repCode : null, $status]);

            $pdo->commit();

            $emailSubject = "Welcome to CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD - Portal Access";
            $emailBody = "Dear $fullName,\n\nYour student account has been created by the administrator.\n\nLogin Credentials:\nURL: http://127.0.0.1:8000/login.php\nEmail: $email\nPassword: $genPass\n\nSincerely,\nCPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD Assessor Services";
            $this->mailService->sendMailtrapEmail($email, $emailSubject, $emailBody);

            return $genPass;
        } catch (\PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Edit student account profile data (Admin operation).
     *
     * @param int $studentId
     * @param array $data
     * @throws Exception
     */
    public function editStudent($studentId, $data)
    {
        $pdo = DB::connection()->getPdo();

        $fullName = trim($data['student_name'] ?? '');
        $dob = trim($data['student_dob'] ?? '');
        $email = trim($data['student_email'] ?? '');
        $whatsappNumber = trim($data['student_whatsapp'] ?? '');
        $streetAddress = trim($data['student_street_address'] ?? '');
        $city = trim($data['student_city'] ?? '');
        $country = trim($data['student_country'] ?? '');
        $zipCode = trim($data['student_zip_code'] ?? '');
        $facultyId = intval($data['student_faculty'] ?? 0);
        $repCode = trim($data['student_rep'] ?? '');
        $status = trim($data['student_status'] ?? 'active');

        if (empty($fullName) || empty($dob) || empty($email) || empty($whatsappNumber) || empty($streetAddress) || empty($city) || empty($country) || empty($facultyId)) {
            throw new Exception('All required fields must be filled out to edit student.');
        }

        // Check duplicate email
        $chk = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $chk->execute([$email, $studentId]);
        if ($chk->fetch()) {
            throw new Exception('This email address is already registered to another user.');
        }

        // Faculty lock constraint: if tuition is paid, lock faculty track
        $chkPaid = $pdo->prepare("SELECT id FROM payments WHERE user_id = ? AND type = 'tuition' AND status = 'paid'");
        $chkPaid->execute([$studentId]);
        $isPaid = $chkPaid->fetchColumn();

        if ($isPaid) {
            $curQuery = $pdo->prepare("SELECT faculty_id FROM users WHERE id = ?");
            $curQuery->execute([$studentId]);
            $currentFacultyId = intval($curQuery->fetchColumn());

            if ($currentFacultyId !== $facultyId) {
                throw new Exception("Lock Error: Faculty Track is locked after tuition fee payment is cleared.");
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, dob = ?, email = ?, whatsapp_number = ?, street_address = ?, city = ?, country = ?, zip_code = ?, faculty_id = ?, rep_code = ?, account_status = ? WHERE id = ? AND role = 'student'");
            $stmt->execute([$fullName, $dob, $email, $whatsappNumber, $streetAddress, $city, $country, $zipCode ? $zipCode : null, $facultyId, $repCode ? $repCode : null, $status, $studentId]);
        } catch (\PDOException $e) {
            throw new Exception('Database error editing student: ' . $e->getMessage());
        }
    }

    /**
     * Delete student profile records (Admin operation).
     *
     * @param int $studentId
     * @throws Exception
     */
    public function deleteStudent($studentId)
    {
        $pdo = DB::connection()->getPdo();
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
            $stmt->execute([$studentId]);
        } catch (\PDOException $e) {
            throw new Exception('Database error deleting student: ' . $e->getMessage());
        }
    }

    /**
     * Revoke academic certificate (Admin operation).
     *
     * @param int $certId
     * @throws Exception
     */
    public function revokeCertificate($certId)
    {
        $pdo = DB::connection()->getPdo();
        try {
            $stmt = $pdo->prepare("UPDATE certificates SET verification_status = 'revoked' WHERE id = ?");
            $stmt->execute([$certId]);
        } catch (\PDOException $e) {
            throw new Exception('Database error revoking certificate: ' . $e->getMessage());
        }
    }

    /**
     * Re-approve academic certificate (Admin operation).
     *
     * @param int $certId
     * @throws Exception
     */
    public function approveCertificate($certId)
    {
        $pdo = DB::connection()->getPdo();
        try {
            $stmt = $pdo->prepare("UPDATE certificates SET verification_status = 'approved' WHERE id = ?");
            $stmt->execute([$certId]);
        } catch (\PDOException $e) {
            throw new Exception('Database error approving certificate: ' . $e->getMessage());
        }
    }

    /**
     * Edit student exam scores (Admin operation).
     *
     * @param int $attemptId
     * @param float $score
     * @param string $status
     * @throws Exception
     */
    public function editExamScore($attemptId, $score, $status)
    {
        $pdo = DB::connection()->getPdo();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT user_id, exam_id FROM exam_attempts WHERE id = ?");
            $stmt->execute([$attemptId]);
            $attemptData = $stmt->fetch();

            if ($attemptData) {
                $userId = $attemptData['user_id'];
                $examId = $attemptData['exam_id'];

                $upStmt = $pdo->prepare("UPDATE exam_attempts SET score = ?, status = ? WHERE id = ?");
                $upStmt->execute([$score, $status, $attemptId]);

                if ($score >= 70.00 && $status === 'completed') {
                    $examQuery = $pdo->prepare("SELECT faculty_id FROM exams WHERE id = ?");
                    $examQuery->execute([$examId]);
                    $courseId = $examQuery->fetchColumn();

                    if ($courseId) {
                        $chkCert = $pdo->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ?");
                        $chkCert->execute([$userId, $courseId]);
                        $existingCert = $chkCert->fetchColumn();

                        if (!$existingCert) {
                            $currentYear = date('Y');
                            $seqQuery = $pdo->prepare("SELECT certificate_uid FROM certificates WHERE certificate_uid LIKE ? ORDER BY id DESC LIMIT 1");
                            $seqQuery->execute(["REG-LDN-$currentYear-%"]);
                            $lastCert = $seqQuery->fetchColumn();

                            $nextNum = 1;
                            if ($lastCert) {
                                $parts = explode('-', $lastCert);
                                $lastNum = intval(end($parts));
                                $nextNum = $lastNum + 1;
                            }

                            $certUid = sprintf("REG-LDN-%s-%05d", $currentYear, $nextNum);
                            $pdfPath = 'uploads/certificates/cert_' . $userId . '_' . $courseId . '.pdf';
                            $pdfFullPath = public_path($pdfPath);

                            $certStmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, exam_attempt_id, certificate_uid, issue_date, pdf_path, verification_status) VALUES (?, ?, ?, ?, CURDATE(), ?, 'approved')");
                            $certStmt->execute([$userId, $courseId, $attemptId, $certUid, $pdfPath]);

                            $stdQuery = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
                            $stdQuery->execute([$userId]);
                            $studentName = $stdQuery->fetchColumn();

                            $facQuery = $pdo->prepare("SELECT name FROM faculties WHERE id = ?");
                            $facQuery->execute([$courseId]);
                            $facultyName = $facQuery->fetchColumn();
                            $courseTitle = "Faculty of " . $facultyName;

                            $this->pdfService->generateCertificatePdf($studentName, $courseTitle, date('Y-m-d'), $certUid, $pdfFullPath);
                        }
                    }
                }
            } else {
                throw new Exception('Exam attempt record not found.');
            }

            $pdo->commit();
        } catch (\PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw new Exception('Database error updating exam attempt: ' . $e->getMessage());
        }
    }

    /**
     * Manually award diploma certificates (Admin operation).
     *
     * @param int $studentId
     * @param int $courseId
     * @throws Exception
     */
    public function manualAwardCertificate($studentId, $courseId)
    {
        $pdo = DB::connection()->getPdo();

        try {
            $pdo->beginTransaction();

            $chkCert = $pdo->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ?");
            $chkCert->execute([$studentId, $courseId]);
            if ($chkCert->fetchColumn()) {
                throw new Exception('This student already has a certificate awarded for the selected program.');
            }

            $currentYear = date('Y');
            $seqQuery = $pdo->prepare("SELECT certificate_uid FROM certificates WHERE certificate_uid LIKE ? ORDER BY id DESC LIMIT 1");
            $seqQuery->execute(["REG-LDN-$currentYear-%"]);
            $lastCert = $seqQuery->fetchColumn();

            $nextNum = 1;
            if ($lastCert) {
                $parts = explode('-', $lastCert);
                $lastNum = intval(end($parts));
                $nextNum = $lastNum + 1;
            }

            $certUid = sprintf("REG-LDN-%s-%05d", $currentYear, $nextNum);
            $pdfPath = 'uploads/certificates/cert_' . $studentId . '_' . $courseId . '.pdf';
            $pdfFullPath = public_path($pdfPath);

            $certStmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, exam_attempt_id, certificate_uid, issue_date, pdf_path, verification_status) VALUES (?, ?, NULL, ?, CURDATE(), ?, 'approved')");
            $certStmt->execute([$studentId, $courseId, $certUid, $pdfPath]);

            $stdQuery = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
            $stdQuery->execute([$studentId]);
            $studentName = $stdQuery->fetchColumn();

            $facQuery = $pdo->prepare("SELECT name FROM faculties WHERE id = ?");
            $facQuery->execute([$courseId]);
            $facultyName = $facQuery->fetchColumn();
            $courseTitle = "Faculty of " . $facultyName;

            $this->pdfService->generateCertificatePdf($studentName, $courseTitle, date('Y-m-d'), $certUid, $pdfFullPath);

            $pdo->commit();
        } catch (\PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw new Exception('Database error awarding manual certificate: ' . $e->getMessage());
        }
    }

    /**
     * Lock or unlock student retake tokens (Admin operation).
     *
     * @param int $studentId
     * @param int $state
     * @throws Exception
     */
    public function toggleExamRetake($studentId, $state)
    {
        $pdo = DB::connection()->getPdo();
        try {
            $upStmt = $pdo->prepare("UPDATE users SET exam_retake_unlocked = ? WHERE id = ?");
            $upStmt->execute([$state, $studentId]);
        } catch (\PDOException $e) {
            throw new Exception('Database error toggling exam retake status: ' . $e->getMessage());
        }
    }

    /**
     * Update user profile settings details.
     *
     * @param int $userId
     * @param string $name
     * @param string $email
     * @throws Exception
     */
    public function updateProfile($userId, $name, $email)
    {
        $pdo = DB::connection()->getPdo();

        if (empty($name) || empty($email)) {
            throw new Exception('Name and Email are required fields.');
        }

        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $userId]);
        } catch (\PDOException $e) {
            throw new Exception('Error updating profile: ' . $e->getMessage());
        }
    }

    /**
     * Process password change settings.
     *
     * @param int $userId
     * @param string $currentHash
     * @param string $currentPw
     * @param string $newPw
     * @param string $confirmPw
     * @throws Exception
     */
    public function changePassword($userId, $currentHash, $currentPw, $newPw, $confirmPw)
    {
        $pdo = DB::connection()->getPdo();

        if (empty($currentPw) || empty($newPw) || empty($confirmPw)) {
            throw new Exception('All three password fields are required.');
        }

        if ($newPw !== $confirmPw) {
            throw new Exception('New Password and Confirm Password do not match.');
        }

        if (strlen($newPw) < 6) {
            throw new Exception('New Password must be at least 6 characters long.');
        }

        if (!password_verify($currentPw, $currentHash)) {
            throw new Exception('Current password is incorrect. Please try again.');
        }

        try {
            $hashed = password_hash($newPw, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hashed, $userId]);
        } catch (\PDOException $e) {
            throw new Exception('Error changing password: ' . $e->getMessage());
        }
    }
    /**
     * Add course (Faculty) (Admin operation).
     *
     * @param array $data
     * @throws Exception
     */
    public function addCourse($data)
    {
        $pdo = DB::connection()->getPdo();
        $name = trim($data['course_name'] ?? '');
        $code = trim($data['course_code'] ?? '');
        $duration = trim($data['course_duration'] ?? '');
        $fee = floatval($data['course_fee'] ?? 0.00);
        $description = trim($data['course_description'] ?? '');

        if (empty($name)) {
            throw new Exception("Course Name cannot be empty.");
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO faculties (name, code, duration, fee, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $code, $duration, $fee, $description]);
        } catch (\PDOException $e) {
            throw new Exception("Database error adding course: " . $e->getMessage());
        }
    }

    /**
     * Edit course (Faculty) (Admin operation).
     *
     * @param int $id
     * @param array $data
     * @throws Exception
     */
    public function editCourse($id, $data)
    {
        $pdo = DB::connection()->getPdo();
        $name = trim($data['course_name'] ?? '');
        $code = trim($data['course_code'] ?? '');
        $duration = trim($data['course_duration'] ?? '');
        $fee = floatval($data['course_fee'] ?? 0.00);
        $description = trim($data['course_description'] ?? '');

        if (empty($name)) {
            throw new Exception("Course Name cannot be empty.");
        }
        try {
            $stmt = $pdo->prepare("UPDATE faculties SET name = ?, code = ?, duration = ?, fee = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $code, $duration, $fee, $description, $id]);
        } catch (\PDOException $e) {
            throw new Exception("Database error updating course: " . $e->getMessage());
        }
    }

    /**
     * Delete course (Faculty) (Admin operation).
     *
     * @param int $id
     * @throws Exception
     */
    public function deleteCourse($id)
    {
        $pdo = DB::connection()->getPdo();
        try {
            $stmt = $pdo->prepare("DELETE FROM faculties WHERE id = ?");
            $stmt->execute([$id]);
        } catch (\PDOException $e) {
            throw new Exception("Database error deleting course: " . $e->getMessage());
        }
    }
}
