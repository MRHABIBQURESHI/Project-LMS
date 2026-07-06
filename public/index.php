<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UK London International Award Board - Portal Home</title>
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="logout.php">Sign out</a>
                <?php else: ?>
                    <a href="login.php">Sign in</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="gov-width-container">
        
        <div style="margin-bottom: 45px;">
            <h1>UK London International Award Board</h1>
            <p style="font-size: 22px; color: #555; max-width: 750px; line-height: 1.6;">
                Welcome to the student and assessor services portal. Select a service below to manage study programs, access timed evaluations, or apply for onboarding.
            </p>
        </div>

        <h2>Online Services & Portals</h2>
        <div class="gov-list-group">
            
            <div class="gov-list-row">
                <div>
                    <span class="gov-list-key">Student Intake & Registration Portal</span>
                    <span class="gov-hint" style="margin-top: 5px;">Enroll in faculties (Business, Health, Nutrition) and start coursework.</span>
                </div>
                <div class="gov-list-action">
                    <a href="register.php">Register now</a>
                </div>
            </div>

            <div class="gov-list-row">
                <div>
                    <span class="gov-list-key">Student & Assessor Dashboard</span>
                    <span class="gov-hint" style="margin-top: 5px;">Access learning modules, upload assignments, sit timed exams, or evaluate coursework.</span>
                </div>
                <div class="gov-list-action">
                    <a href="dashboard.php">Access dashboard</a>
                </div>
            </div>

            <div class="gov-list-row">
                <div>
                    <span class="gov-list-key">Contact Us & Affiliate Onboarding</span>
                    <span class="gov-hint" style="margin-top: 5px;">View office operating hours, contact numbers, or submit a consultant onboarding request.</span>
                </div>
                <div class="gov-list-action">
                    <a href="contact.php">Contact / Partner</a>
                </div>
            </div>

            <div class="gov-list-row">
                <div>
                    <span class="gov-list-key">Cash Remittance Verification Gate</span>
                    <span class="gov-hint" style="margin-top: 5px;">Submit Western Union, WorldRemit, or Ria money transfer details to unlock your profile.</span>
                </div>
                <div class="gov-list-action">
                    <a href="remittance.php">Submit payment reference</a>
                </div>
            </div>

            <div class="gov-list-row">
                <div>
                    <span class="gov-list-key">Terms & Conditions</span>
                    <span class="gov-hint" style="margin-top: 5px;">Read our operational terms, fees schedule, and institutional regulations.</span>
                </div>
                <div class="gov-list-action">
                    <a href="terms.php">View terms</a>
                </div>
            </div>

            <div class="gov-list-row">
                <div>
                    <span class="gov-list-key">Data Protection & Privacy Policy</span>
                    <span class="gov-hint" style="margin-top: 5px;">Information on how we handle personal records, WhatsApp communications, and transcripts.</span>
                </div>
                <div class="gov-list-action">
                    <a href="privacy.php">View privacy policy</a>
                </div>
            </div>

        </div>

        <div style="background-color: #fafcff; padding: 25px; border-left: 5px solid #002F6C; margin-top: 50px;">
            <h3 style="color: #002F6C; font-size: 19px; margin-bottom: 10px;">Verification Notice</h3>
            <p style="font-size: 15px; line-height: 1.5; color: #555; margin-bottom: 0;">
                All certificates issued by the UK London International Award Board are verified independently by our registry department. Check with your representative to retrieve authentication tokens.
            </p>
        </div>

    </main>

</body>
</html>
