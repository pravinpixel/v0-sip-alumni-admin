@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>



<p>Your post titled <strong>{{ $data['title']}}</strong>  has been successfully deleted and moved to your 
<strong>Archive</strong> section.</p>

<p>You will no longer receive any updates, comments, or notifications related to this post.</p>



<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}</p>

@endsection
