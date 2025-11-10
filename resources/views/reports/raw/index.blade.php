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
                        Tasks</h1>
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
                            <a href="{{url('reports/raw')}}" class="text-muted text-hover-primary">Raw Report</a>
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
                                        <span style="color: green;">{{$total_count ?? ''}}</span>
                                    </div>
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
                                    <i class="fa-solid fa-file"><span class="path1"></span><span
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

                                    <div class="w-200px padBt_10">
                                        <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Assigned By" name="assigned_by" id="assigned-by">
                                            <option selected value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->first_name . ' ' . $employee->last_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-200px">
                                        <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Assigned To" name="assigned_to" id="assigned-to">
                                            <option selected value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->first_name . ' ' . $employee->last_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-200px">
                                        <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Priority" name="priority" id="priority">
                                            <option value="">Select Priority</option>
                                            @foreach($priorities as $priority)
                                            <option value="{{ $priority->id }}">
                                                {{ $priority->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-200px">
                                        <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Recurrence" name="recurrence" id="recurrence">
                                            <option value="">Select Priority</option>
                                            <option value="1">yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>

                                    <div class="w-200px">
                                        <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Task Type" name="task_type" id="task-type">
                                            <option value="">Select Priority</option>
                                            @foreach($types as $type)
                                            <option value="{{ $type->id }}">
                                                {{ $type->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-200px">
                                        <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Task Status" name="status" id="status">
                                            <option value="">Select Status</option>
                                            @foreach($statuses as $status)
                                            <option value="{{ $status->id }}">
                                                {{ ucfirst($status->name) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-200px padBt_10">
                                        <div class="clearable-input">
                                            <input type="date" class="form-control date-input" id="start_date"
                                                data-placeholder="Select Start Date" name="start_date">
                                            <div class="input-group-append">
                                                <button class="clear-button" type="button" id="clear-start-date">
                                                    &times;
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="w-200px">
                                        <div class="clearable-input">
                                            <input type="date" class="form-control date-input" id="end_date"
                                                data-placeholder="Select End Date" name="end_date">
                                            <div class="input-group-append">
                                                <button class="clear-button" type="button" id="clear-end-date">
                                                    &times;
                                                </button>
                                            </div>

                                        </div>
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
                                    <!--begin::Table row-->
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-50px">S.No</th>
                                        <th style="min-width: 150px;">Task NO</th>
                                        <th style="min-width: 150px;">Employee ID</th>
                                        <th style="min-width: 150px;">Branch</th>
                                        <th style="min-width: 150px;">Reporting Manager</th>
                                        <th style="min-width: 150px;">Assigned By</th>
                                        <th style="min-width: 150px;">Assignee Emp ID</th>
                                        <th style="min-width: 150px;">Assignee Reporting Manager</th>
                                        <th style="min-width: 150px;">Assignee Branch</th>
                                        <th style="min-width: 150px;">Assigned To</th>
                                        <th style="min-width: 150px;">Creation Date</th>
                                        <th style="min-width: 150px;">Subject</th>
                                        <th style="min-width: 150px;">Details</th>
                                        <th style="min-width: 150px;">Type</th>
                                        <th style="min-width: 150px;">Priority</th>
                                        <th style="min-width: 150px;">Followers</th>
                                        <th style="min-width: 150px;">Additional Followers</th>
                                        <th style="min-width: 150px;">Due Date</th>
                                        <th style="min-width: 150px;">Status</th>
                                        <th style="min-width: 150px;">Recurrence</th>
                                        <th style="min-width: 150px;">Age</th>
                                        <th style="min-width: 150px;">Revision Count</th>
                                        <th style="min-width: 150px;">Documents</th>
                                        <th style="min-width: 150px;">Task Rating</th>
                                        <th style="min-width: 150px;">Rating Remarks</th>
                                        <th style="min-width: 180px;">Mark Completed Date</th>
                                        <th style="min-width: 150px;">Completed Date</th>
                                        <th style="min-width: 150px;">task link</th>

                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    @if($tasks->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center">No results found.</td>
                                    </tr>
                                    @else
                                    @foreach($tasks as $task)
                                    <tr>
                                        <td>{{ $serialNumberStart++ }}</td>
                                        <td>{{$task->task_no ?? ''}}</td>
                                        <td>{{$task->assignedby->employee_id ?? ''}}</td>
                                        <td>{{$task->assignedby && $task->assignedby->location
                                            ? $task->assignedby->location
                                            : '-'}}</td>
                                        <td>{{$task->assignedby && $task->assignedby->reporting_managers 
                                        ? $task->assignedby->reporting_managers
                                        : '-'}}</td>
                                        <td>{{$task->assignedby->name ?? ''}}</td>
                                        <td>{{$task->assignedto->employee_id ?? ''}}</td>
                                        <td>{{$task->assignedto && $task->assignedto->reporting_managers 
                                            ? $task->assignedto->reporting_managers 
                                            : '-'}}</td>
                                        <td>{{$task->assignedto && $task->assignedto->location 
                                            ? $task->assignedto->location 
                                            : '-'}}</td>
                                        <td>{{$task->assignedto->name ?? ''}}</td>
                                        <td>{{$task->created_at ? $task->created_at->format('Y-m-d') : '-'}}</td>
                                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{$task->name ?? ''}}">{{$task->name ?? ''}}</td>
                                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{$task->description ?? ''}}">{{$task->description ?? ''}}</td>
                                        <td>{{$task->category->name ?? ''}}</td>
                                        <td>{{$task->priority->name ?? ''}}</td>
                                        <td>
                                            {{ $task->followers_details->pluck('full_name')->implode(', ')  ?? ''}}
                                        </td>
                                        <td>
                                            @if($task->additional_followers)
                                                {{ implode("\n", array_map('trim', explode(',', $task->additional_followers))) }}
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($task->deadline)->format('d-m-Y') ?? ''}}</td>
                                        <td>{{ ucfirst($task->status->name ?? '') }}</td>
                                        <td>
                                            @if($task->is_recurrence == 1)
                                            <div style="margin-left: 19px;" class="badge badge-light-success">Yes</div>
                                            @else
                                            <div style="margin-left: 19px;" class="badge badge-light-danger">No</div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                            $age_date_check = \Carbon\Carbon::parse($task->created_at)->format('Y-m-d');
                                            $daysDifference = \Carbon\Carbon::now()->startOfDay()->diffInDays($age_date_check);
                                            @endphp
                                            @if($task->is_recurrence == 1)
                                            {{ \Carbon\Carbon::now()->startOfDay()->diffInDays($task->deadline) ?? ''}}
                                            @else
                                            {{ $daysDifference ?? ''}}
                                            @endif
                                        </td>
                                        <td>{{ $task->due_dates_count ?? ''}}</td>
                                        <td style="max-width: 200px;">
                                            @php
                                            $taskDocuments = $task->documents;

                                            $commentDocuments = $task->comments->flatMap(function ($comment) {
                                            return $comment->documents;
                                            });
                                            $allDocuments = $commentDocuments->merge($taskDocuments);
                                            @endphp
                                            @if($allDocuments->isNotEmpty())
                                            @foreach($allDocuments as $document)
                                            <a href="{{ $document->document }}" style="color: rgb(146, 56, 81);" target="_blank">
                                                {{ $document->name }},
                                            </a>
                                            @endforeach
                                            @else
                                            No documents available
                                            @endif
                                        </td>
                                        <td>{{$task->task_rating ?? ''}}</td>
                                        <td>{{$task->rating_remark ?? ''}}</td>
                                        <td>{{ $task->mark_as_completed_date ? \Carbon\Carbon::parse($task->mark_as_completed_date)->format('d-m-Y') : '-' }}</td>
                                        @if($task->status_id == 1)
                                        <td>{{ \Carbon\Carbon::parse($task->status_date)->format('d-m-Y') ?? ''}}</td>
                                        @else
                                        <td>{{'-'}}</td>
                                        @endif
                                        <td><a target="_blank" href="{{ config('app.task_url') . '/task?view_task_id=' . ($task->id ?? '') }}">{{ config('app.task_url') . '/task?view_task_id=' . ($task->id ?? '') }}</a></td>



                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                                <!--end::Table body-->
                            </table>
                        </div>
                        <div class="row float-end mt-5">
                            <div id="pagination-links">
                                {{ $tasks->links('pagination::bootstrap-4') }}
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



    $(document).on('change', '[name="start_date"]', function(e) {
        console.log('in');
        var clearButton = document.getElementById('clear-start-date');
        if ($('#start_date').val()) {
            $('#end_date').attr('min', $('#start_date').val());
            clearButton.classList.add('show');
        } else {
            $('#end_date').attr('min', '');
        }
        search();
    });

    $(document).on('change', '[name="assigned_by"]', function(e) {
        search();
    });

    $(document).on('change', '[name="assigned_to"]', function(e) {
        search();
    });

    $(document).on('change', '[name="priority"]', function(e) {
        search();
    });

    $(document).on('change', '[name="recurrence"]', function(e) {
        search();
    });

    $(document).on('change', '[name="task_type"]', function(e) {
        search();
    });

    $(document).on('change', '[name="status"]', function(e) {
        search();
    })

    $(document).on('change', '[name="end_date"]', function(e) {
        var clearButton = document.getElementById('clear-end-date');
        if ($('#start_date').val()) {
            $('#end_date').attr('min', $('#start_date').val());
            clearButton.classList.add('show');
        } else {
            $('#end_date').attr('min', '');
        }
        search();
    });

    $('#searchInput').on('keyup', function() {
        var searchQuery = $(this).val(); // Get the search input value

        // Call the function to search and refresh the table
        searching(searchQuery);
    });

    $(document).on('click', '#pagination-links .pagination a', function(e) {
        e.preventDefault();
        page = $(this).attr('href').split('page=')[1];
        var searchQuery = $('#searchInput').val();
        search(searchQuery);
    });

    $('#clear-all').on('click', function(e) {
        e.preventDefault(); // Prevent the default anchor click behavior
        $('#start_date').val('');
        $('#end_date').val('');
        $('#assigned-by').val('').trigger('change');
        $('#assigned-to').val('').trigger('change');
        $('#priority').val('').trigger('change');
        $('#recurrence').val('').trigger('change');
        $('#task-type').val('').trigger('change');
        $('#status').val('').trigger('change');
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
            url: "{{ route('reports.raw') }}",
            type: 'GET',
            data: {
                search: searchQuery,
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                assigned_by: $('#assigned-by').val(),
                assigned_to: $('#assigned-to').val(),
                priority: $('#priority').val(),
                recurrence: $('#recurrence').val(),
                task_type: $('#task-type').val(),
                status: $('#status').val(),
                per_page: $('[name="row-count-filter"]').val()
            },
            beforeSend: function() {
                $('#pageLoader').addClass('loading-cursor');
            },
            success: function(data) {

                $('#kt_customers_table tbody').html($(data.tasks).find('#kt_customers_table tbody').html());
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
            url: "{{ route('reports.raw') }}",
            type: 'GET',
            data: {
                search: searchQuery,
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                assigned_by: $('#assigned-by').val(),
                assigned_to: $('#assigned-to').val(),
                priority: $('#priority').val(),
                recurrence: $('#recurrence').val(),
                task_type: $('#task-type').val(),
                status: $('#status').val(),
                page: page,
                per_page: $('[name="row-count-filter"]').val()
            },
            beforeSend: function() {
                $('#pageLoader').addClass('loading-cursor');
            },
            success: function(data) {

                $('#kt_customers_table tbody').html($(data.tasks).find('#kt_customers_table tbody').html());
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
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            assigned_by: $('#assigned-by').val(),
            assigned_to: $('#assigned-to').val(),
            priority: $('#priority').val(),
            recurrence: $('#recurrence').val(),
            task_type: $('#task-type').val(),
            status: $('#status').val(),
            per_page: $('[name="row-count-filter"]').val(),
            page: $('.pagination .active').text()
        };

        $.ajax({
            url: "{{ route('reports.export') }}",
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
                a.download = 'raw_report.csv';
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