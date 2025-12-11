@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>
<br>
<p>Your alumni profile has been blocked by the Admin. As a result,
    your posts and connections will be removed from the platform.</p>
<br>

<p>Admin Remarks:</p> <span>{{ $data['remarks'] }}</span>

<br>

<p>For any clarification or assistance, please contact us at:</p>
<p>
📞 Phone: 044-42023331 / 42605609<br>
📧 Email: {{ $data['support_email'] }}
</p>
<br>
<p>Thank you for your understanding.</p>
<br>

<p>Best regards,<br>
SIP Academy India Team</p>

@endsection
