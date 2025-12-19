@extends('emails.index')

@section('content')

<p>Dear Admin,</p>

<p>The alumni <strong>{{ $data['alumni_name']}}</strong> has deleted their forum post titled <strong>{{ $data['title'] }}</strong>.</p>

<p>The post status has been updated to <strong>Post Deleted</strong> in the admin panel.</p>
<p>You can still view the post details and its last recorded comments for reference.</p>



<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}
</p>

@endsection
