@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>



<p>You’ve received a <strong>new connection request</strong> from
<strong>{{ $data['requester']}}</strong> on the Alumni Portal.</p>


<p>You can log in to your account and navigate to the <strong>Connection</strong> module to 
<strong>accept or reject</strong> the request.</p>



<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}</p>

@endsection
