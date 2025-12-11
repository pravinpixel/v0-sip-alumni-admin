<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title', config('app.name'))</title>
    <meta charset="utf-8" />
    <meta name="description" content="alumni" />
    <meta name="keywords" content="alumni" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="alumni" />
    <meta property="og:site_name" content="alumni" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo/favicon.png') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @section('style')
    <link href="{{ asset('css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/alumni/style.css') }}" />
    @show

    <style>
        /* Responsive Styles for Login Page */
        /* Tablet */
        @media (max-width: 991px) {
            .mt-20 img {
                width: 350px !important;
                margin-top: 40px !important;
            }

            .log {
                width: 90% !important;
                max-width: 450px;
            }
        }

        /* Mobile */
        @media (max-width: 767px) {
            .mt-10 {
                padding-top: 20px !important;
            }

            .mt-10 img {
                width: 280px !important;
            }

            .log {
                width: calc(100% - 32px) !important;
                max-width: 450px !important;
                margin: 0 16px !important;
                padding: 24px !important;
            }

            .w-450px {
                width: 100% !important;
            }

            .form-label {
                font-size: 14px !important;
            }

            .form-control {
                font-size: 14px !important;
                padding: 10px 12px !important;
            }
        }

        /* Small mobile */
        @media (max-width: 480px) {
            .mt-10 {
                padding-top: 15px !important;
            }

            .mt-10 img {
                width: 240px !important;
            }

            .log {
                width: calc(100% - 24px) !important;
                max-width: 450px !important;
                margin: 0 12px !important;
                padding: 20px 16px !important;
            }

            .form-label {
                font-size: 13px !important;
            }

            .form-control {
                font-size: 13px !important;
                padding: 8px 10px !important;
            }

            button[type="submit"] {
                font-size: 14px !important;
                padding: 12px !important;
            }
        }
    </style>
</head>

<body id="kt_body" class="app-blank app-blank bgi-size-cover bgi-position-center bgi-no-repeat" style="background-color:#f8f8f8;">

    <div class="d-flex flex-column flex-root min-vh-6" id="kt_app_root">
        <div class="mt-10">
            <img src="{{ asset('images/logo/logo.png') }}" alt="logo" style="width:380px; height:auto;" class="d-block mx-auto">
        </div>
        <div class="d-flex justify-content-center align-items-center">
            <div class="bg-body d-flex flex-column align-items-center rounded-4 w-450px p-6 log">
                <div class="w-100" style="max-width:450px;">
                    
                    <!-- Change form action to send-otp -->
                    <form id="dynamic-form" method="post" action="{{ url('/send-otp') }}">
                        @csrf

                        <div class="text-center mt-4 mb-4">
                            <span id="invalid-credential-error" style="color:red"></span>
                        </div>

                        <div class="fv-row mb-4">
                            <p class="form-label text-dark fw-bold text-center fs-3 mb-4">Welcome to SIP Abacus Alumni Portal</p>
                            <label class="form-label text-dark fs-7">Please enter your registered mobile number to receive an OTP and continue.</label>
                            <input type="text" name="number" placeholder="Enter 10-digit mobile number"
                                autocomplete="off" class="form-control bg-transparent" maxlength="10"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)">

                            <span class="field-error" id="number-error" style="color:red"></span>
                        </div>

                        <div class="d-grid mt-2">
                            <button type="submit" id="dynamic-submit" class="btn" style="background-color:oklch(0.52 0.24 22); color:white;">
                                <span class="indicator-label">Send OTP</span>
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
    @include('alumni.login.footer')

    @section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('js/alumniCommon.js') }}"></script>

    <script>
        document.querySelector('input[name="number"]').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            // Clear error message when user starts typing
            $("#number-error").text("");
        });

        document.addEventListener('DOMContentLoaded', function() {
            $('#dynamic-submit').on('click', function(e) {
                sendOtp(e);
            });
        });

        function sendOtp(event) {
            event.preventDefault();

            // Clear previous errors
            $("#invalid-credential-error").text("");
            $("#number-error").text("");

            // Get mobile number value
            const mobileNumber = $('input[name="number"]').val().trim();

            // Validate mobile number
            if (mobileNumber === '') {
                $("#number-error").text("Please enter a valid 10-digit mobile number");
                return;
            }

            if (mobileNumber.length !== 10) {
                $("#number-error").text("Please enter a valid 10-digit mobile number");
                return;
            }

            if (!/^\d{10}$/.test(mobileNumber)) {
                $("#number-error").text("Please enter a valid 10-digit mobile number");
                return;
            }

            let formData = new FormData(document.getElementById('dynamic-form'));
            formData.append('is_login', 1);

            $('#dynamic-submit .indicator-label').hide();
            $('#dynamic-submit .indicator-progress').show();

            $.ajax({
                url: $('#dynamic-form').attr('action'), // Now points to /send-otp
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    }
                },

                error: function(xhr) {
                    console.log('Error:', xhr.responseJSON);

                    if (xhr.status === 422 || xhr.status === 400) {
                        let res = xhr.responseJSON;

                        if (res.errors) {
                            $.each(res.errors, function(key, val) {
                                $("#" + key + "-error").text(val[0]);
                            });
                        }

                        if (res.error) {
                            showToast(res.error, 'error');
                        }
                        return;
                    }

                    showToast("Unexpected error occurred.");
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