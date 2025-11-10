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
    /* Add this to your CSS stylesheet */
    .input-group {
        position: relative;
        width: 100%;
    }

    .input-group .form-control {
        width: 100%;
        padding-right: 40px;
        /* Adjust to leave space for the icon */
    }

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
@php
    $decryptedPassword = '';
    if (isset($user->hash_password)) {
        try {
            $decryptedPassword = Crypt::decryptString($user->hash_password);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Handle decryption failure
            // Log the error or take appropriate action
            $decryptedPassword = ''; // Default to an empty string or handle as needed
        }
    }
@endphp
<div id="pageLoader" class="page-loader">
    <div class="loader"></div>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    @if(isset($user))
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Edit User</h1>
                    @else
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Add User</h1>
                    @endif

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{url('dashboard')}}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            <a href="{{url('/user')}}" class="text-muted text-hover-primary">Users</a>
                        </li>
                    </ul>
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
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
                        <form id="dynamic-form" method="post" action="{{ url('/user/save') }}">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{$user->id??''}}">
                            <input type="hidden" name="title" id="title" value="">
                            <div class="page-title d-flex justify-content-center flex-wrap me-3 card_arrow">
                                <a href="{{url('user')}}" style="cursor: pointer;" class="text-muted text-hover-primary">
                                    <i class="fa fa-arrow-left"  aria-hidden="true"></i>
                                </a>
                                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">User</h1>
                            </div>

                            <div class="row form_mtb">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">User Name:</label>
                                        <input type="text" id="user_name" name="user_name" value="{{$user->name??''}}" class="form-control" placeholder="Enter User Name">
                                        <span class="field-error" id="user_name-error" style="color:red"></span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">Mobile Number:</label>
                                        <input type="tel" id="mobile_number" name="mobile_number" value="{{$user->mobile_number??''}}" class="form-control" placeholder="Enter Mobile Number" maxlength="10">
                                        <span class="field-error" id="mobile_number-error" style="color:red"></span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">Email ID:</label>
                                        <input type="text" id="email" name="email" value="{{$user->email??''}}" class="form-control" placeholder="Enter Email ID">
                                        <span class="field-error" id="email-error" style="color:red"></span>
                                    </div>
                                </div>
                                
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">Password:</label>
                                        <div class="input-group">
                                            <input type="password" id="password" name="password" value="{{ $decryptedPassword }}" class="form-control" placeholder="Enter Password">
                                            <i class="toggle-password fa fa-eye-slash" data-toggle="password"></i>
                                        </div>
                                        <span class="field-error" id="password-error" style="color:red"></span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">Confirm Password:</label>
                                        <div class="input-group">
                                            <input type="password" id="retype_password" name="retype_password" value="{{ $decryptedPassword }}" class="form-control" placeholder="Enter Confirm Password">
                                            <i class="toggle-password fa fa-eye-slash" data-toggle="retype_password"></i>
                                        </div>

                                        <span class="field-error" id="retype_password-error" style="color:red"></span>
                                    </div>
                                </div>
                                

                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="role" class="form-label required">User Role:</label>
                                        <select class="form-select form-select-solid" name="role_id" id="role_id" data-control="select2" data-hide-search="true" data-placeholder="Role" data-kt-ecommerce-order-filter="role">
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ isset($user) && $role->id == old('role_id',$user->role_id) ? 'selected' : '' }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="field-error" id="role_id-error" style="color:red"></span>
                                    </div>
                                </div>

                                <div class="col-auto active_btn">
                                    <!-- Checkbox for creating a new user -->
                                    @if(!isset($user) || is_null($user->id))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status_update" name="status" value="1" checked>
                                        <label class="form-check-label" for="status_update">Active</label>
                                    </div>
                                    @endif

                                    <!-- Checkbox for updating an existing user -->
                                    @if(isset($user) && !is_null($user->id))
                                    @if($user->status == 1)
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status_update" name="status" value="1" {{ $user->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_update">Active</label>
                                    </div>
                                    @else
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status_update" name="status" value="0">
                                        <label class="form-check-label" for="status_update">Inactive</label>
                                    </div>
                                    @endif
                                    @endif

                                    <!-- <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status_update" name="status" value="1">
                                        <label class="form-check-label" for="status_update">Active</label>
                                    </div> -->
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
        $("#status_update").on('change', function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
            $('label[for="status_update"]').text('Active');
        } else {
            $(this).val(0);
            $('label[for="status_update"]').text('Inactive');
        }
    });
});
    $(document).ready(function() {
        document.getElementById('mobile_number').addEventListener('input', function(e) {
            var input = e.target.value;
            e.target.value = input.replace(/[^0-9]/g, '');
        });

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

        $('#dynamic-submit').on('click', function(e) {
            saveUpdateUser(e);
        });
    });

    function saveUpdateUser(event) {
        event.preventDefault();
        $('#pageLoader').fadeIn();

        // Serialize the form data
        let allValues = $('#dynamic-form').serialize();
        // Perform the AJAX request
        $.ajax({
            url: $('#dynamic-form').attr('action'), // Get the form action URL
            type: 'POST',
            data: allValues,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token if needed
            },
            success: function(response) {
                $('#pageLoader').fadeOut();
                // Handle success response
                if (response.success) {
                    toastr.success(response.message);
                    window.location.href = '{{ route("user.index") }}';
                } else {
                    toastr.success(response.error);
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