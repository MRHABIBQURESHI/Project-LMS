<?php
require_once __DIR__ . '/db.php';

$error = '';
$success = '';
$generated_code = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_affiliate'])) {
    $name = trim($_POST['rep_name']);
    $email = trim($_POST['rep_email']);
    $whatsapp = trim($_POST['rep_whatsapp']);
    $region = trim($_POST['rep_region']);
    $experience = trim($_POST['rep_experience']);
    $volume = trim($_POST['rep_volume']);

    if (empty($name) || empty($email) || empty($whatsapp) || empty($region) || empty($volume)) {
        $error = 'Please fill in all the required fields in the onboarding form.';
    } else {
        try {
            // Generate unique representative code
            $rep_code = 'REP-' . strtoupper(substr(md5(uniqid()), 0, 6));

            $contact_info = "Email: $email, WhatsApp: $whatsapp, Region: $region, Experience: $experience, Expected Volume: $volume";

            $stmt = $pdo->prepare("INSERT INTO affiliates (name, rep_code, contact_info, application_status, linked_students_count) VALUES (?, ?, ?, 'pending', 0)");
            $stmt->execute([$name, $rep_code, $contact_info]);

            $success = 'Your application for onboarding has been registered successfully. Awaiting administrator review.';
            $generated_code = $rep_code;
        } catch (PDOException $e) {
            $error = 'Database error during onboarding submission: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us & Partnerships - UK London International Award Board</title>
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
                <a href="index.php">Home</a>
                <a href="login.php">Sign in</a>
            </div>
        </div>
    </header>

    <main class="gov-width-container">
        
        <?php if (!empty($success)): ?>
            <div class="gov-success-banner">
                <div class="gov-success-title">Application Submitted</div>
                <p><?php echo htmlspecialchars($success); ?></p>
                <?php if (!empty($generated_code)): ?>
                    <p style="margin-top: 10px;">Your pending Consultant Representative Code is: <strong><code><?php echo htmlspecialchars($generated_code); ?></code></strong></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="gov-error-banner">
                <div class="gov-error-title">Submission problem</div>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <div class="gov-grid-row">
            <!-- Left Column: Contact Parameters -->
            <div class="gov-grid-column-two-thirds">
                <h1>Contact Us & Operating Parameters</h1>
                <p>Feel free to reach out to our registry and administrative office during standard operating parameters.</p>

                <div class="gov-list-group" style="margin-top: 30px;">
                    <div class="gov-list-row">
                        <span class="gov-list-key">Office Address</span>
                        <span class="gov-list-value">120 Pall Mall, London, SW1Y 5ED, United Kingdom</span>
                    </div>
                    <div class="gov-list-row">
                        <span class="gov-list-key">Administrative Working Hours</span>
                        <span class="gov-list-value">Monday to Friday (09:00 - 17:00 GMT)</span>
                    </div>
                    <div class="gov-list-row">
                        <span class="gov-list-key">Registry Support Email</span>
                        <span class="gov-list-value"><a href="mailto:registry@liab-edu.org">registry@liab-edu.org</a></span>
                    </div>
                    <div class="gov-list-row">
                        <span class="gov-list-key">Student Helpline Whatsapp</span>
                        <span class="gov-list-value">+44 7000 000 000</span>
                    </div>
                </div>

                <div style="background-color: #fafcff; padding: 25px; border-left: 5px solid #002F6C; margin-top: 30px; margin-bottom: 40px;">
                    <h3 style="color:#002F6C; margin-bottom:10px;">Verification Search Registry Support</h3>
                    <p style="font-size:16px; margin-bottom:0;">For corporate search inquiries, verification requests, or bulk student record checks, please contact our support department via email enclosing formal authorization.</p>
                </div>
            </div>

            <!-- Right Column: Affiliate Onboarding Application Form -->
            <div class="gov-grid-column-one-third" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                <h2>Affiliate Representative Onboarding</h2>
                <p style="font-size:15px; margin-bottom: 20px; color:#555;">Apply to register as an institutional consultant or affiliate representative to manage student batches.</p>

                <form action="contact.php" method="POST" style="background-color: #fff; padding: 20px; border: 2px solid #002F6C;">
                    <input type="hidden" name="apply_affiliate" value="1">
                    
                    <div class="gov-form-group" style="margin-bottom: 15px;">
                        <label class="gov-label" style="font-size:14px;" for="rep_name">Company / Name</label>
                        <input class="gov-input" id="rep_name" name="rep_name" type="text" style="font-size:14px; max-width:100%;" required>
                    </div>

                    <div class="gov-form-group" style="margin-bottom: 15px;">
                        <label class="gov-label" style="font-size:14px;" for="rep_email">Business Email</label>
                        <input class="gov-input" id="rep_email" name="rep_email" type="email" style="font-size:14px; max-width:100%;" required>
                    </div>

                    <div class="gov-form-group" style="margin-bottom: 15px;">
                        <label class="gov-label" style="font-size:14px;" for="rep_whatsapp">WhatsApp Number</label>
                        <input class="gov-input" id="rep_whatsapp" name="rep_whatsapp" type="tel" style="font-size:14px; max-width:100%;" required>
                    </div>

                    <div class="gov-form-group" style="margin-bottom: 15px;">
                        <label class="gov-label" style="font-size:14px;" for="rep_region">Operating Region</label>
                        <input class="gov-input" id="rep_region" name="rep_region" type="text" placeholder="e.g. South Asia" style="font-size:14px; max-width:100%;" required>
                    </div>

                    <div class="gov-form-group" style="margin-bottom: 15px;">
                        <label class="gov-label" style="font-size:14px;" for="rep_experience">Experience Summary</label>
                        <textarea class="gov-textarea" id="rep_experience" name="rep_experience" rows="3" style="font-size:14px; max-width:100%; font-family:inherit; border: 2px solid #0b0c0c; width:100%;"></textarea>
                    </div>

                    <div class="gov-form-group" style="margin-bottom: 20px;">
                        <label class="gov-label" style="font-size:14px;" for="rep_volume">Expected Annual Students</label>
                        <select class="gov-select" id="rep_volume" name="rep_volume" style="font-size:14px; max-width:100%;" required>
                            <option value="">-- Choose Volume --</option>
                            <option value="1-10">1 to 10 Students</option>
                            <option value="11-50">11 to 50 Students</option>
                            <option value="51-200">51 to 200 Students</option>
                            <option value="200+">More than 200 Students</option>
                        </select>
                    </div>

                    <button type="submit" class="gov-button" style="width:100%; font-size:14px; padding: 8px 10px;">Apply for Onboarding</button>
                </form>
            </div>
        </div>

    </main>

</body>
</html>
