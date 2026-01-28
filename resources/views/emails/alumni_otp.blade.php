@extends('emails.index')

@section('content')

<p class="gap">Dear {{ $data['name'] }},</p>



<p class="gap">Your One-Time Password (OTP) for accessing the <strong>Alumni Portal</strong> is: <strong>{{ $data['otp'] }}.</strong></p>
<p class="gap">This OTP is valid for the next 30 seconds. Please use this code to complete your login process.</p>



<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}</p>

@endsection
