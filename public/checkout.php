<?php
require_once __DIR__ . '/db.php';

// Redirect if not registered first
if (!isset($_SESSION['temp_user_id'])) {
    header("Location: register.php");
    exit;
}

$user_id = $_SESSION['temp_user_id'];
$email = $_SESSION['temp_email'];
$password = $_SESSION['temp_password'];
$full_name = $_SESSION['temp_full_name'];

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['pay_stripe'])) {
        // Simulate successful Stripe checkout
        try {
            $pdo->beginTransaction();

            // Insert paid payment record
            $pay_stmt = $pdo->prepare("INSERT INTO payments (user_id, type, method, amount, status, transaction_ref) VALUES (?, 'tuition', 'stripe', 450.00, 'paid', ?)");
            $tx_ref = 'ST_TX_' . strtoupper(substr(md5(uniqid()), 0, 12));
            $pay_stmt->execute([$user_id, $tx_ref]);

            // Update user status to active
            $up_stmt = $pdo->prepare("UPDATE users SET account_status = 'active' WHERE id = ?");
            $up_stmt->execute([$user_id]);

            $pdo->commit();
            $success = true;

            // Clear temp registration session
            unset($_SESSION['temp_user_id']);
            unset($_SESSION['temp_email']);
            unset($_SESSION['temp_password']);
            unset($_SESSION['temp_full_name']);
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Payment error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tuition Payment Gate - UK London International Award Board</title>
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
        </div>
    </header>

    <main class="gov-width-container">
        <div class="gov-grid-row">
            <div class="gov-grid-column-two-thirds">

                <?php if ($success): ?>
                    <div class="gov-success-banner">
                        <div class="gov-success-title">Payment Successful</div>
                        <p>Your tuition fee of $450.00 has been verified. Your student account is now fully active.</p>
                    </div>

                    <h1>Your Credentials generated successfully</h1>
                    <p>Please note down your login credentials below. You can use these to access the student portal.</p>

                    <div style="background-color: #fafcff; padding: 25px; border-left: 5px solid #00703c; margin-bottom: 30px;">
                        <p style="font-size: 19px; margin-bottom: 10px;"><strong>Email:</strong> <code><?php echo htmlspecialchars($email); ?></code></p>
                        <p style="font-size: 19px; margin-bottom: 0;"><strong>Password:</strong> <code><?php echo htmlspecialchars($password); ?></code></p>
                    </div>

                    <a href="login.php" class="gov-button">Proceed to Sign In</a>

                <?php else: ?>

                    <h1>Choose Tuition Payment Method</h1>
                    <p>To complete your enrollment and activate your student portal, you must pay the program tuition fee of <strong>$450.00</strong>.</p>

                    <?php if (!empty($error)): ?>
                        <div class="gov-error-banner">
                            <div class="gov-error-title">Payment error</div>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="gov-list-group">
                        <div class="gov-list-row" style="flex-direction: column; align-items: flex-start; gap: 15px; padding: 20px 0;">
                            <div>
                                <span class="gov-list-key">Option A: Pay Full Tuition Online (Stripe Elements)</span>
                                <span class="gov-hint" style="margin-top: 5px;">Unlock your course catalog instantly. Processed securely via Stripe.</span>
                            </div>
                            <form action="checkout.php" method="POST">
                                <button type="submit" name="pay_stripe" class="gov-button">Pay $450.00 via Stripe</button>
                            </form>
                        </div>

                        <div class="gov-list-row" style="flex-direction: column; align-items: flex-start; gap: 15px; padding: 20px 0;">
                            <div>
                                <span class="gov-list-key">Option B: Manual Cash Remittance Transfer</span>
                                <span class="gov-hint" style="margin-top: 5px;">Pay via Western Union, WorldRemit, or Ria money transfer. Admin must manually verify your reference key.</span>
                            </div>
                            <a href="remittance.php" class="gov-button gov-button-secondary">Submit Remittance Reference</a>
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </main>

</body>
</html>
