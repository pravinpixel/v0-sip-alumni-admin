@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>



<p>Your alumni profile has been <strong>unblocked by the admin</strong>.You can now log in to your account,
 connect with other alumni, and participate in forum discussions and posts.</p>


<p>We’re glad to have you back in the community!</p>



<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}</p>

@endsection
