@extends('layouts.index')

@section('title', 'User Management')

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

    <div class="content-container">
            <h1 class="main-title">User Management</h1>
            <p class="main-subtitle">Manage admin users and their access
                permissions</p>

        <!-- Main Card -->
        <div class="table-box-container">

            <!-- Search and Actions Bar -->
            <div class="search-filter-container">
                <!-- Search Input -->
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput"
                        placeholder="Search by user ID, name, or email...">
                </div>

                <!-- Action Buttons -->
                <div class="filter-btns">
                    <button id="filterToggleBtn">
                        <i class="fas fa-filter"></i>
                        <span id="filterBtnText">Filter</span>
                    </button>

                    @can('user.create')
                        <button type="button" class="create-button" onclick="window.location='{{ route('user.create') }}'">
                            <i class="fas fa-plus"></i>
                            Create User
                        </button>
                    @endcan
                </div>
            </div>

            @include('masters/user.filter')

            <!-- Table Container -->
            <div style="overflow-x: auto; border-radius: 8px 8px 0 0;">
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #dedede; background-color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 6px;" id="kt_customers_table">
                    <thead>
                        <tr style="background: #ba0028; color: white; font-weight: 700; font-size: 14px;">
                            <th style="padding: 15px; text-align: left;">User ID</th>
                            <th style="padding: 15px; text-align: left;">User Name</th>
                            <th style="padding: 15px; text-align: left;">Email ID</th>
                            <th style="padding: 15px; text-align: left;">Role</th>
                            <th style="padding: 15px; text-align: left;">Status</th>
                            @if(auth()->user()->can('user.edit') || auth()->user()->can('user.delete'))
                                <th style="padding: 15px; text-align: left;">Actions</th>
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
                                <tr style="border-bottom: 1px solid #dedede;">
                                    <td style="padding: 1rem; font-size: 0.875rem; color: #111827;">
                                        {{ $data->user_id }}</td>
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
                                                style="display: none; position: absolute; right: 7rem; bottom: 1.5rem; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0.5rem; padding: 0.5rem; z-index: 100; min-width: 150px;">
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
                    <div id="paginationInfo">
                        Showing {{ $datas->firstItem() ?? 0 }} to {{ $datas->lastItem() ?? 0 }} of {{ $datas->total() }} users
                    </div>
                    <div id="paginationControls">
                        @if ($datas->onFirstPage())
                            <button disabled >
                                <i class="fas fa-chevron-left"></i> Previous
                            </button>
                        @else
                            <button onclick="goToPage({{ $datas->currentPage() - 1 }})">
                                <i class="fas fa-chevron-left"></i> Previous
                            </button>
                        @endif
                        
                        <span>
                            Page {{ $datas->currentPage() }} of {{ $datas->lastPage() }}
                        </span>
                        
                        @if ($datas->hasMorePages())
                            <button onclick="goToPage({{ $datas->currentPage() + 1 }})">
                                Next <i class="fas fa-chevron-right"></i>
                            </button>
                        @else
                            <button disabled >
                                Next <i class="fas fa-chevron-right"></i>
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
        $(document).ready(function () {
            let eventListenersActive = true;
            let currentSortColumn = 0;
            let currentSortDirection = 'desc'; // User ID starts with down arrow (desc)
            
            // Store original pagination info
            let originalPaginationInfo = $('#paginationInfo').html();
            let originalPaginationControls = $('#paginationControls').html();

            // Filter functionality
            $('#filterToggleBtn').on('click', function() {
                const filterSection = $('#filterSection');
                const isVisible = filterSection.is(':visible');
                
                if (isVisible) {
                    filterSection.slideUp();
                    $('#filterBtnText').text('Filter');
                } else {
                    filterSection.slideDown();
                    $('#filterBtnText').text('Close Filter');
                }
            });

            // Filter dropdown functionality
            $('.filter-dropdown-btn').on('click', function(e) {
                e.stopPropagation();
                const dropdown = $(this).siblings('.filter-dropdown-menu');
                
                // Close other dropdowns
                $('.filter-dropdown-menu').not(dropdown).hide();
                
                // Toggle current dropdown
                dropdown.toggle();
            });

            // Filter option selection
            $('.filter-option').on('click', function(e) {
                e.stopPropagation();
                const checkbox = $(this).find('input[type="checkbox"]');
                const filterType = $(this).closest('.filter-dropdown').find('.filter-dropdown-btn').data('filter');
                const optionValue = $(this).data('value');
                
                // Handle "All" options
                if (optionValue === 'all') {
                    if (!checkbox.prop('checked')) {
                        // If selecting "All", uncheck other options and check "All"
                        $(this).siblings('.filter-option').find('input[type="checkbox"]').prop('checked', false);
                        checkbox.prop('checked', true);
                    } else {
                        // If unchecking "All", just uncheck it
                        checkbox.prop('checked', false);
                    }
                } else {
                    // If selecting specific option, uncheck "All"
                    $(this).siblings('.filter-option[data-value="all"]').find('input[type="checkbox"]').prop('checked', false);
                    // Toggle the clicked option
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
                
                // Update filter count
                updateFilterCount(filterType);
                
                // Apply filters
                applyFilters();
            });

            // Clear all filters
            $('#clearFiltersBtn').on('click', function() {
                $('.filter-option input[type="checkbox"]').prop('checked', false);
                $('.filter-count').hide().text('0');
                $(this).hide();
                applyFilters();
                // Restore original pagination when clearing filters
                restoreOriginalPagination();
            });

            // Close dropdowns when clicking outside
            $(document).on('click', function() {
                $('.filter-dropdown-menu').hide();
            });

            // Initialize - make sure all rows are visible by default
            function initializeTable() {
                $('#kt_customers_table tbody tr').show();
            }

            // Call initialization
            initializeTable();

            function updateFilterCount(filterType) {
                const checkedCount = $(`.filter-dropdown-btn[data-filter="${filterType}"]`)
                    .siblings('.filter-dropdown-menu')
                    .find('input[type="checkbox"]:checked').length;
                
                const countBadge = $(`.filter-count[data-filter="${filterType}"]`);
                
                if (checkedCount > 0) {
                    countBadge.text(checkedCount).show();
                    $('#clearFiltersBtn').show();
                } else {
                    countBadge.hide();
                    
                    // Check if any filters are active
                    const totalFilters = $('.filter-option input[type="checkbox"]:checked').length;
                    if (totalFilters === 0) {
                        $('#clearFiltersBtn').hide();
                    }
                }
            }

            function applyFilters() {
                // Check if "All Status" is selected
                const allStatusSelected = $('.filter-dropdown-menu[data-filter="status"] .filter-option[data-value="all"] input[type="checkbox"]').prop('checked');
                const allRoleSelected = $('.filter-dropdown-menu[data-filter="role"] .filter-option[data-value="all"] input[type="checkbox"]').prop('checked');
                
                if (allStatusSelected && allRoleSelected) {
                    // Show all rows if both "All" options are selected
                    $('#kt_customers_table tbody tr').show();
                    updatePaginationInfo();
                    return;
                }

                // Get selected filters
                const selectedStatuses = [];
                const selectedRoles = [];
                
                $('.filter-dropdown-menu[data-filter="status"] input[type="checkbox"]:checked').each(function() {
                    const value = $(this).closest('.filter-option').data('value');
                    if (value !== 'all') {
                        selectedStatuses.push(value);
                    }
                });
                
                $('.filter-dropdown-menu[data-filter="role"] input[type="checkbox"]:checked').each(function() {
                    const value = $(this).closest('.filter-option').data('value');
                    if (value !== 'all') {
                        selectedRoles.push(value.toString());
                    }
                });

                // If no filters selected, show all rows
                if (selectedStatuses.length === 0 && selectedRoles.length === 0) {
                    $('#kt_customers_table tbody tr').show();
                    updatePaginationInfo();
                    return;
                }

                // Apply filters to table rows
                $('#kt_customers_table tbody tr').each(function() {
                    let showRow = true;
                    
                    // Skip the "No results found" row
                    if ($(this).find('td[colspan]').length > 0) {
                        $(this).show();
                        return;
                    }
                    
                    // Check status filter
                    if (selectedStatuses.length > 0 && !allStatusSelected) {
                        const statusCell = $(this).find('td:nth-child(5)'); // Status column (5th column)
                        const statusSpan = statusCell.find('span:last-child'); // Get the status span
                        const statusText = statusSpan.text().trim().toLowerCase();
                        
                        let statusMatch = false;
                        for (let status of selectedStatuses) {
                            if (status === 1 && statusText === 'active') {
                                statusMatch = true;
                                break;
                            } else if (status === 0 && statusText === 'inactive') {
                                statusMatch = true;
                                break;
                            }
                        }
                        
                        if (!statusMatch) {
                            showRow = false;
                        }
                    }
                    
                    // Check role filter
                    if (selectedRoles.length > 0 && !allRoleSelected && showRow) {
                        const roleCell = $(this).find('td:nth-child(4)'); // Role column (4th column)
                        const roleSpan = roleCell.find('span'); // Get the role span
                        const roleText = roleSpan.text().trim();
                        
                        console.log('Role text found:', roleText); // Debug log
                        console.log('Selected role IDs:', selectedRoles); // Debug log
                        
                        // We need to match role names, not IDs
                        // Get the role names from the filter dropdown for the selected IDs
                        let roleMatch = false;
                        for (let roleId of selectedRoles) {
                            // Find the role name for this ID from the dropdown
                            const roleNameElement = $(`.filter-dropdown-menu[data-filter="role"] .filter-option[data-value="${roleId}"] span`);
                            const roleName = roleNameElement.text().trim();
                            
                            console.log('Comparing:', roleText, 'with', roleName); // Debug log
                            
                            if (roleText.toLowerCase() === roleName.toLowerCase()) {
                                roleMatch = true;
                                break;
                            }
                        }
                        
                        if (!roleMatch) {
                            showRow = false;
                        }
                    }
                    
                    $(this).toggle(showRow);
                });

                // Update pagination info after filtering
                updatePaginationInfo();
            }

            // Function to update pagination information based on visible rows
            function updatePaginationInfo() {
                const visibleRows = $('#kt_customers_table tbody tr:visible').not(':contains("No results found")');
                const totalVisible = visibleRows.length;
                
                if (totalVisible === 0) {
                    $('#paginationInfo').text('No users found');
                    $('#paginationControls').hide();
                } else {
                    $('#paginationInfo').text(`Showing 1 to ${totalVisible} of ${totalVisible} users`);
                    $('#paginationControls').hide(); // Hide pagination controls when filtering
                }
            }

            // Function to restore original pagination
            function restoreOriginalPagination() {
                $('#paginationInfo').html(originalPaginationInfo);
                $('#paginationControls').html(originalPaginationControls).show();
            }

            // Simple Column Sorting Function
            function addColumnSorting() {
                // Remove existing sorting setup to prevent duplicates
                $('#kt_customers_table thead th').removeClass('sortable').css('cursor', '').off('click');
                $('#kt_customers_table thead th .sort-icon').remove();

                // Add sorting icons to sortable columns
                $('#kt_customers_table thead th').each(function(index) {
                    if (index <= 3) { // User ID, Name, Email, Role columns
                        $(this).addClass('sortable').css('cursor', 'pointer');
                        
                        // Only User ID column (index 0) gets arrow by default, others get no arrow
                        if (index === 0) {
                            $(this).append(' <i class="fas fa-chevron-down sort-icon ms-2 fs-8" style="color: rgba(219, 203, 203, 0.9);"></i>');
                        } else {
                            $(this).append(' <i class="fas fa-chevron-down sort-icon ms-2 fs-8" style="color: rgba(219, 203, 203, 0.9); display: none;"></i>');
                        }
                    }
                });

                // Handle column header clicks
                $('#kt_customers_table thead th.sortable').off('click').on('click', function() {
                    const columnIndex = $(this).index();
                    
                    // Update sort direction
                    if (currentSortColumn === columnIndex) {
                        currentSortDirection = currentSortDirection === 'desc' ? 'asc' : 'desc';
                    } else {
                        currentSortDirection = 'desc'; // First click always shows down arrow (desc)
                    }
                    currentSortColumn = columnIndex;

                    // Hide all arrows first
                    $('#kt_customers_table thead th .sort-icon').hide();
                    
                    // Show and update only the clicked column icon
                    const icon = $(this).find('.sort-icon');
                    icon.show().removeClass('fa-chevron-up fa-chevron-down');
                    icon.addClass(currentSortDirection === 'desc' ? 'fa-chevron-down' : 'fa-chevron-up');

                    // Sort the table
                    sortTable(columnIndex, currentSortDirection);
                });
            }

            // Sort table function
            function sortTable(columnIndex, direction) {
                const tbody = $('#kt_customers_table tbody');
                const rows = tbody.find('tr').not(':contains("No results found")').get();

                rows.sort(function(a, b) {
                    const aText = $(a).find('td').eq(columnIndex).text().trim();
                    const bText = $(b).find('td').eq(columnIndex).text().trim();

                    // Handle User ID column sorting
                    if (columnIndex === 0) {
                        // Extract numeric part from User ID (handles formats like USER001, USER123, etc.)
                        const aMatch = aText.match(/\d+/);
                        const bMatch = bText.match(/\d+/);
                        
                        const aNum = aMatch ? parseInt(aMatch[0]) : 0;
                        const bNum = bMatch ? parseInt(bMatch[0]) : 0;
                        
                        // If numbers are the same, fall back to text comparison
                        if (aNum === bNum) {
                            return direction === 'desc' ? bText.localeCompare(aText) : aText.localeCompare(bText);
                        }
                        
                        return direction === 'desc' ? bNum - aNum : aNum - bNum;
                    }

                    // Text sorting for other columns
                    if (direction === 'desc') {
                        return bText.localeCompare(aText);
                    } else {
                        return aText.localeCompare(bText);
                    }
                });

                // Reorder rows in DOM
                $.each(rows, function(index, row) {
                    tbody.append(row);
                });
            }

            // Initialize sorting
            addColumnSorting();
            
            // Sort by User ID (column 0) by default on page load
            setTimeout(function() {
                sortTable(0, 'desc');
            }, 100);

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

            // Pagination function
            window.goToPage = function(page) {
                updateTableData(page);
            };

            $(document).on('change', '[name="row-count-filter"]', function (e) {
                if (eventListenersActive) {
                    var page = 1;
                    updateTableData(page);
                }
            });

            function updateTableData(page = '') {
                var searchTerm = $('#searchInput').val() || '';
                var selectedStatus = 'all'; // Default to 'all' since we removed the old filter
                var selectedRole = 'all'; // Default to 'all' since we removed the old filter
                
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
                        $('#paginationInfo').html($(response).find('#paginationInfo').html());
                        $('#paginationControls').html($(response).find('#paginationControls').html());
                        
                        // Update stored original pagination info
                        originalPaginationInfo = $(response).find('#paginationInfo').html();
                        originalPaginationControls = $(response).find('#paginationControls').html();
                        
                        // Sorting is already initialized on headers, no need to re-add
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
                            showToast(response.message);
                            updateTableData();
                        }
                    },
                    error: function (xhr) {
                        toggle.prop('checked', !newStatus);
                        showToast(xhr.responseJSON?.error, 'error');
                    }
                });
            });

            // Attach event listener to the "Delete" button
            $(document).on('click', '.deletestateBtn', function () {
                var userId = $(this).data('user-id');
                confirmBox("Are you sure you want to delete this user?", function() {
                    $.ajax({
                            url: "{{ route('user.delete', ['id' => ':id']) }}".replace(':id', userId),
                            type: 'DELETE',
                            success: function (res) {
                                showToast(res.message);
                                refreshTableContent();
                            },
                            error: function (xhr) {
                                showToast(xhr.responseJSON?.message || 'Failed to delete user', 'error');
                            }
                        });
                });
            });

            function refreshTableContent() {
                $.ajax({
                    url: "{{ route('user.index') }}",
                    type: "GET",
                    dataType: 'html',
                    success: function (response) {
                        // Update the table content with the refreshed data
                        $('#kt_customers_table tbody').html($(response).find('#kt_customers_table tbody').html());
                        $('#paginationInfo').html($(response).find('#paginationInfo').html());
                        $('#paginationControls').html($(response).find('#paginationControls').html());
                        
                        // Update stored original pagination info
                        originalPaginationInfo = $(response).find('#paginationInfo').html();
                        originalPaginationControls = $(response).find('#paginationControls').html();
                        
                        // Sorting is already initialized on headers, no need to re-add
                        
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