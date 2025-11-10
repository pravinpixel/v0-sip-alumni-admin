<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
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
        .code-container {
            text-align: center;
            margin: 20px 0;
        }
        .code {
            display: inline-block;
            background-color: #88344c;
            color: #ffffff;
            padding: 10px 20px;
            font-size: 18px;
            border: 1px solid #88344c;
            border-radius: 5px;
            letter-spacing: 2px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <img src="{{ asset('images/logo/logo.png') }}" alt="{{ config('app.name') }}">
    </div>
    <div class="content">
        <h1>Password Reset Request</h1>
        <p>Dear {{$name}},</p>
        <p>We received a request to reset the password for your account associated with this email address. If you made this request, please use the verification code below to reset your password.</p>
        <div class="code-container">
            <span class="code">{{$token}}</span>
        </div>
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
