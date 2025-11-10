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
    <strong>Success:</strong> Role has been successfully updated.
</div>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Roles</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{url('dashboard')}}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            <a href="{{url('/role')}}" class="text-muted text-hover-primary">Roles</a>
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
                                <!--begin::Filter-->
                                <div class="w-150px me-3">
                                    <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-order-filter="status">
                                        <option value="all">All</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <!--end::Select2-->
                                </div>
                                @if(auth()->user()->can('role.create'))
                                <button type="button" class="btn btn-primary float-end" onclick="window.location='{{url('/role/add_edit')}}'">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
                                        </svg>
                                    </span>
                                    Add Role
                                </button>
                                @endif
                            </div>
                            <!--end::Group actions-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                            <!--begin::Table head-->
                            <thead>
                                <!--begin::Table row-->
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-120px">S.No</th>
                                    <th class="min-w-125px">role</th>
                                    <th class="min-w-125px">Status</th>
                                    @if(auth()->user()->can('role.edit') || auth()->user()->can('role.delete'))
                                    <th class="min-w-125px">Actions</th>
                                    @endif
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="fw-semibold text-gray-600">
                            @if($datas->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">No results found.</td>
                                </tr>
                            @else

                            @foreach($datas as $data)
                                <tr>
                                    <td>
                                        {{ $serialNumberStart++ }}
                                    </td>
                                    <td>
                                        {{$data->name}}
                                    </td>
                                    <td>
                                        @if($data->status==1)

                                        <div class="badge badge-light-success">Active</div>
                                        @else
                                        <div class="badge badge-light-danger"> InActive</div>
                                        @endif
                                    </td>
                                    <!--end::Status=-->
                                    <!--begin::IP Address=-->
                                    @if(auth()->user()->can('role.edit') || auth()->user()->can('role.delete'))
                                    <td class="td">
                                        @can('role.edit')
                                        <a href="{{ route('role.edit', ['id' => $data->id]) }}" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px editbranchbtn">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('role.delete')
                                        <!-- Delete Button -->
                                        <button type="button" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px deletestateBtn" data-role-id="{{ $data->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        @endcan
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                                @endif

                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                        <div class="row">
                            <div id="paginationLinks" class="col-lg-12 col-md-12 col-sm-12">
                               {{ $datas->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
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
        $('#searchInput').keyup(function() {
            updateTableData();

        });
        // Event listener for status select change
        $('[data-kt-ecommerce-order-filter="status"]').on('change', function() {
            updateTableData();

        });
        $(document).on('click', '#paginationLinks a', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            updateTableData(page);
        });

        $(document).on('change', '[name="row-count-filter"]', function (e) {
            var page = 1;
            updateTableData(page);
        });

        function updateTableData(page = '') {
            var searchTerm = $('#searchInput').val();
            var selectedStatus = $('[data-kt-ecommerce-order-filter="status"]').val();
            if($('[name="row-count-filter"]').val()){
                var pageItems = $('[name="row-count-filter"]').val();
            }else{
                var defaultPageItems = 10;
            }
            loadTableData(searchTerm, selectedStatus, page ,pageItems || defaultPageItems);
        }
        updateTableData();

        function loadTableData(searchTerm, selectedStatus, page = '',pageItems='') {
            $.ajax({
                url: "{{ route('role.index') }}?search=" + searchTerm + "&status=" + selectedStatus + "&page=" + page + "&pageItems=" + pageItems,
                type: "GET",
                data: {
                    search: searchTerm,
                    status: selectedStatus,
                    page: page,
                    pageItems:pageItems
                },
                dataType: 'html',
                success: function(response) {
                    console.log(response);
                    $('#kt_customers_table tbody').html($(response).find('#kt_customers_table tbody').html());
                    $('#paginationLinks').html($(response).find('#paginationLinks').html());
                },
                error: function() {
                    console.error('Error loading table data.');
                }
            });
        }
        // Attach event listener to the "Delete" button
        $(document).on('click', '.deletestateBtn', function() {
            var roleId = $(this).data('role-id');
            getRoleUser(roleId, function(hasActiveUsers) {
            var deleteMessage = "Are you sure you would like to delete?";
            if (hasActiveUsers) {
                deleteMessage = "This role has active users. Are you sure you want to delete it?";
            }
            Swal.fire({
                text: deleteMessage,
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
                        url: "{{ route('role.delete', ['id' => ':id']) }}".replace(':id', roleId),
                        type: 'DELETE',
                        success: function(res) {
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
                            refreshTableContent();
                        },
                        error: function(xhr, status, error) {
                            var errorMessage = xhr.responseJSON?.error || "Something went wrong. Please try again.";
                            Swal.fire({
                                title: "Error!",
                                text: errorMessage,
                                icon: "error",
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-danger"
                                },
                                timer: 4000,
                            });
                        }
                    });
                }
            });
          });
        });

        function refreshTableContent() {
            $.ajax({
                url: "{{ route('role.index') }}", // Replace with the actual route name or URL
                type: "GET",
                dataType: 'html',
                success: function(response) {
                    // Update the table content with the refreshed data
                    $('#kt_customers_table tbody').html($(response).find('#kt_customers_table tbody').html());
                    updateTableData();

                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }

        function getRoleUser(roleId, callback) {
                $.ajax({
                    url: "{{ route('role.role_user', ['id' => ':id']) }}".replace(':id', roleId),
                    type: "GET",
                    dataType: 'json',
                    success: function(response) {
                        if (typeof callback === "function") {
                            callback(response.hasActiveUsers);
                        }
                    },
                    error: function() {
                        console.error('Error checking role user status.');
                    }
                });
        }
    });
</script>


@endsection