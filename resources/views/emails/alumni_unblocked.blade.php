@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>

<br>

<p>Your alumni profile has been <strong>unblocked by the admin</strong>.You can now log in to your account,
 connect with other alumni, and participate in forum discussions and posts.</p>

<br>
<p>We’re glad to have you back in the community!</p>

<br>

<p>Warm regards,<br>
<strong>Alumni Portal System</strong><br>
Phone: 044-42023331 / 42605609<br>
Email: {{ $data['support_email'] }}</p>

@endsection
