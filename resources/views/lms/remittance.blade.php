@extends('lms.layout')

@section('title', 'Remittance Verification Gate - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')

@section('content')
<div class="gov-grid-row">
    <div class="gov-grid-column-two-thirds">

        @if ($success)
            <div class="gov-success-banner">
                <div class="gov-success-title">Reference Registered</div>
                <p>Your manual cash remittance details have been queued for admin verification. Your account status is now pending manual unlock.</p>
            </div>

            <h1>Awaiting Verification</h1>
            <p>The administrative assessors will verify your transaction reference code against our ledger records. Please allow up to 48 hours for verification.</p>

            @if (!empty($generated_password))
                <div style="background-color: #fafcff; padding: 25px; border-left: 5px solid #002F6C; margin-bottom: 30px;">
                    <h3 style="color:#002F6C; margin-bottom:10px;">Your Portal Login Credentials</h3>
                    <p style="font-size: 16px; margin-bottom: 5px;">Write these down to login once approved:</p>
                    <p style="font-size: 19px; margin-bottom: 10px;"><strong>Email:</strong> <code>{{ $linked_email }}</code></p>
                    <p style="font-size: 19px; margin-bottom: 0;"><strong>Password:</strong> <code>{{ $generated_password }}</code></p>
                </div>
            @endif

            <a href="{{ route('lms.login') }}" class="gov-button">Go to Sign In</a>

        @else

            <h1>Manual Cash Remittance Gate</h1>
            <p>To complete your transaction, please contact your authorized regional representative or email accounts@cpduk.london to request a secure, single-use active recipient allocation token.</p>
            <p>Once you have sent the funds, please submit the reference key, sender name, and transfer service provider details below to queue your student account for manual verification and unlock.</p>

            @if (!empty($error))
                <div class="gov-error-banner">
                    <div class="gov-error-title">There is a problem</div>
                    <p>{{ $error }}</p>
                </div>
            @endif

            <form action="{{ route('lms.remittance') }}" method="POST" novalidate>
                @csrf
                
                @if ($user_id === 0)
                    <div class="gov-form-group">
                        <label class="gov-label" for="email">Student Registered Email</label>
                        <span class="gov-hint">Enter the email you registered on Page 2 (Registration portal).</span>
                        <input class="gov-input" id="email" name="email" type="email" required value="{{ request()->input('email', '') }}">
                    </div>
                @else
                    <div style="background-color: #fafcff; padding: 15px; border-left: 5px solid #002F6C; margin-bottom: 25px;">
                        Linking payment reference for student: <strong>{{ $linked_email }}</strong>
                    </div>
                @endif

                <div class="gov-form-group">
                    <label class="gov-label" for="sender_name">Sender name</label>
                    <span class="gov-hint">Enter the full name of the person who sent the funds.</span>
                    <input class="gov-input" id="sender_name" name="sender_name" type="text" required value="{{ request()->input('sender_name', '') }}">
                </div>

                <div class="gov-form-group">
                    <label class="gov-label" for="method">Transfer Service Provider</label>
                    <span class="gov-hint">Select the cash remittance company.</span>
                    <select class="gov-select" id="method" name="method" required>
                        <option value="">-- Choose Provider --</option>
                        <option value="western_union" {{ request()->input('method') === 'western_union' ? 'selected' : '' }}>Western Union</option>
                        <option value="ria" {{ request()->input('method') === 'ria' ? 'selected' : '' }}>Ria Money Transfer</option>
                        <option value="worldremit" {{ request()->input('method') === 'worldremit' ? 'selected' : '' }}>WorldRemit</option>
                    </select>
                </div>

                <div class="gov-form-group">
                    <label class="gov-label" for="transaction_ref">Transaction Reference ID (MTCN)</label>
                    <span class="gov-hint">Enter the 8 or 10-digit number from your remittance slip.</span>
                    <input class="gov-input" id="transaction_ref" name="transaction_ref" type="text" required value="{{ request()->input('transaction_ref', '') }}">
                </div>

                <div class="gov-form-group">
                    <label class="gov-label" for="amount">Remittance Amount ($)</label>
                    <span class="gov-hint">The default program registration fee is $450.00.</span>
                    <input class="gov-input" id="amount" name="amount" type="number" step="0.01" min="1.00" value="450.00" required>
                </div>

                <button type="submit" class="gov-button">Submit remittance codes</button>
            </form>

        @endif

    </div>
</div>
@endsection
