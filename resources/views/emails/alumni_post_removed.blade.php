@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>



<p>Your forum post titled <strong>{{ $data['title']}}</strong>  has been  
<strong>removed by the Admin</strong> .</p>

<p>Please find the remark provided by the Admin below:</p>



<p><strong>Admin Remark:</strong> {{ $data['remarks']}}</p>



<p>Your post will no longer be visible to other alumni.</p>
<p>However, you can still view it under the <strong>Archive</strong> tab in your forum section.</p>


<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}</p>

@endsection
