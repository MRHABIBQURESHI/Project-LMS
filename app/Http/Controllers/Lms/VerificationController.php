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
    public function index()
    {
        return view('lms.verification', [
            'search_performed' => false,
            'search_type' => 'student',
            'cert_uid' => '',
            'certificate' => null,
            'paid_successfully' => false,
            'error' => '',
        ]);
    }

    /**
     * Handle verification search and paid corporate queries.
     */
    public function search(Request $request)
    {
        $certUid = trim($request->input('cert_uid', ''));
        $searchType = trim($request->input('search_type', 'student'));
        
        $error = '';
        $certificate = null;
        $paidSuccessfully = false;
        $assignments = [];
        $bestExam = null;

        if (empty($certUid)) {
            $error = 'Please enter a valid Certificate Reference UID.';
        } else {
            try {
                $certificate = $this->verificationService->lookupCertificate($certUid);
                
                if (!$certificate) {
                    $error = 'No verifiable registry match found for reference ID: ' . htmlspecialchars($certUid);
                } elseif ($searchType === 'corporate') {
                    // Check if payment was submitted
                    if ($request->has('process_corporate_payment')) {
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
                            $cardDetails
                        );

                        $paidSuccessfully = true;
                        $certificate = $result['certificate'];
                        $assignments = $result['assignments'];
                        $bestExam = $result['best_exam'];
                    }
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('lms.verification', [
            'search_performed' => true,
            'search_type' => $searchType,
            'cert_uid' => $certUid,
            'certificate' => $certificate,
            'paid_successfully' => $paidSuccessfully,
            'assignments' => $assignments,
            'best_exam' => $bestExam,
            'error' => $error,
            'company_name' => $request->input('company_name'),
            'company_email' => $request->input('company_email'),
        ]);
    }

    /**
     * Serve the premium verification page (maps to verify.php).
     */
    public function verifyDetail(Request $request)
    {
        $certUid = strtoupper(trim($request->input('cert_uid', $request->query('cert_uid', ''))));
        
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
}
