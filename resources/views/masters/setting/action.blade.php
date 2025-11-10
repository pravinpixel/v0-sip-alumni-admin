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
    .toggle-password {
        position: relative;
        bottom: 30px;
        left: 260px;

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
                 <!--begin::Page title-->
                 <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Settings</h1>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            <a href="{{url('dashboard')}}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            <a href="{{url('settings')}}" class="text-muted text-hover-primary">Settings</a>
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
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
                        <form id="dynamic-form" method="post" action="{{ url('settings/create') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="title" id="title" value="">
                            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Signature Upload</h1>
                            </div>


                            <div class="d-flex justify-content-center flex-wrap">
                                <!--begin::Image input-->
                                <div class="image-input image-input-outline" data-kt-image-input="true">
                                    <!--begin::Preview existing avatar-->
                                   
                                   
                                    

                                    <div class="image-input-wrapper w-125px h-125px" style="background-image: url('{{ ($user)->value ? url($user->value) : '' }}')"></div>
                                    <!--end::Preview existing avatar-->
                                    <!--begin::Label-->
                                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                        <i class="bi bi-pencil-fill fs-7"></i>
                                        <!--begin::Inputs-->
                                        <input type="file" name="image" id="image-input" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="avatar_remove" value="0" />
                                        <!--end::Inputs-->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Cancel-->
                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <!--end::Cancel-->
                                    <!--begin::Remove-->
                                    <span id="remove-avatar" class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <!--end::Remove-->
                                </div>

                                <!--end::Image input-->
                            </div>
                            <div class="mb-3 text-center">
                                <!--begin::Hint-->
                                <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                                <!--end::Hint-->
                            </div>



                            <div class="mb-3 text-center">
                                <button disabled type="button" class="btn btn-success btn-submit" id="dynamic-submit">Submit</button>
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
     document.addEventListener('DOMContentLoaded', function () {
        
        const submitButton = document.getElementById('dynamic-submit');
        const imageInput = document.getElementById('image-input');
        const removeAvatarButton = document.getElementById('remove-avatar');

        submitButton.disabled = true;

        imageInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        });
        removeAvatarButton.addEventListener('click', function () {
            submitButton.disabled = true;
        });
    });
    $(document).ready(function() {

        $('#dynamic-submit').on('click', function(e) {
            saveUpdateUser(e);
        });
    });

    function saveUpdateUser(event) {
        event.preventDefault();
        const submitButton = document.getElementById('dynamic-submit');
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
                submitButton.disabled = true;
                // Handle success response
                if (response.success) {
                    $('#pageLoader').fadeOut();
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