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
                        font-family: "Helvetica Neue", "Arial", sans-serif;
                        background-color: #ffffff;
                        color: #1e293b;
                        margin: 0;
                        padding: 0;
                    }
                    .cert-wrapper {
                        width: 100%;
                        height: 100%;
                        padding: 30px;
                        box-sizing: border-box;
                        background: #ffffff;
                    }
                    .cert-border-outer {
                        border: 3px solid #002F6C;
                        height: 100%;
                        padding: 35px 45px;
                        box-sizing: border-box;
                        position: relative;
                    }
                    .cert-border-inner {
                        height: 100%;
                        box-sizing: border-box;
                        position: relative;
                        text-align: center;
                    }
                    .logo-container {
                        margin-top: 10px;
                        margin-bottom: 25px;
                    }
                    .logo {
                        height: 60px;
                        display: block;
                        margin: 0 auto 10px auto;
                    }
                    .org-name-primary {
                        font-size: 24px;
                        font-weight: 800;
                        color: #002F6C;
                        letter-spacing: 0.5px;
                        line-height: 1.2;
                    }
                    .org-name-secondary {
                        font-size: 19px;
                        font-weight: 800;
                        color: #002F6C;
                        letter-spacing: 0.5px;
                        line-height: 1.2;
                        margin-top: 3px;
                    }
                    .cert-to-officially {
                        font-size: 11px;
                        font-weight: bold;
                        color: #64748b;
                        margin-top: 25px;
                        margin-bottom: 20px;
                        letter-spacing: 1px;
                        text-transform: uppercase;
                    }
                    .recipient-name {
                        font-family: "Georgia", "Times New Roman", serif;
                        font-size: 34px;
                        font-weight: bold;
                        color: #1e293b;
                        margin-bottom: 20px;
                    }
                    .cert-reason {
                        font-size: 13px;
                        color: #475569;
                        line-height: 1.5;
                        max-width: 700px;
                        margin: 0 auto 15px auto;
                    }
                    .course-title {
                        font-family: "Georgia", "Times New Roman", serif;
                        font-size: 24px;
                        font-weight: bold;
                        color: #002F6C;
                        margin-bottom: 15px;
                    }
                    .award-rights {
                        font-size: 12px;
                        color: #475569;
                        line-height: 1.5;
                        max-width: 700px;
                        margin: 0 auto 20px auto;
                    }
                    .verified-date {
                        font-family: "Georgia", "Times New Roman", serif;
                        font-size: 13px;
                        font-style: italic;
                        color: #64748b;
                        margin-bottom: 40px;
                    }
                    .watermark {
                        position: absolute;
                        top: 45%;
                        left: 50%;
                        width: 240px;
                        height: 240px;
                        margin-top: -120px;
                        margin-left: -120px;
                        opacity: 0.05;
                        z-index: -10;
                    }
                    .watermark img {
                        width: 100%;
                        height: auto;
                        display: block;
                    }
                </style>
            </head>
            <body>
                <div class="cert-wrapper">
                    <div class="cert-border-outer">
                        <div class="cert-border-inner">
                            
                            ' . ($use_image_logo && $logo_base64 ? '
                                <div class="watermark">
                                    <img src="' . $logo_base64 . '">
                                </div>
                            ' : '') . '

                            <div class="logo-container">
                                ' . ($use_image_logo && $logo_base64 ? '<img src="' . $logo_base64 . '" class="logo">' : '') . '
                                <div class="org-name-primary">CPD UK LONDON</div>
                                <div class="org-name-secondary">INTERNATIONAL CERTIFICATION AWARD BOARD</div>
                            </div>

                            <div class="cert-to-officially">THIS IS TO OFFICIALLY CERTIFY AND RECORD THAT</div>
                            <div class="recipient-name">' . htmlspecialchars($student_name) . '</div>

                            <div class="cert-reason">
                                has successfully completed all institutional assessment portfolios and crossed the required proficiency threshold within the specialized domain of:
                            </div>
                            <div class="course-title">Faculty of ' . htmlspecialchars($course_title) . '</div>

                            <div class="award-rights">
                                and is hereby awarded this formal validation credential with all accompanying rights, honors, and professional recognitions pertaining to this registry board.
                            </div>

                            <div class="verified-date">
                                Verified and signed on this day: ' . $formatted_date . '
                            </div>

                            <table style="width: 100%; position: absolute; bottom: 10px; left: 0; right: 0;">
                                <tr>
                                    <td style="width: 35%; text-align: left; font-family: \'Helvetica Neue\', Arial, sans-serif; font-size: 9px; color: #64748b; line-height: 1.4; vertical-align: bottom;">
                                        <strong style="text-transform: uppercase;">REGISTRY DATA VALIDATION:</strong><br>
                                        Registry Serial ID: ' . htmlspecialchars($cert_uid) . '
                                    </td>
                                    <td style="width: 30%; text-align: center; font-family: \'Helvetica Neue\', Arial, sans-serif; font-size: 10px; color: #64748b; vertical-align: bottom;">
                                        Official Portal Registry
                                    </td>
                                    <td style="width: 35%; text-align: right; vertical-align: bottom; font-family: \'Helvetica Neue\', Arial, sans-serif;">
                                        <div style="font-family: \'Brush Script MT\', \'Lucida Handwriting\', cursive, Georgia; font-size: 26px; color: #002F6C; margin-bottom: 2px;">Maureen Headley</div>
                                        <div style="border-top: 1.5px solid #002F6C; display: inline-block; width: 180px; padding-top: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b;">
                                            Board Registrar
                                        </div>
                                    </td>
                                </tr>
                            </table>

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
