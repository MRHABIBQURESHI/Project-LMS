@extends('lms.layout')

@section('title', 'Student Intake Registry - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')
@section('body_class', 'login-body')
@section('main_class', '')

@section('header')
<!-- Hide default header matching design -->
@endsection

@section('head_extra')
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
<style>
    .login-split-container {
        height: 100vh !important;
        width: 100vw !important;
        overflow: hidden !important;
        position: fixed !important;
        top: 0;
        left: 0;
    }
    .login-left-panel {
        height: 100vh !important;
        overflow-y: auto !important;
        padding: 30px 24px !important;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }
    .login-right-panel {
        height: 100vh !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-card {
        max-width: 700px !important;
        width: 100% !important;
        padding: 32px 36px !important;
        border-radius: 14px;
        box-shadow: 0 6px 32px rgba(0, 47, 108, 0.1);
        margin: auto;
        background: #fff;
    }
    .reg-nav-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .reg-nav-link {
        font-size: 13px;
        font-weight: 600;
        color: #002F6C;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    .reg-nav-link:hover { opacity: 1; }
    .reg-title {
        font-size: 22px;
        font-weight: 700;
        color: #002F6C;
        margin-bottom: 4px;
    }
    .reg-subtitle {
        font-size: 13px;
        color: #777;
        margin-bottom: 22px;
        line-height: 1.45;
    }
    /* Progress bar */
    .progress-bar-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        position: relative;
    }
    .progress-bar-line {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #dbe4ef;
        z-index: 1;
        transform: translateY(-50%);
    }
    .progress-bar-line-active {
        position: absolute;
        top: 50%;
        left: 0;
        width: 0%;
        height: 2px;
        background-color: #002F6C;
        z-index: 2;
        transform: translateY(-50%);
        transition: width 0.35s ease;
    }
    .progress-step {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #f0f4fa;
        border: 2px solid #dbe4ef;
        color: #999;
        font-size: 12px;
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
        color: #fff;
        box-shadow: 0 0 0 4px rgba(0, 47, 108, 0.12);
    }
    .progress-step.completed {
        background-color: #00703c;
        border-color: #00703c;
        color: #fff;
    }
    /* Section heading */
    .step-heading {
        font-size: 11px;
        color: #002F6C;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1.5px solid #EBF3FC;
    }
    /* Grid rows */
    .form-grid-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-bottom: 14px;
    }
    .form-grid-row-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 14px;
    }
    .form-grid-row > .gov-form-group,
    .form-grid-row-3 > .gov-form-group {
        margin-bottom: 0 !important;
    }
    /* Field styling */
    .login-card .gov-label {
        font-size: 12.5px !important;
        font-weight: 600;
        color: #344054;
        margin-bottom: 5px !important;
        display: block;
    }
    .login-card .gov-input,
    .login-card .gov-select {
        max-width: 100% !important;
        width: 100% !important;
        height: 36px !important;
        padding: 7px 11px !important;
        font-size: 13px !important;
        border-radius: 6px;
        border: 1.5px solid #dbe4ef;
        background: #fafcfe;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .login-card .gov-input:focus,
    .login-card .gov-select:focus {
        border-color: #002F6C;
        box-shadow: 0 0 0 3px rgba(0, 47, 108, 0.08);
        outline: none;
        background: #fff;
    }
    .login-card .gov-input[type="file"] {
        height: auto !important;
        padding: 5px 8px !important;
        font-size: 12px !important;
    }
    .login-card .gov-form-group {
        margin-bottom: 14px !important;
    }
    .validation-error-msg {
        font-size: 11px;
        color: #d4351c;
        margin-top: 3px;
        display: none;
    }
    .gov-error-banner {
        word-break: break-word !important;
        overflow-wrap: break-word !important;
    }
    /* Virtual card */
    .virtual-card-wrapper { perspective: 1000px; margin: 12px 0; }
    .virtual-card {
        width: 100%; max-width: 280px; height: 155px;
        background: linear-gradient(135deg, #0e274c 0%, #001736 100%);
        border-radius: 10px; padding: 14px 18px; color: white;
        font-family: 'Courier New', monospace; position: relative;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        margin: 0 auto; overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .virtual-card::before {
        content: ''; position: absolute; top:-50%; right:-20%;
        width:200px; height:200px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        pointer-events: none;
    }
    .v-chip { width:30px; height:22px; background: linear-gradient(135deg,#f3d078,#d4ac0d); border-radius:4px; margin-bottom:12px; }
    .v-number { font-size:14px; letter-spacing:1.5px; margin-bottom:14px; color:#f8fafc; }
    .v-details { display:flex; justify-content:space-between; align-items:flex-end; }
    .v-label-text { font-size:7px; color:#64748b; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px; }
    .v-value-text { font-size:9px; color:#fff; text-transform:uppercase; max-width:130px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .v-brand { font-size:13px; font-weight:800; font-style:italic; color:rgba(255,255,255,0.9); }
    .btn-flex-row { display:flex; gap:12px; margin-top:18px; }
    /* Payment tabs */
    .payment-method-tabs { display:flex; gap:8px; margin-bottom:14px; }
    .payment-tab-btn {
        flex:1; text-align:center; padding:10px 8px; font-size:12px;
        border:1.5px solid #dbe4ef; border-radius:8px; cursor:pointer;
        font-weight:600; color:#555; background:#f5f8fd;
        transition: all 0.2s;
    }
    .payment-tab-btn.active {
        border-color:#002F6C; background:#EBF3FC; color:#002F6C;
    }
    @media (max-width: 768px) {
        .form-grid-row, .form-grid-row-3 {
            grid-template-columns: 1fr;
        }
        .login-card { padding: 24px 18px !important; }
    }
</style>
@endsection

@section('content')
<div class="login-split-container">
    
    <!-- Left Panel: Multi-step registration form -->
    <div class="login-left-panel">
        <div class="login-card">
            <div class="reg-nav-bar">
                <a href="{{ route('lms.home') }}" class="reg-nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Back to Home
                </a>
                <a href="{{ route('lms.login') }}" class="reg-nav-link">Sign In Instead &rarr;</a>
            </div>

            @if (!empty($success))
                <h1 class="login-title" style="margin-bottom: 10px; color: #00703c;">Enrollment Complete</h1>
                <div class="gov-success-banner" style="margin-bottom: 25px; border-radius: 6px;">
                    <p style="font-size: 13px; font-weight:600; margin-bottom: 0; color:#00703c;">Thank you! Your tuition fee payment has been verified. Your student account is now fully active.</p>
                </div>

                <p style="font-size:14px; color:var(--text-secondary); margin-bottom: 20px; line-height: 1.5;">An automated credentials email was sent to your inbox. Write down or capture your temporary access keys below:</p>

                <div style="background-color: #fafcff; padding: 20px; border-left: 4px solid #00703c; border-radius: 6px; margin-bottom: 25px; border: 1.5px solid #EBF3FC; border-left-width: 5px;">
                    <p style="font-size: 15px; margin-bottom: 12px; color:#002F6C;"><strong>Student Portal Link:</strong> <a href="{{ route('lms.login') }}" style="font-weight:600; text-decoration: underline;">Sign In Page</a></p>
                    <p style="font-size: 14px; margin-bottom: 8px; color:var(--text-primary);"><strong>Email Address:</strong> <code>{{ $email }}</code></p>
                    <p style="font-size: 14px; margin-bottom: 0; color:var(--text-primary);"><strong>Temporary Password:</strong> <code>{{ $password }}</code></p>
                </div>

                <p style="color: var(--text-hint); font-size:11px; margin-bottom: 25px; line-height:1.4;"><em>Note: You will be prompted to update this temporary password upon your first access into your dashboard settings.</em></p>
                
                <a href="{{ route('lms.login') }}" class="gov-button" style="display: block; width: 100%; text-decoration: none; border-radius: 6px; text-align:center;">Proceed to Portal Access &rarr;</a>

            @else

                <h1 class="reg-title">Student Registration</h1>
                <p class="reg-subtitle">Register your academic profile to matriculate. Complete your details and secure your enrollment.</p>

                <!-- Step Tracker Badge -->
                <div class="progress-bar-container">
                    <div class="progress-bar-line"></div>
                    <div class="progress-bar-line-active" id="barLine"></div>
                    <div class="progress-step active" id="step1Dot">1</div>
                    <div class="progress-step" id="step2Dot">2</div>
                </div>

                @if (!empty($error))
                    <div class="gov-error-banner" style="padding: 10px 15px; margin-bottom: 20px; border-radius: 6px;">
                        <p style="font-size: 13px; margin-bottom: 0; color:#d4351c; font-weight: 600;">{{ $error }}</p>
                    </div>
                @endif

                <form id="regForm" action="{{ route('lms.register') }}" method="POST" enctype="multipart/form-data" novalidate onsubmit="return validateForm()">
                    @csrf
                    <!-- ======================================================== -->
                    <!-- STEP 1: PERSONAL ACADEMIC PROFILE                        -->
                    <!-- ======================================================== -->
                    <div id="step_1_section">
                        <div class="step-heading">Step 1 &mdash; Personal &amp; Faculty Info</div>

                        <div class="form-grid-row-3">
                            <div class="gov-form-group">
                                <label class="gov-label" for="full_name">Full Name</label>
                                <input class="gov-input" id="full_name" name="full_name" type="text" placeholder="As on passport / ID" required value="{{ request()->input('full_name', '') }}">
                                <span class="validation-error-msg" id="error_full_name"></span>
                            </div>
                            <div class="gov-form-group">
                                <label class="gov-label" for="dob">Date of Birth</label>
                                <input class="gov-input" id="dob" name="dob" type="date" required value="{{ request()->input('dob', '') }}">
                                <span class="validation-error-msg" id="error_dob"></span>
                            </div>
                            <div class="gov-form-group">
                                <label class="gov-label" for="whatsapp_number">WhatsApp Number</label>
                                <input class="gov-input" id="whatsapp_number" name="whatsapp_number" type="tel" placeholder="+44 7000 000000" required value="{{ request()->input('whatsapp_number', '') }}">
                                <span class="validation-error-msg" id="error_whatsapp_number"></span>
                            </div>
                        </div>

                        <div class="form-grid-row">
                            <div class="gov-form-group">
                                <label class="gov-label" for="email">Student Email</label>
                                <input class="gov-input" id="email" name="email" type="email" placeholder="student@example.com" required value="{{ request()->input('email', '') }}">
                                <span class="validation-error-msg" id="error_email"></span>
                            </div>
                            <div class="gov-form-group">
                                <label class="gov-label" for="rep_code">Affiliate / Rep Code <span style="color:#999;font-weight:400;">(optional)</span></label>
                                <input class="gov-input" id="rep_code" name="rep_code" type="text" placeholder="e.g. REP-DEMO-01" value="{{ request()->input('rep_code', '') }}">
                            </div>
                        </div>

                        <div class="form-grid-row">
                            <div class="gov-form-group">
                                <label class="gov-label" for="faculty_id">Academic Faculty</label>
                                <select class="gov-select" id="faculty_id" name="faculty_id" required>
                                    <option value="">-- Choose Program --</option>
                                    @foreach ($facs as $f)
                                        <option value="{{ $f->id }}" {{ request()->input('faculty_id') == $f->id ? 'selected' : '' }}>Faculty of {{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <span class="validation-error-msg" id="error_faculty_id"></span>
                            </div>
                            <div class="gov-form-group">
                                <label class="gov-label" for="id_document">ID / Passport Document <span style="color:#d4351c;">*</span></label>
                                <input class="gov-input" id="id_document" name="id_document" type="file" required accept=".pdf,.jpg,.jpeg,.png">
                                <span class="validation-error-msg" id="error_id_document"></span>
                            </div>
                        </div>

                        <div style="margin-top: 22px; padding-top: 18px; border-top: 1.5px solid #EBF3FC;">
                            <button type="button" class="gov-button" style="width:100%; border-radius:8px; padding: 13px; font-size:14px;" onclick="nextStep()">Continue to Payment &rarr;</button>
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
                                    <span class="validation-error-msg" id="error_card_holder"></span>
                                </div>
                                <div class="gov-form-group">
                                    <label class="gov-label" for="card_number" style="font-size:12px; margin-bottom:4px;">Card Number</label>
                                    <input class="gov-input" id="card_number" name="card_number" type="text" placeholder="4242 4242 4242 4242" maxlength="19">
                                    <span class="validation-error-msg" id="error_card_number"></span>
                                </div>
                            </div>
                            <div class="form-grid-row">
                                <div class="gov-form-group">
                                    <label class="gov-label" for="card_exp" style="font-size:12px; margin-bottom:4px;">Expiry Date</label>
                                    <input class="gov-input" id="card_exp" name="card_exp" type="text" placeholder="MM / YY" maxlength="7">
                                    <span class="validation-error-msg" id="error_card_exp"></span>
                                </div>
                                <div class="gov-form-group">
                                    <label class="gov-label" for="card_cvc" style="font-size:12px; margin-bottom:4px;">CVC</label>
                                    <input class="gov-input" id="card_cvc" name="card_cvc" type="text" placeholder="123" maxlength="4">
                                    <span class="validation-error-msg" id="error_card_cvc"></span>
                                </div>
                            </div>
                            <span class="gov-hint" style="font-size: 11px; margin-bottom: 20px;">🔒 Stripe Elements Secure Transaction. Standard TLS 1.3 encryption.</span>
                        </div>

                        <!-- REMITTANCE CASH NOTIFICATION -->
                        <div id="cashPaymentArea" style="display: none; background-color: #fafcff; padding: 20px; border-left: 4px solid #002F6C; border-radius: 6px; margin-bottom: 20px; border: 1.5px solid #EBF3FC; border-left-width: 5px;">
                            <h3 style="color:#002F6C; font-size:14px; margin-bottom:8px;">Remittance Validation Instructions</h3>
                            <p style="font-size:12px; color:var(--text-secondary); margin-bottom: 0; line-height: 1.5;">To complete your transaction, please contact your authorized regional representative or email accounts@cpduk.london to request a secure, single-use active recipient allocation token.</p>
                        </div>

                        <div class="btn-flex-row">
                            <button type="button" class="gov-button gov-button-secondary" style="flex: 1; border-radius:6px; padding: 13px;" onclick="prevStep()">&larr; Back</button>
                            <button type="submit" class="gov-button" id="submitBtn" style="flex: 2; border-radius:6px; padding: 13px; background-color:#00703c;">Complete & Pay £2,249.00</button>
                        </div>
                    </div>

                </form>

            @endif

        </div>
    </div>

    <!-- Right Panel: Decorative Illustration (Hidden on mobile) -->
    <div class="login-right-panel">
        <img class="login-illustration-img" src="{{ asset('assets/images/logo.png') }}" alt="CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD Logo">
    </div>

</div>
@endsection

@section('scripts_extra')
<script>
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

    function showFieldError(inputId, errorText) {
        var errorSpan = document.getElementById('error_' + inputId);
        if (errorSpan) {
            errorSpan.innerText = errorText;
            errorSpan.style.display = 'block';
        }
    }

    function clearFieldErrors() {
        document.querySelectorAll('.validation-error-msg').forEach(function(span) {
            span.style.display = 'none';
            span.innerText = '';
        });
    }

    function nextStep() {
        clearFieldErrors();
        
        // Validate Step 1 Inputs first
        var name = document.getElementById('full_name').value.trim();
        var dob = document.getElementById('dob').value;
        var email = document.getElementById('email').value.trim();
        var whatsapp = document.getElementById('whatsapp_number').value.trim();
        var faculty = document.getElementById('faculty_id').value;
        var hasError = false;

        if (!name) {
            showFieldError('full_name', 'Full Name is required.');
            hasError = true;
        }
        if (!dob) {
            showFieldError('dob', 'Date of Birth is required.');
            hasError = true;
        }
        if (!email) {
            showFieldError('email', 'Email address is required.');
            hasError = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFieldError('email', 'Please enter a valid email address.');
            hasError = true;
        }
        if (!whatsapp) {
            showFieldError('whatsapp_number', 'WhatsApp contact number is required.');
            hasError = true;
        }
        if (!faculty) {
            showFieldError('faculty_id', 'Please select your Academic Program Faculty.');
            hasError = true;
        }
        
        var idDoc = document.getElementById('id_document');
        if (idDoc && idDoc.files.length === 0) {
            showFieldError('id_document', 'Please upload a valid ID / Passport document (Max 25MB).');
            hasError = true;
        } else if (idDoc && idDoc.files[0] && idDoc.files[0].size > 26214400) {
            showFieldError('id_document', 'File size exceeds the 25MB limit.');
            hasError = true;
        }

        if (hasError) {
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

    // Enforce digits only for WhatsApp (plus optional leading +, spaces) and CVC (digits only)
    var whatsappInput = document.getElementById('whatsapp_number');
    if (whatsappInput) {
        whatsappInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9+\s-]/g, '');
        });
    }

    var cvcInput = document.getElementById('card_cvc');
    if (cvcInput) {
        cvcInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    }

    // Client side validation on submit
    function validateForm() {
        clearFieldErrors();
        
        var name = document.getElementById('full_name').value.trim();
        var dob = document.getElementById('dob').value;
        var email = document.getElementById('email').value.trim();
        var whatsapp = document.getElementById('whatsapp_number').value.trim();
        var faculty = document.getElementById('faculty_id').value;
        var payment = document.getElementById('payment_choice').value;
        var hasStep1Error = false;

        if (!name) {
            showFieldError('full_name', 'Full Name is required.');
            hasStep1Error = true;
        }
        if (!dob) {
            showFieldError('dob', 'Date of Birth is required.');
            hasStep1Error = true;
        }
        if (!email) {
            showFieldError('email', 'Email address is required.');
            hasStep1Error = true;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFieldError('email', 'Please enter a valid email address.');
            hasStep1Error = true;
        }
        if (!whatsapp) {
            showFieldError('whatsapp_number', 'WhatsApp contact number is required.');
            hasStep1Error = true;
        }
        if (!faculty) {
            showFieldError('faculty_id', 'Please select your Academic Program Faculty.');
            hasStep1Error = true;
        }
        
        var idDoc = document.getElementById('id_document');
        if (idDoc && idDoc.files.length === 0) {
            showFieldError('id_document', 'Please upload a valid ID / Passport document (Max 25MB).');
            hasStep1Error = true;
        } else if (idDoc && idDoc.files[0] && idDoc.files[0].size > 26214400) {
            showFieldError('id_document', 'File size exceeds the 25MB limit.');
            hasStep1Error = true;
        }

        if (hasStep1Error) {
            currentStep = 1;
            updateProgress();
            return false;
        }

        if (payment !== 'cash') {
            var holder = document.getElementById('card_holder').value.trim();
            var num = document.getElementById('card_number').value.replace(/\s+/g, '');
            var exp = document.getElementById('card_exp').value.replace(/\s+/g, '');
            var cvc = document.getElementById('card_cvc').value.trim();
            var hasCardError = false;

            if (!holder) {
                showFieldError('card_holder', 'Cardholder Name is required.');
                hasCardError = true;
            }
            if (num.length < 13) {
                showFieldError('card_number', 'Please enter a valid credit card number.');
                hasCardError = true;
            }
            if (exp.length < 5) {
                showFieldError('card_exp', 'Please enter a valid expiration date (MM / YY).');
                hasCardError = true;
            }
            if (cvc.length < 3) {
                showFieldError('card_cvc', 'Please enter a valid CVC.');
                hasCardError = true;
            }

            if (hasCardError) {
                return false;
            }
        }
        return true;
    }
</script>
@endsection
