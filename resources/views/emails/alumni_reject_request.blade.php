@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>

<br>

<p>Your <strong>connection request</strong>  to
<strong>{{ $data['alumni_name']}}</strong> has been <strong>rejected</strong>.</p>

<p>You can explore and connect with other alumni members within the Alumni Portal.</p>

<br>

<p>Warm regards,<br>
<strong>Alumni Portal System</strong><br>
Phone: 044-42023331 / 42605609<br>
Email: {{ $data['support_email'] }}</p>

@endsection
