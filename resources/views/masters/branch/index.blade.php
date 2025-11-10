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
    <strong>Success:</strong> Branch has been successfully updated.
</div>

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Branch Office</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{url('dashboard')}}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            <a href="{{url('masters/branch')}}" class="text-muted text-hover-primary">Branch Office</a>
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
                                <div class="w-110px me-3">
                                      <div style="margin-top: 10px;"><label for="overdue_count">Total Records:</label>
                                      <span style="color: green;">{{$total_count ?? ''}}</span></div>
                                </div>
                                <!--begin::Filter-->
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
                                <div class="w-150px me-3">
                                    <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-order-filter="status">
                                        <option selected value="all">All</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <!--end::Select2-->
                                </div>
                                @can('master.create')
                                <button type="button" class="btn btn-primary float-end" id="addbranchBtn" data-bs-toggle="modal" data-bs-target="#userModal">

                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                            <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
                                        </svg>
                                    </span>
                                    Add Branch
                                </button>
                                @endcan
                            </div>
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
                    <!--begin::Card body jjj-->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                                <!--begin::Table head-->
                                <thead>
                                    <!--begin::Table row-->
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-50px">S.No</th>
                                        <th class="min-w-50px">Branch Code</th>
                                        <th class="min-w-125px">Branch office</th>
                                        <th class="min-w-125px">Status</th>
                                        @if(auth()->user()->can('master.edit') || auth()->user()->can('master.delete'))
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
                                            {{$data->branch_code}}
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
                                        @if(auth()->user()->can('master.edit') || auth()->user()->can('master.delete'))
                                       <td class="td">
                                            @can('master.edit')
                                            <a href="#" class="btn btn-icon btn-active-primary btn-light-primary mx-1 w-30px h-30px editbranchbtn" data-branch-id="{{ $data->id }}" data-action="edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('master.delete')
                                           <!-- Delete Button -->
                                            <button type="button" class="btn btn-icon btn-active-danger btn-light-danger mx-1 w-30px h-30px deletebranchBtn" data-branch-id="{{ $data->id }}">
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

<!-- bigin::modal -->

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Modal Title</h5>
                <button type="button" class="btn-close" id="modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-10 px-lg-17">
                <form id="ajax-form" method="post" action="{{ isset($branch) ? route('branch.update', $branch->id) : route('branch.save') }}">
                    @csrf
                    <input type="hidden" id="branchId" name="branch_id" value="">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label required">Branch ID:</label>
                        <input type="text" id="branch_code" name="branch_code" class="form-control" placeholder="Branch ID">
                        <span class="field-error" id="branch_code-error" style="color:red"></span>
                    </div>

                     <div class="mb-3">
                        <label class="form-label">
                            <span>Parent Branch:</span>
                        </label>
                        <select name="parent_branch" id="parent_branch" class="form-control" data-control="select2" data-minimum-results-for-search="Infinity" data-hide-search="true">
                            <option selected value="">Select Parent Branch</option>
                            @foreach($branches as $branchOption)
                                <option value="{{ $branchOption->id }}">{{ $branchOption->name }}</option>
                            @endforeach
                        </select> 
                        <span class="field-error" style="color:red" id="parent_branch-error"></span>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label required">Branch Name:</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Branch Name">
                        <span class="field-error" id="name-error" style="color:red"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">
                            <span>Status:</span>
                        </label>
                        <select name="status" class="form-control" data-control="select2" data-minimum-results-for-search="Infinity" data-hide-search="true">
                           
                            <option selected value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <span class="field-error" style="color:red" id="status-error"></span>
                    </div>


                    <div class="mb-3 text-center">
                        <button type="submit" class="btn btn-success btn-submit" id="submit">Submit</button>
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>




@endsection

@section('script')
@parent
<script>
    var modal = $('#userModal');
    var modalTitle = $('#modalTitle');
    var modalActionBtn = $('#submit');
    $(document).ready(function() {
        // Attach event listener to the "Edit" button
        $(document).on('click', '.editbranchbtn', function() {
            var branchId = $(this).data('branch-id');
            modalTitle.text('Update Branch');
            modalActionBtn.text('Update');
            $(this).addClass('loading-cursor');
            openModalForAction('update', branchId);
        });
        $('#addbranchBtn').click(function() {
            $('#parent_branch').val('#').trigger('change');
            modalTitle.text('Add Branch');
            modalActionBtn.text('Submit');
            openModalForAction('insert');

        });
        $('#searchInput').keyup(function() {
            updateTableData();

        });

        
        // Set default selected value to "all"
        $('select[data-kt-ecommerce-order-filter="status"]').val('all').trigger('change');
    

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
            loadTableData(searchTerm, selectedStatus, page, pageItems || defaultPageItems);
        }
        updateTableData();

        function loadTableData(searchTerm, selectedStatus, page = '',pageItems='') {
            $.ajax({
                url: "{{ route('branch.index') }}?search=" + searchTerm + "&status=" + selectedStatus + "&page=" + page + "&pageItems=" + pageItems,
                type: "GET",
                data: {
                    search: searchTerm,
                    status: selectedStatus,
                    page: page
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
        $(document).on('click', '.deletebranchBtn', function() {
            var branchId = $(this).data('branch-id');

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
                        url: 'branch/' + branchId,
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

        function openModalForAction(action, branchId = null) {
            $('#ajax-form')[0].reset(); // Reset the form
            var form = $('#ajax-form');
            form.attr('action', action === 'update' ? "{{ route('branch.update', ':id') }}" : "{{ route('branch.save') }}");
            $('#branchId').val(action === 'update' ? branchId : '');
            // Set the state ID in the hidden input
            form.find('.field-error').text('');

            if (action === 'update') {
                $.get("{{ route('branch.get', ':id') }}".replace(':id', branchId), function(data) {
                    $('#branchId').val(branchId);

                    let currentBranchId = branchId;
                    $('#branch_code').val(data.branch_code);
                    $('#name').val(data.name);
                    $('[name="status"]').val(data.status);
                    if ($('[name="status"]').hasClass('select2-hidden-accessible')) {
                        $('[name="status"]').select2('destroy'); // Destroy existing Select2
                    }
                    $('[name="status"]').val(data.status).select2();

                    // Handle parent_branch
                    let $parentSelect = $('#parent_branch');

                    // Remove previous Select2 (safe refresh)
                    if ($parentSelect.hasClass('select2-hidden-accessible')) {
                        $parentSelect.select2('destroy');
                    }
                    

                    $('#parent_branch option').show();
                     
                    $parentSelect.html(`@foreach($branches as $branchOption)<option value="{{ $branchOption->id }}">{{ $branchOption->name }}</option>@endforeach`);
                    $parentSelect.prepend(`<option value="" selected>Select Parent Branch</option>`);
                    $parentSelect.find('option[value="' + branchId + '"]').remove();
                     if(data.parent_id){
                         $('[name="parent_branch"]').val(data.parent_id).select2();
                    }else{
                        console.log('No parent branch found for this branch.');
                        $('[name="parent_branch"]').val('').select2();
                    }

                     // Re-initialize select2 after hiding option
                     $parentSelect.select2();
                   

                    $('#userModal').modal('show');
                    $('.editbranchbtn').removeClass('loading-cursor');
                });
            } else {
                if ($('[name="status"]').hasClass('select2-hidden-accessible')) {
                    $('[name="status"]').select2('destroy'); // Destroy existing Select2
                }
                $('[name="status"]').select2();

                let $parentSelect = $('#parent_branch');
                if ($parentSelect.hasClass('select2-hidden-accessible')) {
                    $parentSelect.select2('destroy');
                }

                $parentSelect.html(`@foreach($branches as $branchOption)<option value="{{ $branchOption->id }}">{{ $branchOption->name }}</option>@endforeach`);
                $parentSelect.prepend(`<option value="" selected>Select Parent Branch</option>`);

                $parentSelect.val('').select2();

                // Set the value for the Select2 dropdown
                $('#countryDropdown').val(branchId).trigger('change');

                $('#userModal').modal('show');
                $('.editbranchbtn').removeClass('loading-cursor');
            }
        }

        // Form submission using AJAX
        $('#ajax-form').on('submit', function(e) {
            
            e.preventDefault();
            var form = $(this);
            $('#pageLoader').fadeIn();
            var parentBranchValue = $('#parent_branch').find('option:selected').text();
            var branchName = $('#name').val();
            if(parentBranchValue.toLowerCase() === branchName.toLowerCase()){
                toastr.error("Parent branch and child branch name should not be same.");
                $('#pageLoader').fadeOut();
                return;
            }
            
            var parentBranchValue = $('#parent_branch').val();
            var serializedData = form.serialize();


            $.ajax({
                url: form.attr('action').replace(':id', $('#branchId').val()),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (form.attr('action').includes('update')) {

                        toastr.success("Branch Location has been successfully updated.");
                    } else {

                        toastr.success("Branch Location has been successfully inserted.");
                    }
                    $('#ajax-form')[0].reset();
                    $('#userModal').modal('hide');
                    location.reload();
                    $('#pageLoader').fadeOut(function() {
                        refreshTableContent();
                    });

                },
                error: function(response) {
                    console.log('Error response:', response);
                    $('#pageLoader').fadeOut();
                    if (response.status === 422 && response.responseJSON.errors) {
                        var errors = response.responseJSON.errors;
                        $('#ajax-form').find(".field-error").text('');
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]); // Display only the first error message for each field
                        });
                    } else {
                        toastr.error(response.responseJSON.error);
                    }
                }
            });
        });

        // function attachEventListeners() {
        //     $(document).on('click', '.editbranchbtn', function() {
        //         var branchId = $(this).data('branch-id');
        //         modalTitle.text('Update branch Location');
        //         modalActionBtn.text('Update');
        //         $(this).addClass('loading-cursor');
        //         openModalForAction('update', branchId);
        //     });
        // }
        function refreshTableContent() {
            $.ajax({
                url: "{{ route('branch.index') }}", // Replace with the actual route name or URL
                type: "GET",
                dataType: 'html',
                success: function(response) {
                    // Update the table content with the refreshed data
                    $('#kt_customers_table tbody').html($(response).find('#kt_customers_table tbody').html());
                    // Reattach event listeners to the updated content
                    // Assuming you have a separate function for attaching event listeners
                    attachEventListeners();
                    updateTableData();

                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }
        $('#userModal').on('hidden.bs.modal', function() {
            var form = $('#ajax-form');
            form[0].reset();
            form.find('.field-error').text('');
        });

    
    });
</script>


@endsection