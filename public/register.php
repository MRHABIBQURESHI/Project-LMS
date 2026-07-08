<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$success = false;
$error = '';
$generated_password = '';
$email = '';

// Load faculties
try {
    $facs = $pdo->query("SELECT * FROM faculties")->fetchAll();
} catch (PDOException $e) {
    $facs = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $email_input = trim($_POST['email'] ?? '');
    $whatsapp_number = trim($_POST['whatsapp_number'] ?? '');
    $faculty_id = intval($_POST['faculty_id'] ?? 0);
    $rep_code = trim($_POST['rep_code'] ?? '');
    $payment_choice = trim($_POST['payment_choice'] ?? ''); // 'upfront', 'installment', 'cash'

    if (empty($full_name) || empty($dob) || empty($email_input) || empty($whatsapp_number) || empty($faculty_id) || empty($payment_choice)) {
        $error = 'Please fill in all the required registration fields.';
    } else {
        try {
            // Check if email already exists
            $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $chk->execute([$email_input]);
            if ($chk->fetch()) {
                $error = 'This email address is already registered.';
            } else {
                // Generate random password
                $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#%';
                $generated_password = substr(str_shuffle($chars), 0, 10);
                $hash = password_hash($generated_password, PASSWORD_DEFAULT);
                
                $status = ($payment_choice === 'cash') ? 'pending_manual_unlock' : 'active';
                
                $pdo->beginTransaction();
                
                // Insert student user
                $stmt = $pdo->prepare("INSERT INTO users (full_name, dob, email, whatsapp_number, password_hash, role, faculty_id, rep_code, account_status) VALUES (?, ?, ?, ?, ?, 'student', ?, ?, ?)");
                $stmt->execute([$full_name, $dob, $email_input, $whatsapp_number, $hash, $faculty_id, $rep_code ? $rep_code : null, $status]);
                $user_id = $pdo->lastInsertId();
                
                if ($payment_choice === 'cash') {
                    // Manual cash remittance choice: redirect to remittance submission
                    $_SESSION['temp_user_id'] = $user_id;
                    $_SESSION['temp_email'] = $email_input;
                    $_SESSION['temp_password'] = $generated_password;
                    $_SESSION['temp_full_name'] = $full_name;
                    
                    $pdo->commit();
                    header("Location: remittance.php");
                    exit;
                } else {
                    // Stripe payment choice
                    $amount = ($payment_choice === 'upfront') ? 2249.00 : 749.00;
                    $installment_number = ($payment_choice === 'installment') ? 1 : null;
                    
                    // Card details
                    $card_holder = trim($_POST['card_holder'] ?? '');
                    $card_number = trim($_POST['card_number'] ?? '');
                    $card_exp = trim($_POST['card_exp'] ?? '');
                    $card_cvc = trim($_POST['card_cvc'] ?? '');
                    
                    // Parse expiration exp_month and exp_year
                    $exp_parts = explode('/', str_replace(' ', '', $card_exp));
                    $exp_month = intval($exp_parts[0] ?? 0);
                    $exp_year = intval('20' . ($exp_parts[1] ?? 0));
                    
                    // Stripe Api Integration
                    $stripe_secret = getenv('STRIPE_SECRET_KEY');
                    if (empty($stripe_secret)) {
                        throw new Exception("Stripe configurations missing in environment setup.");
                    }
                    
                    \Stripe\Stripe::setApiKey($stripe_secret);
                    
                    // Create payment method
                    $paymentMethod = \Stripe\PaymentMethod::create([
                        'type' => 'card',
                        'card' => [
                            'number' => str_replace(' ', '', $card_number),
                            'exp_month' => $exp_month,
                            'exp_year' => $exp_year,
                            'cvc' => $card_cvc,
                        ],
                    ]);
                    
                    // Create and Confirm PaymentIntent
                    $intent = \Stripe\PaymentIntent::create([
                        'amount' => intval($amount * 100), // Stripe cents/pence
                        'currency' => 'gbp',
                        'payment_method' => $paymentMethod->id,
                        'confirm' => true,
                        'automatic_payment_methods' => [
                            'enabled' => true,
                            'allow_redirects' => 'never'
                        ]
                    ]);
                    
                    if ($intent->status === 'succeeded') {
                        $tx_ref = $intent->id;
                    } else {
                        throw new Exception("Stripe Payment incomplete: Status is " . $intent->status);
                    }
                    
                    $pay_stmt = $pdo->prepare("INSERT INTO payments (user_id, type, method, amount, installment_number, status, transaction_ref) VALUES (?, 'tuition', 'stripe', ?, ?, 'paid', ?)");
                    $pay_stmt->execute([$user_id, $amount, $installment_number, $tx_ref]);
                    
                    // Increment linked student count for affiliate representative if matching
                    if (!empty($rep_code)) {
                        $aff_stmt = $pdo->prepare("UPDATE affiliates SET linked_students_count = linked_students_count + 1 WHERE rep_code = ?");
                        $aff_stmt->execute([$rep_code]);
                    }
                    
                    $pdo->commit();
                    
                    // Send automated email simulation
                    $email_subject = "Welcome to UK London International Award Board - Portal Access";
                    $email_body = "Dear $full_name,\n\nYour enrollment is confirmed! Your student account is now active.\n\nLogin Credentials:\nURL: http://127.0.0.1:8000/login.php\nEmail: $email_input\nPassword: $generated_password\n\nSincerely,\nUK London International Award Board Assessor Services";
                    
                    // Log email to file
                    $log_dir = __DIR__ . '/uploads';
                    if (!file_exists($log_dir)) {
                        mkdir($log_dir, 0777, true);
                    }
                    file_put_contents($log_dir . '/emails.txt', "========================================\nTo: $email_input\nSubject: $email_subject\nDate: " . date('Y-m-d H:i:s') . "\nBody:\n$email_body\n========================================\n\n", FILE_APPEND);
                    
                    // Try to send via Mailtrap API and fallback to PHP mail
                    require_once __DIR__ . '/mail_helper.php';
                    sendMailtrapEmail($email_input, $email_subject, $email_body);
                    @mail($email_input, $email_subject, $email_body, "From: registry@liab-edu.org");
                    
                    $success = true;
                    $email = $email_input;
                }
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Stripe Payment / Registration Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Intake Registry - UK London International Award Board</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <style>
        /* Enable scrollable split-screen wrapper for register to prevent vertical cutoff */
        .login-split-container {
            height: auto !important;
            min-height: 100vh;
            overflow: visible !important;
        }
        .login-left-panel {
            min-height: 100vh;
            height: auto !important;
            padding: 20px 15px !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-right-panel {
            min-height: 100vh;
            height: auto !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 580px !important;
            padding: 20px 25px !important;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 47, 108, 0.08);
            margin: 10px auto;
        }
        .progress-bar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            position: relative;
        }
        .progress-bar-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--border-input);
            z-index: 1;
            transform: translateY(-50%);
        }
        .progress-bar-line-active {
            position: absolute;
            top: 50%;
            left: 0;
            width: 0%;
            height: 2px;
            background-color: var(--text-heading);
            z-index: 2;
            transform: translateY(-50%);
            transition: width 0.3s ease;
        }
        .progress-step {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background-color: var(--bg-secondary);
            border: 2px solid var(--border-input);
            color: var(--text-hint);
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
            transition: all 0.3s ease;
            position: relative;
        }
        .progress-step.active {
            background-color: #002F6C;
            border-color: #002F6C;
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(0, 47, 108, 0.15);
        }
        .progress-step.completed {
            background-color: #00703c;
            border-color: #00703c;
            color: #ffffff;
        }
        
        /* Interactive Compact Virtual Credit Card Graphic */
        .virtual-card-wrapper {
            perspective: 1000px;
            margin: 15px 0;
        }
        .virtual-card {
            width: 100%;
            max-width: 290px;
            height: 165px;
            background: linear-gradient(135deg, #0e274c 0%, #001736 100%);
            border-radius: 10px;
            padding: 16px 20px;
            color: white;
            font-family: 'Courier New', Courier, monospace;
            position: relative;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            margin: 0 auto;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .virtual-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .v-chip {
            width: 32px;
            height: 24px;
            background: linear-gradient(135deg, #f3d078 0%, #d4ac0d 100%);
            border-radius: 4px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .v-number {
            font-size: 16px;
            letter-spacing: 1.5px;
            margin-bottom: 18px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.6);
            color: #f8fafc;
        }
        .v-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .v-label-text {
            font-size: 7px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .v-value-text {
            font-size: 10px;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 1px;
            max-width: 140px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .v-brand {
            font-size: 14px;
            font-weight: 800;
            font-style: italic;
            color: rgba(255,255,255,0.9);
        }
        
        .btn-flex-row {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            border-top: 1.5px solid #EBF3FC;
            padding-top: 15px;
        }
        
        /* Interactive Input Enhancements and Height reduction */
        .gov-input:focus {
            box-shadow: 0 0 0 3px rgba(0, 47, 108, 0.1);
        }
        
        .step-heading {
            font-size: 13px;
            color: var(--text-hint);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .form-grid-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 12px;
        }
        .form-grid-row > .gov-form-group {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            margin-bottom: 0 !important;
            height: 100%;
        }
        .form-grid-row > .gov-form-group > .gov-hint {
            flex-grow: 1;
            margin-bottom: 4px !important;
        }
        .login-card .gov-input, .login-card .gov-select {
            max-width: 100% !important;
            width: 100% !important;
            height: 34px !important;
            padding: 6px 10px !important;
            font-size: 13px !important;
            margin-top: auto;
        }
        .login-card .gov-label {
            font-size: 12.5px !important;
            margin-bottom: 2px !important;
            font-weight: 600;
        }
        .login-card .gov-hint {
            font-size: 10px !important;
            margin-bottom: 3px !important;
            line-height: 1.25;
        }
        .login-card .gov-form-group {
            margin-bottom: 10px !important;
        }
        .gov-error-banner {
            word-break: break-word !important;
            overflow-wrap: break-word !important;
        }
        @media (max-width: 580px) {
            .form-grid-row {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .form-grid-row > .gov-form-group {
                margin-bottom: 10px !important;
            }
        }
    </style>
</head>
<body class="login-body">

    <div class="login-split-container">
        
        <!-- Left Panel: Multi-step registration form -->
        <div class="login-left-panel" style="padding: 20px;">
            <div class="login-card">
                <!-- Mobile Logo Branding Header (Only visible on screens < 960px) -->
                <div class="mobile-logo-header">
                    <img src="assets/images/logo.png" alt="UK London International Award Board Logo">
                </div>
                
                <div style="margin-bottom: 20px; text-align: right;">
                    <a href="login.php" style="font-size: 13px; font-weight: 600; color: #002F6C; text-decoration: none;">Sign In Instead &rarr;</a>
                </div>

                <?php if ($success): ?>
                    <h1 class="login-title" style="margin-bottom: 10px; color: #00703c;">Enrollment Complete</h1>
                    <div class="gov-success-banner" style="margin-bottom: 25px; border-radius: 6px;">
                        <p style="font-size: 13px; font-weight:600; margin-bottom: 0; color:#00703c;">Thank you! Your tuition fee payment has been verified. Your student account is now fully active.</p>
                    </div>

                    <p style="font-size:14px; color:var(--text-secondary); margin-bottom: 20px; line-height: 1.5;">An automated credentials email was sent to your inbox. Write down or capture your temporary access keys below:</p>

                    <div style="background-color: #fafcff; padding: 20px; border-left: 4px solid #00703c; border-radius: 6px; margin-bottom: 25px; border: 1.5px solid #EBF3FC; border-left-width: 5px;">
                        <p style="font-size: 15px; margin-bottom: 12px; color:#002F6C;"><strong>Student Portal Link:</strong> <a href="login.php" style="font-weight:600; text-decoration: underline;">Sign In Page</a></p>
                        <p style="font-size: 14px; margin-bottom: 8px; color:var(--text-primary);"><strong>Email Address:</strong> <code><?php echo htmlspecialchars($email); ?></code></p>
                        <p style="font-size: 14px; margin-bottom: 0; color:var(--text-primary);"><strong>Temporary Password:</strong> <code><?php echo htmlspecialchars($generated_password); ?></code></p>
                    </div>

                    <p style="color: var(--text-hint); font-size:11px; margin-bottom: 25px; line-height:1.4;"><em>Note: You will be prompted to update this temporary password upon your first access into your dashboard settings.</em></p>
                    
                    <a href="login.php" class="gov-button" style="display: block; width: 100%; text-decoration: none; border-radius: 6px; text-align:center;">Proceed to Portal Access &rarr;</a>

                <?php else: ?>

                    <h1 class="login-title" style="margin-bottom: 5px;">Student Registration</h1>
                    <p style="font-size:13px; color: var(--text-secondary); margin-bottom: 25px; line-height: 1.45;">Register your academic profile to matriculate. Complete your details and secure your enrollment.</p>

                    <!-- Step Tracker Badge -->
                    <div class="progress-bar-container">
                        <div class="progress-bar-line"></div>
                        <div class="progress-bar-line-active" id="barLine"></div>
                        <div class="progress-step active" id="step1Dot">1</div>
                        <div class="progress-step" id="step2Dot">2</div>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="gov-error-banner" style="padding: 10px 15px; margin-bottom: 20px; border-radius: 6px;">
                            <p style="font-size: 13px; margin-bottom: 0; color:#d4351c; font-weight: 600;"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    <?php endif; ?>

                    <form id="regForm" action="register.php" method="POST" novalidate onsubmit="return validateForm()">
                        
                        <!-- ======================================================== -->
                        <!-- STEP 1: PERSONAL ACADEMIC PROFILE                        -->
                        <!-- ======================================================== -->
                        <div id="step_1_section">
                            <div class="step-heading">Step 1: Personal & Faculty Info</div>
                            
                            <div class="form-grid-row">
                                <div class="gov-form-group">
                                    <label class="gov-label" for="full_name">Full Name (Legal Identity)</label>
                                    <span class="gov-hint" style="font-size: 11px;">Enter name exactly as it appears on formal passports or IDs.</span>
                                    <input class="gov-input" id="full_name" name="full_name" type="text" placeholder="e.g. Habib Qureshi" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                                </div>

                                <div class="gov-form-group">
                                    <label class="gov-label" for="dob">Date of Birth</label>
                                    <span class="gov-hint" style="font-size: 11px;">For award registry validation verification.</span>
                                    <input class="gov-input" id="dob" name="dob" type="date" required value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-grid-row">
                                <div class="gov-form-group">
                                    <label class="gov-label" for="email">Student Primary Email</label>
                                    <span class="gov-hint" style="font-size: 11px;">Credentials and transcript access codes will be sent here.</span>
                                    <input class="gov-input" id="email" name="email" type="email" placeholder="student@example.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>

                                <div class="gov-form-group">
                                    <label class="gov-label" for="whatsapp_number">WhatsApp Contact Number</label>
                                    <span class="gov-hint" style="font-size: 11px;">Include international code (e.g. +447000000000) for tutor comms.</span>
                                    <input class="gov-input" id="whatsapp_number" name="whatsapp_number" type="tel" placeholder="+44 7000 000000" required value="<?php echo isset($_POST['whatsapp_number']) ? htmlspecialchars($_POST['whatsapp_number']) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-grid-row">
                                <div class="gov-form-group">
                                    <label class="gov-label" for="faculty_id">Academic Program Faculty</label>
                                    <span class="gov-hint" style="font-size: 11px;">Loads dynamic Course Modules 3 & 4.</span>
                                    <select class="gov-select" id="faculty_id" name="faculty_id" required>
                                        <option value="">-- Choose Course Focus --</option>
                                        <?php foreach ($facs as $f): ?>
                                            <option value="<?php echo $f['id']; ?>" <?php echo (isset($_POST['faculty_id']) && $_POST['faculty_id'] == $f['id']) ? 'selected' : ''; ?>>Faculty of <?php echo htmlspecialchars($f['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="gov-form-group">
                                    <label class="gov-label" for="rep_code">Consultant / Affiliate Code (Optional)</label>
                                    <span class="gov-hint" style="font-size: 11px;">Enter your representative's registration token, if applicable.</span>
                                    <input class="gov-input" id="rep_code" name="rep_code" type="text" placeholder="e.g. REP-DEMO-01" value="<?php echo isset($_POST['rep_code']) ? htmlspecialchars($_POST['rep_code']) : ''; ?>">
                                </div>
                            </div>

                            <div style="margin-top: 30px; border-top: 1.5px solid #EBF3FC; padding-top: 25px;">
                                <button type="button" class="gov-button" style="width:100%; border-radius:6px; padding: 13px;" onclick="nextStep()">Next Step &rarr;</button>
                            </div>
                        </div>

                        <!-- ======================================================== -->
                        <!-- STEP 2: ACADEMIC PROGRAM TUITION FEE                    -->
                        <!-- ======================================================== -->
                        <div id="step_2_section" style="display: none;">
                            <div class="step-heading">Step 2: Tuition Fee Payment Option</div>
                            
                            <div class="payment-method-tabs">
                                <div class="payment-tab-btn active" id="tab_upfront" onclick="selectPaymentChoice('upfront')" style="padding: 10px; font-size:12px;">
                                    Pay Upfront<br><span style="font-size:10px; font-weight:normal;">£2,249 (Save)</span>
                                </div>
                                <div class="payment-tab-btn" id="tab_installment" onclick="selectPaymentChoice('installment')" style="padding: 10px; font-size:12px;">
                                    3 Installments<br><span style="font-size:10px; font-weight:normal;">£749/mo</span>
                                </div>
                                <div class="payment-tab-btn" id="tab_cash" onclick="selectPaymentChoice('cash')" style="padding: 10px; font-size:12px;">
                                    Remittance Gate<br><span style="font-size:10px; font-weight:normal;">WU / Ria / Receipt</span>
                                </div>
                            </div>

                            <input type="hidden" name="payment_choice" id="payment_choice" value="upfront">

                            <!-- Interactive Credit Card Preview -->
                            <div id="cardGraphicWrapper" class="virtual-card-wrapper">
                                <div class="virtual-card">
                                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                        <div class="v-chip"></div>
                                        <div class="v-brand" id="v_brand_display">VISA</div>
                                    </div>
                                    <div class="v-number" id="v_number_display">•••• •••• •••• ••••</div>
                                    <div class="v-details">
                                        <div>
                                            <div class="v-label-text">Cardholder</div>
                                            <div class="v-value-text" id="v_name_display">YOUR NAME</div>
                                        </div>
                                        <div>
                                            <div class="v-label-text" style="text-align:right;">Expires</div>
                                            <div class="v-value-text" id="v_exp_display" style="text-align:right;">MM/YY</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CARD FIELDS FOR STRIPE -->
                            <div id="stripePaymentArea">
                                <div class="form-grid-row">
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="card_holder" style="font-size:12px; margin-bottom:4px;">Cardholder Name</label>
                                        <input class="gov-input" id="card_holder" name="card_holder" type="text" placeholder="John Doe">
                                    </div>
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="card_number" style="font-size:12px; margin-bottom:4px;">Card Number</label>
                                        <input class="gov-input" id="card_number" name="card_number" type="text" placeholder="4242 4242 4242 4242" maxlength="19">
                                    </div>
                                </div>
                                <div class="form-grid-row">
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="card_exp" style="font-size:12px; margin-bottom:4px;">Expiry Date</label>
                                        <input class="gov-input" id="card_exp" name="card_exp" type="text" placeholder="MM / YY" maxlength="7">
                                    </div>
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="card_cvc" style="font-size:12px; margin-bottom:4px;">CVC</label>
                                        <input class="gov-input" id="card_cvc" name="card_cvc" type="text" placeholder="123" maxlength="4">
                                    </div>
                                </div>
                                <span class="gov-hint" style="font-size: 11px; margin-bottom: 20px;">🔒 Stripe Elements Secure Transaction. Standard TLS 1.3 encryption.</span>
                            </div>

                            <!-- REMITTANCE CASH NOTIFICATION -->
                            <div id="cashPaymentArea" style="display: none; background-color: #fafcff; padding: 20px; border-left: 4px solid #002F6C; border-radius: 6px; margin-bottom: 20px; border: 1.5px solid #EBF3FC; border-left-width: 5px;">
                                <h3 style="color:#002F6C; font-size:14px; margin-bottom:8px;">Remittance Validation Instructions</h3>
                                <p style="font-size:12px; color:var(--text-secondary); margin-bottom: 0; line-height: 1.5;">Your enrollment profile will be created in a <strong>Pending Lock</strong> state. You will be routed immediately to submit your Western Union, WorldRemit, or Ria money order transaction credentials reference for assessors clearance.</p>
                            </div>

                            <div class="btn-flex-row">
                                <button type="button" class="gov-button gov-button-secondary" style="flex: 1; border-radius:6px; padding: 13px;" onclick="prevStep()">&larr; Back</button>
                                <button type="submit" class="gov-button" id="submitBtn" style="flex: 2; border-radius:6px; padding: 13px; background-color:#00703c;">Complete & Pay £2,249.00</button>
                            </div>
                        </div>

                    </form>

                <?php endif; ?>

            </div>
        </div>

        <!-- Right Panel: Decorative Illustration (Hidden on mobile) -->
        <div class="login-right-panel">
            <img class="login-illustration-img" src="assets/images/logo.png" alt="UK London International Award Board Logo">
        </div>

    </div>

    <script>
        // Multi-step form tracking variables
        var currentStep = 1;

        function updateProgress() {
            var barLine = document.getElementById('barLine');
            var step2Dot = document.getElementById('step2Dot');
            
            if (currentStep === 1) {
                barLine.style.width = '0%';
                step2Dot.classList.remove('active');
                step2Dot.classList.remove('completed');
                document.getElementById('step_1_section').style.display = 'block';
                document.getElementById('step_2_section').style.display = 'none';
            } else {
                barLine.style.width = '100%';
                step2Dot.classList.add('active');
                document.getElementById('step1Dot').classList.add('completed');
                document.getElementById('step_1_section').style.display = 'none';
                document.getElementById('step_2_section').style.display = 'block';
            }
        }

        function nextStep() {
            // Validate Step 1 Inputs first
            var name = document.getElementById('full_name').value.trim();
            var dob = document.getElementById('dob').value;
            var email = document.getElementById('email').value.trim();
            var whatsapp = document.getElementById('whatsapp_number').value.trim();
            var faculty = document.getElementById('faculty_id').value;

            if (!name || !dob || !email || !whatsapp || !faculty) {
                alert('Please fill out all the required personal profile registry inputs.');
                return;
            }

            // Simple email structure check
            if (email.indexOf('@') === -1) {
                alert('Please enter a valid email address.');
                return;
            }

            currentStep = 2;
            updateProgress();
        }

        function prevStep() {
            currentStep = 1;
            updateProgress();
            document.getElementById('step1Dot').classList.remove('completed');
        }

        // Set payment selection choice
        function selectPaymentChoice(choice) {
            document.getElementById('payment_choice').value = choice;
            
            // Toggle active classes on tab buttons
            document.querySelectorAll('.payment-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('tab_' + choice).classList.add('active');
            
            var stripeArea = document.getElementById('stripePaymentArea');
            var cashArea = document.getElementById('cashPaymentArea');
            var cardGraphic = document.getElementById('cardGraphicWrapper');
            var submitBtn = document.getElementById('submitBtn');
            
            if (choice === 'cash') {
                stripeArea.style.display = 'none';
                cardGraphic.style.display = 'none';
                cashArea.style.display = 'block';
                submitBtn.innerText = 'Register & Confirm Remittance';
                submitBtn.style.backgroundColor = '#f47738'; // Orange remittance brand colour
            } else {
                stripeArea.style.display = 'block';
                cardGraphic.style.display = 'block';
                cashArea.style.display = 'none';
                submitBtn.style.backgroundColor = '#00703c'; // Green complete success colour
                if (choice === 'upfront') {
                    submitBtn.innerText = 'Complete & Pay £2,249.00';
                } else {
                    submitBtn.innerText = 'Complete & Pay £749.00';
                }
            }
        }

        // Real-time Virtual Credit Card updates
        var cardHolderInput = document.getElementById('card_holder');
        if (cardHolderInput) {
            cardHolderInput.addEventListener('input', function(e) {
                var val = e.target.value.trim().toUpperCase();
                document.getElementById('v_name_display').innerText = val ? val : 'YOUR NAME';
            });
        }

        var cardNumInput = document.getElementById('card_number');
        if (cardNumInput) {
            cardNumInput.addEventListener('input', function(e) {
                var val = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                var formatted = '';
                for (var i = 0; i < val.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formatted += ' ';
                    }
                    formatted += val[i];
                }
                e.target.value = formatted.substring(0, 19);
                document.getElementById('v_number_display').innerText = e.target.value ? e.target.value : '•••• •••• •••• ••••';
                
                // Card Brand detection styling
                var brandDisplay = document.getElementById('v_brand_display');
                if (val.startsWith('4')) {
                    brandDisplay.innerText = 'VISA';
                } else if (val.startsWith('5')) {
                    brandDisplay.innerText = 'MASTERCARD';
                } else if (val.startsWith('3')) {
                    brandDisplay.innerText = 'AMEX';
                } else {
                    brandDisplay.innerText = 'CARD';
                }
            });
        }

        var cardExpInput = document.getElementById('card_exp');
        if (cardExpInput) {
            cardExpInput.addEventListener('input', function(e) {
                var val = e.target.value.replace(/\D/g, '');
                if (val.length >= 2) {
                    e.target.value = val.substring(0, 2) + ' / ' + val.substring(2, 4);
                } else {
                    e.target.value = val;
                }
                document.getElementById('v_exp_display').innerText = e.target.value ? e.target.value : 'MM/YY';
            });
        }

        // Client side validation on submit
        function validateForm() {
            var name = document.getElementById('full_name').value.trim();
            var dob = document.getElementById('dob').value;
            var email = document.getElementById('email').value.trim();
            var whatsapp = document.getElementById('whatsapp_number').value.trim();
            var faculty = document.getElementById('faculty_id').value;
            var payment = document.getElementById('payment_choice').value;

            if (!name || !dob || !email || !whatsapp || !faculty) {
                alert('Please fill out all the required personal profile registry inputs.');
                currentStep = 1;
                updateProgress();
                return false;
            }

            if (payment !== 'cash') {
                var holder = document.getElementById('card_holder').value.trim();
                var num = document.getElementById('card_number').value.replace(/\s+/g, '');
                var exp = document.getElementById('card_exp').value.replace(/\s+/g, '');
                var cvc = document.getElementById('card_cvc').value.trim();

                if (!holder || num.length < 13 || exp.length < 5 || cvc.length < 3) {
                    alert('Please enter valid credit card details for Stripe Elements processing.');
                    return false;
                }
            }
            return true;
        }
    </script>

</body>
</html>
