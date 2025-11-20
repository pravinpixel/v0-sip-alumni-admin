@extends('layouts.index')

@section('title', 'User Management')

@push('styles')
    <style>
        .toggle-switch input:checked+.toggle-slider {
            background-color: #16a34a;
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
        <!-- Header Section -->
        <div style="margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">User Management</h1>
            <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.5rem;">Manage admin users and their access
                permissions</p>
        </div>

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
                        placeholder="Search by user ID, name, or email...">
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <button type="button" id="filter_panel"
                        style="padding: 0.75rem 1.5rem; border: 1px solid #e5e7eb; background: white; border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-filter" style="color: #6b7280;"></i>
                        Filter
                    </button>

                    @can('user.create')
                        <button type="button" onclick="window.location='{{ route('user.create') }}'"
                            style="padding: 0.75rem 1.5rem; background: #dc2626; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-plus"></i>
                            Create User
                        </button>
                    @endcan
                </div>
            </div>

            @include('masters/user.filter')

            <!-- Table Container -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;" id="kt_customers_table">
                    <thead>
                        <tr style="background: #dc2626; color: white;">
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-size: 0.875rem;">User ID</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-size: 0.875rem;">User Name
                            </th>
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-size: 0.875rem;">Email ID
                            </th>
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-size: 0.875rem;">Role</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-size: 0.875rem;">Status</th>
                            @if(auth()->user()->can('user.edit') || auth()->user()->can('user.delete'))
                                <th style="padding: 1rem; text-align: left; font-weight: 700; font-size: 0.875rem;">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if($datas->isEmpty())
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; color: #6b7280;">No results found.
                                </td>
                            </tr>
                        @else
                            @foreach($datas as $data)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 1rem; font-size: 0.875rem; color: #111827;">
                                        USER{{ str_pad($serialNumberStart++, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td style="padding: 1rem; font-size: 0.875rem; color: #111827;">{{$data->name}}</td>
                                    <td style="padding: 1rem; font-size: 0.875rem; color: #111827;">{{$data->email ?? 'N/A'}}</td>
                                    <td style="padding: 1rem;">
                                        <span
                                            style="padding: 0.375rem 0.75rem; background: #f3f4f6; color: #4b5563; border-radius: 0.375rem; font-size: 0.875rem;">
                                            {{$data->role->name ?? 'N/A'}}
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <label class="toggle-switch"
                                                style="position: relative; display: inline-block; width: 44px; height: 24px;">
                                                <input type="checkbox" class="status-toggle" data-user-id="{{$data->id}}"
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
                                    @if(auth()->user()->can('user.edit') || auth()->user()->can('user.delete'))
                                        <td style="padding: 1rem; position: relative;">
                                            <button class="action-menu-btn"
                                                style="background: none; border: none; cursor: pointer; padding: 0.5rem;">
                                                <i class="fas fa-ellipsis-v" style="color: #6b7280;"></i>
                                            </button>
                                            <div class="action-menu"
                                                style="display: none; position: absolute; right: 2rem; top: 2.5rem; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0.5rem; padding: 0.5rem; z-index: 100; min-width: 150px;">
                                                @can('user.edit')
                                                    <a href="{{ route('user.edit', ['id' => $data->id]) }}"
                                                        style="display: block; padding: 0.5rem 1rem; color: #111827; text-decoration: none; font-size: 0.875rem; border-radius: 0.375rem; transition: background 0.2s;"
                                                        onmouseover="this.style.background='#f3f4f6'"
                                                        onmouseout="this.style.background='transparent'">
                                                        <i class="fas fa-edit" style="margin-right: 0.5rem;"></i>Edit
                                                    </a>
                                                @endcan
                                                @can('user.delete')
                                                    <button type="button" class="deletestateBtn" data-user-id="{{ $data->id }}"
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

            <!-- Pagination -->
            <div style="margin-top: 1.5rem;">
                <div id="paginationLinks">
                    {{ $datas->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {
            let eventListenersActive = true;

            // Three-dot menu toggle
            $(document).on('click', '.action-menu-btn', function (e) {
                e.stopPropagation();
                $('.action-menu').not($(this).siblings('.action-menu')).hide();
                $(this).siblings('.action-menu').toggle();
            });

            // Close menu when clicking outside
            $(document).on('click', function () {
                $('.action-menu').hide();
            });

            // Prevent menu from closing when clicking inside it
            $(document).on('click', '.action-menu', function (e) {
                e.stopPropagation();
            });
            $('#searchInput').keyup(function () {
                if (eventListenersActive) {
                    updateTableData();
                }

            });
            // Event listener for status select change
            $('[data-kt-ecommerce-order-filter="status"]').on('change', function () {
                if (eventListenersActive) {
                    updateTableData();
                }

            });

            // Event listener for status select change
            $('[data-kt-ecommerce-order-filter="role"]').on('change', function () {
                if (eventListenersActive) {
                    updateTableData();
                }
            });

            $(document).on('click', '#paginationLinks a', function (e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                updateTableData(page);
            });

            $(document).on('change', '[name="row-count-filter"]', function (e) {
                if (eventListenersActive) {
                    var page = 1;
                    updateTableData(page);
                }
            });

            // Filter Show / Hide Functionality
            $(document).on('click', '#filter_panel', function (e) {
                $("#filter_sub").toggle();
            });

            document.getElementById('clear-filters').addEventListener('click', function () {
                var form = document.getElementById('filter_form');
                eventListenersActive = false;
                // Clear all select inputs
                form.querySelectorAll('select.form-select').forEach(function (select) {
                    select.value = '';
                    if ($(select).data('select2')) {
                        $(select).val('all').trigger('change');
                    }
                });
                var searchInput = form.querySelector('#searchInput');
                if (searchInput) {
                    searchInput.value = '';
                }
                updateTableData();
                eventListenersActive = true;
            });

            function updateTableData(page = '') {
                var searchTerm = $('#searchInput').val();
                var selectedStatus = $('[data-kt-ecommerce-order-filter="status"]').val();
                var selectedRole = $('[data-kt-ecommerce-order-filter="role"]').val();
                if ($('[name="row-count-filter"]').val()) {
                    var pageItems = $('[name="row-count-filter"]').val();
                } else {
                    var defaultPageItems = 10;
                }
                loadTableData(searchTerm, selectedStatus, selectedRole, page, pageItems || defaultPageItems);
            }
            updateTableData();

            function loadTableData(searchTerm, selectedStatus, selectedRole, page = '', pageItems = '') {
                $.ajax({
                    url: "{{ route('user.index') }}?search=" + searchTerm + "&status=" + selectedStatus + "&role=" + selectedRole + "&page=" + page + "&pageItems=" + pageItems,
                    type: "GET",
                    data: {},
                    dataType: 'html',
                    success: function (response) {
                        console.log(response);
                        $('#kt_customers_table tbody').html($(response).find('#kt_customers_table tbody').html());
                        $('#paginationLinks').html($(response).find('#paginationLinks').html());
                    },
                    error: function () {
                        console.error('Error loading table data.');
                    }
                });
            }
            // Status toggle handler
            $(document).on('change', '.status-toggle', function () {
                const userId = $(this).data('user-id');
                const newStatus = $(this).is(':checked') ? 1 : 0;
                const toggle = $(this);

                $.ajax({
                    url: "{{ route('user.toggle-status') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        user_id: userId,
                        status: newStatus
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "Ok",
                                customClass: {
                                    confirmButton: "btn btn-success"
                                },
                                timer: 2000,
                            });
                            updateTableData();
                        }
                    },
                    error: function (xhr) {
                        // Revert toggle on error
                        toggle.prop('checked', !newStatus);
                        Swal.fire({
                            title: "Error!",
                            text: xhr.responseJSON?.message || "Failed to update status",
                            icon: "error",
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                    }
                });
            });

            // Attach event listener to the "Delete" button
            $(document).on('click', '.deletestateBtn', function () {
                var userId = $(this).data('user-id');

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
                }).then(function (result) {
                    if (result.isConfirmed) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "{{ route('user.delete', ['id' => ':id']) }}".replace(':id', userId),
                            type: 'DELETE',
                            success: function (res) {
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
                            }
                        });
                    }
                });
            });

            function refreshTableContent() {
                $.ajax({
                    url: "{{ route('user.index') }}", // Replace with the actual route name or URL
                    type: "GET",
                    dataType: 'html',
                    success: function (response) {
                        // Update the table content with the refreshed data
                        $('#kt_customers_table tbody').html($(response).find('#kt_customers_table tbody').html());
                        updateTableData();

                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }
        });
    </script>


@endsection