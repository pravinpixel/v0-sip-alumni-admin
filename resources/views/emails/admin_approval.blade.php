@extends('emails.index')

@section('content')

<p>Dear Admin,</p>

<p>A <strong>new forum post</strong> has been created by <strong>{{ $data['name'] }}</strong>.</p>

<p>Kindly review and approve the post to make it visible to all alumni members.</p>



<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}
</p>

@endsection
