@extends('emails.index')

@section('content')

<p>Dear {{ $data['name'] }},</p>

<p>Your alumni profile has been <strong>blocked</strong> by the Admin.</p>

<p>As a result:</p>
<ul>
    <li>Your posts will be removed from the platform.</li>
    <li>Your connections will no longer be visible.</li>
</ul>

<br>

<p><strong>Admin Remarks:</strong></p>
<p>{{ $data['remarks'] }}</p>

<br>

<p>For any clarification or assistance, please contact us:</p>

<p>
📞 Phone: 044-42023331 / 42605609<br>
📧 Email: {{ $data['support_email'] }}
</p>

<p>Thank you for your understanding.</p>

<br>

<p>Best regards,<br>
<strong>SIP Academy India Team</strong></p>

@endsection
