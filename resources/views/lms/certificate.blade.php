<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Award Credential - {{ $certificate['certificate_uid'] }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        body {
            background-color: #fafbfd;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: "Helvetica Neue", Arial, sans-serif;
        }
        .print-btn-section {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            width: 100%;
            max-width: 900px;
            justify-content: space-between;
            align-items: center;
        }
        .certificate-container {
            background: #ffffff;
            color: #1e293b;
            width: 100%;
            max-width: 900px;
            margin: 30px auto;
            padding: 60px 80px;
            border: 3px solid #002F6C;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            position: relative;
            text-align: center;
            box-sizing: border-box;
        }
        .logo-container {
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .logo {
            height: 65px;
            display: block;
            margin: 0 auto 10px auto;
        }
        .org-name-primary {
            font-size: 26px;
            font-weight: 800;
            color: #002F6C;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .org-name-secondary {
            font-size: 21px;
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
            font-size: 36px;
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
            font-size: 26px;
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
            font-size: 14px;
            font-style: italic;
            color: #64748b;
            margin-bottom: 50px;
        }
        .certificate-footer-table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }
        .certificate-footer-table td {
            padding: 0;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-btn-section {
                display: none !important;
            }
            .certificate-container {
                margin: 0 !important;
                box-shadow: none !important;
                border: 3px solid #002F6C !important;
                width: 100% !important;
                max-width: 100% !important;
                height: 100vh !important;
                padding: 60px 80px !important;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-section">
        <a href="{{ route('lms.home') }}" style="text-decoration:none; font-size:14px; font-weight:600; color:#002F6C;">&larr; Back to Portal</a>
        <div>
            <button onclick="window.print();" class="gov-button" style="border-radius:6px; padding:8px 20px; background-color:#00703c;">Print / Export PDF</button>
        </div>
    </div>

    <!-- CERTIFICATE CONTAINER -->
    <div class="certificate-container">
        
        <div class="logo-container">
            <img src="{{ asset('assets/images/logo.png') }}" class="logo" alt="CPD UK Logo">
            <div class="org-name-primary">CPD UK LONDON</div>
            <div class="org-name-secondary">INTERNATIONAL CERTIFICATION AWARD BOARD</div>
        </div>

        <div class="cert-to-officially">THIS IS TO OFFICIALLY CERTIFY AND RECORD THAT</div>
        <div class="recipient-name">{{ $certificate['student_name'] }}</div>

        <div class="cert-reason">
            has successfully completed all institutional assessment portfolios and crossed the required proficiency threshold within the specialized domain of:
        </div>
        <div class="course-title">Faculty of {{ $certificate['faculty_name'] }}</div>

        <div class="award-rights">
            and is hereby awarded this formal validation credential with all accompanying rights, honors, and professional recognitions pertaining to this registry board.
        </div>

        <div class="verified-date">
            Verified and signed on this day: {{ date('d F Y', strtotime($certificate['issue_date'])) }}
        </div>

        <table class="certificate-footer-table">
            <tr>
                <td style="width: 35%; text-align: left; font-size: 10px; color: #64748b; line-height: 1.4; vertical-align: bottom;">
                    <strong style="text-transform: uppercase;">REGISTRY DATA VALIDATION:</strong><br>
                    Registry Serial ID: {{ $certificate['certificate_uid'] }}
                </td>
                <td style="width: 30%; text-align: center; font-size: 11px; color: #64748b; vertical-align: bottom;">
                    Official Portal Registry
                </td>
                <td style="width: 35%; text-align: right; vertical-align: bottom;">
                    <div style="font-family: 'Brush Script MT', 'Lucida Handwriting', cursive, Georgia; font-size: 26px; color: #002F6C; margin-bottom: 2px;">Maureen Headley</div>
                    <div style="border-top: 1.5px solid #002F6C; display: inline-block; width: 180px; padding-top: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b;">
                        Board Registrar
                    </div>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>
