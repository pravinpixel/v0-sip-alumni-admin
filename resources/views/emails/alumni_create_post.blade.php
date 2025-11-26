@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>

<br>

<p>Your post titled <strong>{{ $data['title']}}</strong>  has been successfully created and submitted for admin approval.</p>

<p>Once your post is reviewed and approved by the admin, it will be published and visible to all alumni members. You’ll receive an email notification once it’s approved.</p>

<br>

<p>Warm regards,<br>
<strong>Alumni Portal System</strong><br>
Phone: 044-42023331 / 42605609<br>
Email: {{ $data['support_email'] }}</p>

@endsection
