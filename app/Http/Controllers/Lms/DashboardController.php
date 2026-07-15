<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DashboardService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $paymentService;

    public function __construct(DashboardService $dashboardService, PaymentService $paymentService)
    {
        $this->dashboardService = $dashboardService;
        $this->paymentService = $paymentService;
    }

    /**
     * Display student or admin dashboard.
     */
    public function index(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('lms.login');
        }

        $userId = session('user_id');
        
        // Retrieve current user details from database to check account_status and faculty_id
        $currentUser = DB::selectOne("SELECT * FROM users WHERE id = ?", [$userId]);
        if (!$currentUser) {
            session()->flush();
            return redirect()->route('lms.login');
        }

        // Convert user object to array matching legacy structure
        $currentUser = (array) $currentUser;

        $userRole = $currentUser['role'];
        $accountStatus = $currentUser['account_status'];
        $facultyId = $currentUser['faculty_id'];

        // --- ACTIVE EXAM STATE LOCKING MONITOR ---
        if ($userRole === 'student' && $accountStatus === 'active') {
            $activeAttempt = DB::selectOne("SELECT * FROM exam_attempts WHERE user_id = ? AND status = 'in_progress'", [$userId]);
            if ($activeAttempt) {
                // If they are accessing the page via GET or performing any post action that is NOT the exam submit
                if ($request->isMethod('get') || !$request->has('submit_exam_score')) {
                    $activeAttempt = (array) $activeAttempt;
                    
                    DB::transaction(function () use ($activeAttempt, $userId) {
                        // Force fail attempt as violation
                        DB::update("UPDATE exam_attempts SET status = 'force_submitted_violation', score = 0.00, violation_count = 2, end_time = CURRENT_TIMESTAMP WHERE id = ?", [$activeAttempt['id']]);
                        
                        // Hard-lock user profile
                        DB::update("UPDATE users SET account_status = 'locked' WHERE id = ?", [$userId]);
                    });
                    
                    // Destroy active credentials session
                    session()->flush();
                    return redirect()->route('lms.login', ['error' => 'Exam terminal exited. Your session was terminated at 0% and your account has been LOCKED.']);
                }
            }
        }

        $page = $request->query('page', 'dashboard');
        $search = $request->query('search', '');
        $statusFilter = $request->query('status_filter', '');
        $viewId = $request->query('view_id');

        $data = [
            'currentUser' => $currentUser,
            'page' => $page,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'viewId' => $viewId,
            'success_msg' => session('success_msg', ''),
            'error_msg' => session('error_msg', ''),
        ];

        if ($userRole === 'student') {
            if ($accountStatus === 'active') {
                $studentData = $this->dashboardService->getStudentData($userId, $facultyId, $currentUser);
                $data = array_merge($data, $studentData);
            }
        } else {
            // Admin role
            $adminData = $this->dashboardService->getAdminData($page, $search, $statusFilter, $viewId);
            $data = array_merge($data, $adminData);
        }

        return view('lms.dashboard', $data);
    }

    /**
     * Route and process POST actions in the dashboard.
     */
    public function handleAction(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('lms.login');
        }

        $userId = session('user_id');
        $currentUser = DB::selectOne("SELECT * FROM users WHERE id = ?", [$userId]);
        if (!$currentUser) {
            session()->flush();
            return redirect()->route('lms.login');
        }

        $currentUser = (array) $currentUser;
        $userRole = $currentUser['role'];
        $accountStatus = $currentUser['account_status'];

        $successMsg = '';
        $errorMsg = '';

        try {
            // A) Student Role Actions
            if ($userRole === 'student' && $accountStatus === 'active') {
                
                // 1. Coursework Assignment Upload
                if ($request->has('upload_assignment')) {
                    $moduleId = intval($request->input('module_id'));
                    $file = $request->file('assignment_file');
                    if (!$file && isset($_FILES['assignment_file'])) {
                        $file = $_FILES['assignment_file'];
                    }

                    $this->dashboardService->uploadAssignment($userId, $moduleId, $file);
                    $successMsg = 'Coursework document uploaded and registered successfully.';
                }

                // 2. Submit Timed Exam Results
                elseif ($request->has('submit_exam_score')) {
                    $examId = intval($request->input('exam_id'));
                    $score = floatval($request->input('exam_score'));
                    $violations = intval($request->input('violations'));
                    $forceSubmit = $request->has('force_submit_violation');

                    try {
                        $this->dashboardService->submitExamScore($userId, $examId, $score, $violations, $forceSubmit, $currentUser['full_name']);
                        $successMsg = "Exam submitted. You scored $score%. Details logged.";
                    } catch (Exception $ex) {
                        if ($ex->getMessage() === 'VIOLATION_LOCK') {
                            session()->flush();
                            return redirect()->route('lms.login', ['error' => 'Exam violation occurred. Account has been locked.']);
                        }
                        throw $ex;
                    }
                }

                // 3. Start Timed Exam Session
                elseif ($request->has('start_exam_attempt')) {
                    $examId = intval($request->input('exam_id'));
                    $this->dashboardService->startExamAttempt($userId, $examId);
                    $successMsg = 'Exam terminal initialized. Monitoring active.';
                }

                // 4. Pay Exam Resit Fee (£150)
                elseif ($request->has('pay_resit_fee')) {
                    $cardDetails = [
                        'card_holder' => $request->input('card_holder'),
                        'card_number' => $request->input('card_number'),
                        'card_exp' => $request->input('card_exp'),
                        'card_cvc' => $request->input('card_cvc'),
                    ];
                    $this->paymentService->payResitFee($userId, $cardDetails);
                    $successMsg = 'Resit Fee of £229.00 processed successfully. Exam attempt eligibility unlocked.';
                }

                // 5. Pay Installment Fee (£749)
                elseif ($request->has('pay_installment')) {
                    $instNum = intval($request->input('installment_number'));
                    $cardDetails = [
                        'card_holder' => $request->input('card_holder'),
                        'card_number' => $request->input('card_number'),
                        'card_exp' => $request->input('card_exp'),
                        'card_cvc' => $request->input('card_cvc'),
                    ];
                    $this->paymentService->payInstallment($userId, $instNum, $cardDetails);
                    $successMsg = 'Installment ' . $instNum . ' of 3 processed successfully.';
                }
            }

            // B) Admin Role Actions
            if ($userRole === 'admin') {
                
                // 1. Grade Assignment Submission
                if ($request->has('grade_assignment')) {
                    $assignmentId = intval($request->input('assignment_id'));
                    $grade = $request->input('grade');
                    $feedback = $request->input('feedback');

                    $this->dashboardService->gradeAssignment($assignmentId, $grade, $feedback);
                    $successMsg = 'Assignment graded and updated successfully.';
                }

                // 2. Approve/Reject Manual cash remittance
                elseif ($request->has('review_remittance')) {
                    $paymentId = intval($request->input('payment_id'));
                    $action = $request->input('payment_action');

                    $this->dashboardService->reviewRemittance($paymentId, $action);
                    if ($action === 'approve') {
                        $successMsg = 'Remittance approved. Student account activated.';
                    } else {
                        $successMsg = 'Remittance reference rejected.';
                    }
                }

                // 3. Approve Regional Affiliate Partner Onboarding
                elseif ($request->has('review_affiliate')) {
                    $affiliateId = intval($request->input('affiliate_id'));
                    $action = $request->input('aff_action');

                    $this->dashboardService->reviewAffiliate($affiliateId, $action);
                    $successMsg = 'Affiliate application updated to ' . strtoupper(($action === 'approve') ? 'approved' : 'rejected') . '.';
                }

                // 4. Create Student Account
                elseif ($request->has('create_student')) {
                    $studentData = [
                        'student_name' => $request->input('student_name'),
                        'student_dob' => $request->input('student_dob'),
                        'student_email' => $request->input('student_email'),
                        'student_whatsapp' => $request->input('student_whatsapp'),
                        'student_faculty' => $request->input('student_faculty'),
                        'student_rep' => $request->input('student_rep'),
                        'student_status' => $request->input('student_status'),
                    ];

                    $genPass = $this->dashboardService->createStudent($studentData);
                    $successMsg = "Student created successfully!<br><strong>Email:</strong> <code>" . htmlspecialchars($studentData['student_email']) . "</code><br><strong>Generated Password:</strong> <code>" . htmlspecialchars($genPass) . "</code>";
                }

                // 5. Update / Edit Student Account Details
                elseif ($request->has('edit_student')) {
                    $studentId = intval($request->input('student_id'));
                    $studentData = [
                        'student_name' => $request->input('student_name'),
                        'student_dob' => $request->input('student_dob'),
                        'student_email' => $request->input('student_email'),
                        'student_whatsapp' => $request->input('student_whatsapp'),
                        'student_faculty' => $request->input('student_faculty'),
                        'student_rep' => $request->input('student_rep'),
                        'student_status' => $request->input('student_status'),
                    ];

                    $this->dashboardService->editStudent($studentId, $studentData);
                    $successMsg = 'Student account record updated successfully.';
                }

                // 6. Delete Student Account Record
                elseif ($request->has('delete_student')) {
                    $deleteId = intval($request->input('delete_id'));
                    $this->dashboardService->deleteStudent($deleteId);
                    $successMsg = 'Student profile and credentials deleted successfully.';
                }

                // 7. Revoke Certificate Registry Record
                elseif ($request->has('revoke_certificate')) {
                    $certId = intval($request->input('cert_id'));
                    $this->dashboardService->revokeCertificate($certId);
                    $successMsg = 'Student certificate reference has been REVOKED and flagged publically.';
                }

                // 8. Re-Approve Certificate Registry Record
                elseif ($request->has('approve_certificate')) {
                    $certId = intval($request->input('cert_id'));
                    $this->dashboardService->approveCertificate($certId);
                    $successMsg = 'Student certificate reference has been RE-APPROVED and validated.';
                }

                // 9. Edit Assignment Grade & Feedback
                elseif ($request->has('edit_assignment_grade')) {
                    $assignmentId = intval($request->input('assignment_id'));
                    $grade = $request->input('grade');
                    $feedback = $request->input('feedback');

                    $this->dashboardService->gradeAssignment($assignmentId, $grade, $feedback);
                    $successMsg = 'Assignment grade and feedback updated successfully.';
                }

                // 10. Edit Exam Score & Status
                elseif ($request->has('edit_exam_score')) {
                    $attemptId = intval($request->input('attempt_id'));
                    $score = floatval($request->input('score'));
                    $status = $request->input('status');

                    $this->dashboardService->editExamScore($attemptId, $score, $status);
                    $successMsg = 'Exam attempt details updated successfully.';
                }

                // 11. Manually Award Certificate
                elseif ($request->has('manual_award_certificate')) {
                    $studentId = intval($request->input('user_id'));
                    $courseId = intval($request->input('course_id'));

                    $this->dashboardService->manualAwardCertificate($studentId, $courseId);
                    $successMsg = 'Certificate manually awarded and PDF generated successfully.';
                }

                // 12. Toggle Exam Retake Lock/Unlock
                elseif ($request->has('toggle_exam_retake')) {
                    $studentId = intval($request->input('user_id'));
                    $newState = intval($request->input('new_state'));

                    $this->dashboardService->toggleExamRetake($studentId, $newState);
                    $successMsg = $newState ? 'Exam retake successfully unlocked.' : 'Exam retake successfully locked.';
                }

                // 13. Toggle Phase II Coursework Expedite
                elseif ($request->has('toggle_phase2_expedite')) {
                    $studentId = intval($request->input('user_id'));
                    $newState = intval($request->input('new_state'));

                    DB::update("UPDATE users SET phase2_expedited = ? WHERE id = ?", [$newState, $studentId]);
                    
                    if ($newState) {
                        $studentInfo = DB::selectOne("SELECT whatsapp_number, full_name FROM users WHERE id = ?", [$studentId]);
                        if ($studentInfo) {
                            $whatsappMsg = "Dear " . $studentInfo->full_name . ", Your Phase II Specialty modules have been manually expedited by the Academic Committee.";
                            app(\App\Services\MailService::class)->sendWhatsApp($studentInfo->whatsapp_number, $whatsappMsg);
                        }
                        $successMsg = 'Phase II coursework lock successfully bypassed. WhatsApp alert dispatched.';
                    } else {
                        $successMsg = '14-Day speed lock re-applied successfully.';
                    }
                }
            }

            // UNIVERSAL ACTIONS
            
            // Profile Update (Name & Email)
            if ($request->has('update_profile')) {
                $newName = $request->input('profile_name');
                $newEmail = $request->input('profile_email');

                $this->dashboardService->updateProfile($userId, $newName, $newEmail);
                
                session([
                    'user_name' => $newName,
                    'user_email' => $newEmail,
                ]);

                $successMsg = 'Profile details updated successfully.';
            }

            // Password Change
            elseif ($request->has('change_password')) {
                $currentPw = $request->input('current_password');
                $newPw = $request->input('new_password');
                $confirmPw = $request->input('confirm_password');

                $this->dashboardService->changePassword($userId, $currentUser['password_hash'], $currentPw, $newPw, $confirmPw);
                $successMsg = 'Password changed successfully.';
            }

        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
        }

        // Redirect back with messages
        return redirect()->to($request->getRequestUri())
            ->with('success_msg', $successMsg)
            ->with('error_msg', $errorMsg);
    }
}
