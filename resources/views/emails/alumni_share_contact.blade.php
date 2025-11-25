@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>

<br>

<p>You’ve received a <strong>new connection request</strong> from
<strong>{{ $data['requester']}}</strong> on the Alumni Portal.</p>

<br>
<p>You can log in to your account and navigate to the <strong>Connection</strong> module to 
<strong>accept or reject</strong> the request.</p>

<br>

<p>Warm regards,<br>
<strong>Alumni Portal System</strong><br>
Phone: 044-42023331 / 42605609<br>
Email: {{ $data['support_email'] }}</p>

@endsection
