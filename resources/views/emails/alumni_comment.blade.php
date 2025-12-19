@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>



<p>A new comment has been added to your post titled <strong>{{ $data['title']}}</strong>  in the alumni forum.
You can log in to your alumni portal to view and reply to the comment.</p>

<p>Stay engaged and continue the conversation with your alumni network!</p>



<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}</p>

@endsection
