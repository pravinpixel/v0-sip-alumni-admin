@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>

<p>Your alumni profile has been <strong>unblocked by the admin</strong>.</p>

<p>You can now:</p>
<ul>
    <li>Log in to your alumni account</li>
    <li>Connect with other alumni</li>
    <li>Participate in forum discussions</li>
    <li>Share and engage with posts</li>
</ul>

<p>We’re glad to have you back in the community!</p>

<br>

<p>Warm regards,<br>
<strong>Alumni Portal System</strong><br>
Phone: 044-42023331 / 42605609<br>
Email: {{ $data['support_email'] }}</p>

@endsection
