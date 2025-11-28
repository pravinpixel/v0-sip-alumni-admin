@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>

<br>

<p>Your forum post titled <strong>{{ $data['title']}}</strong>  has been reviewed and  
<strong>rejected by the Admin </strong>.</p>

<p>Please find the remark provided by the Admin below:</p>

<br>
<p><strong>{{ $data['title']}} </strong>{{ $data['remarks']}}</p>

<br>
<p>You may revise and resubmit a new post if needed.</p>
<br>

<p>Warm regards,<br>
<strong>Alumni Portal System</strong><br>
Phone: 044-42023331 / 42605609<br>
Email: {{ $data['support_email'] }}</p>

@endsection
