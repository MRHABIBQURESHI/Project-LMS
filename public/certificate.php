<?php
require_once __DIR__ . '/db.php';

$uid = trim($_GET['uid'] ?? '');
$certificate = null;
$error = '';

if (empty($uid)) {
    $error = 'No Certificate reference UID provided.';
} else {
    try {
        $stmt = $pdo->prepare("
            SELECT cert.*, u.full_name as student_name, f.name as faculty_name, f.id as faculty_id
            FROM certificates cert
            JOIN users u ON cert.user_id = u.id
            LEFT JOIN faculties f ON u.faculty_id = f.id
            WHERE cert.certificate_uid = ?
        ");
        $stmt->execute([$uid]);
        $certificate = $stmt->fetch();
        
        if (!$certificate) {
            $error = 'Invalid certificate validation credentials token.';
        } else {
            // Check if certificate PDF mockup folder exists, and generate a placeholder file if not
            $cert_dir = __DIR__ . '/uploads/certificates';
            if (!file_exists($cert_dir)) {
                mkdir($cert_dir, 0777, true);
            }
            $pdf_file = $cert_dir . '/cert_' . $certificate['user_id'] . '_' . $certificate['faculty_id'] . '.pdf';
            if (!file_exists($pdf_file)) {
                // Write simple PDF-like contents so physical file exists for student downloads
                $placeholder_content = "%PDF-1.4\n%...\nVerifiable Certificate for " . $certificate['student_name'] . "\nUID: " . $certificate['certificate_uid'] . "\nAward: Faculty of " . $certificate['faculty_name'] . "\nDate: " . $certificate['issue_date'];
                file_put_contents($pdf_file, $placeholder_content);
            }
        }
    } catch (PDOException $e) {
        $error = 'Database Registry connection error: ' . $e->getMessage();
    }
}

if ($error): ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Credential Error - UK London International Award Board</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body style="background:var(--bg-secondary); display:flex; align-items:center; justify-content:center; height:100vh; padding-bottom:0;">
        <div class="db-card" style="max-width:500px; padding:30px; text-align:center; box-shadow:var(--shadow-modal);">
            <h1 style="color:#d4351c; font-size:24px;">Verification Failed</h1>
            <p><?php echo htmlspecialchars($error); ?></p>
            <a href="index.php" class="gov-button" style="margin-top:15px; border-radius:6px;">Return to Home Portal</a>
        </div>
    </body>
    </html>
<?php 
exit;
endif;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Award Credential - <?php echo htmlspecialchars($certificate['certificate_uid']); ?></title>
    <link rel="stylesheet" href="style.css">
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
        <a href="index.php" style="text-decoration:none; font-size:14px; font-weight:600; color:#002F6C;">&larr; Back to Portal</a>
        <div>
            <button onclick="window.print();" class="gov-button" style="border-radius:6px; padding:8px 20px; background-color:#00703c;">Print / Export PDF</button>
        </div>
    </div>

    <!-- CERTIFICATE CONTAINER -->
    <div class="certificate-container">
        
        <!-- Header Crest details -->
        <div style="margin-bottom:20px;">
            <img src="assets/images/logo.png" alt="LIAB Logo" style="max-height: 70px; object-fit: contain; margin-bottom: 10px;">
            <div style="font-size: 13px; font-weight:bold; letter-spacing: 2px; color: #0d233a;">UK LONDON INTERNATIONAL AWARD BOARD</div>
        </div>

        <div class="certificate-title">DIPLOMA OF ACADEMIC EXCELLENCE</div>
        <div class="certificate-subtitle">Verifiable Board Certification Registry</div>

        <p style="font-size:16px; font-style:italic; margin-bottom: 10px; color:#555;">This is to certify that the Board Registry of Assessors has matriculated and approved</p>
        
        <div class="certificate-recipient"><?php echo htmlspecialchars($certificate['student_name']); ?></div>
        
        <p style="font-size:15px; font-style:italic; margin-top: 15px; color:#555;">upon the successful demonstration of required competencies, research modules, andtimed assessments within the</p>
        
        <div class="certificate-faculty">FACULTY OF <?php echo strtoupper($certificate['faculty_name']); ?></div>

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
                <strong>Verifiable Reference ID:</strong> <code><?php echo htmlspecialchars($certificate['certificate_uid']); ?></code><br>
                <strong>Registry Award Date:</strong> <?php echo htmlspecialchars($certificate['issue_date']); ?><br>
                <strong>Authentication Portal:</strong> <a href="verification.php" style="color:#0d233a; font-weight:600;">http://127.0.0.1:8000/verification.php</a>
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
