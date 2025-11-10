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
        /* Additional styling */
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
                <table style="border-collapse: collapse; width: 100%;">
                    <tr>
                        <th>Total Open Task</th>
                        <th>Total Closed Task</th>
                        <th>Average Star Rating</th>
                    </tr>
                    <tr>
                        <td style="border: 2px solid black; padding: 8px; text-align: left;">{{ $tasks['overall_completed_task'] ?? '' }}</td>
                        <td style="border: 2px solid black; padding: 8px; text-align: left;">{{ $tasks['overall_pending_task'] }}</td>
                        <td style="border: 2px solid black; padding: 8px; text-align: left;">{{ $tasks['overall_rating'] }}</td>
                    </tr>
                </table>

                <p>
                    Please make sure that you follow up with the Task assignee to complete the pending task as soon as possible.
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
                                        <img src="{{ $message->embed($tasks['signature_logo']) }}" alt="{{ config('app.name') }}" style="margin-right: 10px;" />
                                    </td>
                                    <td style="font-size: 18px; color: red; vertical-align: middle;">
                                        <strong>{{ $tasks['auth_user']->first_name ?? '' }} {{ $tasks['auth_user']->last_name ?? '' }}</strong>
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
