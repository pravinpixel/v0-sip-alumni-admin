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

<div class="content-container">
    <h1 class="main-title">Roles Management</h1>
    <p class="main-subtitle">
        Manage admin roles and permissions
    </p>
    <!-- Main Card -->
    <div class="table-box-container">

        <!-- Search and Actions Bar -->
        <div class="search-filter-container">
            <!-- Search Input -->
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput"
                    placeholder="Search by role ID or name...">
            </div>

            <!-- Action Buttons -->
            <div class="filter-btns">
                <button id="filterToggleBtn">
                    <i class="fas fa-filter"></i>
                    <span id="filterBtnText">Filter</span>
                </button>

                @can('role.create')
                <button type="button" class="create-button" onclick="window.location='{{ route('role.create') }}'">
                    <i class="fas fa-plus"></i>
                    Create Role
                </button>
                @endcan
            </div>
        </div>

        <!-- Filter Section (Hidden by default) -->
        <div id="filterSection" style="display: none;">
            <div class="filter-wrapper">
                <div class="filter-row">
                    <!-- Status Filter Dropdown -->
                    <div class="filter-dropdown">
                        <button type="button" class="filter-dropdown-btn" data-filter="status">
                            <span>Status</span>
                            <div class="d-flex align-items-center gap-1">
                                <span class="filter-count" data-filter="status">0</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </button>
                        <div class="filter-dropdown-menu" data-filter="status">
                            <div class="filter-option" data-value="all">
                                <input type="checkbox">
                                <span>All Status</span>
                            </div>
                            <div class="filter-option" data-value="1">
                                <input type="checkbox">
                                <span>Active</span>
                            </div>
                            <div class="filter-option" data-value="0">
                                <input type="checkbox">
                                <span>Inactive</span>
                            </div>
                        </div>
                    </div>

                    <!-- Clear Filters Button -->
                    <button type="button" id="clearFiltersBtn" style="display: none; background: #ba0028; color: white; border: none; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-size: 14px; font-weight: 500;">
                        Clear All Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-responsive" style="overflow-x: auto;">
            <table id="dataTable">
                <thead>
                    <tr id="tableHeaderRow">
                        <th class="table-header">Role ID</th>
                        <th class="table-header">Role Name</th>
                        <th class="table-header">Status</th>
                        @if(auth()->user()->can('role.edit') || auth()->user()->can('role.delete'))
                        <th class="table-header">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if($datas->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center">No results found.
                        </td>
                    </tr>
                    @else
                    @foreach($datas as $data)
                    <tr>
                        <td>
                            {{$data->role_id}}</td>
                        <td>{{$data->name}}</td>
                        <td>
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
                        <td style="position: relative;">
                            <button class="action-menu-btn"
                                style="background: none; border: none; cursor: pointer; padding: 0.5rem;">
                                <i class="fas fa-ellipsis-v" style="color: #6b7280;"></i>
                            </button>
                            <div class="action-menu"
                                style="display: none; position: absolute; right: 13rem; bottom: 2rem; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0.5rem; padding: 0.5rem; z-index: 100; min-width: 150px;">
                                @can('role.edit')
                                <a href="{{ route('role.edit', ['id' => $data->id]) }}"
                                    style="display: block; padding: 0.5rem 1rem; color: #111827; text-decoration: none; font-size: 12px; border-radius: 0.375rem; transition: background 0.2s;"
                                    onmouseover="this.style.background='#f3f4f6'"
                                    onmouseout="this.style.background='transparent'">
                                    <i class="fas fa-edit" style="margin-right: 0.5rem;"></i>Edit
                                </a>
                                @endcan
                                @can('role.delete')
                                <button type="button" class="deletestateBtn" data-role-id="{{ $data->id }}"
                                    style="display: block; width: 100%; text-align: left; padding: 0.5rem 1rem; background: none; border: none; color: #dc2626; cursor: pointer; font-size: 12px; border-radius: 0.375rem; transition: background 0.2s;"
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
        </div>

        <!-- Pagination -->
        <div class="user-role-pagination">
                <div id="paginationInfo">
                    Showing {{ $datas->firstItem() ?? 0 }} to {{ $datas->lastItem() ?? 0 }} of {{ $datas->total() }} roles
                </div>
                <div id="paginationControls">
                    @if ($datas->onFirstPage())
                        <button disabled >
                            <i class="fas fa-chevron-left"></i> Previous
                        </button>
                    @else
                        <button onclick="goToPage({{ $datas->currentPage() - 1 }})" >
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

@endsection

@section('script')
@parent
<script>
    $(document).ready(function() {
        let currentSortColumn = 0;
        let currentSortDirection = 'desc'; // Role ID starts with down arrow (desc)
        
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
            
            // Handle "All Status" option
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
            
            if (allStatusSelected) {
                // Show all rows if "All Status" is selected
                $('#dataTable tbody tr').show();
                updatePaginationInfo();
                return;
            }

            // Get selected status filters
            const selectedStatuses = [];
            $('.filter-dropdown-menu[data-filter="status"] input[type="checkbox"]:checked').each(function() {
                const value = $(this).closest('.filter-option').data('value');
                if (value !== 'all') {
                    selectedStatuses.push(value);
                }
            });

            console.log('Selected statuses:', selectedStatuses); // Debug log

            // If no filters selected, show all rows
            if (selectedStatuses.length === 0) {
                $('#dataTable tbody tr').show();
                updatePaginationInfo();
                return;
            }

            // Apply filters to table rows
            $('#dataTable tbody tr').each(function() {
                let showRow = false;
                
                // Skip the "No results found" row
                if ($(this).find('td[colspan]').length > 0) {
                    $(this).show();
                    return;
                }
                
                const statusCell = $(this).find('td:nth-child(3)'); // Status column
                const statusSpan = statusCell.find('span:last-child'); // Get the status span
                const statusText = statusSpan.text().trim().toLowerCase();
                
                console.log('Row status text:', statusText); // Debug log
                
                // Check if this row matches any selected filter
                for (let status of selectedStatuses) {
                    if (status === 1 && statusText === 'active') {
                        showRow = true;
                        break;
                    } else if (status === 0 && statusText === 'inactive') {
                        showRow = true;
                        break;
                    }
                }
                
                $(this).toggle(showRow);
            });

            // Update pagination info after filtering
            updatePaginationInfo();
        }

        // Function to update pagination information based on visible rows
        function updatePaginationInfo() {
            const visibleRows = $('#dataTable tbody tr:visible').not(':contains("No results found")');
            const totalVisible = visibleRows.length;
            
            if (totalVisible === 0) {
                $('#paginationInfo').text('No roles found');
                $('#paginationControls').hide();
            } else {
                $('#paginationInfo').text(`Showing 1 to ${totalVisible} of ${totalVisible} roles`);
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
            $('#dataTable thead th').removeClass('sortable').css('cursor', '').off('click');
            $('#dataTable thead th .sort-icon').remove();

            // Add sorting icons to sortable columns
            $('#dataTable thead th').each(function(index) {
                if (index <= 1) { // Role ID, Role Name columns
                    $(this).addClass('sortable').css('cursor', 'pointer');
                    
                    if (index === 0) {
                        $(this).append(' <i class="fas fa-chevron-down sort-icon ms-2 fs-8" style="color: rgba(219, 203, 203, 0.9);"></i>');
                    } else {
                        $(this).append(' <i class="fas fa-chevron-down sort-icon ms-2 fs-8" style="color: rgba(219, 203, 203, 0.9); display: none;"></i>');
                    }
                }
            });

            // Handle column header clicks
            $('#dataTable thead th.sortable').off('click').on('click', function() {
                const columnIndex = $(this).index();
                
                // Update sort direction
                if (currentSortColumn === columnIndex) {
                    currentSortDirection = currentSortDirection === 'desc' ? 'asc' : 'desc';
                } else {
                    currentSortDirection = 'desc';
                }
                currentSortColumn = columnIndex;
                $('#dataTable thead th .sort-icon').hide();
                
                const icon = $(this).find('.sort-icon');
                icon.show().removeClass('fa-chevron-up fa-chevron-down');
                icon.addClass(currentSortDirection === 'desc' ? 'fa-chevron-down' : 'fa-chevron-up');

                // Sort the table
                sortTable(columnIndex, currentSortDirection);
            });
        }

        // Sort table function
        function sortTable(columnIndex, direction) {
            const tbody = $('#dataTable tbody');
            const rows = tbody.find('tr').not(':contains("No results found")').get();

            rows.sort(function(a, b) {
                const aText = $(a).find('td').eq(columnIndex).text().trim();
                const bText = $(b).find('td').eq(columnIndex).text().trim();

                // Handle Role ID column sorting
                if (columnIndex === 0) {
                    // Extract numeric part from Role ID (handles formats like ROLE001, ROLE123, etc.)
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

                // Text sorting for other columns (Role Name)
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
        
        setTimeout(function() {
            sortTable(0, 'desc');
        }, 100);

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
                    $('#dataTable tbody').html($(response).find('#dataTable tbody').html());
                    $('#paginationInfo').html($(response).find('#paginationInfo').html());
                    $('#paginationControls').html($(response).find('#paginationControls').html());
                    
                    // Update stored original pagination info
                    originalPaginationInfo = $(response).find('#paginationInfo').html();
                    originalPaginationControls = $(response).find('#paginationControls').html();
                    
                    // Sorting is already initialized on headers, no need to re-add
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
                    $('#dataTable tbody').html($(response).find('#dataTable tbody').html());
                    $('#paginationInfo').html($(response).find('#paginationInfo').html());
                    $('#paginationControls').html($(response).find('#paginationControls').html());
                    
                    // Update stored original pagination info
                    originalPaginationInfo = $(response).find('#paginationInfo').html();
                    originalPaginationControls = $(response).find('#paginationControls').html();
                    
                    // Sorting is already initialized on headers, no need to re-add
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