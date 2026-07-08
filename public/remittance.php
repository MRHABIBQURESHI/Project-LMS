<?php
require_once __DIR__ . '/db.php';

$success = false;
$error = '';
$linked_email = '';
$generated_password = '';

// Determine linked user ID
$user_id = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $linked_email = $_SESSION['user_email'];
} elseif (isset($_SESSION['temp_user_id'])) {
    $user_id = $_SESSION['temp_user_id'];
    $linked_email = $_SESSION['temp_email'];
    $generated_password = $_SESSION['temp_password'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_name = trim($_POST['sender_name']);
    $transaction_ref = trim($_POST['transaction_ref']);
    $amount = floatval($_POST['amount']);
    $method = trim($_POST['method']);
    
    // If not logged in and no temp session, look up user by email input
    if ($user_id === 0) {
        $email_input = trim($_POST['email']);
        if (empty($email_input)) {
            $error = 'Please enter your registered student email address.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ? AND role = 'student'");
                $stmt->execute([$email_input]);
                $user = $stmt->fetch();
                if ($user) {
                    $user_id = $user['id'];
                    $linked_email = $user['email'];
                } else {
                    $error = 'Student email address not found in our records.';
                }
            } catch (PDOException $e) {
                $error = 'Database error during lookup: ' . $e->getMessage();
            }
        }
    }

    if (empty($sender_name) || empty($transaction_ref) || $amount <= 0 || empty($method)) {
        $error = 'Please fill in all transaction details (Sender Name, Ref Number, Amount, and Method).';
    } elseif ($user_id > 0) {
        try {
            $pdo->beginTransaction();

            // Insert pending manual remittance payment record
            // Matches ENUM('stripe', 'western_union', 'ria', 'worldremit') in schema, so map input method to these values
            $mapped_method = 'western_union';
            $method_lower = strtolower($method);
            if (strpos($method_lower, 'ria') !== false) {
                $mapped_method = 'ria';
            } elseif (strpos($method_lower, 'world') !== false) {
                $mapped_method = 'worldremit';
            } elseif (strpos($method_lower, 'stripe') !== false) {
                $mapped_method = 'stripe';
            } else {
                $mapped_method = 'western_union';
            }

            $pay_stmt = $pdo->prepare("INSERT INTO payments (user_id, type, method, amount, status, transaction_ref) VALUES (?, 'tuition', ?, ?, 'pending_manual_unlock', ?)");
            $pay_stmt->execute([$user_id, $mapped_method, $amount, $transaction_ref]);

            // Set user status to pending_manual_unlock
            $up_stmt = $pdo->prepare("UPDATE users SET account_status = 'pending_manual_unlock' WHERE id = ?");
            $up_stmt->execute([$user_id]);

            $pdo->commit();
            $success = true;

            // Clear temp registration session if they paid manually
            if (isset($_SESSION['temp_user_id'])) {
                unset($_SESSION['temp_user_id']);
                // Keep email & password in variable to display on screen, but clean sessions
                unset($_SESSION['temp_email']);
                unset($_SESSION['temp_password']);
                unset($_SESSION['temp_full_name']);
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remittance Verification Gate - UK London International Award Board</title>
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                <?php else: ?>
                    <a href="login.php">Sign in</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="gov-width-container">
        <div class="gov-grid-row">
            <div class="gov-grid-column-two-thirds" style="margin: 0 auto; float: none;">

                <?php if ($success): ?>
                    <div class="legal-card" style="margin-top: 30px; border-top: 4px solid #00703c;">
                        <div class="gov-success-banner" style="margin-bottom: 20px; border-radius: 6px;">
                            <div class="gov-success-title">Reference Registered</div>
                            <p style="font-size:13px; font-weight:600; margin-bottom:0; color:#00703c;">Your manual cash remittance details have been queued for administrative verification.</p>
                        </div>

                        <h1 style="margin-top: 0; font-size: 24px;">Awaiting Verification</h1>
                        <p style="font-size:14px; color:var(--text-secondary); line-height:1.5; margin-bottom: 20px;">The administrative assessors will verify your transaction reference code against our ledger records. Please allow up to 48 hours for verification.</p>

                        <?php if (!empty($generated_password)): ?>
                            <div style="background-color: #fafcff; padding: 20px; border-left: 4px solid #002F6C; border-radius: 6px; border: 1.5px solid #EBF3FC; border-left-width: 5px; margin-bottom: 25px;">
                                <h3 style="color:#002F6C; font-size:14px; margin-bottom:8px;">Your Student Portal Credentials</h3>
                                <p style="font-size: 13px; color:var(--text-primary); margin-bottom: 8px;"><strong>Email Address:</strong> <code><?php echo htmlspecialchars($linked_email); ?></code></p>
                                <p style="font-size: 13px; color:var(--text-primary); margin-bottom: 0;"><strong>Temporary Password:</strong> <code><?php echo htmlspecialchars($generated_password); ?></code></p>
                            </div>
                        <?php endif; ?>

                        <a href="login.php" class="gov-button" style="border-radius: 6px; padding: 12px 30px;">Go to Sign In &rarr;</a>
                    </div>

                <?php else: ?>

                    <div class="legal-card" style="margin-top: 30px;">
                        <h1 style="margin-top: 0;">Manual Cash Remittance Gate</h1>
                        <p style="font-size:14px; color:var(--text-secondary); margin-bottom: 25px; line-height:1.45;">Provide the reference key, sender name, and transfer service provider details to queue your student account for manual verification and unlock.</p>

                        <?php if (!empty($error)): ?>
                            <div class="gov-error-banner" style="padding: 10px 15px; margin-bottom: 20px; border-radius: 6px; word-break: break-word; overflow-wrap: break-word;">
                                <p style="font-size: 13px; margin-bottom: 0; color:#d4351c; font-weight: 600;"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                        <?php endif; ?>

                        <form action="remittance.php" method="POST" novalidate>
                            
                            <?php if ($user_id === 0): ?>
                                <div class="gov-form-group">
                                    <label class="gov-label" for="email">Student Registered Email</label>
                                    <span class="gov-hint">Enter the email you registered on the Student Intake portal.</span>
                                    <input class="gov-input" id="email" name="email" type="email" style="max-width: 100%; width: 100%;" required>
                                </div>
                            <?php else: ?>
                                <div style="background-color: #fafcff; padding: 12px 16px; border-left: 4px solid #002F6C; border-radius: 6px; border: 1.5px solid #EBF3FC; border-left-width: 5px; font-size:13px; margin-bottom: 20px;">
                                    Linking payment reference for student: <strong><?php echo htmlspecialchars($linked_email); ?></strong>
                                </div>
                            <?php endif; ?>

                            <div class="form-grid-row">
                                <div class="gov-form-group">
                                    <label class="gov-label" for="sender_name">Sender name</label>
                                    <span class="gov-hint">Enter the full name of the sender.</span>
                                    <input class="gov-input" id="sender_name" name="sender_name" type="text" required>
                                </div>

                                <div class="gov-form-group">
                                    <label class="gov-label" for="amount">Remittance Amount ($)</label>
                                    <span class="gov-hint">The default registration fee is $450.00.</span>
                                    <input class="gov-input" id="amount" name="amount" type="number" step="0.01" min="1.00" value="450.00" required>
                                </div>
                            </div>

                            <div class="form-grid-row">
                                <div class="gov-form-group">
                                    <label class="gov-label" for="method">Transfer Service Provider</label>
                                    <span class="gov-hint">Select the cash remittance company.</span>
                                    <select class="gov-select" id="method" name="method" required>
                                        <option value="">-- Choose Provider --</option>
                                        <option value="western_union">Western Union</option>
                                        <option value="ria">Ria Money Transfer</option>
                                        <option value="worldremit">WorldRemit</option>
                                    </select>
                                </div>

                                <div class="gov-form-group">
                                    <label class="gov-label" for="transaction_ref">Reference ID (MTCN / PIN)</label>
                                    <span class="gov-hint">Enter the reference code from the receipt.</span>
                                    <input class="gov-input" id="transaction_ref" name="transaction_ref" type="text" required>
                                </div>
                            </div>

                            <button type="submit" class="gov-button" style="width: 100%; border-radius: 6px; padding: 13px; font-size:15px; margin-top: 15px;">Submit Remittance Codes &rarr;</button>
                        </form>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </main>

</body>
</html>
