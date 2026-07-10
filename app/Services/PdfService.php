<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;

class PdfService
{
    /**
     * Generates a premium, non-editable PDF certificate.
     *
     * @param string $student_name Candidate's full legal name
     * @param string $course_title Program of study
     * @param string $issue_date Date of award
     * @param string $cert_uid Unique sequential Certificate Serial Number
     * @param string $dest_path Physical path where the PDF will be saved
     * @return bool True if generated successfully, false otherwise
     */
    public function generateCertificatePdf($student_name, $course_title, $issue_date, $cert_uid, $dest_path)
    {
        try {
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);

            // Convert logo to base64 inline only if GD extension is loaded (required by Dompdf to parse image size)
            $use_image_logo = extension_loaded('gd');
            $logo_base64 = '';
            if ($use_image_logo) {
                $logo_path = public_path('assets/images/logo.png');
                if (file_exists($logo_path)) {
                    $logo_data = file_get_contents($logo_path);
                    $logo_base64 = 'data:image/' . pathinfo($logo_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logo_data);
                } else {
                    $use_image_logo = false;
                }
            }

            // Format issue date (e.g., 09 July 2026)
            $formatted_date = date('d F Y', strtotime($issue_date));

            // Premium HTML layout for the certificate (styled for landscape A4, optimized for Dompdf CSS 2.1 compiler)
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <style>
                    @page {
                        size: a4 landscape;
                        margin: 0;
                    }
                    body {
                        font-family: "Georgia", "Times New Roman", serif;
                        background-color: #f7f9fa;
                        color: #1e293b;
                        margin: 0;
                        padding: 0;
                    }
                    .cert-wrapper {
                        width: 100%;
                        height: 100%;
                        padding: 40px;
                        box-sizing: border-box;
                        background: #ffffff;
                    }
                    .cert-border-outer {
                        border: 15px solid #0f2942;
                        height: 100%;
                        padding: 5px;
                        box-sizing: border-box;
                    }
                    .cert-border-inner {
                        border: 3px double #cba135;
                        height: 100%;
                        padding: 40px;
                        box-sizing: border-box;
                        position: relative;
                        text-align: center;
                    }
                    .logo-container {
                        margin-bottom: 25px;
                    }
                    .logo {
                        height: 75px;
                    }
                    .org-name {
                        font-family: "Helvetica Neue", "Arial", sans-serif;
                        font-size: 13px;
                        font-weight: bold;
                        letter-spacing: 4px;
                        color: #0f2942;
                        margin-top: 10px;
                    }
                    .title {
                        font-size: 32px;
                        font-weight: normal;
                        color: #0f2942;
                        margin: 20px 0 10px 0;
                        letter-spacing: 2px;
                    }
                    .subtitle {
                        font-family: "Helvetica Neue", "Arial", sans-serif;
                        font-size: 11px;
                        font-weight: 600;
                        letter-spacing: 3px;
                        color: #cba135;
                        text-transform: uppercase;
                        margin-bottom: 30px;
                    }
                    .cert-to {
                        font-style: italic;
                        font-size: 16px;
                        color: #64748b;
                        margin-bottom: 15px;
                    }
                    .recipient-name {
                        font-size: 42px;
                        font-weight: bold;
                        color: #0f2942;
                        margin-bottom: 20px;
                        border-bottom: 2px solid #e2e8f0;
                        display: inline-block;
                        padding-bottom: 10px;
                        min-width: 450px;
                    }
                    .cert-reason {
                        font-style: italic;
                        font-size: 15px;
                        color: #475569;
                        line-height: 1.6;
                        max-width: 650px;
                        margin: 0 auto 20px auto;
                    }
                    .course-title {
                        font-size: 26px;
                        font-weight: bold;
                        color: #cba135;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        margin-bottom: 30px;
                    }
                    .metadata-table {
                        width: 100%;
                        margin-top: 40px;
                    }
                    .sig-block {
                        width: 30%;
                        text-align: center;
                        vertical-align: bottom;
                    }
                    .sig-line {
                        border-top: 1px solid #cba135;
                        margin-top: 5px;
                        padding-top: 5px;
                        font-family: "Helvetica Neue", "Arial", sans-serif;
                        font-size: 10px;
                        font-weight: bold;
                        text-transform: uppercase;
                        color: #64748b;
                    }
                    .sig-handwritten {
                        font-family: "Brush Script MT", "Lucida Handwriting", cursive, Georgia;
                        font-size: 24px;
                        color: #0f2942;
                        margin-bottom: 5px;
                        font-style: italic;
                    }
                    .seal-block {
                        width: 40%;
                        text-align: center;
                        vertical-align: middle;
                    }
                    .seal {
                        width: 85px;
                        height: 85px;
                        border-radius: 50%;
                        background: radial-gradient(circle, #f3e5ab 0%, #cba135 100%);
                        border: 2px solid #b08a28;
                        display: inline-block;
                        position: relative;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    }
                    .seal-text {
                        font-family: "Helvetica Neue", "Arial", sans-serif;
                        font-size: 8px;
                        font-weight: 800;
                        color: #0f2942;
                        position: absolute;
                        width: 100%;
                        top: 36%;
                        text-align: center;
                        letter-spacing: 0.5px;
                    }
                    .footer-bar {
                        position: absolute;
                        bottom: 25px;
                        left: 40px;
                        right: 40px;
                        border-top: 1px solid #e2e8f0;
                        padding-top: 15px;
                    }
                    .footer-meta {
                        font-family: "Helvetica Neue", "Arial", sans-serif;
                        font-size: 9px;
                        color: #64748b;
                        text-align: left;
                        line-height: 1.5;
                    }
                    .verification-info {
                        font-weight: bold;
                        color: #0f2942;
                    }
                </style>
            </head>
            <body>
                <div class="cert-wrapper">
                    <div class="cert-border-outer">
                        <div class="cert-border-inner">
                            
                            <div class="logo-container">
                                ' . ($use_image_logo && $logo_base64 ? '<img src="' . $logo_base64 . '" class="logo">' : '
                                <div style="margin: 0 auto 5px auto; width: 64px; height: 64px; border: 3px double #cba135; border-radius: 50%; background: #0f2942; line-height: 58px; text-align: center; color: #cba135; font-size: 20px; font-weight: bold; font-family: Georgia, serif;">LIAB</div>') . '
                                <div class="org-name">CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</div>
                            </div>

                            <div class="title">DIPLOMA OF ACADEMIC EXCELLENCE</div>
                            <div class="subtitle">Verifiable Board Certification Registry</div>

                            <div class="cert-to">This academic award is registered and presented to</div>
                            <div class="recipient-name">' . htmlspecialchars($student_name) . '</div>

                            <div class="cert-reason">
                                upon the successful demonstration of required competencies, research modules, and timed assessments within the program of study
                            </div>
                            <div class="course-title">' . htmlspecialchars($course_title) . '</div>

                            <table class="metadata-table">
                                <tr>
                                    <td class="sig-block">
                                        <div class="sig-handwritten">M. C. Assessor</div>
                                        <div class="sig-line">Assessor Registrar</div>
                                    </td>
                                    <td class="seal-block">
                                        <div class="seal">
                                            <div class="seal-text">BOARD REGISTRY<br>SEAL</div>
                                        </div>
                                    </td>
                                    <td class="sig-block">
                                        <div class="sig-handwritten">Sir Richard Cole</div>
                                        <div class="sig-line">Registry Board Director</div>
                                    </td>
                                </tr>
                            </table>

                            <div class="footer-bar">
                                <table style="width: 100%;">
                                    <tr>
                                        <td class="footer-meta">
                                            <strong>Verifiable Reference ID:</strong> <span class="verification-info">' . htmlspecialchars($cert_uid) . '</span><br>
                                            <strong>Registry Award Date:</strong> ' . $formatted_date . '<br>
                                            <strong>Authentication Gate:</strong> cpduk.london/verify
                                        </td>
                                        <td style="text-align: right; font-family: \'Helvetica Neue\', Arial, sans-serif; font-size: 8px; color: #64748b; vertical-align: bottom;">
                                            SECURE REGISTRY RECORD &bull; NON-EDITABLE DOCUMENT &bull; ISSUED BY LIAB SENIOR BOARD
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </body>
            </html>
            ';

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // Save PDF to destination
            $dir = dirname($dest_path);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            
            file_put_contents($dest_path, $dompdf->output());
            return true;
        } catch (Exception $e) {
            error_log("Failed to generate certificate PDF: " . $e->getMessage());
            return false;
        }
    }
}
