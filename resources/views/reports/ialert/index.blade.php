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


    .padBt_10 {
        padding-bottom: 10px;
    }

    .date-input {
        z-index: 999;
    }

    .clearable-input {
        position: relative;
    }

    .clearable-input .clear-button {
        position: absolute;
        right: 33px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        font-size: 1.5rem;
        line-height: 1;
        cursor: pointer;
        color: #aaa;
        padding: 0;
        margin: 0;
        display: none;
    }

    .clearable-input .clear-button.show {
        display: block;
    }

    .clearable-input .clear-button:hover {
        color: #000;
    }
</style>
@endsection
@section('content')
<div id="pageLoader" class="page-loader">
    <div class="loader"></div>
</div>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Ialert Report</h1>
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
                            <a href="{{url('reports/ialertReport')}}" class="text-muted text-hover-primary">Ialert Report</a>
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
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Card-->
                <div class="card">
                    <h1 style="margin-left: 3%; margin-top: 2%;" class="d-flex text-dark fw-bold fs-3 flex-column">
                        Download Ialert Report</h1>
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6 pb-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Search-->
                            
                            <div class="d-flex align-items-center position-relative my-1">
                               <!--begin::Export-->
                                <button type="button" class="btn btn-primary me-3" data-bs-toggle="tooltip"
                                    id="export">
                                    <i class="fa-solid fa-download"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    Download Report
                                </button>
                                <!--end::Export-->
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                
                               

                            </div>
                            <!--end::Toolbar-->

                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->

                   


                    <!--begin::Card body-->
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
            <!--begin::Modals-->
            <!--begin::Modal - Customers - Add-->
            <!--end::Modal - Customers - Add-->
        </div>
        <!--end::Content container-->
    </div>

    <!--end::Content-->

</div>
<!--end::Content wrapper-->

@endsection

@section('script')
@parent


<script>
    $(document).on('click', '#export', function() {
        $('#pageLoader').fadeIn();
        let pageData = {
            search: $('#searchInput').val(),
            year: $('#year').val(),
            month: $('#month').val(),
            employee: $('#employee').val(),
            branch: $('#branch').val(),
            per_page: $('[name="row-count-filter"]').val(),
            page: $('.pagination .active').text()
        };
        
        $.ajax({
            url: "{{ route('reports.ialertExport') }}",
            type: 'GET',
            data: pageData,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(blob) {
                $('#pageLoader').fadeOut();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'ialert-admin.xlsx';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            },
            error: function(xhr, status, error) {
                console.error("Export Error: " + status + error);
            }
        });
    });
</script>
@endsection