@extends('layouts.index')

@section('title', 'Task Master | Usha Fire')
@section('style')
@parent
<style>
    .start {
        border: 1px solid #e0e0de;
        border-radius: 2%;
        height: 100%;
    }

    .pdfbox {
        list-style: none;
        display: flex;
        gap: 10px;
        border: 1px solid #e0e0de;
        padding: 2%;
        border-radius: 2%;
        width: 66%;
    }
</style>
@endsection


@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <!--begin::Title-->
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Task Bulk Upload</h1>
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
                    <a href="{{url('task')}}" class="text-muted text-hover-primary">Tasks</a>
                </li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->
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
<div class="card mb-5 mb-xl-8">
    <!-- Back Arrow -->
    <!--begin::Header-->
    <div style="display: flex;align-items:baseline;margin-left: 28px;">
        <h3 style="margin-left: 10px;" class="card-title align-items-start flex-column pt-6">
            <center> <span class="card-label fw-bold fs-3 mb-1">Tasks Upload</span></center>
        </h3>
    </div>

    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body py-3">
        <!--begin::Table container-->
        <div class="row mt-5">
            <form class="form" id="dynamic-form" method="post" action="{{ route('task.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="border-0 pb-3">
                    <label for="file">Choose Excel File:</label>
                    <input type="file" class="form-control w-50" id="file-input" name="file" required>
                    <span class="field-error" id="file-error" style="color:red;display:block"></span>
                    <span class="common-error" id="common-error" style="color:red;display:block"></span>
                    <br>
                </div>

                <button type="button" disabled class="btn btn-primary" id="dynamic-submit">
                    Import Tasks
                </button>
                <a id="download-task-template" class="btn btn-primary">Download Template</a>
            </form>
        </div>
        <!--end::Table container-->
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file-input');
        const importButtonElement = document.querySelector('#dynamic-submit');
        const exportButtonElement = document.querySelector('#download-task-template');

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                importButtonElement.disabled = false;
                document.getElementById('file-error').textContent = '';
                document.getElementById('common-error').textContent = '';
            } else {
                importButtonElement.disabled = true;
            }
        });

        document.getElementById('dynamic-form').addEventListener('reset', function() {
            importButtonElement.disabled = true;
        });

        $('#download-task-template').on('click', function(e) {
            exportButtonElement.textContent = "Downloading...";
            exportTask(e);
        });
        $('#dynamic-submit').on('click', function(e) {
            importButtonElement.textContent = "Importing...";
            importTask(e);
        });

        function exportTask(event) {
            event.preventDefault();
            $('#pageLoader').fadeIn();
            // Perform the AJAX request
            $.ajax({
                url: "{{ route('task.export-template') }}", // Laravel route for exporting template
                type: 'GET',
                xhrFields: {
                    responseType: 'blob' // Necessary for downloading files
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token if needed
                },
                success: function(response) {
                    console.log("====response", response);
                    exportButtonElement.textContent = "Download Task Template";
                    var blob = new Blob([response], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "task_template.xlsx"; // You can set the file name here
                    link.click(); // Trigger download
                },
                error: function(xhr, status, error) {
                    console.log(error);
                    exportButtonElement.textContent = "Download Task Template";
                    console.log("Error response:", error);
                },
                complete: function() {
                    exportButtonElement.textContent = "Download Task Template";
                }
            });
        }

        function importTask(event) {
            event.preventDefault();
            $('#pageLoader').fadeIn();
            let formData = new FormData(document.getElementById('dynamic-form'));

            // Perform the AJAX request
            $.ajax({
                url: $('#dynamic-form').attr('action'), // Get the form action URL
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token if needed
                },
                success: function(response) {
                    console.log('=====response', response);
                    toastr.success(response.message);
                    $('#dynamic-form')[0].reset();
                    importButtonElement.textContent = "Import Tasks";

                },
                error: function(xhr, status, error) {
                    console.log("Error response:", xhr.responseJSON);
                    importButtonElement.textContent = "Import Tasks";
                    $('#pageLoader').fadeOut();
                    if (xhr.status === 422) {
                        $('#dynamic-form')[0].reset();
                        if (xhr.responseJSON.type === 'laravel_validation') {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').text(value[0]);
                            });
                        } else if (xhr.responseJSON.type === 'excel_validation') {
                            var excelErrors = xhr.responseJSON.errors;
                            if (excelErrors.length > 0) {
                                $('#dynamic-form').find(".common-error").append(`<p style="color:red;">Ensure each column contains valid and correct data.</p>`);
                            }
                        }
                    } else {
                        $('#dynamic-form')[0].reset();
                        toastr.error(xhr.responseJSON.message)
                    }
                },
                complete: function() {
                    importButtonElement.textContent = "Import Tasks";
                }
            });
        }

    });
</script>
@parent
@endsection