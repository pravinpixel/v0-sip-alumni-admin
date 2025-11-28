@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>

<br>

<p>Your forum post titled <strong>{{ $data['title']}}</strong>  has been  
<strong>removed by the Admin</strong> .</p>

<p>Please find the remark provided by the Admin below:</p>

<br>

<p><strong>Admin Remark:</strong> {{ $data['remarks']}}</p>

<br>

<p>Your post will no longer be visible to other alumni.</p>
<p>However, you can still view it under the <strong>Archive</strong> tab in your forum section.</p>
<br>

<p>Warm regards,<br>
<strong>Alumni Portal System</strong><br>
Phone: 044-42023331 / 42605609<br>
Email: {{ $data['support_email'] }}</p>

@endsection
