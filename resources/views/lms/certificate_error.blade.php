@extends('lms.layout')

@section('title', 'Credential Error - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')

@section('header')
<!-- Hide header matching design -->
@endsection

@section('content')
<div style="background:var(--bg-secondary); display:flex; align-items:center; justify-content:center; height:90vh; padding-bottom:0; width: 100%;">
    <div class="db-card" style="max-width:500px; padding:30px; text-align:center; box-shadow:var(--shadow-modal); background:#fff; border-radius: 8px; border: 1.5px solid #EBF3FC;">
        <h1 style="color:#d4351c; font-size:24px; margin-top:0;">Verification Failed</h1>
        <p style="font-size:15px; color:#555; line-height:1.5; margin-bottom: 20px;">{{ $error }}</p>
        <a href="{{ route('lms.home') }}" class="gov-button" style="margin-top:15px; border-radius:6px; display:inline-block; text-decoration:none;">Return to Home Portal</a>
    </div>
</div>
@endsection
