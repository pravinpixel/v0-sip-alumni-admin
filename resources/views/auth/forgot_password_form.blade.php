<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>@yield('title', config('app.name') )</title>

    <meta charset="utf-8" />
    <meta name="description" content="test" />
    <meta name="keywords" content="admin" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="test" />
    <meta property="og:site_name" content="test" />
    <link rel="shortcut icon" href="" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @section('style')
    <link rel="stylesheet" href="{{ asset('plugins/global/plugins.bundle.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.bundle.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    @show

    <style>

        .input-group .toggle-password {
                position: absolute;
                top: 50%;
                right: 10px;
                /* Adjust as needed */
                transform: translateY(-50%);
                cursor: pointer;
                font-size: 1.2em;
                /* Adjust for icon size */
                z-index: 999;
            }

        @media (max-width: 768px) {
            .input-group .toggle-password {
                font-size: 1em;
                /* Adjust size for smaller screens */
                right: 5px;
                /* Adjust for smaller screens */
            }
        }

        @media (max-width: 480px) {
            .input-group .toggle-password {
                font-size: 0.9em;
                /* Further adjust for very small screens */
                right: 3px;
                /* Adjust as needed */
            }
        }
    </style>

</head>

<body id="kt_app_body" data-kt-app-layout="light-sidebar" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

            <body id="kt_body" class="app-blank app-blank bgi-size-cover bgi-position-center bgi-no-repeat">
                <div class="d-flex flex-column flex-root top" id="kt_app_root">
                    <div class="d-flex flex-column flex-lg-row flex-column-fluid ">
                        <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 ">
                            <div class="bg-body d-flex flex-center rounded-4 w-md-600px p-10 log" style="padding-bottom: 10% !important;">
                                <div class="w-md-400px ">

                                    <form id="dynamic-form" method="post" action="{{ url('reset-forgot-password-submit') }}">
                                        @csrf
                                        <img style="height: 100%;width:30%;margin-left:35%;margin-top:4%;" src="{{ asset('images/logo/logo.png') }}" alt="logo">
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <input type="hidden" name="email" value="{{ $email }}">
                                        <div class="text-center mb-10">
                                                <!--begin::Title-->
                                                    <h2 class="text-gray-900 fw-bolder mb-3">
                                                            Reset Forgot Password
                                                    </h2>
                                                <!--end::Title-->
                                        </div>
                                        <!-- <div class="fv-row mb-8">
                                            <label class=" form-label">Email:</label>
                                            <div class="input-group">
                                                <input type="text" placeholder="Email" name="email" autocomplete="off" class=" form-control bg-transparent" value="{{old('email')}}" required />
                                            </div>
                                            <span class="field-error" id="email-error" style="color:red"></span>
                                        </div> -->

                                        <div class="fv-row mb-8">
                                            <label for="name" class="form-label required">Password:</label>
                                                <div class="input-group">
                                                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password">
                                                    <i class="toggle-password fa fa-eye-slash" data-toggle="password"></i>
                                                </div>  
                                                <span class="field-error" id="password-error" style="color:red"></span>
                                            
                                        </div>

                                        <div class="fv-row mb-8">
                                            <label for="name" class="form-label required">Confirm Password:</label>
                                                <div class="input-group">
                                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Enter Confirm Password">
                                                    <i class="toggle-password fa fa-eye-slash" data-toggle="confirm_password"></i>
                                                </div>  
                                                <span class="field-error" id="confirm_password-error" style="color:red"></span>
                                            
                                        </div>

                                        <div class="d-grid mb-10">
                                            <button type="button" class="btn btn-success btn-submit" id="dynamic-submit">
                                                <span class="indicator-label">Submit</span>
                                                <span class="indicator-progress">Please wait...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                            </button>
                                        </div>
                                        
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
            @section('script')
            <script>
                var defaultThemeMode = "light";
                var themeMode;
                if (document.documentElement) {
                    if (document.documentElement.hasAttribute("data-theme-mode")) {
                        themeMode = document.documentElement.getAttribute("data-theme-mode");
                    } else {
                        if (localStorage.getItem("data-theme") !== null) {
                            themeMode = localStorage.getItem("data-theme");
                        } else {
                            themeMode = defaultThemeMode;
                        }
                    }
                    if (themeMode === "system") {
                        themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
                    }
                    document.documentElement.setAttribute("data-theme", themeMode);
                }


                // Password Eye Toggle Script
                document.querySelectorAll('.toggle-password').forEach(button => {
                    button.addEventListener('click', function() {
                        let input = document.getElementById(this.getAttribute('data-toggle'));
                        let icon = this.querySelector('i');

                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    });
                });

                document.getElementById('dynamic-submit').addEventListener('click', function(e) {
                    // Prevent the default form submission or button behavior if necessary
                    e.preventDefault();
                    resetPassword(e);
                });


                function resetPassword(event) {
                    event.preventDefault();
                    let formData = new FormData(document.getElementById('dynamic-form'));
                    // Perform the AJAX request
                    $.ajax({
                            url: $('#dynamic-form').attr('action'), // Get the form action URL
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token if needed
                            },
                            success: function(response) {
                                // Handle success response
                               if(response.success){
                                    toastr.success(response.message);
                                    window.location.href = '{{ route("index.login") }}';
                               }
                            },
                            error: function(response) {
                                if (response.status === 422 && response.responseJSON.error) {
                                    var errors = response.responseJSON.error;
                                    $('#dynamic-form').find(".field-error").text('');
                                    $.each(errors, function(key, value) {
                                        $('#' + key + '-error').text(value[0]); // Display only the first error message for each field
                                    });
                                } else {
                                    toastr.error(response.responseJSON.error)
                                }
                            }
            });
                    
                }
            </script>
            <script src="{{ asset('plugins/global/plugins.bundle.js') }}"></script>
            <script src="{{ asset('js/scripts.bundle.js') }}"></script>
            <script src="{{ asset('js/common.js') }}"></script>
            <script src="{{ asset('plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
            @show
</body>

</html>