<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>OTP Verification - {{ config('app.name') }}</title>
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
    <link rel="icon" type="image/png" href="{{ asset('images/logo/favicon.png') }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @section('style')
    <link href="{{ asset('css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/alumni/style.css') }}" />
    <style>
        .otp-inputs {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .otp-input {
            width: 52px;
            height: 52px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            transition: all 0.3s;
            outline: none;
        }

        .otp-input:focus {
            border-color: oklch(0.52 0.24 22);
            box-shadow: 0 0 0 3px rgba(240, 98, 146, 0.1);
        }

        .otp-input.filled {
            border-color: oklch(0.52 0.24 22);
            background: #fff5f8;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            color: #333;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .back-btn:hover {
            color: #666;
        }

        .back-btn i {
            margin-right: 8px;
        }

        .resend-btn {
            background: transparent;
            color: oklch(0.52 0.24 22);
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .resend-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .resend-btn:hover:not(:disabled) {
            opacity: 0.8;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        @media (max-width: 480px) {
            .otp-input {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }

            .otp-inputs {
                gap: 8px;
            }
        }
    </style>
    @show
</head>

<body id="kt_body" class="app-blank app-blank bgi-size-cover bgi-position-center bgi-no-repeat" style="background-color:#f8f8f8;">

    <div class="d-flex flex-column flex-root min-vh-100" id="kt_app_root">
        <div class="flex-grow-1 d-flex justify-content-center align-items-center">
            <div class="bg-body d-flex flex-column align-items-center rounded-4 w-450px p-6 log">
                <div class="w-100" style="max-width:450px;">

                    <a href="{{ url('/') }}" class="back-btn">
                        <i class="fa fa-arrow-left"></i>
                        Back
                    </a>

                    <h3 class="text-left fw-bold mb-2">Verify OTP</h3>
                    <p class="text-left text-muted mb-4">
                        Enter the 6-digit code sent to +91 <strong>{{ session('verify_mobile', 'your mobile') }}</strong>
                    </p>

                    <form id="otp-form">
                        @csrf
                        <input type="hidden" name="mobile" value="{{ session('verify_mobile') }}">

                        <div class="otp-inputs">
                            <input type="text" class="otp-input" maxlength="1" name="otp1" required>
                            <input type="text" class="otp-input" maxlength="1" name="otp2" required>
                            <input type="text" class="otp-input" maxlength="1" name="otp3" required>
                            <input type="text" class="otp-input" maxlength="1" name="otp4" required>
                            <input type="text" class="otp-input" maxlength="1" name="otp5" required>
                            <input type="text" class="otp-input" maxlength="1" name="otp6" required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" id="verify-otp-btn" class="btn" style="background-color:oklch(0.52 0.24 22); color:white; opacity: 0.5;" disabled>
                                <span class="indicator-label">Verify OTP</span>
                                <span class="indicator-progress" style="display:none;">
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <button type="button" id="resend-otp-btn" class="resend-btn">Resend OTP</button>
                            <span id="countdown" class="text-muted" style="display:none;"></span>
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
    <script src="{{ asset('js/alumniCommon.js') }}"></script>

    <script>
        let resendTimer;
        let countdown = 30;

        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.otp-input');

            // Auto-focus first input
            inputs[0].focus();

            // OTP input functionality
            inputs.forEach((input, index) => {
                input.addEventListener('keypress', (e) => {
                    // Only allow numbers
                    if (!/^\d$/.test(e.key)) {
                        e.preventDefault();
                        return;
                    }
                    
                    if (input.value.length >= 1) {
                        e.preventDefault();
                        return;
                    }
                });

                input.addEventListener('input', (e) => {
                    let value = e.target.value;

                    value = value.replace(/\D/g, '').slice(0, 1);
                    e.target.value = value;

                    if (value.length === 1) {
                        input.classList.add('filled');
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        } else {
                            input.blur();
                        }
                    } else {
                        input.classList.remove('filled');
                    }

                    checkOTPComplete();

                    if (index === inputs.length - 1 && value.length === 1) {
                        const allFilled = Array.from(inputs).every(input => input.value.length === 1);
                        if (allFilled) {
                            setTimeout(() => verifyOTP(), 100);
                        }
                    }
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !input.value && index > 0) {
                        inputs[index - 1].focus();
                        inputs[index - 1].value = '';
                        inputs[index - 1].classList.remove('filled');
                        checkOTPComplete();
                    }
                });

                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                    pastedData.split('').forEach((char, i) => {
                        if (inputs[i]) {
                            inputs[i].value = char;
                            inputs[i].classList.add('filled');
                        }
                    });
                    
                    checkOTPComplete();
                    
                    if (pastedData.length === 6) {
                        inputs[5].blur(); 
                        setTimeout(() => verifyOTP(), 100);
                    } else if (pastedData.length > 0) {
                        const nextEmptyIndex = pastedData.length;
                        if (nextEmptyIndex < inputs.length) {
                            inputs[nextEmptyIndex].focus();
                        }
                    }
                });
            });

            $('#verify-otp-btn').on('click', function(e) {
                verifyOTP();
            });

            $('#resend-otp-btn').on('click', function(e) {
                resendOTP();
            });

            startResendCountdown();
        });

        function checkOTPComplete() {
            const inputs = document.querySelectorAll('.otp-input');
            const allFilled = Array.from(inputs).every(input => input.value.length === 1);
            const verifyBtn = document.getElementById('verify-otp-btn');
            
            if (allFilled) {
                verifyBtn.disabled = false;
                verifyBtn.style.opacity = '1';
                verifyBtn.style.cursor = 'pointer';
            } else {
                verifyBtn.disabled = true;
                verifyBtn.style.opacity = '0.5';
                verifyBtn.style.cursor = 'not-allowed';
            }
        }

        function verifyOTP() {
            // Collect OTP
            const otpInputs = document.querySelectorAll('.otp-input');
            let otp = '';
            otpInputs.forEach(input => {
                otp += input.value;
            });

            if (otp.length !== 6) {
                showToast('Please enter complete 6-digit OTP', 'error');
                otpInputs[0].focus();
                return;
            }

            const mobile = '{{ session("verify_mobile") }}';

            if (!mobile) {
                showToast('Session expired. Please request OTP again.', 'error');
                setTimeout(() => {
                    window.location.href = '{{ url("/") }}';
                }, 2000);
                return;
            }

            $('#verify-otp-btn .indicator-label').hide();
            $('#verify-otp-btn .indicator-progress').show();

            // Prepare form data
            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('mobile', mobile);
            formData.append('otp', otp);

            $.ajax({
                url: '{{ route("verify.otp") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'Accept': 'application/json'
                },
                success: function(response) {

                    if (response.success) {
                        showToast('OTP verified successfully!', 'success');

                        setTimeout(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.href = '{{ url("/") }}';
                            }
                        }, 1000);
                    } else {
                        showToast('Invalid OTP', 'error');
                        clearOTPFields();
                    }
                },
                error: function(xhr) {
                    switch (xhr.status) {
                        case 400:
                            showToast(xhr.responseJSON?.error || 'Invalid OTP. Please try again.', 'error');
                            break;

                        case 422:
                            const errors = xhr.responseJSON.errors;
                            if (errors && errors.otp) {
                                showToast(errors.otp[0], 'error');
                            } else if (xhr.responseJSON.error) {
                                showToast(xhr.responseJSON.error, 'error');
                            } else {
                                showToast('Invalid OTP. Please try again.', 'error');
                            }
                            break;

                        case 500:
                            showToast('Server error. Please try again later.', 'error');
                            break;

                        default:
                            showToast('Something went wrong. Please try again.', 'error');
                    }
                    clearOTPFields();
                },
                complete: function() {
                    $('#verify-otp-btn .indicator-label').show();
                    $('#verify-otp-btn .indicator-progress').hide();
                }
            });
        }

        function resendOTP() {
            $('#resend-otp-btn').prop('disabled', true);

            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('number', $('input[name="mobile"]').val());

            $.ajax({
                url: '{{ route("send.otp") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Resend Success:', response);

                    if (response.success) {
                        showToast('OTP sent successfully!', 'success');
                        clearOTPFields();
                        startResendCountdown();
                    } else {
                        showToast(response.error || 'Failed to send OTP', 'error');
                        $('#resend-otp-btn').prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    console.log('Resend Error:', xhr.responseJSON);

                    switch (xhr.status) {
                        case 400:
                            showToast(xhr.responseJSON?.error || 'Invalid mobile number', 'error');
                            break;
                        case 500:
                            showToast('Server error. Please try again later.', 'error');
                            break;
                        default:
                            showToast('Failed to resend OTP. Please try again.', 'error');
                    }
                    $('#resend-otp-btn').prop('disabled', false);
                }
            });
        }

        function startResendCountdown() {
            $('#resend-otp-btn').hide();
            $('#countdown').show();
            countdown = 30;
            updateCountdownText();

            resendTimer = setInterval(function() {
                countdown--;
                updateCountdownText();

                if (countdown <= 0) {
                    clearInterval(resendTimer);
                    $('#countdown').hide();
                    $('#resend-otp-btn').show();
                    $('#resend-otp-btn').prop('disabled', false);
                }
            }, 1000);
        }

        function updateCountdownText() {
            $('#countdown').text('Resend OTP in ' + countdown + 's');
        }

        function clearOTPFields() {
            const inputs = document.querySelectorAll('.otp-input');
            inputs.forEach(input => {
                input.value = '';
                input.classList.remove('filled');
            });
            inputs[0].focus();
            checkOTPComplete();
        }
    </script>
    @show
</body>

</html>