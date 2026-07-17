@extends('lms.layout')

@section('title', 'Privacy Policy & Data Protection - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')

@section('head_extra')
<style>
    .policy-tabs {
        display: flex;
        border-bottom: 2px solid var(--border-main);
        margin-bottom: 30px;
        gap: 10px;
    }
    .policy-tab {
        padding: 12px 20px;
        font-size: 15px;
        font-weight: 600;
        color: var(--text-secondary);
        text-decoration: none;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
    }
    .policy-tab:hover {
        color: var(--text-primary);
    }
    .policy-tab.active {
        color: #002F6C;
        border-bottom-color: #002F6C;
    }
    .policy-content-section {
        display: none;
    }
    .policy-content-section.active {
        display: block;
    }
</style>
@endsection

@section('content')
<div class="gov-grid-row">
    <div class="gov-grid-column-two-thirds" style="width: 100%; max-width: 900px; margin: 0 auto;">
        
        <h1>CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</h1>
        <p class="gov-hint">Legal, Regulations, and Policies Dashboard</p>

        <!-- Policy Tabs Navigation -->
        <div class="policy-tabs">
            <a onclick="switchPolicyTab('privacy')" id="tab-privacy" class="policy-tab active">Data Privacy Policy</a>
            <a onclick="switchPolicyTab('terms')" id="tab-terms" class="policy-tab">Terms & Conditions</a>
            <a onclick="switchPolicyTab('dispute')" id="tab-dispute" class="policy-tab">Complaints & Dispute Resolution</a>
        </div>

        <!-- 1. DATA PRIVACY POLICY SECTION -->
        <div id="section-privacy" class="policy-content-section active">
            <h2>Data Protection & Privacy Policy</h2>
            <p>The CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD is committed to protecting your privacy and ensuring the security of your personal records and academic data.</p>

            <h2>1. Information We Collect</h2>
            <p>When you register on our student intake portal, we collect the following datasets:</p>
            <ul style="list-style-type: square; margin-left: 20px; margin-bottom: 20px; font-size:16px; padding-left:10px; color: var(--text-primary);">
                <li style="margin-bottom:8px;">Full Name (for certificate profiling)</li>
                <li style="margin-bottom:8px;">Date of Birth (for verification authentication)</li>
                <li style="margin-bottom:8px;">Email Address (for transactional and credential notifications)</li>
                <li style="margin-bottom:8px;">WhatsApp Number (for immediate support communications)</li>
                <li style="margin-bottom:8px;">Consultant/Representative Code (for enrollment tracking)</li>
            </ul>

            <h2>2. How We Use Your Data</h2>
            <p>We process your data exclusively to:</p>
            <ul style="list-style-type: square; margin-left: 20px; margin-bottom: 20px; font-size:16px; padding-left:10px; color: var(--text-primary);">
                <li style="margin-bottom:8px;">Manage your student dashboard and course modules.</li>
                <li style="margin-bottom:8px;">Verify exam attempts and check anti-cheating logs.</li>
                <li style="margin-bottom:8px;">Generate and store PDF transcripts and verifiable certificates.</li>
                <li style="margin-bottom:8px;">Verify tuition fee payments and Western Union/Ria money order records.</li>
            </ul>

            <h2>3. Record Retention and Portability</h2>
            <p>Your academic records, assignment document uploads, and certification registry are stored securely in our databases. In compliance with data regulations, you have the right to request access to your records or ask for account closure. Note that closed accounts will delete issued certificate numbers from the public registry.</p>

            <h2>4. Data Security</h2>
            <p>We implement strict security protocols to prevent unauthorized access or file modification. All password strings are securely hashed. Uploaded assignment documents (up to 25MB) are stored in restricted local directory structures.</p>

            <h2>5. Contact Our Privacy Office</h2>
            <p>For questions or records access requests, contact our registry support desk at: <a href="mailto:privacy@cpduk.london">privacy@cpduk.london</a>.</p>

            <h2>6. Independent Registry Status Disclaimer</h2>
            <p style="background-color: var(--bg-secondary); padding: 15px; border-left: 5px solid #002F6C; font-size:15px; color: var(--text-primary); border-radius: 4px;">
                CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD operates strictly as an independent, private international certification award registry. We hold no direct affiliation, endorsement, or structural connection with any official UK government department or state regulatory authority.
            </p>
        </div>

        <!-- 2. TERMS & CONDITIONS SECTION -->
        <div id="section-terms" class="policy-content-section">
            <h2>Terms & Conditions</h2>
            <p>Welcome to the CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD. Please read these terms and conditions carefully before enrolling or using our learning management system.</p>

            <h2>1. Acceptance of Terms</h2>
            <p>By registering on this portal, you agree to comply with and be bound by the operating rules, assessment guidelines, and integrity rules set forth by the institute.</p>

            <h2>2. Institutional Certification Disclaimer</h2>
            <p style="background-color: var(--bg-secondary); padding: 15px; border-left: 5px solid #002F6C; font-size:15px; color: var(--text-primary); border-radius: 4px;">
                <strong>IMPORTANT NOTICE:</strong> CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD operates strictly as an independent, private international certification award registry. We hold no direct affiliation, endorsement, or structural connection with any official UK government department or state regulatory authority.
            </p>

            <h2>3. Program Fees and Remittance</h2>
            <p>All fees paid for tuition, enrollment processing, assessment gates, and certificate registry lookups are strictly non-refundable and non-transferable under any circumstances, without exception. Upon submission of payment via Stripe or manual cash remittance, the applicant waives all rights to cancellation, fee reversals, or refund demands. The student remains entirely responsible for providing valid transaction references via the manual gate. Administrative clearance takes up to 48 working hours.</p>

            <h2>4. Academic Evaluation Policy</h2>
            <p>Evaluations are timed and subject to strict anti-cheating monitoring. Any attempt to exit the exam screen, switch tabs, or use external resources will result in automatic session termination and score forfeiture.</p>

            <h2>5. Academic Integrity & Anti-Plagiarism</h2>
            <p>Students must submit original coursework. All assignment uploads must be compiled by the enrolled student. Plagiarism, copywriting, or submission of AI-generated content without appropriate references constitutes a violation of academic integrity and may lead to account suspension.</p>

            <h2>6. Modification of Terms</h2>
            <p>The institute reserves the right to modify these terms, course modules, and evaluation parameters at any time. Notice of significant changes will be updated directly on this page.</p>
        </div>

        <!-- 3. COMPLAINTS & DISPUTE RESOLUTION SECTION -->
        <div id="section-dispute" class="policy-content-section">
            <h2>Complaints & Dispute Resolution</h2>

            <h2>1. Lodging an Official Institutional Grievance</h2>
            <p>Candidates wishing to lodge a formal complaint regarding evaluation metrics, syllabus access tracking logs, or administrative processing paths must submit a signed case portfolio file directly to the compliance panel via email at <a href="mailto:compliance@cpduk.london">compliance@cpduk.london</a>.</p>
            <p>Anonymous reports, informal live-chat complaints, or messages sent via external channels will not enter the registry tracking archives.</p>

            <h2>2. Evaluation Appeals & Verification Timelines</h2>
            <p>If a student disputes a final transcript mark landing below our strict 50% database-wide passing threshold, they have exactly 7 calendar days from the conclusion of the 14-day hold review state to request an independent script-verification audit.</p>
            <p>Grievances regarding portfolio markings require a flat administrative reinvestigation processing fee of £99.00 GBP, securely cleared via our card gateway before the Academic Assessment Committee re-evaluates the script logs.</p>

            <h2>3. Finality of Financial Covenants & Refund Terms</h2>
            <p>The Complaints Department strictly enforces Section 4.3 of our institutional student handbook rules. All onboarding fees, course installment tokens, and exam resit entry costs remain 100% non-refundable and non-transferable under all operational circumstances, without exception.</p>
            <p>Filing a grievance or open complaint log does not pause, suspend, or change ongoing monthly installment liabilities or system accounting collection cycles.</p>

            <h2>4. Arbitration Boundaries & Jurisdiction Laws</h2>
            <p>The International Certification Award Board operates entirely as an independent international registry separate from standard state regulatory bodies.</p>
            <p>All formal dispute resolution, contract execution rules, and legal liabilities are subject to the exclusive jurisdiction of the courts of London, United Kingdom</p>
        </div>

    </div>
</div>
@endsection

@section('scripts_extra')
<script>
    function switchPolicyTab(tabId) {
        // Toggle tab navigation classes
        document.querySelectorAll('.policy-tab').forEach(function(el) {
            el.classList.remove('active');
        });
        document.getElementById('tab-' + tabId).classList.add('active');

        // Toggle content sections visibility
        document.querySelectorAll('.policy-content-section').forEach(function(el) {
            el.classList.remove('active');
        });
        document.getElementById('section-' + tabId).classList.add('active');
    }

    // Auto-select tab if hash parameter exists in URL
    document.addEventListener("DOMContentLoaded", function() {
        var hash = window.location.hash.replace('#', '');
        if (hash === 'privacy' || hash === 'terms' || hash === 'dispute') {
            switchPolicyTab(hash);
        }
    });
</script>
@endsection
