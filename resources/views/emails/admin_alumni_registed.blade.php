@extends('emails.index')

@section('content')

<p>Dear Admin,</p>

<p>A new alumni has successfully registered on the <strong>Alumni Portal</strong>.  
The profile is now active and visible in the directory.</p>

<br>

<p><strong>Alumni Details:</strong></p>

<p><strong>Name:</strong> {{ $data['name'] }}</p>
<p><strong>Email:</strong> {{ $data['email'] }}</p>
<p><strong>Mobile Number:</strong> {{ $data['mobile'] }}</p>
<p><strong>Year of Passing:</strong> {{ $data['year_of_passing'] }}</p>
<p><strong>Department:</strong> {{ $data['department'] }}</p>

<br>

<p>Warm regards,<br>
<strong>Alumni Portal System</strong><br>
Phone: 044-42023331 / 42605609<br>
Email: {{ $data['support_email'] }}
</p>

@endsection
