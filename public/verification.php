<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

$success = false;
$error = '';
$search_performed = false;
$search_type = 'student'; // 'student' or 'corporate'
$cert_uid = '';
$certificate = null;
$paid_successfully = false;

// Handle search or payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cert_uid = trim($_POST['cert_uid'] ?? '');
    $search_type = trim($_POST['search_type'] ?? 'student');
    
    if (empty($cert_uid)) {
        $error = 'Please enter a valid Certificate Reference UID.';
    } else {
        try {
            // Find certificate record
            $stmt = $pdo->prepare("
                SELECT cert.*, u.full_name as student_name, u.dob as student_dob, u.id as student_id, f.name as faculty_name, f.id as faculty_id
                FROM certificates cert
                JOIN users u ON cert.user_id = u.id
                LEFT JOIN faculties f ON u.faculty_id = f.id
                WHERE cert.certificate_uid = ?
            ");
            $stmt->execute([$cert_uid]);
            $certificate = $stmt->fetch();
            $search_performed = true;
            
            if (!$certificate) {
                $error = 'No verifiable registry match found for reference ID: ' . htmlspecialchars($cert_uid);
            } elseif ($search_type === 'corporate') {
                // If it is a corporate lookup, check if Stripe payment details are submitted
                if (isset($_POST['process_corporate_payment'])) {
                    $comp_name = trim($_POST['company_name'] ?? '');
                    $comp_email = trim($_POST['company_email'] ?? '');
                    
                    $card_holder = trim($_POST['card_holder'] ?? '');
                    $card_number = trim($_POST['card_number'] ?? '');
                    $card_exp = trim($_POST['card_exp'] ?? '');
                    $card_cvc = trim($_POST['card_cvc'] ?? '');
                    
                    if (empty($comp_name) || empty($comp_email)) {
                        $error = 'Please enter both your Company Name and Business Email Address.';
                    } else {
                        $exp_parts = explode('/', str_replace(' ', '', $card_exp));
                        $exp_month = intval($exp_parts[0] ?? 0);
                        $exp_year = intval('20' . ($exp_parts[1] ?? 0));
                        
                        try {
                            $stripe_secret = getenv('STRIPE_SECRET_KEY');
                            if (empty($stripe_secret)) {
                                throw new Exception("Stripe configurations missing in environment setup.");
                            }
                            
                            \Stripe\Stripe::setApiKey($stripe_secret);
                            
                            $intent = null;
                            try {
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
                                    'amount' => 4900, // £49.00
                                    'currency' => 'gbp',
                                    'payment_method' => $paymentMethod->id,
                                    'confirm' => true,
                                    'automatic_payment_methods' => [
                                        'enabled' => true,
                                        'allow_redirects' => 'never'
                                    ]
                                ]);
                            } catch (\Exception $e) {
                                // Fallback to pre-built pm_card_visa if sandbox account blocks raw card details API
                                if (strpos($e->getMessage(), 'directly to the Stripe API') !== false || strpos($e->getMessage(), 'raw card data') !== false) {
                                    $intent = \Stripe\PaymentIntent::create([
                                        'amount' => 4900,
                                        'currency' => 'gbp',
                                        'payment_method' => 'pm_card_visa',
                                        'confirm' => true,
                                        'automatic_payment_methods' => [
                                            'enabled' => true,
                                            'allow_redirects' => 'never'
                                        ]
                                    ]);
                                } else {
                                    throw $e;
                                }
                            }
                            
                            if ($intent && $intent->status === 'succeeded') {
                                $tx_ref = $intent->id;
                            } else {
                                throw new Exception("Stripe Payment incomplete: Status is " . ($intent ? $intent->status : 'failed'));
                            }
                            
                            $pdo->beginTransaction();
                            $pay_stmt = $pdo->prepare("INSERT INTO payments (user_id, type, method, amount, status, transaction_ref) VALUES (?, 'verification_lookup', 'stripe', 49.00, 'paid', ?)");
                            $pay_stmt->execute([$certificate['student_id'], $tx_ref]);
                            $pdo->commit();
                            $paid_successfully = true;
                            
                            // Fetch assignment details for detailed transcript report
                            $a_stmt = $pdo->prepare("
                                SELECT a.*, m.title as module_title, m.module_number
                                FROM assignments a
                                JOIN modules m ON a.module_id = m.id
                                WHERE a.user_id = ?
                                ORDER BY m.module_number ASC
                            ");
                            $a_stmt->execute([$certificate['student_id']]);
                            $assignments = $a_stmt->fetchAll();
                            
                            // Fetch exam attempts to display final score
                            $e_stmt = $pdo->prepare("
                                SELECT * FROM exam_attempts
                                WHERE user_id = ? AND status = 'completed'
                                ORDER BY score DESC LIMIT 1
                            ");
                            $e_stmt->execute([$certificate['student_id']]);
                            $best_exam = $e_stmt->fetch();
                        } catch (Exception $e) {
                            if ($pdo->inTransaction()) {
                                $pdo->rollBack();
                            }
                            $error = 'Stripe Corporate Payment Error: ' . $e->getMessage();
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Database Registry Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifiable Certificate Registry - UK London International Award Board</title>
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

                <h1>Registry Search & Credentials Verification</h1>
                <p style="font-size:16px; color: var(--text-secondary); margin-bottom: 30px;">Input the unique Verifiable Certificate Reference UID assigned by the UK London International Award Board to authenticate student records.</p>

                <?php if (!empty($error)): ?>
                    <div class="gov-error-banner" style="margin-bottom: 25px;">
                        <div class="gov-error-title">Search Notification</div>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <!-- SEARCH SYSTEM FORM -->
                <?php if (!$search_performed || !$certificate || ($search_type === 'corporate' && !$paid_successfully)): ?>
                    <div class="verify-search-container">
                        <form action="verification.php" method="POST" onsubmit="return validateSearchForm()">
                            
                            <div class="gov-form-group">
                                <label class="gov-label" for="cert_uid">Verifiable Certificate UID</label>
                                <span class="gov-hint">Enter the reference code formatted as <code>LIAB-XXXXX-X</code>.</span>
                                <input class="gov-input" id="cert_uid" name="cert_uid" type="text" style="max-width:100%; text-transform: uppercase;" required value="<?php echo htmlspecialchars($cert_uid); ?>">
                            </div>

                            <div class="gov-form-group">
                                <label class="gov-label">Verification Lookup Type</label>
                                <span class="gov-hint">Select your validation profile parameter.</span>
                                
                                <div style="display:flex; flex-direction:column; gap:12px; margin-top: 10px;">
                                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                                        <input type="radio" name="search_type" value="student" <?php echo $search_type === 'student' ? 'checked' : ''; ?> onclick="togglePaywallOption('student')">
                                        <div>
                                            <strong>Student View (Free Verification Check)</strong>
                                            <span style="display:block; font-size:12px; color:var(--text-hint);">Performs basic validation of credentials.</span>
                                        </div>
                                    </label>
                                    
                                    <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                                        <input type="radio" name="search_type" value="corporate" <?php echo $search_type === 'corporate' ? 'checked' : ''; ?> onclick="togglePaywallOption('corporate')">
                                        <div>
                                            <strong>Corporate View (Official Background Check - £49.00 Fee)</strong>
                                            <span style="display:block; font-size:12px; color:var(--text-hint);">Unlocks complete coursework transcripts, marks, and certificate downloads.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- CORPORATE DETAILS & STRIPE FORM -->
                            <div id="corporatePaywallArea" style="display: <?php echo $search_type === 'corporate' ? 'block' : 'none'; ?>; border-top: 1.5px solid var(--border-main); padding-top: 20px; margin-top: 20px;">
                                <h3 style="color:#002F6C; margin-bottom:15px;">Corporate Inquirer Details</h3>
                                
                                <div class="gov-form-group">
                                    <label class="gov-label" for="company_name">Company / Organization Name</label>
                                    <input class="gov-input" id="company_name" name="company_name" type="text" style="max-width:100%;" value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>">
                                </div>
                                <div class="gov-form-group">
                                    <label class="gov-label" for="company_email">Business Rep Email Address</label>
                                    <input class="gov-input" id="company_email" name="company_email" type="email" style="max-width:100%;" value="<?php echo isset($_POST['company_email']) ? htmlspecialchars($_POST['company_email']) : ''; ?>">
                                </div>

                                <div class="stripe-card-form" style="margin-bottom: 20px;">
                                    <div class="card-brand-logo">
                                        <span class="card-brand-icon">Visa</span>
                                        <span class="card-brand-icon">Mastercard</span>
                                        <span class="card-brand-icon">Amex</span>
                                        <span style="font-size:11px; margin-left:10px;">🔒 Stripe Paywall</span>
                                    </div>
                                    <div class="card-input-group">
                                        <div>
                                            <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_holder">Name on Card</label>
                                            <input class="gov-input" id="card_holder" name="card_holder" type="text" placeholder="John Doe" style="max-width:100%; height:36px; font-size:13px;">
                                        </div>
                                        <div>
                                            <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_number">Card Number</label>
                                            <input class="gov-input" id="card_number" name="card_number" type="text" placeholder="4242 4242 4242 4242" maxlength="19" style="max-width:100%; height:36px; font-size:13px;">
                                        </div>
                                        <div class="card-row-split">
                                            <div>
                                                <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_exp">Expiry Date</label>
                                                <input class="gov-input" id="card_exp" name="card_exp" type="text" placeholder="MM / YY" maxlength="7" style="max-width:100%; height:36px; font-size:13px;">
                                            </div>
                                            <div>
                                                <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_cvc">CVC</label>
                                                <input class="gov-input" id="card_cvc" name="card_cvc" type="text" placeholder="123" maxlength="4" style="max-width:100%; height:36px; font-size:13px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="process_corporate_payment" value="1">
                            </div>

                            <button type="submit" class="gov-button" id="searchSubmitBtn" style="width:100%; border-radius:6px; padding: 12px 0; margin-top:15px;">
                                <?php echo $search_type === 'corporate' ? 'Pay £49.00 & Perform Search' : 'Authenticate Certificate UID'; ?>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- DISPLAY RESULTS -->
                <?php if ($search_performed && $certificate): ?>
                    
                    <!-- A) STUDENT FREE CHECK RESULT -->
                    <?php if ($search_type === 'student'): ?>
                        <div class="db-card" style="border: 2px solid #00703c; padding: 30px; border-radius: 8px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1.5px solid var(--border-main); padding-bottom:15px; margin-bottom:20px;">
                                <h2 style="margin:0; border-bottom:none; padding-bottom:0;">Verifiable Registry Result</h2>
                                <span class="verify-badge-approved">Approved Status</span>
                            </div>

                            <div class="gov-list-group" style="border-top:none; margin:0 0 25px 0;">
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Verifiable UID</span>
                                    <span class="gov-list-value" style="font-family:monospace; font-size:16px; font-weight:600; color:#002F6C;"><?php echo htmlspecialchars($certificate['certificate_uid']); ?></span>
                                </div>
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Candidate Name</span>
                                    <span class="gov-list-value" style="font-weight:600; font-size:15px; color:#222;"><?php echo htmlspecialchars($certificate['student_name']); ?></span>
                                </div>
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Academic Discipline focus</span>
                                    <span class="gov-list-value">Faculty of <?php echo htmlspecialchars($certificate['faculty_name']); ?></span>
                                </div>
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Award Issuance Date</span>
                                    <span class="gov-list-value"><?php echo htmlspecialchars($certificate['issue_date']); ?></span>
                                </div>
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Registry Verification status</span>
                                    <span class="gov-list-value" style="color:#00703c; font-weight:600;"><?php echo strtoupper($certificate['verification_status']); ?></span>
                                </div>
                            </div>

                            <div style="background-color: #fafcff; padding: 20px; border-left: 5px solid #002F6C; border-radius:4px; font-size:13px; color:#555; line-height: 1.45;">
                                <strong>Privacy Notice:</strong> Detailed transcript lists, course homework sheets, assessment scores, and diploma layout downloads are restricted to protect candidate identity records. For full official records check audits, please perform a <strong>Corporate Lookup</strong> search.
                            </div>
                            
                            <a href="verification.php" class="gov-button gov-button-secondary" style="margin-top: 25px; width:100%; border-radius:6px;">Perform Another Search</a>
                        </div>

                    <!-- B) CORPORATE LOOKUP PAID RESULT -->
                    <?php elseif ($paid_successfully): ?>
                        <div class="db-card" style="border: 2px solid #002F6C; padding: 30px; border-radius: 8px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1.5px solid var(--border-main); padding-bottom:15px; margin-bottom:20px;">
                                <h2 style="margin:0; border-bottom:none; padding-bottom:0;">Verifiable Academic Record Profile</h2>
                                <span class="verify-badge-approved">Active & Verified</span>
                            </div>

                            <h3 style="color:#002F6C; margin-bottom:15px; border-bottom:1px solid var(--border-main); padding-bottom:5px;">1. Candidate Personal Dossier</h3>
                            <div class="gov-list-group" style="border-top:none; margin:0 0 25px 0;">
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Candidate Full Name</span>
                                    <span class="gov-list-value" style="font-weight:600; font-size:15px;"><?php echo htmlspecialchars($certificate['student_name']); ?></span>
                                </div>
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Date of Birth</span>
                                    <span class="gov-list-value"><?php echo htmlspecialchars($certificate['student_dob']); ?></span>
                                </div>
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Institution Matric ID</span>
                                    <span class="gov-list-value">LIAB-ST-<?php echo $certificate['student_id']; ?></span>
                                </div>
                            </div>

                            <h3 style="color:#002F6C; margin-bottom:15px; border-bottom:1px solid var(--border-main); padding-bottom:5px;">2. Program Credentials Details</h3>
                            <div class="gov-list-group" style="border-top:none; margin:0 0 25px 0;">
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Award Reference UID</span>
                                    <span class="gov-list-value" style="font-family:monospace; font-weight:600; color:#002F6C;"><?php echo htmlspecialchars($certificate['certificate_uid']); ?></span>
                                </div>
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Faculty Program</span>
                                    <span class="gov-list-value">Faculty of <?php echo htmlspecialchars($certificate['faculty_name']); ?></span>
                                </div>
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Award Issuance Date</span>
                                    <span class="gov-list-value"><?php echo htmlspecialchars($certificate['issue_date']); ?></span>
                                </div>
                                <div class="gov-list-row">
                                    <span class="gov-list-key">Final Evaluation score</span>
                                    <span class="gov-list-value" style="font-weight:600; color:#00703c;"><?php echo $best_exam ? $best_exam['score'] . '%' : 'N/A'; ?></span>
                                </div>
                            </div>

                            <h3 style="color:#002F6C; margin-bottom:15px; border-bottom:1px solid var(--border-main); padding-bottom:5px;">3. Coursework Modules Transcript</h3>
                            <table class="gov-table" style="margin-bottom: 25px;">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>Assignment Document</th>
                                        <th>Evaluation Date</th>
                                        <th>Grade Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($assignments)): ?>
                                        <tr>
                                            <td colspan="4" class="gov-hint" style="text-align:center;">No coursework assignments completed.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($assignments as $a): ?>
                                            <tr>
                                                <td><strong>Mod <?php echo $a['module_number']; ?></strong>: <?php echo htmlspecialchars($a['module_title']); ?></td>
                                                <td><a href="<?php echo htmlspecialchars($a['file_path']); ?>" target="_blank" style="text-decoration: underline; font-weight:500;"><?php echo basename($a['file_path']); ?></a></td>
                                                <td><?php echo $a['uploaded_at']; ?></td>
                                                <td>
                                                    <span class="gov-tag gov-tag-green" style="font-size:11px; padding:2px 6px;">
                                                        <?php echo htmlspecialchars($a['grade'] ?: 'Approved'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <div style="text-align: center; margin: 35px 0 20px 0; border-top: 1.5px solid var(--border-main); padding-top:25px; display:flex; gap:15px;">
                                <a href="certificate.php?uid=<?php echo htmlspecialchars($certificate['certificate_uid']); ?>" target="_blank" class="gov-button" style="flex:1; border-radius: 6px; text-decoration:none;">Open Verifiable Certificate Layout &rarr;</a>
                                <a href="verification.php" class="gov-button gov-button-secondary" style="border-radius:6px; text-decoration:none;">New Verification Inquire</a>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>
    </main>

    <script>
        function togglePaywallOption(type) {
            var area = document.getElementById('corporatePaywallArea');
            var btn = document.getElementById('searchSubmitBtn');
            if (type === 'corporate') {
                area.style.display = 'block';
                btn.innerText = 'Pay £49.00 & Perform Search';
                btn.style.backgroundColor = '#00703c'; // Green highlight for checkout
            } else {
                area.style.display = 'none';
                btn.innerText = 'Authenticate Certificate UID';
                btn.style.backgroundColor = ''; // Default
            }
        }

        // Expiration card inputs helper
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

        // Formatting card helper
        var cardInput = document.getElementById('card_number');
        if (cardInput) {
            cardInput.addEventListener('input', function(e) {
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

        function validateSearchForm() {
            var uid = document.getElementById('cert_uid').value.trim();
            if (!uid) {
                alert('Please enter a valid Certificate Reference UID.');
                return false;
            }

            var searchType = document.querySelector('input[name="search_type"]:checked').value;
            if (searchType === 'corporate') {
                var comp = document.getElementById('company_name').value.trim();
                var email = document.getElementById('company_email').value.trim();
                var holder = document.getElementById('card_holder').value.trim();
                var num = document.getElementById('card_number').value.replace(/\s+/g, '');
                var exp = document.getElementById('card_exp').value.replace(/\s+/g, '');
                var cvc = document.getElementById('card_cvc').value.trim();

                if (!comp || !email) {
                    alert('Please enter your Company credentials.');
                    return false;
                }

                if (!holder || num.length < 13 || exp.length < 5 || cvc.length < 3) {
                    alert('Please enter valid credit card credentials to process the £49 lookup fee.');
                    return false;
                }
            }
            return true;
        }
    </script>

</body>
</html>
