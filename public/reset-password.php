<?php
require_once __DIR__ . '/db.php';

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

$valid_request = false;
$error = '';
$success = false;

// Validate the token and email in session
if (isset($_SESSION['reset_email']) && isset($_SESSION['reset_token'])) {
    if ($_SESSION['reset_email'] === $email && $_SESSION['reset_token'] === $token) {
        $valid_request = true;
    }
}

if (!$valid_request) {
    $error = 'Invalid or expired password reset link. Please request a new link.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_request) {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($password) || empty($confirm_password)) {
        $error = 'Please fill in both password fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match. Re-type the password.';
    } else {
        try {
            // Hash new password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Update database user record
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            $stmt->execute([$hashed, $email]);

            // Clear reset token session details
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_token']);

            $success = true;
        } catch (PDOException $e) {
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
    <title>Reset Password - UK London International Award Board</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
</head>
<body class="login-body">

    <div class="login-split-container">
        
        <!-- Left Panel: Glassmorphism Card (matching login screen) -->
        <div class="login-left-panel">
            <div class="login-card">
                
                <div style="margin-bottom: 25px; text-align: left;">
                    <img src="assets/images/logo.png" alt="Logo" style="max-height: 48px; object-fit: contain;">
                </div>
                <h1 class="login-title" style="font-size: 24px;">New Password</h1>

                <?php if ($success): ?>
                    <div class="gov-success-banner" style="padding: 12px; margin-bottom: 25px;">
                        <div class="gov-success-title">Password Updated</div>
                        <p style="font-size: 13px; margin-bottom: 0;">Your password has been successfully reset. Proceed to login.</p>
                    </div>

                    <a href="login.php" class="gov-button" style="width: 100%;">Go to Sign In</a>

                <?php else: ?>

                    <p style="font-size: 14px; margin-bottom: 20px; color: #555;">Choose a new secure password of at least 6 characters.</p>

                    <?php if (!empty($error)): ?>
                        <div class="gov-error-banner" style="padding: 10px 15px; margin-bottom: 20px;">
                            <p style="font-size: 13px; margin-bottom: 0; color:#d4351c; font-weight: 600;"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($valid_request): ?>
                        <form action="reset-password.php?email=<?php echo urlencode($email); ?>&token=<?php echo htmlspecialchars($token); ?>" method="POST" novalidate>
                            <div class="gov-form-group">
                                <label class="gov-label" for="password">New Password</label>
                                <input class="gov-input" id="password" name="password" type="password" required placeholder="Min 6 characters">
                            </div>

                            <div class="gov-form-group">
                                <label class="gov-label" for="confirm_password">Confirm New Password</label>
                                <input class="gov-input" id="confirm_password" name="confirm_password" type="password" required placeholder="Verify new password">
                            </div>

                            <button type="submit" class="gov-button">Update Password</button>
                        </form>
                    <?php else: ?>
                        <div style="margin-top: 30px; text-align: center;">
                            <a href="forgot-password.php" class="gov-button" style="width: 100%;">Request New Reset Link</a>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>

        <!-- Right Panel: Side Panel Illustration -->
        <div class="login-right-panel">
            <img class="login-illustration-img" src="login_illustration.png" alt="UK London International Award Board Illustration">
        </div>

    </div>

</body>
</html>
