@extends('lms.layout')

@section('title', 'Terms & Conditions - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD')

@section('content')
<div class="gov-grid-row">
    <div class="gov-grid-column-two-thirds">
        <p>Redirecting to our Legal, Regulations, and Policies Dashboard...</p>
        <script>
            window.location.replace("{{ route('lms.privacy') }}#terms");
        </script>
    </div>
</div>
@endsection
