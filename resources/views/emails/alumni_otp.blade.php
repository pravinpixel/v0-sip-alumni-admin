@extends('emails.index')

@section('content')
<style>
    .gap {
        margin-bottom: 10px;
    }
</style>

<p class="gap">Welcome to the <strong>SIP Abacus Alumni Network</strong></p>

<p class="gap">Your One-Time Password (OTP) for SIP Abacus Alumni Portal verification is:</p>

<p>🔐 {{ $data['otp'] }}</p>
<p class="gap">⏳ Valid for 10 minutes</p>

<p class="gap">Please enter this code to continue.</p>

<p class="gap">For your security, do not share this OTP with anyone.</p>

<p class="gap">If you did not request this verification, please ignore this email or contact us for assistance.</p>

<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}</p>

@endsection
