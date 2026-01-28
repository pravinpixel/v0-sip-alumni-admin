@extends('layouts.index')

@section('title', 'Announcement Management')

@push('styles')
    <style>
        .toggle-switch input:checked+.toggle-slider {
            background-color: #ba0028 !important;
        }

        .status-active {
            background-color: #ba0028;
            color: white;
        }

        .status-inactive {
            background-color: #fcd176;
            color: black;
        }
        .toggle-slider {
            background-color: #dedede !important;
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
        <h1 class="main-title">Announcements</h1>
        <p class="main-subtitle">Manage and publish announcements to alumni</p>

        <!-- Main Card -->
        <div class="table-box-container">

            <!-- Search and Actions Bar -->
            <div class="search-filter-container">
                <!-- Search Input -->
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput"
                        placeholder="Search by announcement title...">
                </div>

                <!-- Action Buttons -->
                <div class="filter-btns">
                    <button id="filterToggleBtn">
                        <i class="fas fa-filter"></i>
                        <span id="filterBtnText">Filter</span>
                    </button>

                    @can('announcement.create')
                    <button type="button" class="create-button" onclick="window.location='{{ route('admin.announcements.create') }}'">
                        <i class="fas fa-plus"></i>
                        Create Announcement
                    </button>
                    @endcan
                </div>
            </div>

            @include('announcements.filter')

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-responsive" style="overflow-x: auto;">
                    <table id="dataTable">
                        <thead>
                            <tr id="tableHeaderRow">
                                <th class="table-header">Created On</th>
                                <th class="table-header">Announcement Title</th>
                                <th class="table-header">Announcement Description</th>
                                <th class="table-header">Announcement Expiry</th>
                                <th class="table-header">Status</th>
                                @if(auth()->user()->can('announcement.edit') || auth()->user()->can('announcement.delete'))
                                <th class="table-header">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if($datas->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">No results found.
                                    </td>
                                </tr>
                            @else
                                @foreach($datas as $data)
                                    <tr>
                                        <td>{{ $data->created_at->format('M j, Y') }}</td>
                                        <td>{{ $data->title }}</td>
                                        <td title="{{ $data->description }}">{{ \Illuminate\Support\Str::limit($data->description, 50) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($data->expiry_date)->format('M j, Y') }}</td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <label class="toggle-switch" style="position: relative; display: inline-block; width: 44px; height: 24px;">
                                                    <input type="checkbox" class="status-toggle" 
                                                        data-announcement-id="{{ $data->id }}"
                                                        {{ $data->status == 1 ? 'checked' : '' }}
                                                        style="opacity: 0; width: 0; height: 0;">
                                                    <span class="toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; transition: 0.4s; border-radius: 24px;"></span>
                                                </label>
                                                <span class="status-badge {{ $data->status == 1 ? 'status-active' : 'status-inactive' }}" style="border-radius: 1rem;">
                                                    {{ $data->status == 1 ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </td>
                                        @if(auth()->user()->can('announcement.edit') || auth()->user()->can('announcement.delete'))
                                        <td style="position: relative;">
                                            <button class="action-menu-btn"
                                                style="background: none; border: none; cursor: pointer; padding: 0.5rem;">
                                                <i class="fas fa-ellipsis-v" style="color: #6b7280;"></i>
                                            </button>
                                            <div class="action-menu"
                                                style="display: none; position: absolute; right: 7rem; bottom: 2rem; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0.5rem; padding: 0.5rem; z-index: 100; min-width: 150px;">
                                                @can('announcement.edit')
                                                <a href="{{ route('admin.announcements.edit', $data->id) }}" 
                                                   style="display: block; padding: 0.5rem 1rem; color: #111827; text-decoration: none; font-size: 12px; border-radius: 0.375rem; transition: background 0.2s;"
                                                   onmouseover="this.style.background='#f3f4f6'"
                                                   onmouseout="this.style.background='transparent'">
                                                    <i class="fas fa-edit" style="margin-right: 0.5rem;"></i>Edit
                                                </a>
                                                @endcan
                                                @can('announcement.delete')
                                                <button class="delete-announcement-btn" 
                                                        data-announcement-id="{{ $data->id }}"
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
                    Showing {{ $datas->firstItem() ?? 0 }} to {{ $datas->lastItem() ?? 0 }} of {{ $datas->total() }} announcements
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

@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {
            let eventListenersActive = true;
            let currentSortColumn = 0;
            let currentSortDirection = 'desc';
            
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
            });

            function applyFilters() {
                updateTableData(1); 
            }

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

            function updateTableData(page = 1) {
                let searchTerm = $('#searchInput').val() || '';
                let statuses = [];
                $('.filter-dropdown-menu[data-filter="status"] input:checked').each(function () {
                    let val = $(this).closest('.filter-option').data('value');
                    if (val !== 'all') statuses.push(val);
                });

                let pageItems = $('[name="row-count-filter"]').val() || 10;
                $.ajax({
                    url: "{{ route('admin.announcements.index') }}",
                    type: "GET",
                    data: {
                        search: searchTerm,
                        status: statuses,
                        page: page,
                        pageItems: pageItems
                    },
                    success: function (response) {
                        $('#dataTable tbody').html($(response).find('#dataTable tbody').html());
                        $('#paginationInfo').html($(response).find('#paginationInfo').html());
                        $('#paginationControls').html($(response).find('#paginationControls').html());
                    }
                });
            }

            // Status toggle handler
            $(document).on('change', '.status-toggle', function () {
                const announcementId = $(this).data('announcement-id');
                const newStatus = $(this).is(':checked') ? 1 : 0;
                const toggle = $(this);

                $.ajax({
                    url: "{{ route('admin.announcements.toggle-status') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: announcementId,
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
                        showToast(xhr.responseJSON?.error || 'Failed to update status', 'error');
                    }
                });
            });

            // Delete announcement handler
            $(document).on('click', '.delete-announcement-btn', function () {
                const announcementId = $(this).data('announcement-id');
                
                confirmBox("Delete Announcement", "Are you sure you want to delete this announcement?", function() {
                    $.ajax({
                        url: "{{ route('admin.announcements.delete', '') }}/" + announcementId,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                showToast(response.message);
                                refreshTableContent();
                            }
                        },
                        error: function (xhr) {
                            showToast(xhr.responseJSON?.error || 'Failed to delete announcement', 'error');
                        }
                    });
                });
            });

            function refreshTableContent() {
                $.ajax({
                    url: "{{ route('admin.announcements.index') }}",
                    type: "GET",
                    dataType: 'html',
                    success: function (response) {
                        // Update the table content with the refreshed data
                        $('#dataTable tbody').html($(response).find('#dataTable tbody').html());
                        $('#paginationInfo').html($(response).find('#paginationInfo').html());
                        $('#paginationControls').html($(response).find('#paginationControls').html());
                        
                        // Update stored original pagination info
                        originalPaginationInfo = $(response).find('#paginationInfo').html();
                        originalPaginationControls = $(response).find('#paginationControls').html();
                        
                        updateTableData();
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }

            // Initialize table data
            updateTableData();
        });
    </script>

@endsection