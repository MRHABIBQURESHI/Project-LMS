<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CertificateController extends Controller
{
    /**
     * Show verifiable certificate detail page.
     */
    public function show(Request $request)
    {
        $uid = trim($request->query('uid', ''));
        $certificate = null;
        $error = '';

        if (empty($uid)) {
            $error = 'No Certificate reference UID provided.';
        } else {
            try {
                $certificate = DB::selectOne("
                    SELECT cert.*, u.full_name as student_name, f.name as faculty_name, f.id as faculty_id
                    FROM certificates cert
                    JOIN users u ON cert.user_id = u.id
                    LEFT JOIN faculties f ON u.faculty_id = f.id
                    WHERE cert.certificate_uid = ?
                ", [$uid]);
                
                if (!$certificate) {
                    $error = 'Invalid certificate validation credentials token.';
                } else {
                    $certificate = (array) $certificate;

                    // Check if certificate PDF mockup folder exists, and generate a placeholder file if not
                    $certDir = public_path('uploads/certificates');
                    if (!file_exists($certDir)) {
                        mkdir($certDir, 0777, true);
                    }
                    $pdfFile = $certDir . '/cert_' . $certificate['user_id'] . '_' . $certificate['faculty_id'] . '.pdf';
                    if (!file_exists($pdfFile)) {
                        // Write simple PDF-like contents so physical file exists for student downloads
                        $placeholderContent = "%PDF-1.4\n%...\nVerifiable Certificate for " . $certificate['student_name'] . "\nUID: " . $certificate['certificate_uid'] . "\nAward: Faculty of " . $certificate['faculty_name'] . "\nDate: " . $certificate['issue_date'];
                        file_put_contents($pdfFile, $placeholderContent);
                    }
                }
            } catch (Exception $e) {
                $error = 'Database Registry connection error: ' . $e->getMessage();
            }
        }

        if ($error) {
            return view('lms.certificate_error', ['error' => $error]);
        }

        return view('lms.certificate', ['certificate' => $certificate]);
    }
}
