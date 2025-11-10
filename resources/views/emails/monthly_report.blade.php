<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        #content {
            padding: 15px;
        }

        #content table {
            border-collapse: collapse;
        }

        #content th,
        #content td {
            border: 2px solid black;
            padding: 8px;
            text-align: left;
        }

        .warning {
            color: red;
        }

        .info {
            color: blue;
        }

        #footer {
            padding: 10px;
        }

        #footer img {
            height: 70px;
            padding: 15px;
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
            height: 70px;
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
            <img src="{{ $message->embed(asset('images/logo/logo.png')) }}" alt="{{ config('app.name') }}">
        </div>
        <div id="content">
            <div>
                <p>Hi {{$tasks['auth_user']->name ?? ''}},</p>
                <p>
                    Please find the attached monthly report, which includes an overview of tasks completed and the pending tasks that still need to be addressed.
                </p>
            </div>
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
                                        <img src="{{ $message->embed($tasks['setting']) }}" alt="{{ config('app.name') }}" style="margin-right: 10px;" />
                                    </td>
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
