@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>



<p>Your forum post titled <strong>{{ $data['title']}}</strong>  has been approved by the admin and is now published for all alumni members to view.</p>

<p>If any comments are added to your post, you will receive an email notification.</p>



<p>Thank you for sharing your thoughts with the alumni community!</p>



<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}</p>

@endsection
