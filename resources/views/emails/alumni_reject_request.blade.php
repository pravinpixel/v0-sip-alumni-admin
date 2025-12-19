@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>



<p>Your <strong>connection request</strong>  to
<strong>{{ $data['alumni_name']}}</strong> has been <strong>rejected</strong>.</p>

<p>You can explore and connect with other alumni members within the Alumni Portal.</p>



<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}</p>

@endsection
