<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Restricted - UK London International Award Board</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
</head>
<body>

    <header class="gov-header">
        <div class="gov-header-container">
            <a href="index.php" style="display:flex; align-items:center; gap:12px; text-decoration:none;">
                <img src="assets/images/logo.png" alt="Logo" style="max-height: 40px; object-fit: contain;">
                <span class="gov-header-title">UK London International Award Board</span>
            </a>
            <div class="gov-header-nav">
                <a href="login.php">Sign in</a>
            </div>
        </div>
    </header>

    <main class="gov-width-container">
        <div class="gov-grid-row">
            <div class="gov-grid-column-two-thirds">
                
                <div class="gov-error-banner" style="border-color: #002F6C; background-color: #fafcff; margin-top: 40px;">
                    <div class="gov-error-title" style="color: #002F6C;">Registration Restricted</div>
                    <p>Public self-registration is disabled for the UK London International Award Board LMS.</p>
                </div>

                <h1>Institutional Onboarding Protocol</h1>
                <p>Student profile records and portal login credentials are created and managed exclusively by our registry assessors.</p>
                <p>If you are an enrolled student and have not received your account username and password, please contact your representative consultant or reach out to the helpline at <strong>registry@liab-edu.org</strong>.</p>

                <div style="margin-top: 30px;">
                    <a href="login.php" class="gov-button">Return to Sign In</a>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
