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
    .action_td {
        display: flex;
    }

    .btn:not(.btn-outline):not(.btn-dashed):not(.border-hover):not(.border-active):not(.btn-flush):not(.btn-icon) {
        border: 0 !important;
        padding: 10px !important;
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
<div id="successMessage" class="alert alert-success" style="display: none;">
    <strong>Success:</strong> Organization has been successfully updated.
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
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Organization</h1>
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
                            <a href="{{url('organization')}}" class="text-muted text-hover-primary">Organization</a>
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page title-->
                <!--begin::Actions-->
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <!--begin::Filter menu-->
                    <div class="m-0 mt-3">
                        <button type="button" class="btn btn-primary me-3" data-bs-toggle="tooltip"
                            id="export">
                            <i class="fa-solid fa-download"><span class="path1"></span><span
                                    class="path2"></span></i>
                            Download Report
                        </button>
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
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Toolbar-->
                                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                    <!--begin::Filter-->
                                    <div class="w-150px me-3">
                                        <div style="margin-top: 10px; margin-left: 19px; font-size: 13px"><label for="overdue_count">Total Records:</label>
                                            <span id="total-records" style="color: green;">{{$total_count ?? ''}}</span>
                                        </div>
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
                                    <!--begin::Add customer-->
                                    @can('organization.create')
                                    <a type="button" class="btn btn-primary" href="{{url('/organization/create')}}">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
                                            </svg>
                                        </span>
                                        Add Organization
                                    </a>
                                    @endcan
                                    <!--end::Add customer-->
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
                        <!--begin::Card title-->

                    </div>
                    <!--end::Card header-->
                    @include('organization.filter')
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
                                        <th class="min-w-150px">Customer Code</th>
                                        <th class="min-w-150px">Company Name</th>
                                        <th class="min-w-125px">Address</th>
                                        <th class="min-w-120px">Location</th>
                                        <th class="min-w-200px">Primary Contact Detail 1</th>
                                        <th class="min-w-200px">Primary Contact Detail 2</th>

                                        <th class="min-w-125px">Actions</th>

                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-semibold text-gray-600">
                                    @if($organizations->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center">No results found.</td>
                                    </tr>
                                    @else
                                    @foreach($organizations as $organization)
                                    <tr>
                                        <td>{{ $serialNumberStart++ }}</td>
                                        <td>
                                            {{ $organization->customer_code ?? '' }}
                                        </td>
                                        <!--begin::Name=-->
                                        <td>
                                            {{ $organization->company_name ?? '' }}
                                        </td>
                                        <!--end::Name=-->
                                        <!--begin::Branch Name=-->
                                        <td>
                                            {{$organization->address}}
                                        </td>
                                        <!--end::Branch Name=-->
                                        <!--begin::Location=-->
                                        <td>
                                            {{$organization->location->name ?? ''}}
                                        </td>
                                        <!--end::Location=-->

                                        <!--begin::phone_number=-->
                                        <td>
                                            {{$organization->primary_contact_detail_1}}
                                        </td>
                                        <!--end::phone_number=-->
                                        <!--begin::department=-->
                                        <td>
                                            {{$organization->primary_contact_detail_2}}
                                        </td>
                                        <!--end::department=-->
                                        <!--begin::action=-->
                                        @if(auth()->user()->can('organization.edit') || auth()->user()->can('organization.delete'))
                                        <td class="td action_td">
                                            @can('organization.edit')
                                            <a href="{{url('/organization/edit', $organization->id)}}" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px ">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('organization.delete')
                                            <button type="button" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px" onclick="deleteOrganization('{{ $organization->id }}')">
                                                <i class="fa fa-trash"></i></button>
                                            @endcan
                                        </td>
                                        @endif
                                        <!--end::action=-->
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                                <!--end::Table body-->


                            </table>
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="show-pg">
                                        <!--  <p>Showing 1 to 9 of 9 entries</p> -->
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-0 col-sm-0">

                                </div>
                                <div id="pagination-links" class="col-lg-4 col-md-6 col-sm-12">
                                    {{ $organizations->links('pagination::bootstrap-4') }}
                                </div>

                            </div>
                        </div>
                        <!--end::Table-->
                    </div>
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
            updateTableData();
        });
        // Filter Show / Hide Functionality
        $(document).on('click', '#filter_panel', function(e) {
            $("#filter_sub").toggle();
        });

        $(document).on('change', '[name="location"]', function(e) {
            if (eventListenersActive) {
                updateTableData();
            }
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
            var searchInput = form.querySelector('#searchInput');
            if (searchInput) {
                searchInput.value = '';
            }
            updateTableData();
            eventListenersActive = true;
        });

        $('#pagination-links').on('click', '.pagination a', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1]; // Use index 1 to get the page number
            updateTableData(page);
        });

        $(document).on('change', '[name="row-count-filter"]', function(e) {
            if (eventListenersActive) {
                // Set Default to First Page to load table from starting
                var page = 1
                updateTableData(page);
            }
        });

        // Function to load table data with search term and status
        function loadTableData(searchTerm, location, page = '', pageItems = '') {
            // showProgressBar();
            $.ajax({
                url: "{{ route('organization.index') }}?search=" + searchTerm + "&location=" + location + "&page=" + page + "&pageItems=" + pageItems,
                type: "GET",
                dataType: 'html',
                success: function(response) {
                    console.log(response);
                    $('#kt_customers_table tbody').html($(response).find('#kt_customers_table tbody').html());
                    $('#pagination-links').html($(response).find('#pagination-links').html());
                    $('#total-records').html('<span style="color: green;">' + $(response).find('#total-records').text() + '</span>');

                },
                error: function() {
                    console.error('Error loading table data.');
                },
                complete: function() {
                    //    hideProgressBar();
                }
            });
        }

        // Function to update table data based on search and status
        function updateTableData(page = '') {
            var searchTerm = $('#searchInput').val();
            var location = $('[name="location"]').val();
            var currentPage = $('ul.pagination li.active span').text();
            var pageItems;
            if ($('[name="row-count-filter"]').val()) {
                pageItems = $('[name="row-count-filter"]').val();
            } else {
                var defaultPageItems = 10;
            }
            loadTableData(searchTerm, location, page || currentPage, pageItems || defaultPageItems);
            console.log('Search and/or status updated');
        }

        updateTableData();
    });
</script>
<script>
    function deleteOrganization(organizationId) {
        Swal.fire({
            text: 'Are you sure you would like to delete?',
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
                    url: 'organization/' + organizationId,
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
                    }
                });
            }
        });

    }

    function refreshTableContent() {
        $.ajax({
            url: "{{ route('organization.index') }}",
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
<script>
    $(document).on('click', '#export', function() {
        $('#pageLoader').fadeIn();
        let pageData = {
            search: $('#searchInput').val(),
            location: $('#location').val(),
        };
        
        $.ajax({
            url: "{{ route('organization.export') }}",
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
                a.download = 'organization.xlsx';
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