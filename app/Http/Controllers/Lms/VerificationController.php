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
    /**
     * Display the search verification form.
     */
    public function index(Request $request)
    {
        return view('lms.verification', [
            'search_performed' => false,
            'cert_uid' => trim($request->query('cert_uid', '')),
            'certificate' => null,
            'centre' => null,
            'paid_successfully' => false,
            'error' => '',
            'company_name' => '',
            'company_email' => '',
        ]);
    }

    /**
     * Handle verification search and paid queries.
     */
    public function search(Request $request)
    {
        $certUid = strtoupper(trim($request->input('cert_uid', '')));
        
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

                $paidSuccessfully = true;
                if ($isCentre) {
                    $centre = $result['centre'];
                } else {
                    $certificate = $result['certificate'];
                    $assignments = $result['assignments'];
                    $bestExam = $result['best_exam'];
                }
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

    /**
     * Handle verify POST request (forward to verifyView).
     */
    public function verify(Request $request)
    {
        return $this->verifyView($request);
    }
}
