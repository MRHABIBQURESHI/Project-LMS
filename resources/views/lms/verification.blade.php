@extends('lms.layout')

@section('title', 'Verifiable Certificate Registry - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')

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
            confirmButtonColor: '#222222'
        });
    };
</script>
@endsection

@section('content')
<div class="gov-grid-row">
    <div class="gov-grid-column-two-thirds" style="margin: 0 auto; float: none;">

        <h1>Registry Search & Credentials Verification</h1>
        <p style="font-size:16px; color: var(--text-secondary); margin-bottom: 30px;">Input the unique Verifiable Certificate Reference UID assigned by the CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD to authenticate student records.</p>

        @if (!empty($error))
            <div class="gov-error-banner" style="margin-bottom: 25px;">
                <div class="gov-error-title">Search Notification</div>
                <p>{{ $error }}</p>
            </div>
        @endif

        <!-- SEARCH SYSTEM FORM -->
        @if (!$search_performed || !$paid_successfully)
            <div class="verify-search-container">
                <form action="{{ route('lms.verification') }}" method="POST" onsubmit="return validateSearchForm()">
                    @csrf
                    <div class="gov-form-group">
                        <label class="gov-label" for="cert_uid">Certificate Serial ID or Centre ID</label>
                        <span class="gov-hint">Enter Certificate Serial ID or Centre ID (e.g. REG-LDN-2026-00001 or CTR-LDN-2026-00001)</span>
                        <input class="gov-input" id="cert_uid" name="cert_uid" type="text" style="max-width:100%; text-transform: uppercase;" required value="{{ $cert_uid }}">
                    </div>

                    <!-- CORPORATE DETAILS & STRIPE FORM -->
                    <div id="corporatePaywallArea" style="border-top: 1.5px solid var(--border-main); padding-top: 20px; margin-top: 20px;">
                        <h3 style="color:#222222; margin-bottom:15px;">Inquirer Details</h3>
                        
                        <div class="gov-form-group">
                            <label class="gov-label" for="company_name">Company / Organization Name</label>
                            <input class="gov-input" id="company_name" name="company_name" type="text" style="max-width:100%;" required value="{{ $company_name ?? '' }}">
                        </div>
                        <div class="gov-form-group">
                            <label class="gov-label" for="company_email">Business Rep Email Address</label>
                            <input class="gov-input" id="company_email" name="company_email" type="email" style="max-width:100%;" required value="{{ $company_email ?? '' }}">
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
                                    <input class="gov-input" id="card_holder" name="card_holder" type="text" placeholder="John Doe" required style="max-width:100%; height:36px; font-size:13px;">
                                </div>
                                <div>
                                    <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_number">Card Number</label>
                                    <input class="gov-input" id="card_number" name="card_number" type="text" placeholder="4242 4242 4242 4242" maxlength="19" required style="max-width:100%; height:36px; font-size:13px;">
                                </div>
                                <div class="card-row-split">
                                    <div>
                                        <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_exp">Expiry Date</label>
                                        <input class="gov-input" id="card_exp" name="card_exp" type="text" placeholder="MM / YY" maxlength="7" required style="max-width:100%; height:36px; font-size:13px;">
                                    </div>
                                    <div>
                                        <label class="gov-label" style="font-size:12px; margin-bottom:4px;" for="card_cvc">CVC</label>
                                        <input class="gov-input" id="card_cvc" name="card_cvc" type="text" placeholder="123" maxlength="4" required style="max-width:100%; height:36px; font-size:13px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="process_corporate_payment" value="1">
                    </div>

                    <button type="submit" class="gov-button" id="searchSubmitBtn" style="width:100%; border-radius:6px; padding: 12px 0; margin-top:15px; background-color: #222222;">
                        Pay £49.00 &amp; Perform Verification
                    </button>
                </form>
            </div>
        @endif

        <!-- DISPLAY RESULTS -->
        @if ($search_performed && $paid_successfully)
            
            <!-- A) CENTRE LOOKUP PAID RESULT -->
            @if ($result_type === 'centre' && $centre)
                <div class="db-card" style="border: 2px solid #EBF3FC; padding: 30px; border-radius: 8px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1.5px solid var(--border-main); padding-bottom:15px; margin-bottom:20px;">
                        <h2 style="margin:0; border-bottom:none; padding-bottom:0;">Verified Centre Approval Record</h2>
                        <span class="verify-badge-approved" style="background-color: #222222; color: white; padding: 4px 10px; border-radius: 4px; font-size:12px; font-weight:bold;">Active &amp; Approved</span>
                    </div>

                    <h3 style="color:#222222; margin-bottom:15px; border-bottom:1px solid var(--border-main); padding-bottom:5px;">1. Academic Centre Profile</h3>
                    <div class="gov-list-group" style="border-top:none; margin:0 0 25px 0;">
                        <div class="gov-list-row">
                            <span class="gov-list-key">Approved Centre Name</span>
                            <span class="gov-list-value" style="font-weight:600; font-size:15px; color:#222;">{{ $centre['name'] }}</span>
                        </div>
                        <div class="gov-list-row">
                            <span class="gov-list-key">Verified Centre ID</span>
                            <span class="gov-list-value" style="font-family:monospace; font-weight:600; color:#222222;">{{ $centre['rep_code'] }}</span>
                        </div>
                        <div class="gov-list-row">
                            <span class="gov-list-key">Contact Details</span>
                            <span class="gov-list-value" style="font-size:13px; color:#555;">{{ $centre['contact_info'] }}</span>
                        </div>
                    </div>

                    <h3 style="color:#222222; margin-bottom:15px; border-bottom:1px solid var(--border-main); padding-bottom:5px;">2. Registry Validation Status</h3>
                    <div class="gov-list-group" style="border-top:none; margin:0 0 25px 0;">
                        <div class="gov-list-row">
                            <span class="gov-list-key">Validation Status</span>
                            <span class="gov-list-value" style="font-weight:600; color:#00703c;">{{ strtoupper($centre['application_status'] ?? 'APPROVED') }}</span>
                        </div>
                        <div class="gov-list-row">
                            <span class="gov-list-key">Clearance Audit Status</span>
                            <span class="gov-list-value" style="font-weight:600; color:#00703c;">CLEARED</span>
                        </div>
                        <div class="gov-list-row">
                            <span class="gov-list-key">Linked Matriculations</span>
                            <span class="gov-list-value">{{ $centre['linked_students_count'] ?? 0 }} Students registered</span>
                        </div>
                    </div>

                    <div style="text-align: center; margin: 35px 0 20px 0; border-top: 1.5px solid var(--border-main); padding-top:25px; display:flex; gap:15px;">
                        <button onclick="window.print();" class="gov-button" style="flex:1; border-radius: 6px; text-align:center;">Print Centre Audit Report</button>
                        <a href="{{ route('lms.verification') }}" class="gov-button gov-button-secondary" style="border-radius:6px; text-decoration:none; text-align:center;">New Verification Inquire</a>
                    </div>
                </div>

            <!-- B) CERTIFICATE LOOKUP PAID RESULT -->
            @elseif ($result_type === 'certificate' && $certificate)
                <div class="db-card" style="border: 2px solid #EBF3FC; padding: 30px; border-radius: 8px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1.5px solid var(--border-main); padding-bottom:15px; margin-bottom:20px;">
                        <h2 style="margin:0; border-bottom:none; padding-bottom:0;">Verifiable Academic Record Profile</h2>
                        <span class="verify-badge-approved">Active &amp; Verified</span>
                    </div>

                    <h3 style="color:#222222; margin-bottom:15px; border-bottom:1px solid var(--border-main); padding-bottom:5px;">1. Candidate Personal Dossier</h3>
                    <div class="gov-list-group" style="border-top:none; margin:0 0 25px 0;">
                        <div class="gov-list-row">
                            <span class="gov-list-key">Candidate Full Name</span>
                            <span class="gov-list-value" style="font-weight:600; font-size:15px;">{{ $certificate['student_name'] }}</span>
                        </div>
                        <div class="gov-list-row">
                            <span class="gov-list-key">Date of Birth</span>
                            <span class="gov-list-value">{{ $certificate['student_dob'] }}</span>
                        </div>
                        <div class="gov-list-row">
                            <span class="gov-list-key">Institution Matric ID</span>
                            <span class="gov-list-value">LIAB-ST-{{ $certificate['student_id'] }}</span>
                        </div>
                    </div>

                    <h3 style="color:#222222; margin-bottom:15px; border-bottom:1px solid var(--border-main); padding-bottom:5px;">2. Program Credentials Details</h3>
                    <div class="gov-list-group" style="border-top:none; margin:0 0 25px 0;">
                        <div class="gov-list-row">
                            <span class="gov-list-key">Award Reference UID</span>
                            <span class="gov-list-value" style="font-family:monospace; font-weight:600; color:#222222;">{{ $certificate['certificate_uid'] }}</span>
                        </div>
                        <div class="gov-list-row">
                            <span class="gov-list-key">Faculty Program</span>
                            <span class="gov-list-value">Faculty of {{ $certificate['faculty_name'] }}</span>
                        </div>
                        <div class="gov-list-row">
                            <span class="gov-list-key">Award Issuance Date</span>
                            <span class="gov-list-value">{{ $certificate['issue_date'] }}</span>
                        </div>
                        <div class="gov-list-row">
                            <span class="gov-list-key">Final Evaluation score</span>
                            <span class="gov-list-value" style="font-weight:600; color:#00703c;">{{ $best_exam ? $best_exam['score'] . '%' : 'N/A' }}</span>
                        </div>
                    </div>

                    <h3 style="color:#222222; margin-bottom:15px; border-bottom:1px solid var(--border-main); padding-bottom:5px;">3. Coursework Modules Transcript</h3>
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
                            @if (empty($assignments))
                                <tr>
                                    <td colspan="4" class="gov-hint" style="text-align:center;">No coursework assignments completed.</td>
                                </tr>
                            @else
                                @foreach ($assignments as $a)
                                    <tr>
                                        <td><strong>Mod {{ $a['module_number'] }}</strong>: {{ $a['module_title'] }}</td>
                                        <td><a href="{{ asset($a['file_path']) }}" target="_blank" style="text-decoration: underline; font-weight:500;">{{ basename($a['file_path']) }}</a></td>
                                        <td>{{ $a['uploaded_at'] }}</td>
                                        <td>
                                            <span class="gov-tag gov-tag-green" style="font-size:11px; padding:2px 6px;">
                                                {{ $a['grade'] ?: 'Approved' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div style="text-align: center; margin: 35px 0 20px 0; border-top: 1.5px solid var(--border-main); padding-top:25px; display:flex; gap:15px;">
                        <a href="{{ route('lms.certificate', ['uid' => $certificate['certificate_uid']]) }}" target="_blank" class="gov-button" style="flex:1; border-radius: 6px; text-decoration:none; text-align:center;">Open Verifiable Certificate Layout &rarr;</a>
                        <a href="{{ route('lms.verification') }}" class="gov-button gov-button-secondary" style="border-radius:6px; text-decoration:none; text-align:center;">New Verification Inquire</a>
                    </div>
                </div>
            @endif

        @endif

    </div>
</div>
@endsection

@section('scripts_extra')
<script>
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
            alert('Please enter a valid Certificate Reference UID or Centre ID.');
            return false;
        }

        var comp = document.getElementById('company_name').value.trim();
        var email = document.getElementById('company_email').value.trim();
        var holder = document.getElementById('card_holder').value.trim();
        var num = document.getElementById('card_number').value.replace(/\s+/g, '');
        var exp = document.getElementById('card_exp').value.replace(/\s+/g, '');
        var cvc = document.getElementById('card_cvc').value.trim();

        if (!comp || !email) {
            alert('Please enter your credentials.');
            return false;
        }

        if (!holder || num.length < 13 || exp.length < 5 || cvc.length < 3) {
            alert('Please enter valid credit card credentials to process the £49 lookup fee.');
            return false;
        }
        return true;
    }
</script>
@endsection
