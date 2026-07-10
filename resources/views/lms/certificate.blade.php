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
        /* Custom handwriting font styles for signature blocks */
        .signature-font {
            font-family: 'Brush Script MT', 'Lucida Handwriting', cursive;
            font-size: 26px;
            color: #0b2240;
            border-bottom: 1.5px solid #d4ac0d;
            padding-bottom: 5px;
            display: inline-block;
            min-width: 180px;
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
        
        <!-- Header Crest details -->
        <div style="margin-bottom:20px;">
            <img src="{{ asset('assets/images/logo.png') }}" alt="LIAB Logo" style="max-height: 70px; object-fit: contain; margin-bottom: 10px;">
            <div style="font-size: 13px; font-weight:bold; letter-spacing: 2px; color: #0d233a;">CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</div>
        </div>

        <div class="certificate-title">DIPLOMA OF ACCELLENCE</div>
        <div class="certificate-subtitle">Verifiable Board Certification Registry</div>

        <p style="font-size:16px; font-style:italic; margin-bottom: 10px; color:#555;">This is to certify that the Board Registry of Assessors has matriculated and approved</p>
        
        <div class="certificate-recipient">{{ $certificate['student_name'] }}</div>
        
        <p style="font-size:15px; font-style:italic; margin-top: 15px; color:#555;">upon the successful demonstration of required competencies, research modules, and timed assessments within the</p>
        
        <div class="certificate-faculty">FACULTY OF {{ strtoupper($certificate['faculty_name']) }}</div>

        <p style="font-size:14px; color:#666; max-width:600px; margin: 15px auto; line-height: 1.5;">This academic award is registered under the board credentials ledger. Full verification of completed module grades, coursework files, and assessments can be verified by entering the credential UID into our public verification portal.</p>

        <!-- Seal & Metadata Signatures -->
        <div class="certificate-metadata">
            <div style="text-align: center;">
                <div class="signature-font">M. C. Assessor</div>
                <div style="font-size:11px; margin-top:5px; font-weight:bold; text-transform:uppercase; color:#777;">Assessor Registrar</div>
            </div>
            
            <div>
                <div class="certificate-seal"></div>
                <div style="font-size:10px; margin-top:5px; font-weight:600; color:#bf9000;">BOARD REGISTRY SEAL</div>
            </div>

            <div style="text-align: center;">
                <div class="signature-font">Sir Richard Cole</div>
                <div style="font-size:11px; margin-top:5px; font-weight:bold; text-transform:uppercase; color:#777;">Registry Board Director</div>
            </div>
        </div>

        <!-- Verification credentials footer -->
        <div style="margin-top: 40px; display:flex; justify-content:space-between; align-items:flex-end; border-top:1.5px solid #bf9000; padding-top: 20px;">
            <div style="text-align:left; font-size:11px; color:#555; line-height: 1.45;">
                <strong>Verifiable Reference ID:</strong> <code>{{ $certificate['certificate_uid'] }}</code><br>
                <strong>Registry Award Date:</strong> {{ $certificate['issue_date'] }}<br>
                <strong>Authentication Portal:</strong> <a href="{{ route('lms.verification') }}" style="color:#0d233a; font-weight:600;">http://127.0.0.1:8000/verification.php</a>
            </div>
            
            <div style="text-align:right;">
                <!-- Simulated QR code verification boundary -->
                <div style="border: 2px solid #0d233a; padding: 4px; display:inline-block; background:#fff;">
                    <div style="width: 55px; height: 55px; background: repeating-linear-gradient(45deg, #000, #000 3px, #fff 3px, #fff 6px); opacity:0.85;"></div>
                </div>
                <div style="font-size:9px; text-transform:uppercase; margin-top: 3px; font-weight:bold; color:#777;">Verifiable Registry QR</div>
            </div>
        </div>

    </div>

</body>
</html>
