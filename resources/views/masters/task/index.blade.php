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
@endsection
@section('content')
<div id="pageLoader" class="page-loader">
    <div class="loader"></div>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div id="successMessage" class="alert alert-success" style="display: none;">
    <strong>Success:</strong> Employee has been successfully updated.
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
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Tasks</h1>
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
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" id="searchInput" class="form-control form-control-solid w-250px ps-15" placeholder="Search" />
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
                                    <select class="form-select form-select-solid" name="row-count-filter" data-control="select2" data-hide-search="true" data-placeholder="">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <!--end::Select2-->
                                </div>

                                <button type="button" class="btn btn-primary me-3" data-bs-toggle="tooltip" id="filter_panel">
                                    <i class="fa-solid fa-filter"><span class="path1"></span><span class="path2"></span></i>
                                    Filter
                                </button>
                                <!--end::Filter-->
                                <!--begin::Export-->
                                <!--end::Export-->

                            </div>
                            <!--end::Toolbar-->
                            <!--begin::Group actions-->
                            <div class="d-flex justify-content-end align-items-center d-none" data-kt-customer-table-toolbar="selected">
                                <div class="fw-bold me-5">
                                    <span class="me-2" data-kt-customer-table-select="selected_count"></span>Selected
                                </div>
                                <button type="button" class="btn btn-danger" data-kt-customer-table-select="delete_selected">Delete Selected</button>
                            </div>
                            <!--end::Group actions-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    @include('masters/task.filter')
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5 table-responsive" id="kt_customers_table">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-50px">S.No</th>
                                        <th class="min-w-75px">Task id</th>
                                        <th style="width: 125px; min-width: 125px;">Date</th>
                                        <th class="min-w-150px">Name</th>
                                        <th class="min-w-150px">Assigned by</th>
                                        <th class="min-w-150px">Assigned To</th>
                                        <th style="width: 125px; min-width: 125px;">priority</th>
                                        <th style="width: 125px; min-width: 125px;">Status</th>
                                        <th style="width: 125px; min-width: 125px;">View</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    @if($users->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center">No results found.</td>
                                    </tr>
                                    @else
                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            {{ $serialNumberStart++ }}
                                        </td>
                                        <!--begin::deadline=-->
                                        <td>
                                            {{ $user->task_no ?? '' }}
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($user->date)->format('d/m/Y') }}
                                        </td>
                                        <!--end::deadline=-->

                                        <!--begin::Name=-->
                                        <td>
                                            {{$user->name ?? ''}}
                                        </td>
                                        <!--end::Name=-->

                                        <!--begin::assignedby=-->
                                        <td>
                                            {{ $user->assignedby->name ?? ''}}
                                        </td>
                                        <!--end::assignedby=-->

                                        <!--begin::assignedto=-->
                                        <td>
                                            {{ $user->assignedto->name ?? ''}}
                                        </td>
                                        <!--end::assignedto=-->


                                        <!--begin::priority=-->
                                        <td>
                                            {{ $user->priority->name ?? ''}}
                                        </td>
                                        <!--end::priority=-->

                                        <!--begin::Status=-->
                                        <td>
                                            @if($user->status_id =='1')
                                            <div class="badge badge-light-success">Completed</div>
                                            @elseif($user->status_id =='2')
                                            <div class="badge badge-light-warning">Inprogress</div>
                                            @elseif($user->status_id =='8')
                                            <div class="badge badge-light-warning">Closed</div>
                                            @elseif($user->status_id =='9')
                                            <div class="badge badge-light-warning">Deleted</div>
                                            @else
                                            <div class="badge badge-light-danger">-</div>
                                            @endif
                                        </td>
                                        <!--end::Status=-->

                                        <!--begin::View Icon=-->
                                        <td class="td">
                                            <a href="{{url('/task/view', $user->id)}}" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px ">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                        <!--end::View Icon=-->
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                                <!--end::Table body-->


                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="show-pg">
                                    <!--  <p>Showing 1 to 9 of 9 entries</p> -->
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-0 col-sm-0">

                            </div>
                            <div id="pagination-links" class="col-lg-4 col-md-6 col-sm-12">
                                {{ $users->links('pagination::bootstrap-4') }}
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
</div>


<!-- <-----delete model---->
<div class="modal fade del" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <input type="hidden" id="cityId" name="city_id" value="">
            <div class="modal-body let">
                Are you sure you want to delete this record?

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@parent


<script type="text/javascript">
    $(document).ready(function() {
        let eventListenersActive = true;
        // Event listener for search input change
        $('#searchInput').keyup(function() {
            if (eventListenersActive) {
                updateTableData();
            }
        });

        // Filter Show / Hide Functionality
        $(document).on('click', '#filter_panel', function(e) {
            $("#filter_sub").toggle();
        });

        $(document).on('change', '[name="employee"]', function(e) {
            if (eventListenersActive) {
                updateTableData();
            }
        });
        $(document).on('change', '[name="designation"]', function(e) {
            if (eventListenersActive) {
                updateTableData();
            }
        });
        $(document).on('change', '[name="department"]', function(e) {
            if (eventListenersActive) {
                updateTableData();
            }
        });
        $(document).on('change', '[name="branch"]', function(e) {
            if (eventListenersActive) {
                updateTableData();
            }
        });
        $(document).on('change', '[name="location"]', function(e) {
            if (eventListenersActive) {
                updateTableData();
            }
        });
        $(document).on('change', '[name="assigned_by"]', function(e) {
            if (eventListenersActive) {
                updateTableData();
            }
        });
        $(document).on('change', '[name="assigned_to"]', function(e) {
            if (eventListenersActive) {
                updateTableData();
            }
        });
        $(document).on('change', '[name="priority"]', function(e) {
            if (eventListenersActive) {
                updateTableData();
            }
        });
        $(document).on('click', '#clear-start-date', function(e) {
            var clearButton = document.getElementById('clear-start-date');
            clearButton.classList.remove('show');
            document.getElementById('start_date').value = '';
            if (eventListenersActive) {
                updateTableData();
            }
        });
        $(document).on('click', '#clear-end-date', function(e) {
            var clearButton = document.getElementById('clear-end-date');
            clearButton.classList.remove('show');
            document.getElementById('end_date').value = '';
            if (eventListenersActive) {
                updateTableData();
            }
        });


        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        $(document).on('change', '[name="start_date"]', function(e) {
            var clearButton = document.getElementById('clear-start-date');
            if (startDateInput.value) {
                endDateInput.min = startDateInput.value;
                clearButton.classList.add('show');
            } else {
                endDateInput.min = '';
            }
            updateTableData();
        });

        $(document).on('change', '[name="end_date"]', function(e) {
            var clearButton = document.getElementById('clear-end-date');
            if (startDateInput.value) {
                endDateInput.min = startDateInput.value;
                clearButton.classList.add('show');
            } else {
                endDateInput.min = '';
            }
            updateTableData();
        });

        document.getElementById('clear-filters').addEventListener('click', function() {
            var form = document.getElementById('filter_form');
            eventListenersActive = false;
            // Clear all select inputs
            form.querySelectorAll('select.form-select').forEach(function(select) {
                select.value = '';
                if ($(select).data('select2')) {
                    $(select).val('').trigger('change');
                }
            });

            // Clear all date inputs
            form.querySelectorAll('input[type="date"]').forEach(function(dateInput) {
                dateInput.value = '';
            });
            var searchInput = form.querySelector('#searchInput');
            if (searchInput) {
                searchInput.value = '';
            }
            updateTableData();
            eventListenersActive = true;
        });

        // Event listener for status select change
        $('[data-kt-ecommerce-order-filter="status"]').on('change', function() {
            if (eventListenersActive) {
                updateTableData();
            }
        });

        $('#pagination-links').on('click', '.pagination a', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1]; // Use index 1 to get the page number
            updateTableData(page);
        });

        $(document).on('change', '[name="row-count-filter"]', function(e) {
            if (eventListenersActive) {
                var page = 1;
                updateTableData(page);
            }
        });

        // Function to load table data with search term and status
        function loadTableData(searchTerm, selectedStatus, employee, designation, department, branch, location, assigned_by, assigned_to, priority, start_date, end_date, page = '', pageItems = '') {
            // showProgressBar();
            $.ajax({
                url: "{{ route('task.index') }}?search=" + searchTerm + "&status=" + selectedStatus + "&employee=" + employee + "&designation=" + designation + "&department=" + department + "&branch=" + branch + "&location=" + location + "&assigned_by=" + assigned_by + "&assigned_to=" + assigned_to + "&priority=" + priority + "&start_date=" + start_date + "&end_date=" + end_date + "&page=" + page + "&pageItems=" + pageItems,
                type: "GET",
                dataType: 'html',
                success: function(response) {
                    console.log(response);
                    $('#kt_customers_table tbody').html($(response).find('#kt_customers_table tbody').html());
                    $('#pagination-links').html($(response).find('#pagination-links').html()); // Update the pagination links

                },
                error: function() {
                    console.error('Error loading table data.');
                },
                complete: function() {
                    // hideProgressBar();
                }
            });
        }

        $(window).on('beforeunload', function() {
            $('#searchInput').val('');
            $('[name="employee"]').val('');
            $('[name="designation"]').val('');
            $('[name="department"]').val('');
            $('[name="branch"]').val('');
            $('[name="location"]').val('');
            $('[name="assigned_by"]').val('');
            $('[name="assigned_to"]').val('');
            $('[name="priority"]').val('');
        });

        // Function to update table data based on search and status
        function updateTableData(page = '') {
            var searchTerm = $('#searchInput').val();
            var selectedStatus = $('[data-kt-ecommerce-order-filter="status"]').val();
            var employee = $('[name="employee"]').val();
            var designation = $('[name="designation"]').val();
            var department = $('[name="department"]').val();
            var branch = $('[name="branch"]').val();
            var location = $('[name="location"]').val();
            var assigned_by = $('[name="assigned_by"]').val();
            var assigned_to = $('[name="assigned_to"]').val();
            var priority = $('[name="priority"]').val();
            var start_date = $('[name="start_date"]').val();
            var end_date = $('[name="end_date"]').val();
            var currentPage = $('ul.pagination li.active span').text();
            if ($('[name="row-count-filter"]').val()) {
                var pageItems = $('[name="row-count-filter"]').val();
            } else {
                var defaultPageItems = 10;
            }
            loadTableData(searchTerm, selectedStatus, employee, designation, department, branch, location, assigned_by, assigned_to, priority, start_date, end_date, page || currentPage, pageItems || defaultPageItems); // Use the provided page or current page if not provided
            console.log('Search and/or status updated');
        }

        updateTableData();


    });
</script>
<script>
    function deleteUser(userId) {
        Swal.fire({
            text: "Are you sure you would like to delete?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, return",
            customClass: {
                confirmButton: "btn btn-danger",
                cancelButton: "btn btn-active-light"
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: 'task/' + userId,
                    type: 'DELETE',
                    success: function(res) {
                        refreshTableContent();
                        Swal.fire({
                            title: "Deleted!",
                            text: res.message,
                            icon: "success",
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-success"
                            },
                            timer: 3000,
                        });

                        // Call the function here
                    }
                });
            }
        });
    }

    function refreshTableContent() {
        $.ajax({
            url: "{{ route('task.index') }}", // Replace with the actual route name or URL
            type: "GET",
            dataType: 'html',
            success: function(response) {
                // Update the table content with the refreshed data
                $('#kt_customers_table tbody').html($(response).find('#kt_customers_table tbody').html());
                // Reattach event listeners to the updated content
            },
            error: function() {
                console.error('Error loading table content.');
            }
        });
    }
</script>
@endsection