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

                <form action="login.php" method="POST" novalidate onsubmit="return validateLogin()">
                    <div class="gov-form-group">
                        <label class="gov-label" for="email">Email</label>
                        <input class="gov-input" id="email" name="email" type="email" autocomplete="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" placeholder="Enter your email">
                    </div>

                    <div class="gov-form-group" style="margin-bottom: 12px;">
                        <label class="gov-label" for="password">Password</label>
                        <div class="pw-wrapper">
                            <input class="gov-input" id="password" name="password" type="password" autocomplete="current-password" required placeholder="Enter password">
                            <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('password', this)" aria-label="Show password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                    </div>

                    <div style="margin-bottom: 24px; text-align: right;">
                        <a href="forgot-password.php" style="font-size: 13px; color: #777777; text-decoration: none; font-weight: 500;">Forgot Password?</a>
                    </div>

                    <button type="submit" class="gov-button">Sign in</button>
                </form>

                <div style="margin-top: 30px; border-top: 1px solid #EBF3FC; padding-top: 20px; font-size: 12px; color: #777; line-height: 1.5;">
                    <strong>Demo Accounts:</strong><br>
                    - Admin: <code>mr.habib477@gmail.com</code> / <code>1234567890</code><br>
                    - Student: <code>mr.habib4777@gmail.com</code> / <code>1234567890</code>
                </div>

            </div>
        </div>

        <!-- Right Panel: Decorative Illustration (Hidden on mobile) -->
        <div class="login-right-panel">
            <img class="login-illustration-img" src="assets/images/logo.png" alt="UK London International Award Board Logo">
        </div>

    </div>

    <script>
        function togglePasswordVisibility(inputId, buttonEl) {
            var input = document.getElementById(inputId);
            if (!input) return;
            var type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            if (type === 'password') {
                buttonEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                buttonEl.setAttribute('aria-label', 'Show password');
            } else {
                buttonEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
                buttonEl.setAttribute('aria-label', 'Hide password');
            }
        }

        function validateLogin() {
            var email = document.getElementById('email').value.trim();
            var password = document.getElementById('password').value;
            if (!email || !password) {
                alert('Please enter both your email and password.');
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
