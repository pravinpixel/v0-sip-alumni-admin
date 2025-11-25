<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title', config('app.name'))</title>
    <meta charset="utf-8" />
    <meta name="description" content="test" />
    <meta name="keywords" content="alumni" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="test" />
    <meta property="og:site_name" content="test" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    @section('style')
    <link href="{{ asset('css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/alumni/style.css') }}" />
    @show
</head>

<body id="kt_body" class="app-blank app-blank bgi-size-cover bgi-position-center bgi-no-repeat" style="background-color:#f8f8f8;">

    <div class="d-flex flex-column flex-root min-vh-100" id="kt_app_root">
        <div class="flex-grow-1 d-flex justify-content-center align-items-center">
            <div class="bg-body d-flex flex-column align-items-center rounded-4 w-450px p-6 log">
                <div class="w-100" style="max-width:450px;">

                    <img src="{{ asset('images/logo/sip_logo.png') }}" alt="logo" style="width:180px; height:auto;" class="d-block mx-auto">
                    
                    <!-- Change form action to send-otp -->
                    <form id="dynamic-form" method="post" action="{{ url('/send-otp') }}">
                        @csrf

                        <div class="text-center mt-4 mb-4">
                            <span id="invalid-credential-error" style="color:red"></span>
                        </div>

                        <div class="fv-row mb-4">
                            <label class="form-label text-dark fw-bold">Mobile Number</label>
                            <input type="text" name="number" placeholder="Enter 10-digit mobile number"
                                autocomplete="off" class="form-control bg-transparent" maxlength="10"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)">

                            <span class="field-error" id="number-error" style="color:red"></span>
                        </div>

                        <div class="d-grid mt-2">
                            <button type="button" id="dynamic-submit" class="btn" style="background-color:oklch(0.52 0.24 22); color:white;">
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

    @section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>

    <script>
        document.querySelector('input[name="number"]').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });

        document.addEventListener('DOMContentLoaded', function() {
            $('#dynamic-submit').on('click', function(e) {
                sendOtp(e);
            });
        });

        function sendOtp(event) {
            event.preventDefault();

            let formData = new FormData(document.getElementById('dynamic-form'));

            $("#invalid-credential-error").text("");
            $("#number-error").text("");

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
                            $("#invalid-credential-error").text(res.error);
                        }
                        return;
                    }

                    alert("Unexpected error occurred.");
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