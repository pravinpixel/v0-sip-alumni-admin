@extends('emails.index')

@section('content')

<p>Hi {{ $data['name'] }},</p>
<br>
<p>Congratulations! 🎉 Your registration on the <strong>SIP Abacus Alumni Portal</strong> has been successfully completed.</p>
<br>
<p>You can now <strong>log in using your registered mobile number</strong> to:</p>
<br>
<ul>
    <li>Connect with other alumni across regions.</li>
    <li>Explore and engage with posts shared by the alumni community.</li>
    <li>Build your professional and personal network within the SIP Abacus alumni group.</li>
</ul>
<br>
<p>
    👉 <strong>Login here:</strong>
    <a href="{{ $data['url'] }}">{{ $data['url'] }}</a>
</p>
<p>We’re excited to have you as part of our growing alumni family!</p>
<br>
<p>Warm regards,<br>
        <strong>Alumni Portal System</strong><br>
        Phone: 044-42023331 / 42605609<br>
        Email: {{ $data['support_email'] }}
    </p>

@endsection
