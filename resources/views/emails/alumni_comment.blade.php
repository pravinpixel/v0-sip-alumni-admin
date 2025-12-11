@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>

<br>

<p>A new comment has been added to your post titled <strong>{{ $data['title']}}</strong>  in the alumni forum.
You can log in to your alumni portal to view and reply to the comment.</p>
<br>
<p>Stay engaged and continue the conversation with your alumni network!</p>

<br>

<p>Warm regards,<br>
<strong>Alumni Portal System</strong><br>
Phone: 044-42023331 / 42605609<br>
Email: {{ $data['support_email'] }}</p>

@endsection
