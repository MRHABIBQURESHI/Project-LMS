<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\VerificationService;
use Exception;

class VerificationController extends Controller
{
    protected $verificationService;

    public function __construct(VerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Display the search verification form.
     */
    public function index(Request $request)
    {
        $certUid = strtoupper(trim($request->query('serial_id', $request->query('cert_uid', ''))));
        
        $searchPerformed = false;
        $paidSuccessfully = false;
        $certificate = null;
        $centre = null;
        $assignments = [];
        $bestExam = null;
        $resultType = 'certificate';
        $error = '';
        $companyName = '';
        $companyEmail = '';

        // Retrieve from session if redirect back after success
        if (session('paid_successfully') === true && session('last_searched_serial_id')) {
            $certUid = session('last_searched_serial_id');
            $paidSuccessfully = true;
            $searchPerformed = true;
            $companyName = session('company_name', '');
            $companyEmail = session('company_email', '');
            
            try {
                $isCentre = str_starts_with($certUid, 'CTR-');
                if ($isCentre) {
                    $centre = $this->verificationService->lookupCentre($certUid);
                    $resultType = 'centre';
                } else {
                    $certificate = $this->verificationService->lookupCertificate($certUid);
                    $resultType = 'certificate';
                    
                    // Fetch details
                    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
                    $aStmt = $pdo->prepare("
                        SELECT a.*, m.title as module_title, m.module_number
                        FROM assignments a
                        JOIN modules m ON a.module_id = m.id
                        WHERE a.user_id = ?
                        ORDER BY m.module_number ASC
                    ");
                    $aStmt->execute([$certificate['student_id']]);
                    $assignments = $aStmt->fetchAll();

                    $exStmt = $pdo->prepare("
                        SELECT * FROM exam_attempts
                        WHERE user_id = ? AND status = 'completed'
                        ORDER BY score DESC LIMIT 1
                    ");
                    $exStmt->execute([$certificate['student_id']]);
                    $bestExam = $exStmt->fetch();
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('lms.verification', [
            'search_performed' => $searchPerformed,
            'cert_uid' => $certUid,
            'certificate' => $certificate,
            'centre' => $centre,
            'paid_successfully' => $paidSuccessfully,
            'assignments' => $assignments,
            'best_exam' => $bestExam,
            'error' => $error,
            'result_type' => $resultType,
            'company_name' => $companyName ?: trim($request->query('company_name', '')),
            'company_email' => $companyEmail ?: trim($request->query('company_email', '')),
        ]);
    }

    /**
     * Handle verification search and paid queries.
     */
    public function search(Request $request)
    {
        $certUid = strtoupper(trim($request->input('serial_id', $request->input('cert_uid', ''))));
        
        $error = '';
        $certificate = null;
        $centre = null;
        $paidSuccessfully = false;
        $assignments = [];
        $bestExam = null;
        $resultType = 'certificate';

        if (empty($certUid)) {
            $error = 'Please enter a valid Serial ID or Centre ID.';
        } else {
            // Save to session immediately before Stripe attempt
            session([
                'last_searched_serial_id' => $certUid,
                'company_name' => $request->input('company_name'),
                'company_email' => $request->input('company_email'),
                'paid_successfully' => false
            ]);

            try {
                $isCentre = str_starts_with($certUid, 'CTR-');
                
                if ($isCentre) {
                    $centre = $this->verificationService->lookupCentre($certUid);
                    if (!$centre) {
                        throw new Exception('No verifiable registry match found for Centre ID: ' . htmlspecialchars($certUid));
                    }
                    $resultType = 'centre';
                } else {
                    $certificate = $this->verificationService->lookupCertificate($certUid);
                    if (!$certificate) {
                        throw new Exception('No verifiable registry match found for reference ID: ' . htmlspecialchars($certUid));
                    }
                    $resultType = 'certificate';
                }

                // Process corporate check fee payment of £49.00
                $cardDetails = [
                    'card_holder' => $request->input('card_holder'),
                    'card_number' => $request->input('card_number'),
                    'card_exp' => $request->input('card_exp'),
                    'card_cvc' => $request->input('card_cvc'),
                ];

                $result = $this->verificationService->processCorporatePayment(
                    $certUid,
                    $request->input('company_name'),
                    $request->input('company_email'),
                    $cardDetails,
                    $isCentre
                );

                // Set paid successfully in session and redirect to GET route
                session(['paid_successfully' => true]);
                
                return redirect()->route('lms.verification');

            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('lms.verification', [
            'search_performed' => true,
            'cert_uid' => $certUid,
            'certificate' => $certificate,
            'centre' => $centre,
            'paid_successfully' => $paidSuccessfully,
            'assignments' => $assignments,
            'best_exam' => $bestExam,
            'error' => $error,
            'result_type' => $resultType,
            'company_name' => $request->input('company_name'),
            'company_email' => $request->input('company_email'),
        ]);
    }

    /**
     * Serve the premium verification page (maps to verify.php).
     */
    public function verifyView(Request $request)
    {
        $certUid = strtoupper(trim($request->input('serial_id', $request->input('cert_uid', $request->query('serial_id', $request->query('cert_uid', ''))))));
        
        $success = false;
        $error = '';
        $searchPerformed = false;
        $result = null;

        if (!empty($certUid)) {
            $searchPerformed = true;
            $result = $this->verificationService->verifyCertificate($certUid);
            if ($result) {
                if ($result['verification_status'] === 'approved') {
                    $success = true;
                } else {
                    $error = 'This certificate has been revoked or invalidated.';
                }
            } else {
                $error = 'No certificate record found with the specified Serial ID.';
            }
        }

        return view('lms.verify', [
            'success' => $success,
            'error' => $error,
            'search_performed' => $searchPerformed,
            'cert_uid' => $certUid,
            'result' => $result,
        ]);
    }

    /**
     * Handle verify POST request (forward to verifyView).
     */
    public function verify(Request $request)
    {
        return $this->verifyView($request);
    }
}
