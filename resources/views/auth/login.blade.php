<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title', config('app.name'))</title>

    <meta charset="utf-8" />
    <meta name="description" content="test" />
    <meta name="keywords" content="admin" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="test" />
    <meta property="og:site_name" content="test" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo/favicon.png') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    @section('style')
    <link rel="stylesheet" href="{{ asset('plugins/global/plugins.bundle.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.bundle.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    @show
</head>

<body id="kt_body" class="app-blank app-blank bgi-size-cover bgi-position-center bgi-no-repeat">

    <div class="d-flex flex-column flex-root min-vh-100" id="kt_app_root">
        <!-- 🌟 Logo at the top -->
        <div class="text-center mt-10 mb-5">
            <img src="{{ asset('images/logo/sip_logo.png') }}" alt="logo" style="width:200px; height:auto;">
            <div class="text-gray-600 fw-semibold fs-6 mt-6">Alumni Admin Portal</div>
        </div>

        <!-- 🔲 Centered login box -->
        <div class="flex-grow-1 d-flex justify-content-center align-items-start">
            <div class="bg-body d-flex flex-column align-items-center rounded-4 w-md-500px p-10 log shadow-lg">
                <div class="w-100" style="max-width:400px;">
                    <h2 class="text-center mb-4 fw-bold" style="font-size:30px;">Admin Login</h2>
                    <p class="text-center mb-8">Enter your credentials to access the admin panel</p>

                    <form id="dynamic-form" method="post" action="{{ url('admin/login_check') }}">
                        @csrf

                        <div class="text-center mb-11">
                            <span id="invalid-credential-error" style="color:red"></span>
                        </div>

                        <div class="fv-row mb-6">
                            <label class="form-label text-dark fw-bold">Email Address</label>
                            <input type="text" name="email" placeholder="Email Address" autocomplete="off" class="form-control bg-transparent" />
                            <span class="field-error" id="email-error" style="color:red"></span>
                        </div>

                        <div class="fv-row mb-3">
                            <label class="form-label text-dark fw-bold">Password</label>
                            <input type="password" id="password-field" placeholder="Password" name="password" class="form-control bg-transparent" required />
                            <span toggle="#password-field" class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                            <span class="field-error" id="password-error" style="color:red"></span>
                        </div>

                        <div class="d-grid mt-10">
                            <button type="submit" id="dynamic-submit" class="btn" style="background-color:oklch(0.48 0.22 18.5); color:white;">
                                <span class="indicator-label">Log In</span>
                                <span class="indicator-progress">
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .toast-notification {
            position: fixed;
            bottom: 25px;
            right: 25px;
            padding: 14px 20px;
            color: #fff;
            font-size: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 999999 !important;
            /* IMPORTANT */
            animation: slideInUp 0.3s ease;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(40px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOutDown {
            from {
                transform: translateY(0);
                opacity: 1;
            }

            to {
                transform: translateY(40px);
                opacity: 0;
            }
        }
    </style>

    @section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#dynamic-submit').on('click', function(e) {
                loginChecking(e);
            });
        });

        function loginChecking(event) {
            event.preventDefault();
            let formData = new FormData(document.getElementById('dynamic-form'));

            $('#dynamic-submit .indicator-label').hide();
            $('#dynamic-submit .indicator-progress').show();

            $.ajax({
                url: $('#dynamic-form').attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response) {
                        showToast('Login successfully');
                        window.location.href = '{{ route("dashboard.view") }}';
                    }
                },
                error: function(response) {
                    if (response.status === 422 && response.responseJSON.error) {
                        var errors = response.responseJSON.error;
                        $('#dynamic-form').find(".field-error").text('');
                        if (typeof errors === 'object') {
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').text(value[0]);
                            });
                        } else {
                            $('#invalid-credential-error').text(errors);
                        }
                    } else {
                        alert(response.responseJSON?.error || 'An unexpected error occurred.');
                    }
                },
                complete: function() {
                    $('#dynamic-submit .indicator-label').show();
                    $('#dynamic-submit .indicator-progress').hide();
                }
            });
        }
    </script>
    @show

</body>

</html>