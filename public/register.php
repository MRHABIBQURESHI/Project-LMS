<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

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
                <a href="login.php">Sign In</a>
            </div>
        </div>
    </header>

    <main class="gov-width-container">
        <div class="gov-grid-row">
            <div class="gov-grid-column-two-thirds" style="margin: 0 auto; float: none;">

                <?php if ($success): ?>
                    <div class="gov-success-banner" style="margin-top: 20px; border-radius: 8px;">
                        <div class="gov-success-title">Enrollment Complete</div>
                        <p>Thank you. Your tuition payment has been verified successfully. Your student account is now fully active.</p>
                    </div>

                    <h1>Access Credentials Generated</h1>
                    <p>An automated confirmation email has been dispatched. Please write down or capture your login details below to access the coursework terminal immediately.</p>

                    <div style="background-color: #fafcff; padding: 30px; border-left: 6px solid #00703c; border-radius: 6px; margin: 30px 0; box-shadow: var(--shadow-card);">
                        <p style="font-size: 19px; margin-bottom: 12px; color: #002F6C;"><strong>Student Portal Link:</strong> <a href="login.php" style="font-weight: 600; text-decoration: underline;">Sign In Page</a></p>
                        <p style="font-size: 18px; margin-bottom: 10px;"><strong>Registered Email Address:</strong> <code><?php echo htmlspecialchars($email); ?></code></p>
                        <p style="font-size: 18px; margin-bottom: 0;"><strong>One-Time Security Password:</strong> <code><?php echo htmlspecialchars($generated_password); ?></code></p>
                    </div>

                    <p style="color: var(--text-secondary); font-size:14px; margin-bottom: 30px;"><em>Note: You will be prompted to change this temporary password upon accessing your profile dashboard settings.</em></p>
                    
                    <a href="login.php" class="gov-button" style="padding: 12px 35px; border-radius: 6px;">Proceed to Portal Access &rarr;</a>

                <?php else: ?>

                    <h1>Student Intake & Registration Portal</h1>
                    <p style="font-size:16px; color: var(--text-secondary); margin-bottom: 30px;">Register your academic profile to matriculate. Secure your coursework modules instantly via card verification, or choose manual money order routing.</p>

                    <?php if (!empty($error)): ?>
                        <div class="gov-error-banner" style="margin-bottom: 25px;">
                            <div class="gov-error-title">There is a problem</div>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    <?php endif; ?>

                    <form id="regForm" action="register.php" method="POST" novalidate onsubmit="return validateForm()">
                        
                        <h2 style="margin-top: 10px;">1. Personal Academic Profile</h2>
                        
                        <div class="gov-form-group">
                            <label class="gov-label" for="full_name">Full Name (Legal Identity)</label>
                            <span class="gov-hint">Enter your name exactly as it appears on formal identity documents.</span>
                            <input class="gov-input" id="full_name" name="full_name" type="text" style="max-width:100%;" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                        </div>

                        <div class="gov-form-group">
                            <label class="gov-label" for="dob">Date of Birth</label>
                            <span class="gov-hint">Used for certificate registries verification.</span>
                            <input class="gov-input" id="dob" name="dob" type="date" style="max-width:100%;" required value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>">
                        </div>

                        <div class="gov-form-group">
                            <label class="gov-label" for="email">Student Primary Email</label>
                            <span class="gov-hint">Your login credentials and transcript notification will be sent here.</span>
                            <input class="gov-input" id="email" name="email" type="email" style="max-width:100%;" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>

                        <div class="gov-form-group">
                            <label class="gov-label" for="whatsapp_number">WhatsApp Contact Number</label>
                            <span class="gov-hint">Include international dialing codes (e.g. +447000000000) for tutor comms.</span>
                            <input class="gov-input" id="whatsapp_number" name="whatsapp_number" type="tel" style="max-width:100%;" required value="<?php echo isset($_POST['whatsapp_number']) ? htmlspecialchars($_POST['whatsapp_number']) : ''; ?>">
                        </div>

                        <div class="gov-form-group">
                            <label class="gov-label" for="faculty_id">Academic Program Faculty</label>
                            <span class="gov-hint">Select your discipline. This loads dynamic Course Modules 3 & 4.</span>
                            <select class="gov-select" id="faculty_id" name="faculty_id" style="max-width:100%;" required>
                                <option value="">-- Choose Course Focus --</option>
                                <?php foreach ($facs as $f): ?>
                                    <option value="<?php echo $f['id']; ?>" <?php echo (isset($_POST['faculty_id']) && $_POST['faculty_id'] == $f['id']) ? 'selected' : ''; ?>>Faculty of <?php echo htmlspecialchars($f['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="gov-form-group">
                            <label class="gov-label" for="rep_code">Consultant / Affiliate Code (Optional)</label>
                            <span class="gov-hint">Enter your representative representative's registration token, if applicable.</span>
                            <input class="gov-input" id="rep_code" name="rep_code" type="text" placeholder="e.g. REP-1234" style="max-width:100%;" value="<?php echo isset($_POST['rep_code']) ? htmlspecialchars($_POST['rep_code']) : ''; ?>">
                        </div>

                        <h2>2. Academic Program Tuition Fee</h2>
                        <p style="font-size:14px; margin-bottom:15px; color:#555;">Choose your payment structure. Full credentials activation requires verification.</p>

                        <div class="payment-method-tabs">
                            <div class="payment-tab-btn active" id="tab_upfront" onclick="selectPaymentChoice('upfront')">
                                Pay Upfront (£2,249)
                            </div>
                            <div class="payment-tab-btn" id="tab_installment" onclick="selectPaymentChoice('installment')">
                                3 Installments (£749/mo)
                            </div>
                            <div class="payment-tab-btn" id="tab_cash" onclick="selectPaymentChoice('cash')">
                                Remittance Gate (WU/Ria)
                            </div>
                        </div>

                        <input type="hidden" name="payment_choice" id="payment_choice" value="upfront">

                        <!-- STRIPE ELEMENTS FORM CARD PLACEHOLDER -->
                        <div id="stripePaymentArea" class="stripe-card-form">
                            <div class="card-brand-logo">
                                <span class="card-brand-icon">Visa</span>
                                <span class="card-brand-icon">Mastercard</span>
                                <span class="card-brand-icon">Amex</span>
                                <span style="font-size:12px; margin-left: 10px;">🔒 Stripe Elements Secure</span>
                            </div>
                                                     <div class="card-input-group">
                                <div>
                                    <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_holder">Cardholder Name</label>
                                    <input class="gov-input" id="card_holder" name="card_holder" type="text" placeholder="John Doe" style="max-width:100%; height: 38px; font-size: 13px;">
                                </div>

                                <div>
                                    <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_number">Card Number</label>
                                    <div style="position:relative;">
                                        <input class="gov-input" id="card_number" name="card_number" type="text" placeholder="4242 •••• •••• 4242" maxlength="19" style="max-width:100%; height: 38px; font-size: 13px; padding-left: 40px;">
                                        <span style="position:absolute; left:12px; top: 8px; font-size: 16px;">💳</span>
                                    </div>
                                </div>

                                <div class="card-row-split">
                                    <div>
                                        <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_exp">Expiry Date</label>
                                        <input class="gov-input" id="card_exp" name="card_exp" type="text" placeholder="MM / YY" maxlength="7" style="max-width:100%; height: 38px; font-size: 13px;">
                                    </div>
                                    <div>
                                        <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_cvc">Security Code (CVC)</label>
                                        <input class="gov-input" id="card_cvc" name="card_cvc" type="text" placeholder="123" maxlength="4" style="max-width:100%; height: 38px; font-size: 13px;">
                                    </div>
                                </div>
                            </div>
                            <span class="gov-hint" style="font-size: 11px;">Your card transaction is processed instantly via Stripe API with standard TLS 1.3 encryption.</span>
                        </div>

                        <div id="cashPaymentArea" style="display: none; background-color: #fafcff; padding: 25px; border-left: 5px solid #002F6C; margin-top: 15px; border-radius: 4px;">
                            <h3 style="color:#002F6C; margin-bottom:10px;">Western Union / Ria / WorldRemit Remittance</h3>
                            <p style="font-size:14px; margin-bottom: 0; line-height: 1.5;">Upon clicking register below, your profile record will be created in a <strong>Pending Lock</strong> state, and you will be routed to submit the Money Transfer Control Number (MTCN) or remittance receipt reference. An administrator will unlock your portal access keys within 48 hours of ledger clearance.</p>
                        </div>

                        <div style="margin-top: 35px; border-top: 1.5px solid #EBF3FC; padding-top: 25px;">
                            <button type="submit" class="gov-button" id="submitBtn" style="padding: 14px 40px; font-size:16px; border-radius:6px;">Register and Pay £2,249.00</button>
                        </div>

                    </form>

                <?php endif; ?>

            </div>
        </div>
    </main>

    <script>
        // Set payment selection
        function selectPaymentChoice(choice) {
            document.getElementById('payment_choice').value = choice;
            
            // Toggle active classes on buttons
            document.querySelectorAll('.payment-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('tab_' + choice).classList.add('active');
            
            var stripeArea = document.getElementById('stripePaymentArea');
            var cashArea = document.getElementById('cashPaymentArea');
            var submitBtn = document.getElementById('submitBtn');
            
            if (choice === 'cash') {
                stripeArea.style.display = 'none';
                cashArea.style.display = 'block';
                submitBtn.innerText = 'Register & Submit Remittance References';
                submitBtn.style.backgroundColor = '#f47738'; // Amber highlight for cash route
            } else {
                stripeArea.style.display = 'block';
                cashArea.style.display = 'none';
                if (choice === 'upfront') {
                    submitBtn.innerText = 'Register and Pay £2,249.00';
                } else {
                    submitBtn.innerText = 'Register and Pay First Installment (£749.00)';
                }
                submitBtn.style.backgroundColor = ''; // Reset to default
            }
        }

        // Format expiration card inputs
        var expInput = document.getElementById('card_exp');
        if (expInput) {
            expInput.addEventListener('input', function(e) {
                var val = e.target.value.replace(/\D/g, '');
                if (val.length >= 2) {
                    e.target.value = val.substring(0, 2) + ' / ' + val.substring(2, 4);
                } else {
                    e.target.value = val;
                }
            });
        }

        // Format card numbers (adds spaces every 4 digits)
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
            });
        }

        // Client side validation
        function validateForm() {
            var name = document.getElementById('full_name').value.trim();
            var dob = document.getElementById('dob').value;
            var email = document.getElementById('email').value.trim();
            var whatsapp = document.getElementById('whatsapp_number').value.trim();
            var faculty = document.getElementById('faculty_id').value;
            var payment = document.getElementById('payment_choice').value;

            if (!name || !dob || !email || !whatsapp || !faculty) {
                alert('Please fill out all personal profile registry inputs.');
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
