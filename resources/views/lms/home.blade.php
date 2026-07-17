@extends('lms.layout')

@section('title', 'CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')

@section('content')
<div style="margin-bottom: 30px; border-bottom: 1.5px solid #EBF3FC; padding-bottom: 20px;">
    <h1 style="color: #222222; margin-bottom: 0; font-weight: bold;">CPD UK LONDON INTERNATIONAL CERTIFICATION AWARD BOARD</h1>
</div>

<!-- ====================================================================== -->
<!-- HOW IT WORKS: 3-STEP JOURNEY -->
<!-- ====================================================================== -->
<div style="background-color: var(--bg-card); border-radius: 12px; padding: 30px; margin: 30px 0; border: 1.5px solid var(--border-main); box-shadow: var(--shadow-card);">
    <h2 style="color: var(--text-heading); margin-top: 0; margin-bottom: 10px; font-weight: bold; letter-spacing: -0.5px; border-bottom: none; padding-bottom: 0; display: flex; align-items: center; gap: 8px;">
        <span>🧭</span> How It Works: Your 3-Step Fast-Track Journey
    </h2>
    <p style="color: var(--text-secondary); font-size: 15px; margin-bottom: 25px; line-height: 1.6;">
        Our streamlined academic framework is engineered to accelerate your transition into higher education through three definitive stages.
    </p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        <!-- Step 1 -->
        <div style="background-color: var(--bg-secondary); border: 1.5px solid var(--border-main); border-radius: 8px; padding: 20px; display: flex; flex-direction: column;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <span style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background-color: var(--btn-primary-bg); color: var(--btn-primary-text); border-radius: 50%; font-weight: bold; font-size: 14px;">1</span>
                <h3 style="margin: 0; font-size: 16px; color: var(--text-heading); font-weight: bold;">Registration & Phase I Matriculation</h3>
            </div>
            <ul style="margin: 0; padding-left: 20px; color: var(--text-primary); font-size: 13.5px; line-height: 1.6; flex-grow: 1;">
                <li style="margin-bottom: 8px;">Select your chosen academic faculty path.</li>
                <li style="margin-bottom: 8px;">Complete your onboarding documentation.</li>
                <li style="margin-bottom: 8px;">Gain immediate portal access to begin the 270-Hour Universal Foundation Framework.</li>
                <li style="margin-bottom: 0;">Complete Semester 1 core prerequisites.</li>
            </ul>
        </div>

        <!-- Step 2 -->
        <div style="background-color: var(--bg-secondary); border: 1.5px solid var(--border-main); border-radius: 8px; padding: 20px; display: flex; flex-direction: column;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <span style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background-color: var(--btn-primary-bg); color: var(--btn-primary-text); border-radius: 50%; font-weight: bold; font-size: 14px;">2</span>
                <h3 style="margin: 0; font-size: 16px; color: var(--text-heading); font-weight: bold;">Specialized Faculty Progression</h3>
            </div>
            <ul style="margin: 0; padding-left: 20px; color: var(--text-primary); font-size: 13.5px; line-height: 1.6; flex-grow: 1;">
                <li style="margin-bottom: 8px;">Advance into Phase II of your academic architectural journey.</li>
                <li style="margin-bottom: 8px;">Unlock your chosen specialized faculty syllabus totaling 870 hours across three definitive units.</li>
                <li style="margin-bottom: 0;">Study at your own pace via our secure, sophisticated distance-learning environment.</li>
            </ul>
        </div>

        <!-- Step 3 -->
        <div style="background-color: var(--bg-secondary); border: 1.5px solid var(--border-main); border-radius: 8px; padding: 20px; display: flex; flex-direction: column;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <span style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background-color: var(--btn-primary-bg); color: var(--btn-primary-text); border-radius: 50%; font-weight: bold; font-size: 14px;">3</span>
                <h3 style="margin: 0; font-size: 16px; color: var(--text-heading); font-weight: bold;">Examination & Placement Services</h3>
            </div>
            <ul style="margin: 0; padding-left: 20px; color: var(--text-primary); font-size: 13.5px; line-height: 1.6; flex-grow: 1;">
                <li style="margin-bottom: 8px;">Sit your secure, monitored online examinations.</li>
                <li style="margin-bottom: 8px;">Receive your independent academic transcript showing exactly 1,140 completed hours.</li>
                <li style="margin-bottom: 8px;">Leverage your verified credentials to bypass traditional on-campus Foundation Year programs.</li>
                <li style="margin-bottom: 0;">Upgrade your path with our dedicated placement team for just £249 to secure direct entry into our trusted partner universities across the United Kingdom.</li>
            </ul>
        </div>
    </div>
</div>

<!-- OFFICIAL CREDENTIAL VERIFICATION REGISTRY -->
<div style="background-color: #FFFFFF; border-radius: 12px; padding: 30px; margin: 30px 0; border: 1.5px solid #EBF3FC; box-shadow: 0 4px 12px rgba(235, 243, 252, 0.4);">
    <h2 style="color: #222222; margin-top: 0; margin-bottom: 12px; font-weight: bold; letter-spacing: -0.5px; border-bottom: none; padding-bottom: 0;">OFFICIAL CREDENTIAL VERIFICATION REGISTRY</h2>
    <p style="color: #555555; font-size: 15px; margin-bottom: 15px;">
        Verify the authenticity of qualifications, transcripts, and certificates issued by the registry.
    </p>
    
    <div style="background-color: #EBF3FC; border-left: 4px solid #222222; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
        <p style="font-size: 13.5px; color: #222222; margin: 0; line-height: 1.5;">
            *Note: Accessing secure verification records requires a flat data-retrieval processing fee of £49.00 GBP. Direct credit card processing via secure Stripe gateway integration is activated below. Ensure you submit valid inquirer details during lookup.
        </p>
    </div>

    <!-- Search Form that Triggers Stripe Gateway Modal -->
    <form onsubmit="event.preventDefault(); openStripeModal();" style="display: flex; flex-direction: column; gap: 10px; max-width: 700px;">
        <label for="homepage_serial_id" style="font-size: 14px; font-weight: 600; color: #222222;">Enter Certificate Serial ID or Centre ID (e.g. REG-LDN-2026-00001 or CTR-LDN-2026-00001)</label>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <input class="gov-input" type="text" id="homepage_serial_id" name="serial_id" placeholder="REG-LDN-2026-00001 or CTR-LDN-2026-00001" required style="flex-grow: 1; min-width: 280px; height: 44px; border: 1.5px solid #EBF3FC; border-radius: 6px; padding: 10px 15px; font-size: 14px; background-color: #FFFFFF; color: #222222;">
            <button type="submit" class="gov-button" style="white-space: nowrap; margin-top: 0; padding: 12px 28px; height: 44px; border-radius: 6px; border-bottom: none; background-color: #222222; color: #ffffff; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">Validate Serial ID</button>
        </div>
    </form>
</div>

<!-- SERVICES PORTALS LIST -->
<p style="font-size: 18px; color: var(--text-heading); max-width: 800px; line-height: 1.6; font-weight: normal; margin: 40px 0 15px 0;">
    Welcome to the student services portal. Select service below to manage study programs, access timed evaluations, or apply for onboarding.
</p>
<h2 style="color: #222222; margin-top: 20px; margin-bottom: 20px; font-weight: bold; border-bottom: 2px solid #EBF3FC; padding-bottom: 8px;">Online Services & Portals</h2>
<div class="gov-list-group" style="background-color: #FFFFFF; border: 1.5px solid #EBF3FC; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(235, 243, 252, 0.3);">
    
    <div class="gov-list-row" style="padding: 20px; border-bottom: 1.5px solid #EBF3FC; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="flex: 1; min-width: 280px;">
            <span class="gov-list-key" style="font-size: 16px; font-weight: 600; color: #222222; display: block;">Candidate Registration & Assessment Registry Portal</span>
            <span class="gov-hint" style="margin-top: 5px; color: #555555; display: block; font-size: 13.5px;">Register for qualifications, intake programs, and enter the assessment registry.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.register') }}" class="gov-button" style="background-color: #222222; color: #FFFFFF !important; padding: 10px 20px; border-radius: 6px; font-size: 13.5px; text-decoration: none; display: inline-block; font-weight: 600; transition: all 0.2s ease;">Register now</a>
        </div>
    </div>

    <div class="gov-list-row" style="padding: 20px; border-bottom: 1.5px solid #EBF3FC; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="flex: 1; min-width: 280px;">
            <span class="gov-list-key" style="font-size: 16px; font-weight: 600; color: #222222; display: block;">CPD UK London Academic Institute Dashboard</span>
            <span class="gov-hint" style="margin-top: 5px; color: #555555; display: block; font-size: 13.5px;">Access portal, submit portfolios (25MB), or enter exam.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.dashboard') }}" class="gov-button" style="background-color: #222222; color: #FFFFFF !important; padding: 10px 20px; border-radius: 6px; font-size: 13.5px; text-decoration: none; display: inline-block; font-weight: 600; transition: all 0.2s ease;">Access dashboard</a>
        </div>
    </div>

    <div class="gov-list-row" style="padding: 20px; border-bottom: 1.5px solid #EBF3FC; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="flex: 1; min-width: 280px;">
            <span class="gov-list-key" style="font-size: 16px; font-weight: 600; color: #222222; display: block;">Institutional Affiliate & Centre Approval Portal</span>
            <span class="gov-hint" style="margin-top: 5px; color: #555555; display: block; font-size: 13.5px;">Apply for institutional validation, clear academic center audits, or download your verified Centre Approval Certificate.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.contact') }}" class="gov-button" style="background-color: #222222; color: #FFFFFF !important; padding: 10px 20px; border-radius: 6px; font-size: 13.5px; text-decoration: none; display: inline-block; font-weight: 600; transition: all 0.2s ease;">Contact / Partner</a>
        </div>
    </div>

    <div class="gov-list-row" style="padding: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="flex: 1; min-width: 280px;">
            <span class="gov-list-key" style="font-size: 16px; font-weight: 600; color: #222222; display: block;">Legal, Data Protection & Privacy Disclaimers</span>
            <span class="gov-hint" style="margin-top: 5px; color: #555555; display: block; font-size: 13.5px;">Read our operational terms, fees schedule, and institutional regulations or data privacy policies.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.privacy') }}" class="gov-button" style="background-color: transparent; border: 1.5px solid #EBF3FC; color: #222222 !important; padding: 10px 20px; border-radius: 6px; font-size: 13.5px; text-decoration: none; display: inline-block; font-weight: 600; transition: all 0.2s ease;">View policies</a>
        </div>
    </div>

</div>

<!-- ACADEMIC PROSPECTUS PAGES 3 & 4 SECTION -->
<div style="margin-top: 50px; background-color: #FFFFFF; border: 1.5px solid #EBF3FC; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(235, 243, 252, 0.3);">
    <h2 style="color: #222222; margin-top: 0; margin-bottom: 20px; font-weight: bold; border-bottom: 2px solid #EBF3FC; padding-bottom: 8px;">Prospectus Guidelines: Academic Regulations & Fees</h2>
    
    <div class="gov-grid-row" style="display: flex; gap: 30px; flex-wrap: wrap;">
        <div class="gov-grid-column-one-half" style="flex: 1; min-width: 300px;">
            <h3 style="color: #222222; font-size: 16px; font-weight: 600; margin-top: 0; margin-bottom: 12px;">Page 3: Scope of Board & Academic Evaluation Policy</h3>
            <ul style="font-size: 13.5px; color: #222222; line-height: 1.8; margin-left: 20px; list-style-type: square; padding-left: 0;">
                <li style="margin-bottom: 10px;"><strong>Independent Registry:</strong> CPD UK LONDON INTERNATIONAL CERTIFICATION AWARD BOARD operates strictly as an independent, private international certification award registry. We hold no direct affiliation, endorsement, or structural connection with any official UK government department or state regulatory authority.</li>
                <li style="margin-bottom: 10px;"><strong>Evaluation Mode:</strong> All curriculum metrics are portfolio-focused. Continuous evaluation assignments require plagiarism clearances below 15%.</li>
                <li style="margin-bottom: 10px;"><strong>Exam Environment:</strong> Final examinations are strictly timed (120 minutes) with browser monitoring enabled. Session exit will terminate results immediately.</li>
            </ul>
        </div>
        
        <div class="gov-grid-column-one-half" style="flex: 1; min-width: 300px; border-left: 1.5px solid #EBF3FC; padding-left: 30px;">
            <h3 style="color: #222222; font-size: 16px; font-weight: 600; margin-top: 0; margin-bottom: 12px;">Page 4: Tuition Clearance & Resit Scheduling</h3>
            <ul style="font-size: 13.5px; color: #222222; line-height: 1.8; margin-left: 20px; list-style-type: square; padding-left: 0;">
                <li style="margin-bottom: 10px;"><strong>Program Fees Schedule:</strong> Complete tuition clearance is fixed at £2,249 full price or cleared in three sequential installments starting with £751 followed by two payments of £749 each.</li>
                <li style="margin-bottom: 10px;"><strong>Passing Threshold:</strong> Assessment validation is configured at 50%. A score below 50% constitutes a fail, triggering account lockout.</li>
                <li style="margin-bottom: 10px;"><strong>Resit Terminal Reactivation:</strong> Failed assessments require a Board Resit Fee of £229 to restore testing tokens. Retakes are managed under academic guidelines.</li>
            </ul>
            <div style="font-size: 12px; color: #555555; margin-top: 15px;">*Optional Partner University Placement Concierge package available post-graduation for £249.</div>
        </div>
    </div>
</div>

<div style="background-color: #EBF3FC; padding: 25px; border-left: 5px solid #222222; margin-top: 40px; border-radius: 4px;">
    <h3 style="color: #222222; font-size: 17px; margin-top: 0; margin-bottom: 8px; font-weight: bold;">Verification Notice</h3>
    <p style="font-size: 13.5px; line-height: 1.5; color: #222222; margin-bottom: 0;">
        All certificates issued by the CPD UK LONDON INTERNATIONAL CERTIFICATION AWARD BOARD are verified independently by our registry department. Check with your representative to retrieve authentication tokens.
    </p>
</div>

<!-- STRIPE MODAL GATEWAY -->
<div id="stripeModal" class="stripe-modal-overlay" style="display: none;">
    <div class="stripe-modal-content">
        <div class="stripe-modal-header">
            <h3>🔒 Secure Stripe Payment Gateway</h3>
            <button type="button" class="stripe-modal-close" onclick="closeStripeModal()">&times;</button>
        </div>
        <form id="stripePaymentForm" action="{{ route('lms.verification') }}" method="POST" onsubmit="return validateModalForm()">
            @csrf
            <input type="hidden" id="modal_cert_uid" name="cert_uid">
            
            <div class="stripe-modal-body">
                <div style="background-color: #EBF3FC; border-left: 4px solid #222222; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    <p style="font-size: 13px; color: #222222; margin: 0; line-height: 1.5;">
                        <strong>Note:</strong> Retrieving registry records for <strong id="display_serial_id"></strong> requires a flat processing fee of <strong>£49.00 GBP</strong>. Direct credit card processing via secure Stripe gateway is activated.
                    </p>
                </div>
                
                <h4 style="color: #222222; margin-bottom: 12px; font-size: 14px; font-weight: 700; border-bottom: 1.5px solid #EBF3FC; padding-bottom: 5px;">Inquirer Details</h4>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222222; margin-bottom: 5px;" for="modal_company_name">Company / Organization Name</label>
                    <input class="gov-input" id="modal_company_name" name="company_name" type="text" style="width: 100%; height: 38px; border: 1.5px solid #EBF3FC; border-radius: 6px; padding: 8px 12px; font-size: 13.5px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222222; margin-bottom: 5px;" for="modal_company_email">Business Rep Email Address</label>
                    <input class="gov-input" id="modal_company_email" name="company_email" type="email" style="width: 100%; height: 38px; border: 1.5px solid #EBF3FC; border-radius: 6px; padding: 8px 12px; font-size: 13.5px;" required>
                </div>

                <h4 style="color: #222222; margin-top: 20px; margin-bottom: 12px; font-size: 14px; font-weight: 700; border-bottom: 1.5px solid #EBF3FC; padding-bottom: 5px;">Credit Card Details</h4>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222222; margin-bottom: 5px;" for="modal_card_holder">Name on Card</label>
                    <input class="gov-input" id="modal_card_holder" name="card_holder" type="text" placeholder="John Doe" style="width: 100%; height: 38px; border: 1.5px solid #EBF3FC; border-radius: 6px; padding: 8px 12px; font-size: 13.5px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #222222; margin-bottom: 5px;" for="modal_card_number">Card Number</label>
                    <input class="gov-input" id="modal_card_number" name="card_number" type="text" placeholder="4242 4242 4242 4242" maxlength="19" style="width: 100%; height: 38px; border: 1.5px solid #EBF3FC; border-radius: 6px; padding: 8px 12px; font-size: 13.5px;" required>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 1; margin-bottom: 15px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222222; margin-bottom: 5px;" for="modal_card_exp">Expiry Date</label>
                        <input class="gov-input" id="modal_card_exp" name="card_exp" type="text" placeholder="MM / YY" maxlength="7" style="width: 100%; height: 38px; border: 1.5px solid #EBF3FC; border-radius: 6px; padding: 8px 12px; font-size: 13.5px;" required>
                    </div>
                    <div style="flex: 1; margin-bottom: 15px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; color: #222222; margin-bottom: 5px;" for="modal_card_cvc">CVC</label>
                        <input class="gov-input" id="modal_card_cvc" name="card_cvc" type="text" placeholder="123" maxlength="4" style="width: 100%; height: 38px; border: 1.5px solid #EBF3FC; border-radius: 6px; padding: 8px 12px; font-size: 13.5px;" required>
                    </div>
                </div>
            </div>
            <div class="stripe-modal-footer">
                <button type="button" class="stripe-btn-cancel" onclick="closeStripeModal()">Cancel</button>
                <button type="submit" class="stripe-btn-submit">Pay £49.00 &amp; Verify</button>
            </div>
        </form>
    </div>
</div>
    <!-- ====================================================================== -->
<!-- PREMIUM DYNAMIC FOOTER & APP PROMOTION SECTION -->
<!-- ====================================================================== -->
<!-- 1. APP STORE & GOOGLE PLAY DOWNLOAD PROMOTION CARD -->
<div style="background: linear-gradient(135deg, #0b1a30 0%, #152b4f 100%); color: #ffffff; border-radius: 12px; padding: 35px 40px; margin-top: 60px; margin-bottom: 40px; box-shadow: 0 8px 24px rgba(11, 26, 48, 0.12); border: 1px solid rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: space-between; gap: 40px; flex-wrap: wrap;">
    <!-- Left Ratings and Info -->
    <div style="flex: 2; min-width: 300px; text-align: left;">
        <h3 style="color: #ffffff; font-size: 20px; font-weight: 700; margin: 0 0 10px 0; letter-spacing: -0.3px;">Get the CPD UK London Mobile App</h3>
        <p style="color: #a0aec0; font-size: 13.5px; line-height: 1.5; margin: 0 0 15px 0;">Access your coursework modules, evaluations, and certifications on the go. Complete lessons offline and sync automatically.</p>
        
        <!-- Ratings in Image 2 -->
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: #cbd5e0;">
                <svg width="14" height="14" fill="#ffd700" viewBox="0 0 24 24"><path d="M12 .587l3.668 7.431 8.2 1.191-5.934 5.787 1.4 8.168L12 18.896l-7.334 3.857 1.4-8.168L.132 9.209l8.2-1.191L12 .587z"/></svg>
                <span><strong>4.9 stars</strong> ~4M ratings on App Store</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: #cbd5e0;">
                <svg width="14" height="14" fill="#ffd700" viewBox="0 0 24 24"><path d="M12 .587l3.668 7.431 8.2 1.191-5.934 5.787 1.4 8.168L12 18.896l-7.334 3.857 1.4-8.168L.132 9.209l8.2-1.191L12 .587z"/></svg>
                <span><strong>4.8 stars</strong> &gt;1.3M ratings on Google Play</span>
            </div>
        </div>
    </div>

    <!-- Right Download Badges -->
    <div style="flex: 1.5; min-width: 360px; display: flex; gap: 15px; flex-wrap: wrap; justify-content: flex-end; align-items: center;">
        <!-- Google Play Button -->
        <a href="https://play.google.com/store" target="_blank" style="display: flex; align-items: center; gap: 10px; background-color: #000000; border: 1px solid #2d3748; border-radius: 8px; padding: 10px 20px; color: #ffffff; text-decoration: none; transition: background-color 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.15); min-width: 170px;" onmouseover="this.style.backgroundColor='#1a202c';" onmouseout="this.style.backgroundColor='#000000';">
            <svg viewBox="0 0 36 36" width="22" height="22" style="display: block; flex-shrink: 0;">
                <path fill="#ea4335" d="M33.4 16.9L7.1.3C6.3-.2 5 .1 4.5.9c-.2.3-.3.7-.3 1.1v32c0 1 .8 1.8 1.8 1.8.4 0 .8-.1 1.1-.3l26.3-16.6c.8-.5 1-1.6.5-2.4-.2-.3-.5-.5-.8-.5z"/>
                <path fill="#fabc05" d="M4.2 2L18 18 4.2 34"/>
                <path fill="#34a853" d="M4.2 34l22.6-14.3L18 18"/>
                <path fill="#4285f4" d="M4.2 2L18 18 26.8 3.7"/>
            </svg>
            <div style="text-align: left;">
                <span style="font-size: 8.5px; text-transform: uppercase; display: block; opacity: 0.7; font-weight: 500; letter-spacing: 0.5px;">Get it on</span>
                <span style="font-size: 14.5px; font-weight: bold; display: block; line-height: 1.1;">Google Play</span>
            </div>
        </a>

        <!-- App Store Button -->
        <a href="https://www.apple.com/app-store/" target="_blank" style="display: flex; align-items: center; gap: 10px; background-color: #000000; border: 1px solid #2d3748; border-radius: 8px; padding: 10px 20px; color: #ffffff; text-decoration: none; transition: background-color 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.15); min-width: 170px;" onmouseover="this.style.backgroundColor='#1a202c';" onmouseout="this.style.backgroundColor='#000000';">
            <svg viewBox="0 0 384 512" width="22" height="22" fill="#ffffff" style="display: block; flex-shrink: 0;">
                <path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 47.5-24.4 76.5 26.9 2.4 51.2-16 68.3-38.9z"/>
            </svg>
            <div style="text-align: left;">
                <span style="font-size: 8.5px; text-transform: uppercase; display: block; opacity: 0.7; font-weight: 500; letter-spacing: 0.5px;">Download on the</span>
                <span style="font-size: 14.5px; font-weight: bold; display: block; line-height: 1.1;">App Store</span>
            </div>
        </a>
    </div>
</div>


@endsection

@section('scripts_extra')
<script>
    function openStripeModal() {
        var serialId = document.getElementById('homepage_serial_id').value.trim();
        if (!serialId) {
            alert('Please enter Certificate Serial ID or Centre ID.');
            return;
        }
        document.getElementById('modal_cert_uid').value = serialId;
        document.getElementById('display_serial_id').innerText = serialId;
        document.getElementById('stripeModal').style.display = 'flex';
    }

    // Expiration card inputs helper
    var expInput = document.getElementById('modal_card_exp');
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
    var cardInput = document.getElementById('modal_card_number');
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

    function closeStripeModal() {
        document.getElementById('stripeModal').style.display = 'none';
    }

    function validateModalForm() {
        var comp = document.getElementById('modal_company_name').value.trim();
        var email = document.getElementById('modal_company_email').value.trim();
        var holder = document.getElementById('modal_card_holder').value.trim();
        var num = document.getElementById('modal_card_number').value.replace(/\s+/g, '');
        var exp = document.getElementById('modal_card_exp').value.replace(/\s+/g, '');
        var cvc = document.getElementById('modal_card_cvc').value.trim();

        if (!comp || !email) {
            alert('Please enter inquirer details.');
            return false;
        }

        if (!holder || num.length < 13 || exp.length < 5 || cvc.length < 3) {
            alert('Please enter valid credit card details.');
            return false;
        }
        return true;
    }
</script>
@endsection

