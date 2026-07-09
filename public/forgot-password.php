<?php
require_once __DIR__ . '/db.php';

$success = '';
$error = '';
$reset_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = 'Please enter your registered email address.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate a mock reset token
                $token = bin2hex(random_bytes(16));
                
                // Save token in session for local validation
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_token'] = $token;

                $success = 'We have sent a simulated password reset link to your email.';
                $reset_link = 'reset-password.php?email=' . urlencode($email) . '&token=' . $token;
            } else {
                $error = 'Email address not found in our system.';
            }
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
    <title>Forgot Password - UK London International Award Board</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global alert override with SweetAlert2
        window.alert = function(message) {
            var isSuccess = /success|complete|confirmed|verified|approved/i.test(message);
            Swal.fire({
                icon: isSuccess ? 'success' : 'warning',
                title: isSuccess ? 'Confirmation' : 'Registry Notice',
                text: message,
                confirmButtonColor: '#002F6C'
            });
        };
    </script>
</head>
<body class="login-body">

    <div class="login-split-container">
        
        <!-- Left Panel: Glassmorphism Card (matching login screen) -->
        <div class="login-left-panel">
            <div class="login-card">
                
                <!-- Mobile Logo Branding Header (Only visible on screens < 960px) -->
                <div class="mobile-logo-header">
                    <img src="assets/images/logo.png" alt="UK London International Award Board Logo">
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                    <a href="index.php" style="font-size:13px; font-weight:600; color:#002F6C; text-decoration:none; display:flex; align-items:center; gap:5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Back to Home
                    </a>
                </div>

                <h1 class="login-title" style="font-size: 24px;">Reset Password</h1>
                <p style="font-size: 14px; margin-bottom: 20px; color: #555;">Enter your registered email address and we will generate a password reset verification link.</p>

                <?php if (!empty($success)): ?>
                    <div class="gov-success-banner" style="padding: 12px; margin-bottom: 20px;">
                        <p style="font-size: 13px; margin-bottom: 0; color:#00703c; font-weight: 600;"><?php echo htmlspecialchars($success); ?></p>
                        <?php if (!empty($reset_link)): ?>
                            <p style="margin-top: 15px; font-size: 13px; margin-bottom: 0;">
                                [Local Test Link]:<br>
                                <a href="<?php echo htmlspecialchars($reset_link); ?>" class="gov-button" style="padding: 8px 16px; font-size: 13px; margin-top: 8px; border-radius: 4px; display: inline-block;">Reset Password Now</a>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="gov-error-banner" style="padding: 10px 15px; margin-bottom: 20px;">
                        <p style="font-size: 13px; margin-bottom: 0; color:#d4351c; font-weight: 600;"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <form action="forgot-password.php" method="POST" novalidate onsubmit="return validateForgotPassword()">
                    <div class="gov-form-group">
                        <label class="gov-label" for="email">Email Address</label>
                        <input class="gov-input" id="email" name="email" type="email" autocomplete="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" placeholder="Enter registered email">
                    </div>

                    <button type="submit" class="gov-button">Send Reset Link</button>
                </form>

                <div style="margin-top: 30px; border-top: 1px solid #EBF3FC; padding-top: 20px; text-align: center;">
                    <a href="login.php" style="font-size: 14px; font-weight: 600; color: #002F6C; text-decoration: none;">Return to Sign In</a>
                </div>

            </div>
        </div>

        <!-- Right Panel: Side Panel Illustration -->
        <div class="login-right-panel">
            <img class="login-illustration-img" src="assets/images/logo.png" alt="UK London International Award Board Logo">
        </div>

    </div>

    <script>
        function validateForgotPassword() {
            var email = document.getElementById('email').value.trim();
            if (!email) {
                alert('Please enter your registered email address.');
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Please enter a valid email address.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
