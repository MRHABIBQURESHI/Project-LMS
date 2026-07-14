@extends('lms.layout')

@section('title', 'CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD - Portal Home')

@section('content')
<div style="margin-bottom: 45px;">
    <h1>CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</h1>
    <p style="font-size: 22px; color: #555; max-width: 750px; line-height: 1.6;">
        Welcome to the student services portal. Select a service below to manage study programs, access timed evaluations, or apply for onboarding.
    </p>
</div>

<!-- OFFICIAL CREDENTIAL VERIFICATION REGISTRY -->
<div style="background-color: var(--bg-card); border-radius: 12px; padding: 30px; margin: 30px 0; border: 1.5px solid #EBF3FC; box-shadow: var(--shadow-card);">
    <h2 style="color: #002F6C; margin-top: 0; margin-bottom: 10px;">OFFICIAL CREDENTIAL VERIFICATION REGISTRY</h2>
    <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">
        Verify the authenticity of qualifications, transcripts, and certificates issued by the registry.
    </p>
    <form action="{{ route('lms.verification') }}" method="GET" style="display: flex; gap: 12px; max-width: 600px; flex-wrap: wrap;">
        <input class="gov-input" type="text" name="cert_uid" placeholder="Enter Certificate Serial ID or Centre ID (e.g. REG-LDN-2026-00001 or CTR-LDN-2026-00001)" required style="flex-grow: 1; min-width: 250px; height: 42px;">
        <button type="submit" class="gov-button" style="white-space: nowrap; margin-top: 0; padding: 10px 24px; height: 42px; border-radius: 6px; border-bottom: none;">Validate Serial ID</button>
    </form>
</div>

<h2>Online Services & Portals</h2>
<div class="gov-list-group">
    
    <div class="gov-list-row">
        <div>
            <span class="gov-list-key">Candidate Registration & Assessment Registry Portal</span>
            <span class="gov-hint" style="margin-top: 5px;">Register for qualifications, intake programs, and enter the assessment registry.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.register') }}">Register now</a>
        </div>
    </div>

    <div class="gov-list-row">
        <div>
            <span class="gov-list-key">CPD UK London Academic Institute Dashboard</span>
            <span class="gov-hint" style="margin-top: 5px;">Access portal, submit portfolios (25MB), or enter exam</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.dashboard') }}">Access dashboard</a>
        </div>
    </div>

    <div class="gov-list-row">
        <div>
            <span class="gov-list-key">Institutional Affiliate & Centre Approval Portal</span>
            <span class="gov-hint" style="margin-top: 5px;">Apply for institutional validation, clear academic center audits, or download your verified Centre Approval Certificate.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.contact') }}">Contact / Partner</a>
        </div>
    </div>

    <div class="gov-list-row">
        <div>
            <span class="gov-list-key">Legal, Data Protection & Privacy Disclaimers</span>
            <span class="gov-hint" style="margin-top: 5px;">Read our operational terms, fees schedule, and institutional regulations or data privacy policies.</span>
        </div>
        <div class="gov-list-action">
            <a href="{{ route('lms.privacy') }}">View policies</a>
        </div>
    </div>

</div>

<div style="background-color: #fafcff; padding: 25px; border-left: 5px solid #002F6C; margin-top: 50px;">
    <h3 style="color: #002F6C; font-size: 19px; margin-bottom: 10px;">Verification Notice</h3>
    <p style="font-size: 15px; line-height: 1.5; color: #555; margin-bottom: 0;">
        All certificates issued by the CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD are verified independently by our registry department. Check with your representative to retrieve authentication tokens.
    </p>
</div>
@endsection
