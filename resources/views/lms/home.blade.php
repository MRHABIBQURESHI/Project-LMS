@extends('lms.layout')

@section('title', 'CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')

@section('content')
<div style="margin-bottom: 45px; border-bottom: 1.5px solid #EBF3FC; padding-bottom: 30px;">
    <h1 style="color: #002F6C; margin-bottom: 15px;">CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</h1>
    <p style="font-size: 20px; color: #222; max-width: 800px; line-height: 1.6; font-weight: normal; margin-top: 0;">
        Welcome to the student services portal. Select a service below to manage study programs, access timed evaluations, or apply for onboarding.
    </p>
</div>

<!-- OFFICIAL CREDENTIAL VERIFICATION REGISTRY -->
<div style="background-color: #FFFFFF; border-radius: 12px; padding: 30px; margin: 30px 0; border: 1.5px solid #EBF3FC; box-shadow: 0 4px 12px rgba(235, 243, 252, 0.4);">
    <h2 style="color: #002F6C; margin-top: 0; margin-bottom: 12px; font-weight: bold; letter-spacing: -0.5px;">OFFICIAL CREDENTIAL VERIFICATION REGISTRY</h2>
    
    <div style="background-color: #fafbfe; border-left: 4px solid #f47738; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
        <p style="font-size: 13.5px; color: #222; margin: 0; line-height: 1.5;">
            <strong>Note:</strong> Accessing secure verification records requires a flat data-retrieval processing fee of <strong>£49.00 GBP</strong>. Direct credit card processing via Stripe sandbox is integrated below. Ensure you submit valid inquirer details during lookup.
        </p>
    </div>

    <form action="{{ route('lms.verification') }}" method="GET" style="display: flex; gap: 12px; max-width: 700px; flex-wrap: wrap;">
        <input class="gov-input" type="text" name="cert_uid" placeholder="Enter Certificate Serial ID or Centre ID (e.g. REG-LDN-2026-00001)" required style="flex-grow: 1; min-width: 280px; height: 44px; border: 1px solid #ccc; border-radius: 6px; padding: 10px 15px; font-size: 14px;">
        <button type="submit" class="gov-button" style="white-space: nowrap; margin-top: 0; padding: 12px 28px; height: 44px; border-radius: 6px; border-bottom: none; background-color: #002F6C;">Validate Serial ID</button>
    </form>
</div>

<!-- SERVICES PORTALS LIST -->
<h2 style="color: #002F6C; margin-top: 40px; margin-bottom: 20px; font-weight: bold;">Online Services & Portals</h2>
<div class="gov-list-group" style="background-color: #FFFFFF; border: 1px solid #EBF3FC; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(235, 243, 252, 0.3);">
    
    <div class="gov-list-row" style="padding: 20px; border-bottom: 1px solid #EBF3FC;">
        <div>
            <span class="gov-list-key" style="font-size: 16px; font-weight: 600; color: #222;">Candidate Registration & Assessment Registry Portal</span>
            <span class="gov-hint" style="margin-top: 5px; color: #555;">Register for qualifications, intake programs, and enter the assessment registry.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.register') }}" class="gov-button" style="background-color: #00703c; padding: 8px 16px; border-radius: 4px; font-size: 13px; text-decoration: none; border-bottom: none; display: inline-block;">Register now &rarr;</a>
        </div>
    </div>

    <div class="gov-list-row" style="padding: 20px; border-bottom: 1px solid #EBF3FC;">
        <div>
            <span class="gov-list-key" style="font-size: 16px; font-weight: 600; color: #222;">CPD UK London Academic Institute Dashboard</span>
            <span class="gov-hint" style="margin-top: 5px; color: #555;">Access portal, submit portfolios (25MB), or enter the assessment terminal.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.dashboard') }}" class="gov-button" style="background-color: #002F6C; padding: 8px 16px; border-radius: 4px; font-size: 13px; text-decoration: none; border-bottom: none; display: inline-block;">Access dashboard &rarr;</a>
        </div>
    </div>

    <div class="gov-list-row" style="padding: 20px; border-bottom: 1px solid #EBF3FC;">
        <div>
            <span class="gov-list-key" style="font-size: 16px; font-weight: 600; color: #222;">Institutional Affiliate & Centre Approval Portal</span>
            <span class="gov-hint" style="margin-top: 5px; color: #555;">Apply for institutional validation, clear academic center audits, or download your verified Centre Approval Certificate.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.contact') }}" class="gov-button" style="background-color: #4c4c4c; padding: 8px 16px; border-radius: 4px; font-size: 13px; text-decoration: none; border-bottom: none; display: inline-block;">Contact / Partner &rarr;</a>
        </div>
    </div>

    <div class="gov-list-row" style="padding: 20px;">
        <div>
            <span class="gov-list-key" style="font-size: 16px; font-weight: 600; color: #222;">Legal, Data Protection & Privacy Disclaimers</span>
            <span class="gov-hint" style="margin-top: 5px; color: #555;">Read our operational terms, fees schedule, and institutional regulations or data privacy policies.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.privacy') }}" class="gov-button gov-button-secondary" style="padding: 8px 16px; border-radius: 4px; font-size: 13px; text-decoration: none; display: inline-block;">View policies &rarr;</a>
        </div>
    </div>

</div>

<!-- ACADEMIC PROSPECTUS PAGES 3 & 4 SECTION -->
<div style="margin-top: 50px; background-color: #FFFFFF; border: 1px solid #EBF3FC; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(235, 243, 252, 0.3);">
    <h2 style="color: #002F6C; margin-top: 0; margin-bottom: 20px; font-weight: bold; border-bottom: 2px solid #002F6C; padding-bottom: 8px;">Prospectus Guidelines: Academic Regulations & Fees</h2>
    
    <div class="gov-grid-row" style="display: flex; gap: 30px; flex-wrap: wrap;">
        <div class="gov-grid-column-one-half" style="flex: 1; min-width: 300px;">
            <h3 style="color: #002F6C; font-size: 16px; font-weight: 600; margin-top: 0; margin-bottom: 12px;">Page 3: Scope of Board & Academic Evaluation Policy</h3>
            <ul style="font-size: 13.5px; color: #222; line-height: 1.8; margin-left: 20px; list-style-type: square; padding-left: 0;">
                <li style="margin-bottom: 10px;"><strong>Independent Registry:</strong> CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD operates strictly as an independent, private international certification award registry. We hold no direct affiliation, endorsement, or structural connection with any official UK government department or state regulatory authority.</li>
                <li style="margin-bottom: 10px;"><strong>Evaluation Mode:</strong> All curriculum metrics are portfolio-focused. Continuous evaluation assignments require plagiarism clearances below 15%.</li>
                <li style="margin-bottom: 10px;"><strong>Exam Environment:</strong> Final examinations are strictly timed (120 minutes) with browser monitoring enabled. Session exit will terminate results immediately.</li>
            </ul>
        </div>
        
        <div class="gov-grid-column-one-half" style="flex: 1; min-width: 300px; border-left: 1.5px solid #EBF3FC; padding-left: 30px;">
            <h3 style="color: #002F6C; font-size: 16px; font-weight: 600; margin-top: 0; margin-bottom: 12px;">Page 4: Tuition Clearance & Resit Scheduling</h3>
            <ul style="font-size: 13.5px; color: #222; line-height: 1.8; margin-left: 20px; list-style-type: square; padding-left: 0;">
                <li style="margin-bottom: 10px;"><strong>Program Fees Schedule:</strong> Complete tuition clearance is fixed at £2,249 full price or cleared in three sequential installments of £749 each.</li>
                <li style="margin-bottom: 10px;"><strong>Passing Threshold:</strong> Assessment validation is configured at 40%. A score below 40% constitutes a fail, triggering account lockout.</li>
                <li style="margin-bottom: 10px;"><strong>Resit Terminal Reactivation:</strong> Failed assessments require a Board Resit Fee of £229 to restore testing tokens. Retakes are managed under academic guidelines.</li>
            </ul>
        </div>
    </div>
</div>

<div style="background-color: #fafcff; padding: 25px; border-left: 5px solid #002F6C; margin-top: 40px; border-radius: 4px;">
    <h3 style="color: #002F6C; font-size: 17px; margin-top: 0; margin-bottom: 8px; font-weight: bold;">Verification Notice</h3>
    <p style="font-size: 13.5px; line-height: 1.5; color: #222; margin-bottom: 0;">
        All certificates issued by the CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD are verified independently by our registry department. Check with your representative to retrieve authentication tokens.
    </p>
</div>
@endsection
