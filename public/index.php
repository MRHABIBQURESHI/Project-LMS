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
        <div class="portal-card-grid">
            
            <div class="portal-card">
                <div>
                    <div class="portal-card-title">📝 Student Registration</div>
                    <div class="portal-card-desc">Enroll in faculties (Business, Health, Nutrition) and start coursework modules.</div>
                </div>
                <a href="register.php" class="portal-card-btn">Register now &rarr;</a>
            </div>

            <div class="portal-card">
                <div>
                    <div class="portal-card-title">🖥️ Learning Terminal</div>
                    <div class="portal-card-desc">Access modules, upload assignment files, sit timed exams, or perform grading audits.</div>
                </div>
                <a href="dashboard.php" class="portal-card-btn">Access dashboard &rarr;</a>
            </div>

            <div class="portal-card">
                <div>
                    <div class="portal-card-title">🤝 Partner Onboarding</div>
                    <div class="portal-card-desc">Apply to onboarding as local representative Batch Manager or Affiliate Consultant.</div>
                </div>
                <a href="contact.php" class="portal-card-btn">Apply/Contact &rarr;</a>
            </div>

            <div class="portal-card">
                <div>
                    <div class="portal-card-title">💵 Cash Remittance Gate</div>
                    <div class="portal-card-desc">Queue money order code confirmations (Western Union/Ria) to unlock account locks.</div>
                </div>
                <a href="remittance.php" class="portal-card-btn">Submit details &rarr;</a>
            </div>

            <div class="portal-card">
                <div>
                    <div class="portal-card-title">📄 Terms & Conditions</div>
                    <div class="portal-card-desc">Operational terms, fee structures, academic honesty policies, and institutional rules.</div>
                </div>
                <a href="terms.php" class="portal-card-btn">View terms &rarr;</a>
            </div>

            <div class="portal-card">
                <div>
                    <div class="portal-card-title">🔒 Privacy & Data Policy</div>
                    <div class="portal-card-desc">Directives regarding profiles logs, file integrity, and student records portability rights.</div>
                </div>
                <a href="privacy.php" class="portal-card-btn">View privacy policy &rarr;</a>
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
