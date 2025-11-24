<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Email' }}</title>
</head>
<body style="background:#f5f5f5; padding:30px; font-family: Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <!-- Email Container -->
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding:15px 0; border-bottom:1px solid #e5e5e5;">
                            <h2 style="margin:0; color:#333;">SIP Abacus Alumni Portal</h2>
                        </td>
                    </tr>

                    <!-- Dynamic Content -->
                    <tr>
                        <td style="padding:20px;">
                            @yield('content')
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
