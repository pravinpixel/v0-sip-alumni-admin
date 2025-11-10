@php
$decryptedPassword = '';
if (isset($employee->hash_password)) {
    try {
        $decryptedPassword = Crypt::decryptString($employee->hash_password);
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        $decryptedPassword = '';
    }
}
    $ImageSrc = null;
    if (!empty($employee['signature_logo'])) {
        $path = parse_url($employee['signature_logo'], PHP_URL_PATH);
        $filename = basename($path);
        $imagePath = public_path('storage/signature/' . $filename);
        $imagePath = str_replace('/', DIRECTORY_SEPARATOR, $imagePath);
        if (file_exists($imagePath)) {
            $ImageSrc = $message->embed($imagePath);
        }
    }
    $logoSource = public_path('assets/images/logo/logo.png');
    $logoPath = str_replace('/', DIRECTORY_SEPARATOR, $logoSource);
    if (file_exists($logoPath)) {
        $LogoSrc = $message->embed($logoPath);
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
            padding-top: 20px;
            font-size: 12px;
            color: #999999;
        }

        .footer img {
            width: auto;
            max-height: 70px;
            height: 70px;
            display: block;
            padding: 15px;
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
            <img style="width: 100px;" src="{{ $LogoSrc }}" alt="{{ config('app.name') }}">
        </div>
        <div class="content">
            <p>Hi {{$employee->first_name ?? ''}} {{$employee->last_name ?? ''}},</p>
            <p>Welcome to Task Master, your ultimate task management tool!!</p>
            <p>🚀📈 We’re thrilled to have you with us. Your Task Master login credentials are given below.</p>
            <hr>
            <p><strong>Phone Number:</strong> {{ $employee->phone_number ?? '' }}</p>
            <p><strong>Password:</strong> {{ $decryptedPassword }}</p>
            <p><strong>Website:</strong> <a href="{{ config('app.task_url')}}">{{ config('app.task_url')}}</a></p>
            <hr>
            <p>Here’s a quick guide to get you started:</p>
            <ol>
                <li style="margin-top: 10px;margin-bottom:10px;">📝 <strong style="font-size: 16px;color: #666666;">Create Tasks with Ease:</strong>
                    <p style="display: inline;">Set up tasks with just a few clicks. Add deadlines, priorities, and notes to keep everything on track.</p>
                </li>

                <li style="margin-top: 10px;margin-bottom:10px;">🔔 <strong style="font-size: 16px;color: #666666;">Never Miss a Deadline:</strong>
                    <p style="display: inline;">Set Due Date reminders so you never miss a task.</p>
                </li>

                <li style="margin-top: 10px;margin-bottom:10px;">🌐 <strong style="font-size: 16px;color: #666666;">Collaborate with Ease:</strong>
                    <p style="display: inline;">Share tasks with your team. Work together seamlessly and communicate effectively.</p>
                </li>

                <li style="margin-top: 10px;margin-bottom:10px;">⭐️ <strong style="font-size: 16px;color: #666666;">Task Rating:</strong>
                    <p style="display: inline;">On the completion of a task you will have a power to give a star rating to the task assignee.</p>
                </li>
            </ol>
            <p>Need help? Our support team is here for you. Just reach out through the email on enquiry@ushafire.in</p>
            <p>😁 Happy Tasking </p>
        </div>
        <div class="footer">
            <div>
                <div>Wishing you a safe week ahead...</div>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" align="left">
                    <tr>
                        <td style="padding: 10px; text-align: left;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="left">
                                <tr>
                                    <td style="padding: 0; text-align: center;">
                                        <img src="{{ $ImageSrc }}" alt="{{ config('app.name') }}" style="margin-right: 10px;" />
                                    </td>
                                    @if(auth()->check())
                                    <td style="font-size: 18px; color: red; vertical-align: middle;">
                                        <strong>{{ auth()->user()->name ?? '' }}</strong>
                                    </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
</body>

</html>