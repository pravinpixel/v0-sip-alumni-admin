@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>



<p>Your forum post titled <strong>{{ $data['title']}}</strong>  has been reviewed and  
<strong>rejected by the Admin </strong>.</p>

<p>Please find the remark provided by the Admin below:</p>


<p><strong>Admin Remark: </strong>{{ $data['remarks']}}</p>


<p>You may revise and resubmit a new post if needed.</p>


<p>Warm regards,
<strong>Alumni Portal System</strong>
Phone: 044-42023331 / 42605609
Email: {{ $data['support_email'] }}</p>

@endsection
