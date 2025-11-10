@php
$ImageSrc = null;
    if (!empty($task->signature_logo)) {
        $path = parse_url($task->signature_logo, PHP_URL_PATH);
        $filename = basename($path);
        $imagePath = public_path('storage/signature/' . $filename);
        $imagePath = str_replace('/', DIRECTORY_SEPARATOR, $imagePath);

        if (file_exists($imagePath)) {
            $ImageSrc = $message->embed($imagePath);
        }
    }
    $LogoSrc = null;
    $logoSource = public_path('assets/images/logo/logo.png');
    $logoPath = str_replace('/', DIRECTORY_SEPARATOR, $logoSource);
    if (file_exists($logoPath)) {
        $LogoSrc = $message->embed($logoPath);
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title', config('app.name') )</title>
    <style>
        .header {
            text-align: center;
            padding-bottom: 20px;
        }

        .header img {
            width: 100px;
        }
        #content {
            padding: 15px;
            line-height: 1.6;
        }

        #content p {
            font-size: 16px;
            color: #666666;
        }

        #content h1 {
            font-size: 24px;
            color: #333333;
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
            color: red !important;
        }

        .info {
            color: blue;
        }

        #footer {
            padding: 10px;
        }

        #footer img {
            height: 70px;
            width: auto;
            max-height: 70px;
            display: block;
            padding: 15px;
        }
    </style>

    @section('style')
    @show
</head>

<body>
    <div class="header">
        <img width="100px" style="width: 100px;" src="{{ $LogoSrc }}" alt="{{ config('app.name') }}">
    </div>
    <div id="content">
        @yield('content')
    </div>
    <div id="footer">
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
                                <td style="font-size: 18px; color: red; vertical-align: middle;">
                                    <strong>{{ $task->auth_user->first_name ?? ''}} {{$task->auth_user->last_name ?? ''}}</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>


        </div>
        
    </div>
</body>

</html>
