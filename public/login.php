<?php
require_once __DIR__ . '/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'Please enter both your email address and password.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
            $user = false;
        }

        if ($user && password_verify($password, $user['password_hash'])) {
            // Password verified, start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - UK London International Award Board</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
</head>
<body class="login-body">

    <div class="login-split-container">
        
        <!-- Left Panel: Form Section (Glassmorphism layout matching reference image) -->
        <div class="login-left-panel">
            <div class="login-card">
                
                <div style="margin-bottom: 25px; text-align: left;">
                    <img src="assets/images/logo.png" alt="Logo" style="max-height: 48px; object-fit: contain;">
                </div>
                <h1 class="login-title">Login</h1>

                <?php if (!empty($error)): ?>
                    <div class="gov-error-banner" style="padding: 10px 15px; margin-bottom: 20px;">
                        <p style="font-size: 13px; margin-bottom: 0; color:#d4351c; font-weight: 600;"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="gov-error-banner" style="padding: 10px 15px; margin-bottom: 20px;">
                        <p style="font-size: 13px; margin-bottom: 0; color:#d4351c; font-weight: 600;"><?php echo htmlspecialchars($_GET['error']); ?></p>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" novalidate>
                    <div class="gov-form-group">
                        <label class="gov-label" for="email">Email</label>
                        <input class="gov-input" id="email" name="email" type="email" autocomplete="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" placeholder="Enter your email">
                    </div>

                    <div class="gov-form-group" style="margin-bottom: 12px;">
                        <label class="gov-label" for="password">Password</label>
                        <input class="gov-input" id="password" name="password" type="password" autocomplete="current-password" required placeholder="Enter password">
                    </div>

                    <div style="margin-bottom: 24px; text-align: right;">
                        <a href="forgot-password.php" style="font-size: 13px; color: #777777; text-decoration: none; font-weight: 500;">Forgot Password?</a>
                    </div>

                    <button type="submit" class="gov-button">Sign in</button>
                </form>

                <div style="margin-top: 30px; border-top: 1px solid #EBF3FC; padding-top: 20px; font-size: 12px; color: #777; line-height: 1.5;">
                    <strong>Demo Accounts:</strong><br>
                    - Admin: <code>admin@mail.com</code> / <code>1234567890</code><br>
                    - Student: <code>student@mail.com</code> / <code>1234567890</code>
                </div>

            </div>
        </div>

        <!-- Right Panel: Decorative Illustration (Hidden on mobile) -->
        <div class="login-right-panel">
            <img class="login-illustration-img" src="login_illustration.png" alt="UK London International Award Board Illustration">
        </div>

    </div>

</body>
</html>
