@extends('layouts.index')

@section('title', 'Task Master | Usha Fire')

@section('style')
@parent
<style>
    .loading-cursor {
        cursor: wait !important;
    }

    .del {
        margin-top: 10% !important;
    }

    .let {
        font-size: 120% !important;

    }
</style>
<style>
    .input-group {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        z-index: 99;
    }

    .form_mtb {
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .active_btn {
        display: flex;
        align-items: center;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
@endsection
@section('content')
<div id="pageLoader" class="page-loader">
    <div class="loader"></div>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <!--begin::Filter menu-->
                    <div class="m-0">
                        <!--begin::Menu toggle-->
                        <!--end::Menu 1-->
                    </div>
                    <!--end::Filter menu-->
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Toolbar container-->
        </div>
        <!--end::Toolbar-->
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <form id="dynamic-form" method="post" action="{{ url('password-update') }}"  enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{$user->id??''}}">
                            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Change Password</h1>
                            </div>

                            <div class="row form_mtb">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">Current Password:</label>
                                        <div class="input-group">
                                        <input type="password" autocomplete="new-password" id="current_password" name="current_password" class="form-control" placeholder="Enter Current Password">
                                        <i class="toggle-password fa fa-eye-slash" data-toggle="current_password"></i>
                                        </div>
                                        <span class="field-error" id="current_password-error" style="color:red"></span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">New Password:</label>
                                        <div class="input-group">
                                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter New Password">
                                        <i class="toggle-password fa fa-eye-slash" data-toggle="new_password"></i>
                                        </div>
                                        <span class="field-error" id="new_password-error" style="color:red"></span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">Confirm New Password:</label>
                                            <div class="input-group">
                                                <input type="password" id="retype_password" name="retype_password" class="form-control" placeholder="Enter New Confirm Password">
                                                <i class="toggle-password fa fa-eye-slash" data-toggle="retype_password"></i>
                                            </div>
                                        <span class="field-error" id="retype_password-error" style="color:red"></span>
                                    </div>
                                </div>
                            </div>


                            <div class="mb-3 text-center">
                                <button type="button" class="btn btn-success btn-submit" id="dynamic-submit">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!--end::Card body-->
                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
</div>



@endsection

@section('script')
@parent
<script>
    $(document).ready(function() {
        // Password Eye Toggle Script
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                event.stopPropagation();
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

        $('#dynamic-submit').on('click', function(e) {
            updatePassword(e);
        });
    });

    function updatePassword(event) {
        event.preventDefault();
        $('#pageLoader').fadeIn();
        // Serialize the form data
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
                $('#pageLoader').fadeOut();
                // Handle success response
                if (response.success) {
                    toastr.success(response.message);
                    window.location.reload();
                }
            },
            error: function(response) {
                $('#pageLoader').fadeOut();
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


@endsection