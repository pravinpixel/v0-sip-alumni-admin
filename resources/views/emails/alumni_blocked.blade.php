@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>

<p>Your alumni profile has been blocked by the Admin. As a result,
    your posts and connections will be removed from the platform.</p>


<p>Admin Remarks:</p> <span>{{ $data['remarks'] }}</span>



<p>For any clarification or assistance, please contact us at:</p>
<p>
📞 Phone: 044-42023331 / 42605609
📧 Email: {{ $data['support_email'] }}
</p>

<p>Thank you for your understanding.</p>


<p>Best regards,
SIP Academy India Team</p>

@endsection
