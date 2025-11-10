@php
    $decryptedPassword = '';
    if (isset($password)) {
        try {
            $decryptedPassword = Crypt::decryptString($password);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Handle decryption failure
            // Log the error or take appropriate action
            $decryptedPassword = ''; // Default to an empty string or handle as needed
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
        }
        .header img {
            width: 100px;
        }
        .content {
            line-height: 1.6;
        }
        .content h1 {
            font-size: 24px;
            color: #333333;
        }
        .content p {
            font-size: 16px;
            color: #666666;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            font-size: 12px;
            color: #999999;
        }

        .btn {
            --bs-btn-padding-x: 1.5rem;
            --bs-btn-padding-y: 0.775rem;
            --bs-btn-font-size: 1.1rem;
            --bs-btn-font-weight: 500;
            --bs-btn-line-height: 1.5;
            --bs-btn-color: #181C32;
            --bs-btn-bg: transparent;
            --bs-btn-border-width: 1px;
            --bs-btn-border-color: transparent;
            --bs-btn-border-radius: 0.475rem;
            --bs-btn-hover-border-color: transparent;
            --bs-btn-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15), 0 1px 1px rgba(0, 0, 0, 0.075);
            --bs-btn-disabled-opacity: 0.65;
            --bs-btn-focus-box-shadow: 0 0 0 0.25rem rgba(var(--bs-btn-focus-shadow-rgb), .5);
            display: inline-block;
            padding: var(--bs-btn-padding-y) var(--bs-btn-padding-x);
            font-family: var(--bs-btn-font-family);
            font-size: var(--bs-btn-font-size);
            font-weight: var(--bs-btn-font-weight);
            line-height: var(--bs-btn-line-height);
            color: var(--bs-btn-color);
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            border: var(--bs-btn-border-width) solid var(--bs-btn-border-color);
            border-radius: var(--bs-btn-border-radius);
            background-color: var(--bs-btn-bg);
            box-shadow: var(--bs-btn-box-shadow);
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            border: 0;
            text-decoration: none;
        }
        .btn-primary {
            background: rgb(146, 56, 81);
            color: #ffffff;
        }
        .btn-group {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('images/logo/logo.png') }}" alt="{{ config('app.name') }}">
        </div>
        <div class="content">
            <h1>Account Information</h1>
            <p>Dear {{$name}},</p>
            <p>Your account information. Below are your credentials:</p>
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Password:</strong> {{ $decryptedPassword }}</p>
            
            <p>If you did not request a password reset, please ignore this email or contact our support team if you have any concerns.</p>
            <p>Thank you for using our services!</p>
        </div>
        <div class="footer">
            <p>Best regards,</p>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>



