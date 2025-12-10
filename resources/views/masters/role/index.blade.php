@extends('layouts.index')

@section('title', 'Role Management')

@push('styles')
<style>
    .toggle-switch input:checked+.toggle-slider {
        background-color: #16a34a !important;
    }

    .toggle-slider {
        background-color: #dc2626 !important;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    .toggle-switch input:checked+.toggle-slider:before {
        transform: translateX(20px);
    }
</style>
@endpush

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div style="padding: 2rem;">
    <h1 style="font-size: 40px; font-weight: 700; color: #333; margin-bottom: 8px;">Roles Management</h1>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
        Manage admin roles and permissions
    </p>
    <!-- Main Card -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem;">

        <!-- Search and Actions Bar -->
        <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;">
            <!-- Search Input -->
            <div style="position: relative; flex: 1; max-width: 600px;">
                <i class="fas fa-search"
                    style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                <input type="text" id="searchInput"
                    style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                    placeholder="Search by role ID or name...">
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <button type="button" id="filter_btn"
                    style="padding: 0.75rem 1.5rem; border: 1px solid #e5e7eb; background: white; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-filter" style="color: #6b7280;"></i>
                    Filter
                </button>

                @can('role.create')
                <button type="button" onclick="window.location='{{ route('role.create') }}'"
                    style="padding: 0.75rem 1.5rem; background: #ba0028; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-plus"></i>
                    Create Role
                </button>
                @endcan
            </div>
        </div>

        <!-- Filter Section (Hidden by default) -->
        <div id="filter_section"
            style="display: none; margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
            <select data-kt-ecommerce-order-filter="status"
                style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                <option value="all">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

        <!-- Table Container -->
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #dedede; background-color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 6px;" id="kt_customers_table">
                <thead>
                    <tr style="background: #ba0028; color: white; font-weight: 700; font-size: 14px;">
                        <th style="padding: 15px; text-align: left;">Role ID</th>
                        <th style="padding: 15px; text-align: left;">Role Name</th>
                        <th style="padding: 15px; text-align: left;">Status</th>
                        @if(auth()->user()->can('role.edit') || auth()->user()->can('role.delete'))
                        <th style="padding: 15px; text-align: left;">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if($datas->isEmpty())
                    <tr>
                        <td colspan="4" style="padding: 2rem; text-align: center; color: #6b7280;">No results found.
                        </td>
                    </tr>
                    @else
                    @foreach($datas as $data)
                    <tr style="border-bottom: 1px solid #dedede;">
                        <td style="padding: 1rem; font-size: 0.875rem; color: #111827;">
                            {{$data->role_id}}</td>
                        <td style="padding: 1rem; font-size: 0.875rem; color: #111827;">{{$data->name}}</td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <label class="toggle-switch"
                                    style="position: relative; display: inline-block; width: 44px; height: 24px;">
                                    <input type="checkbox" class="status-toggle" data-role-id="{{$data->id}}"
                                        {{$data->status == 1 ? 'checked' : ''}} style="opacity: 0; width: 0; height: 0;">
                                    <span class="toggle-slider"
                                        style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: 0.4s; border-radius: 24px;"></span>
                                </label>
                                <span
                                    style="padding: 0.375rem 0.75rem; background: {{$data->status == 1 ? '#dcfce7' : '#fee2e2'}}; color: {{$data->status == 1 ? '#16a34a' : '#dc2626'}}; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;">
                                    {{$data->status == 1 ? 'Active' : 'Inactive'}}
                                </span>
                            </div>
                        </td>
                        @if(auth()->user()->can('role.edit') || auth()->user()->can('role.delete'))
                        <td style="padding: 1rem; position: relative;">
                            <button class="action-menu-btn"
                                style="background: none; border: none; cursor: pointer; padding: 0.5rem;">
                                <i class="fas fa-ellipsis-v" style="color: #6b7280;"></i>
                            </button>
                            <div class="action-menu"
                                style="display: none; position: absolute; right: 10rem; bottom: 1.5rem; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0.5rem; padding: 0.5rem; z-index: 100; min-width: 150px;">
                                @can('role.edit')
                                <a href="{{ route('role.edit', ['id' => $data->id]) }}"
                                    style="display: block; padding: 0.5rem 1rem; color: #111827; text-decoration: none; font-size: 0.875rem; border-radius: 0.375rem; transition: background 0.2s;"
                                    onmouseover="this.style.background='#f3f4f6'"
                                    onmouseout="this.style.background='transparent'">
                                    <i class="fas fa-edit" style="margin-right: 0.5rem;"></i>Edit
                                </a>
                                @endcan
                                @can('role.delete')
                                <button type="button" class="deletestateBtn" data-role-id="{{ $data->id }}"
                                    style="display: block; width: 100%; text-align: left; padding: 0.5rem 1rem; background: none; border: none; color: #dc2626; cursor: pointer; font-size: 0.875rem; border-radius: 0.375rem; transition: background 0.2s;"
                                    onmouseover="this.style.background='#fee2e2'"
                                    onmouseout="this.style.background='transparent'">
                                    <i class="fas fa-trash" style="margin-right: 0.5rem;"></i>Delete
                                </button>
                                @endcan
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        
        <style>
            /* Add padding to tbody cells */
            #kt_customers_table tbody td {
                padding: 12px 15px;
                vertical-align: middle;
                box-sizing: border-box;
                border-bottom: 1px solid #dedede;
            }
        </style>

        <!-- Pagination -->
        <div style="margin-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div id="paginationInfo" style="color: #6b7280; font-size: 0.875rem;">
                    Showing {{ $datas->firstItem() ?? 0 }} to {{ $datas->lastItem() ?? 0 }} of {{ $datas->total() }} roles
                </div>
                <div id="paginationControls" style="display: flex; align-items: center; gap: 0.5rem;">
                    @if ($datas->onFirstPage())
                        <button disabled style="padding: 0.5rem 1rem; border: 1px solid #e5e7eb; background: #f9fafb; color: #9ca3af; border-radius: 0.375rem; font-size: 0.875rem; cursor: not-allowed;">
                            ‹ Previous
                        </button>
                    @else
                        <button onclick="goToPage({{ $datas->currentPage() - 1 }})" style="padding: 0.5rem 1rem; border: 1px solid #e5e7eb; background: white; color: #374151; border-radius: 0.375rem; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                            ‹ Previous
                        </button>
                    @endif
                    
                    <span style="margin: 0 1rem; font-weight: 500; color: #374151;">
                        Page {{ $datas->currentPage() }} of {{ $datas->lastPage() }}
                    </span>
                    
                    @if ($datas->hasMorePages())
                        <button onclick="goToPage({{ $datas->currentPage() + 1 }})" style="padding: 0.5rem 1rem; border: 1px solid #e5e7eb; background: white; color: #374151; border-radius: 0.375rem; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                            Next ›
                        </button>
                    @else
                        <button disabled style="padding: 0.5rem 1rem; border: 1px solid #e5e7eb; background: #f9fafb; color: #9ca3af; border-radius: 0.375rem; font-size: 0.875rem; cursor: not-allowed;">
                            Next ›
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@parent
<script>
    $(document).ready(function() {
        // Three-dot menu toggle
        $(document).on('click', '.action-menu-btn', function(e) {
            e.stopPropagation();
            $('.action-menu').not($(this).siblings('.action-menu')).hide();
            $(this).siblings('.action-menu').toggle();
        });

        // Close menu when clicking outside
        $(document).on('click', function() {
            $('.action-menu').hide();
        });

        // Prevent menu from closing when clicking inside it
        $(document).on('click', '.action-menu', function(e) {
            e.stopPropagation();
        });

        // Filter toggle
        $('#filter_btn').click(function() {
            $('#filter_section').toggle();
        });

        $('#searchInput').keyup(function() {
            updateTableData();
        });

        $('[data-kt-ecommerce-order-filter="status"]').on('change', function() {
            updateTableData();
        });

        // Pagination function
        window.goToPage = function(page) {
            updateTableData(page);
        };

        $(document).on('change', '[name="row-count-filter"]', function(e) {
            var page = 1;
            updateTableData(page);
        });

        function updateTableData(page = '') {
            var searchTerm = $('#searchInput').val();
            var selectedStatus = $('[data-kt-ecommerce-order-filter="status"]').val();
            if ($('[name="row-count-filter"]').val()) {
                var pageItems = $('[name="row-count-filter"]').val();
            } else {
                var defaultPageItems = 10;
            }
            loadTableData(searchTerm, selectedStatus, page, pageItems || defaultPageItems);
        }
        updateTableData();

        function loadTableData(searchTerm, selectedStatus, page = '', pageItems = '') {
            $.ajax({
                url: "{{ route('role.index') }}?search=" + searchTerm + "&status=" + selectedStatus + "&page=" + page + "&pageItems=" + pageItems,
                type: "GET",
                data: {
                    search: searchTerm,
                    status: selectedStatus,
                    page: page,
                    pageItems: pageItems
                },
                dataType: 'html',
                success: function(response) {
                    $('#kt_customers_table tbody').html($(response).find('#kt_customers_table tbody').html());
                    $('#paginationInfo').html($(response).find('#paginationInfo').html());
                    $('#paginationControls').html($(response).find('#paginationControls').html());
                },
                error: function() {
                    console.error('Error loading table data.');
                }
            });
        }

        // Status toggle handler
        $(document).on('change', '.status-toggle', function() {
            const roleId = $(this).data('role-id');
            const newStatus = $(this).is(':checked') ? 1 : 0;
            const toggle = $(this);

            $.ajax({
                url: "{{ route('role.toggle-status') }}",
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    role_id: roleId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        updateTableData();
                    }
                },
                error: function(xhr) {
                    // Revert toggle on error
                    toggle.prop('checked', !newStatus);
                    showToast(xhr.responseJSON?.error, 'error');
                }
            });
        });

        $(document).on('click', '.deletestateBtn', function(e) {
            e.stopPropagation();
            var roleId = $(this).data('role-id');
            confirmBox("Are you sure you want to delete this user?", function() {
                $.ajax({
                    url: "{{ route('role.delete', ['id' => ':id']) }}".replace(':id', roleId),
                    type: 'DELETE',
                    success: function(res) {
                        showToast(res.message);
                        refreshTableContent();
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON?.error || "Something went wrong. Please try again.";
                        showToast(errorMessage, 'error');
                    }
                });
            });
            // var roleId = $(this).data('role-id');
            // $('.action-menu').hide(); 
            // getRoleUser(roleId, function (hasActiveUsers) {
            //     var deleteMessage = "Are you sure you would like to delete?";
            //     if (hasActiveUsers) {
            //         deleteMessage = "This role has active users. Are you sure you want to delete it?";
            //     }
            //     Swal.fire({
            //         text: deleteMessage,
            //         icon: "warning",
            //         showCancelButton: true,
            //         buttonsStyling: false,
            //         confirmButtonText: "Yes, delete it!",
            //         cancelButtonText: "No, return",
            //         customClass: {
            //             confirmButton: "btn btn-danger",
            //             cancelButton: "btn btn-active-light"
            //         }
            //     }).then(function (result) {
            //         if (result.isConfirmed) {
            //             $.ajaxSetup({
            //                 headers: {
            //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //                 }
            //             });
            //             $.ajax({
            //                 url: "{{ route('role.delete', ['id' => ':id']) }}".replace(':id', roleId),
            //                 type: 'DELETE',
            //                 success: function (res) {
            //                     Swal.fire({
            //                         title: "Deleted!",
            //                         text: res.message,
            //                         icon: "success",
            //                         confirmButtonText: "Ok, got it!",
            //                         customClass: {
            //                             confirmButton: "btn btn-success"
            //                         },
            //                         timer: 3000,
            //                     });
            //                     refreshTableContent();
            //                 },
            //                 error: function (xhr, status, error) {
            //                     var errorMessage = xhr.responseJSON?.error || "Something went wrong. Please try again.";
            //                     Swal.fire({
            //                         title: "Error!",
            //                         text: errorMessage,
            //                         icon: "error",
            //                         confirmButtonText: "Ok, got it!",
            //                         customClass: {
            //                             confirmButton: "btn btn-danger"
            //                         },
            //                         timer: 4000,
            //                                   });
            //                     }
            //                 });
            //         }
            //     });
            // });
        });

        function refreshTableContent() {
            $.ajax({
                url: "{{ route('role.index') }}",
                type: "GET",
                dataType: 'html',
                success: function(response) {
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