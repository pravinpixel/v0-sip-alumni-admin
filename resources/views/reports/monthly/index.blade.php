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
                        Tasks Monthly Report</h1>
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
                            <a href="{{url('reports/monthly')}}" class="text-muted text-hover-primary">Monthly Report</a>
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
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <!--begin::Card title-->
                        <div class="card-title">
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                            transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                        <path
                                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" id="searchInput"
                                    class="form-control form-control-solid w-250px ps-15" placeholder="Search" />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                <!--begin::Filter-->
                                <div class="w-150px me-3">
                                    <div style="margin-top: 10px;"><label for="overdue_count">Total Records:</label>
                                    <span style="color: green;">{{$total_count ?? ''}}</span></div>
                                </div>

                                <div class="w-100px me-3">
                                    <!--begin::Select2-->
                                    <select class="form-select form-select-solid" name="row-count-filter"
                                        data-control="select2" data-hide-search="true" data-placeholder="">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <!--end::Select2-->
                                </div>

                                <button type="button" class="btn btn-primary me-3" data-bs-toggle="tooltip"
                                    id="filter_panel">
                                    <i class="fa-solid fa-filter"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    Filter
                                </button>
                                <!--end::Filter-->
                                <!--begin::Export-->
                                <button type="button" class="btn btn-primary me-3" data-bs-toggle="tooltip"
                                    id="export">
                                    <i class="fa-solid fa-download"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    Export
                                </button>
                                <!--end::Export-->

                            </div>
                            <!--end::Toolbar-->

                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->

                    <form id="filter_form">
                        <div class="card-header border-0 pt-6" id="filter_sub" style="display: none">
                            <div class="card-title">
                                <div class="row row-gap-10px">

                                    <div class="w-200px padBt_10" >
                                        <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Employee" name="employee" id="employee">
                                            <option selected value="">Select Employee</option>
                                            @foreach($employeesLists as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->first_name . ' ' . $employee->last_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    
                                    <div class="w-200px">
                                        <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Branch" name="branch" id="branch">
                                            <option value="">Select Branch</option>
                                            @foreach($types as $type)
                                            <option value="{{ $type->id }}">
                                                {{ $type->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-200px">
                                        <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Select Year" id="year" name="year">
                                           <option value="">Select Branch</option>
                                            @php
                                                $currentYear = date('Y');
                                                $startYear = $currentYear - 50; // Example: display years from the current year to 10 years ago
                                            @endphp
                                            @for ($year = $currentYear; $year >= $startYear; $year--)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="w-200px">
                                        <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Select Month" id="month" name="month">
                                            <option value="">Select Month</option>
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                    <!--begin::clear all btn-->
                                    <div class="w-200px">
                                        <button type="button" class="btn btn-primary me-3" data-bs-toggle="tooltip" id="clear-all">
                                            Clear all
                                        </button>
                                    </div>
                                    <!--end::clear all btn-->

                                </div>
                            </div>
                        </div>
                    </form>


                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5 table-responsive"
                                id="kt_customers_table">
                                <!--begin::Table head-->
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="min-width:50px; border: 1px solid #dee2e6;">S.No</th>
                                        <th rowspan="2" style="min-width:50px; border: 1px solid #dee2e6;">Branch</th>
                                        <th rowspan="2" style="min-width:50px; border: 1px solid #dee2e6;">Employee Name</th>
                                        <th colspan="3" style="font-weight: bold;  min-width: 150px; border: 1px solid #dee2e6;"><center>MONTH</center></th>
                                        <th colspan="3" style="font-weight: bold; min-width: 150px; border: 1px solid #dee2e6;"><center>OVERALL</center></th>
                                        <th colspan="4" style="font-weight: bold; min-width: 150px; border: 1px solid #dee2e6;">PENDING TASK ANALYSIS</th>
                                    </tr>
                                    <tr>
                                        <th style="min-width:50px; border: 1px solid #dee2e6;">Total Task Assigned</th>
                                        <th style="min-width:50px; border: 1px solid #dee2e6;">No of Task Completed</th>
                                        <th style="min-width:50px; border: 1px solid #dee2e6;">Monthly Avg Rating</th>
                                        <th style="min-width:50px; border: 1px solid #dee2e6;">Total Task Assigned</th>
                                        <th style="min-width:50px; border: 1px solid #dee2e6;">Total Completed Task</th>
                                        <th style="min-width:50px; border: 1px solid #dee2e6;">Cumulative Avg Rating</th>
                                        <th style="min-width:50px; border: 1px solid #dee2e6;">0-14</th>
                                        <th style="min-width:50px; border: 1px solid #dee2e6;">15-30</th>
                                        <th style="min-width:50px; border: 1px solid #dee2e6;">30></th>
                                        <th style="min-width:50px; border: 1px solid #dee2e6;">Total Task Pending</th>
                                    </tr>
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                        @if($employees->isEmpty())
                                            <tr>
                                                <td colspan="12" class="text-center">No results found.</td>
                                            </tr>
                                        @else
                                        @foreach($employees as $data)
                                        <tr>
                                            <td>{{ $serialNumberStart++ }}</td>
                                            <td>{{ $data['branch'] }}</td>
                                            <td>{{ $data['employee_name'] }}</td>
                                            
                                            <!-- Monthly Data -->
                                            <td>{{ $data['totalTasksAssigned'] }}</td>
                                            <td>{{ $data['monthlycompletedTasks'] }}</td>
                                            <td>{{ $data['rating'] }}</td>
                                            
                                            <!-- Overall Data (Assuming same as Monthly for this example) -->
                                            <td>{{ $data['overallTasksAssigned'] }}</td>
                                            <td>{{ $data['completedTasks'] }}</td>
                                            <td>{{ $data['overall_rating'] }}</td>

                                            <!-- Pending Task Analysis -->
                                            <td>{{ $data['totalTaskPending_0_14'] }}</td>
                                            <td>{{ $data['totalTaskPending_15_30'] }}</td>
                                            <td>{{ $data['totalTaskPending_30_plus'] }}</td>
                                            <td>{{ $data['pendingTasks'] }}</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                </tbody>
                                <!--end::Table body-->
                            </table>
                        </div>
                        <div class="row float-end mt-5">
                            <div id="pagination-links">
                              {{ $employees->links('pagination::bootstrap-4') }}
                            </div>
                        </div>

                    </div>
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
<script type="text/javascript">
    var page = 1;

    $(document).on('click', '#filter_panel', function(e) {
        $("#filter_sub").toggle();
    });

    $(document).on('click', '#pagination-links .pagination a', function(e) {
        e.preventDefault();
        page = $(this).attr('href').split('page=')[1];
        var searchQuery = $('#searchInput').val();
        search(searchQuery);
    });

    $(document).on('change', '[name="year"]', function(e) {
        var searchQuery = $('#searchInput').val();
        search(searchQuery);
    });

   $(document).on('change', '[name="employee"]', function(e) {
       var searchQuery = $('#searchInput').val();
       search(searchQuery);
   });

   $(document).on('change', '[name="branch"]', function(e) {
       var searchQuery = $('#searchInput').val();
       search(searchQuery);
   });

   $(document).on('change', '[name="task_type"]', function(e) {
       var searchQuery = $('#searchInput').val();
       search(searchQuery);
   });

    $(document).on('change', '[name="month"]', function(e) {
        var searchQuery = $('#searchInput').val();
        search(searchQuery);
    });

    $('#searchInput').on('keyup', function() {
        var searchQuery = $(this).val();
        searching(searchQuery);
    });

    $('#clear-all').on('click', function(e) {
        e.preventDefault(); // Prevent the default anchor click behavior
        $('#year').val('').trigger('change');
        $('#month').val('').trigger('change');
        $('#employee').val('').trigger('change');
        $('#branch').val('').trigger('change');
        $("#filter_sub").hide();

        search();

    });

    $(document).on('click', '#clear-start-date', function(e) {
        var clearButton = document.getElementById('clear-start-date');
        clearButton.classList.remove('show');
        $('#start_date').val('');
        search();
    });

    $(document).on('click', '#clear-end-date', function(e) {
        var clearButton = document.getElementById('clear-end-date');
        clearButton.classList.remove('show');
        $('#end_date').val('');
        search();
    });

    // Handle row count filter change
    $(document).on('change', '[name="row-count-filter"]', function(e) {
        page = 1; // Reset to the first page
        search(); // Call search to fetch data with the new per page count
    });

    function searching(searchQuery) {
        $.ajax({
            url: "{{ route('reports.monthly') }}",
            type: 'GET',
            data: {
                search: searchQuery,
                year: $('#year').val(),
                month: $('#month').val(),
                employee: $('#employee').val(),
                branch: $('#branch').val(),
                per_page: $('[name="row-count-filter"]').val()
            },
            beforeSend: function() {
                $('#pageLoader').addClass('loading-cursor');
            },
            success: function(data) {

                $('#kt_customers_table tbody').html($(data.employees).find('#kt_customers_table tbody').html());
                $('#pagination-links').html(data.pagination);
            },
            complete: function() {
                $('#pageLoader').removeClass('loading-cursor');
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + error);
            }
        });
    }


    function search(searchQuery) {
        $.ajax({
            url: "{{ route('reports.monthly') }}",
            type: 'GET',
            data: {
                search: searchQuery,
                year: $('#year').val(),
                month: $('#month').val(),
                employee: $('#employee').val(),
                branch: $('#branch').val(),
                page: page,
                per_page: $('[name="row-count-filter"]').val()
            },
            beforeSend: function() {
                $('#pageLoader').addClass('loading-cursor');
            },
            success: function(data) {

                $('#kt_customers_table tbody').html($(data.employees).find('#kt_customers_table tbody').html());
                $('#pagination-links').html(data.pagination);
            },
            complete: function() {
                $('#pageLoader').removeClass('loading-cursor');
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + error);
            }
        });
    }
</script>


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
            url: "{{ route('reports.monthlyExport') }}",
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
                a.download = 'monthly_report.xlsx';
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