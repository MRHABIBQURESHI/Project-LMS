@extends('lms.layout')

@section('title', 'Tuition Payment Gate - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')

@section('content')
<div class="gov-grid-row">
    <div class="gov-grid-column-two-thirds">

        @if ($success)
            <div class="gov-success-banner">
                <div class="gov-success-title">Payment Successful</div>
                <p>Your tuition fee of $450.00 has been verified. Your student account is now fully active.</p>
            </div>

            <h1>Your Credentials generated successfully</h1>
            <p>Please note down your login credentials below. You can use these to access the student portal.</p>

            <div style="background-color: #fafcff; padding: 25px; border-left: 5px solid #00703c; margin-bottom: 30px;">
                <p style="font-size: 19px; margin-bottom: 10px;"><strong>Email:</strong> <code>{{ $email }}</code></p>
                <p style="font-size: 19px; margin-bottom: 0;"><strong>Password:</strong> <code>{{ $password }}</code></p>
            </div>

            <a href="{{ route('lms.login') }}" class="gov-button">Proceed to Sign In</a>

        @else

            <h1>Choose Tuition Payment Method</h1>
            <p>To complete your enrollment and activate your student portal, you must pay the program tuition fee of <strong>$450.00</strong>.</p>

            @if (!empty($error))
                <div class="gov-error-banner">
                    <div class="gov-error-title">Payment error</div>
                    <p>{{ $error }}</p>
                </div>
            @endif

            <div class="gov-list-group">
                <div class="gov-list-row" style="flex-direction: column; align-items: flex-start; gap: 15px; padding: 20px 0;">
                    <div>
                        <span class="gov-list-key">Option A: Pay Full Tuition Online (Stripe Elements)</span>
                        <span class="gov-hint" style="margin-top: 5px;">Unlock your course catalog instantly. Processed securely via Stripe.</span>
                    </div>
                    <form action="{{ route('lms.checkout') }}" method="POST">
                        @csrf
                        <button type="submit" name="pay_stripe" class="gov-button">Pay $450.00 via Stripe</button>
                    </form>
                </div>

                <div class="gov-list-row" style="flex-direction: column; align-items: flex-start; gap: 15px; padding: 20px 0;">
                    <div>
                        <span class="gov-list-key">Option B: Manual Cash Remittance Transfer</span>
                        <span class="gov-hint" style="margin-top: 5px;">To complete your transaction, please contact your authorized regional representative or email accounts@cpduk.london to request a secure, single-use active recipient allocation token. Admin must manually verify your reference key.</span>
                    </div>
                    <a href="{{ route('lms.remittance') }}" class="gov-button gov-button-secondary">Submit Remittance Reference</a>
                </div>
            </div>

        @endif

    </div>
</div>
@endsection
