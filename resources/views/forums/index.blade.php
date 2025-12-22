@extends('layouts.index')
@section('title', 'Directory - Alumni Tracking')

@section('content')
<div style="margin-bottom: 30px;">
    <h1 style="font-size: 40px; font-weight: 700; color: #333; margin-bottom: 8px;">Forums</h1>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
        Manage community discussions and forum posts
    </p>
    <div class="p-6" style="background-color: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <!-- Search and Filter -->
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                <input type="text" id="searchInput" placeholder="Search by alumni name or post title..."
                    style="width: 100%; padding: 10px 15px 10px 40px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>
            <button id="filterToggleBtn"
                style="background-color: #ba0028; color: white; border: 1px solid #ccc; border-radius: 6px; padding: 10px 20px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; font-weight: 500;"
                onmouseover="this.style.backgroundColor='#ba0028'; this.style.color='#fff';"
                onmouseout="this.style.backgroundColor='white'; this.style.color='#000000ff';">
                <i class="fas fa-filter"></i>
                <span id="filterBtnText">Filter</span>
            </button>
        </div>

        <!-- Filter Section -->
        <div id="filterSection" style="display: none; margin-bottom: 20px;">
            <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <!-- Status Filter Dropdown -->
                    <div class="filter-dropdown" style="position: relative;">
                        <button type="button" class="filter-dropdown-btn" data-filter="statuses"
                            style="background: white; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; min-width: 180px; justify-content: space-between;"
                            onmouseover="this.style.background='#ba0028'; this.style.color='#fff';" onmouseout="this.style.background='white'; this.style.color='#000000ff';">
                            <span>Status</span>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span class="filter-count" data-filter="statuses" style="background: #ba0028; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; font-weight: 600; align-items: center; justify-content: center; display: none;">0</span>
                                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                            </div>
                        </button>
                        <div class="filter-dropdown-menu" data-filter="statuses" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 4px; background: white; border: 1px solid #d1d5db; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 1000; min-width: 200px; max-height: 300px; overflow-y: auto;">
                            <!-- Options will be loaded here -->
                        </div>
                    </div>

                    <!-- From Date -->
                    <div style="min-width: 180px;">
                        <input type="date" id="filterFromDate" placeholder="From Date"
                            style="width: 100%; padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; cursor: pointer;">
                    </div>

                    <!-- To Date -->
                    <div style="min-width: 180px;">
                        <input type="date" id="filterToDate" placeholder="To Date"
                            style="width: 100%; padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; cursor: pointer;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Filters Display -->
        <div id="activeFiltersContainer" style="display: none; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span style="font-weight: 600; font-size: 14px; color: #374151;">Active Filters:</span>
                <div id="activeFiltersChips" style="display: flex; gap: 8px; flex-wrap: wrap; flex: 1;">
                    <!-- Filter chips will be added here -->
                </div>
                <button id="clearAllFiltersBtn"
                    style="background: transparent; border: none; color: #ba0028; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: underline;">
                    Clear All Filters
                </button>
            </div>
        </div>

        <!-- Forums Table Container -->
        <div class="table-container" style="border-radius: 8px; border: 1px solid #dedede; border-bottom: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; margin-top: 0; margin-bottom: 0;">
            <!-- Table Wrapper (Scrollable) -->
            <div class="table-responsive" style="overflow-x: auto;">
                <table id="forumsTable" class="display forums-table" style="width: 100%; min-width: 1000px; border-collapse: collapse; background-color: white; margin: 0;">
                    <thead>
                        <tr style="background: #ba0028; color: white; font-weight: 700; font-size: 14px;">
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Created On</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Alumni</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Contact</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">View Post</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Action Taken On</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Status</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination Area (Bottom of Table Design) -->
        <div class="pagination-bottom-area" style="background: #ffffff; border-radius: 0 0 8px 8px; padding: 16px 30px; display: flex; justify-content: space-between; align-items: center; margin-top: 0;">
            <div class="dt-info-custom" style="color: #6b7280; font-size: 14px; font-weight: 400;">
                <!-- Info will be populated here -->
            </div>
            <div class="dt-pagination-custom" style="display: flex; align-items: center; gap: 8px;">
                <!-- Pagination will be populated here -->
            </div>
        </div>
    </div>
</div>
<style>
    /* Add padding to tbody cells */
    #forumsTable tbody td {
        padding: 12px 15px;
        /* Adjust as needed */
        vertical-align: middle;
        box-sizing: border-box;
        border-bottom: 1px solid #dedede;
    }

    /* Prevent duplicate headers */
    #forumsTable {
        margin: 0 !important;
        padding: 0 !important;
    }

    #forumsTable thead th {
        position: relative;
        border-bottom: 2px solid #dedede;
    }

    /* Ensure only one header row */
    #forumsTable thead {
        display: table-header-group;
    }

    /* Hide any duplicate headers that might be created */
    #forumsTable thead:not(:first-child) {
        display: none !important;
    }

    /* Prevent DataTables from creating additional header elements */
    .dataTables_wrapper .dataTables_scroll .dataTables_scrollHead {
        display: none !important;
    }

    /* Ensure the original header stays visible */
    #forumsTable > thead {
        display: table-header-group !important;
    }

    /* Hide any cloned headers */
    .dataTables_scrollHead table thead {
        display: none !important;
    }

    /* Table responsive wrapper */
    .table-responsive {
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f3f4f6;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* DataTables sorting icons */
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_desc:after {
        color: white !important;
    }

    /* Bottom pagination area (matches original table design) */
    .pagination-bottom-area {
        position: relative;
        z-index: 10;
        background: #ffffff !important;
    }

    /* Ensure DataTables doesn't interfere with our fixed pagination */
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        display: none !important;
    }

    /* Custom pagination button styles (original design) */
    .dt-pagination-custom button {
        background: #ffffff;
        border: 1px solid #d1d5db;
        color: #374151;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .dt-pagination-custom button:hover:not(:disabled) {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #111827;
    }

    .dt-pagination-custom button:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f9fafb;
        color: #9ca3af;
    }

    .dt-pagination-custom span {
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
        margin: 0 12px;
    }
</style>

<!-- Post Details Modal -->
<div id="postDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; overflow-y: auto;">
    <div style="min-height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; border-radius: 12px; max-width: 400px; width: 100%; position: relative; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
            <!-- Header -->
            <div style="padding: 24px 32px; border-bottom: 1px solid #e5e7eb; position: relative; border: 0">
                <button onclick="closePostModal()" style="position: absolute; top: 16px; right: 16px; width: 32px; height: 32px; border-radius: 50%; background: transparent; border: none; color: #9ca3af; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 20px;"
                    onmouseover="this.style.background='#f3f4f6'; this.style.color='#111827'" onmouseout="this.style.background='transparent'; this.style.color='#9ca3af'">
                    ×
                </button>
                <h2 style="font-size: 28px; font-weight: 700; color: #ba0028; margin: 0;">Post Details</h2>
            </div>

            <!-- Body -->
            <div style="padding: 20px;">
                <!-- Post Title -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; font-size: 14px; color: #6b7280; margin-bottom: 8px;">POST TITTLE</label>
                    <div id="postTitle" style="background: #f9fafb; padding: 12px; border-radius: 8px; font-size: 18px; font-weight: 600; color: #111827; border: 1px solid #d1d5db;"></div>
                </div>
                <hr>

                <!-- Post Description -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; font-size: 14px; color: #6b7280; margin-bottom: 8px;">POST DESCRIPTION</label>
                    <div id="postDescription" style="background: #f9fafb; padding: 12px; border-radius: 8px; font-size: 15px; color: #374151; line-height: 1.6; border: 1px solid #d1d5db;"></div>
                </div>
                <hr>
                <!-- Labels -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 700; font-size: 14px; color: #6b7280; margin-bottom: 8px;">LABELS</label>
                    <div id="postLabels" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <!-- Labels will be added here -->
                    </div>
                </div>
                <hr>

                <!-- View Comments Button -->
                <button id="viewCommentsBtn" onclick="viewComments()" style="width: 100%; background: #ba0028; color: white; border: none; border-radius: 4px; padding: 10px; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: background 0.2s;"
                    onmouseover="this.style.background='#9a0020'" onmouseout="this.style.background='#ba0028'">
                    <i class="fas fa-comments"></i>
                    <span id="commentsText">View Comments (0)</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Post Modal -->
<div id="rejectPostModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; overflow-y: auto;">
    <div style="min-height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; border-radius: 12px; max-width: 500px; width: 100%; position: relative; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
                <!-- Body -->
                <div style="padding: 32px;">
                    <!-- Post Title Display -->
                    <div style="margin-bottom: 20px;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #111827;">Reject Post</h2>
                    <p style="font-size: 14px; color: #6b7280; margin-top: 12px;">Post: <span id="rejectPostTitle" style="font-weight: 400; color: #6b7280;;"></span></p>
                </div>

                <!-- Rejection Remarks -->
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; color: #111827; margin-bottom: 8px;">
                        Rejection Remarks <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea id="rejectionRemarks" rows="2" placeholder="Please provide a reason for rejecting this post..."
                        style="width: 100%; padding: 12px; border: 2px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical; outline: none; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#d1d5db'"></textarea>
                    <span id="remarksError" style="display: none; color: #dc2626; font-size: 12px; margin-top: 4px;">Please provide rejection remarks</span>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button onclick="closeRejectModal()" style="background: white; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 24px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        Cancel
                    </button>
                    <button id="rejectPostBtn" onclick="confirmRejectPost()" disabled
                        style="background: #9ca3af; color: white; border: none; border-radius: 8px; padding: 10px 24px; font-size: 14px; font-weight: 600; cursor: not-allowed; transition: background 0.2s; opacity: 0.6;">
                        Reject Post
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove Post Modal -->
<div id="removePostModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; overflow-y: auto;">
    <div style="min-height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; border-radius: 12px; max-width: 500px; width: 100%; position: relative; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
            <!-- Header -->
            
            <!-- Body -->
            <div style="padding: 32px;">
                    <!-- Description -->
                    <div style="margin-bottom: 20px;">
                    <h2 style="font-size: 24px; font-weight: 700; color: #111827; margin-bottom: 10px;">Remove Post</h2>
                    <p style="font-size: 12px; color: #6b7280; margin: 0; line-height: 1.6;">
                        Removing this post will make it no longer available to any of the alumni. Please provide a reason for removal.
                    </p>
                </div>

                <!-- Removal Remarks -->
                <div style="margin-bottom: 24px;">
                    <textarea id="removalRemarks" rows="3" placeholder="Enter remarks for removing this post..."
                        style="width: 100%; padding: 10px; border: 2px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical; outline: none; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#d1d5db'"></textarea>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button onclick="closeRemoveModal()" style="background: white; color: #513737ff; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 24px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        Cancel
                    </button>
                    <button id="removePostBtn" onclick="confirmRemovePost()" disabled
                        style="background: #f12020ff; color: white; border: none; border-radius: 8px; padding: 10px 24px; font-size: 14px; font-weight: 600; cursor: not-allowed; transition: background 0.2s; opacity: 0.6;">
                        Remove Post
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


<script>
    let selectedFilters = {
        statuses: []
    };

    $(document).ready(function() {
        // Load filter options
        loadFilterOptions();

        // Destroy existing DataTable if it exists to prevent duplicates
        if ($.fn.DataTable.isDataTable('#forumsTable')) {
            $('#forumsTable').DataTable().destroy();
        }

        const table = $('#forumsTable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: "{{ route('admin.forums.data') }}",
                type: 'GET',
                data: function(d) {
                    d.statuses = selectedFilters.statuses;
                    d.from_date = $('#filterFromDate').val();
                    d.to_date = $('#filterToDate').val();
                }
            },

            columns: [{
                    data: 'created_at',
                    name: 'created_at',
                    orderable: true
                },
                {
                    data: 'alumni',
                    name: 'alumni',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'contact',
                    name: 'contact',
                    orderable: true
                },
                {
                    data: 'view_post',
                    name: 'view_post',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action_taken_on',
                    name: 'action_taken_on',
                    orderable: true
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],

            paging: true,
            searching: true,
            ordering: true,
            pageLength: 10,
            lengthChange: false,
            order: [[0, 'desc']],
            scrollX: false,
            autoWidth: false,
            dom: 't',
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ posts"
            }
        });
        
        table.on('draw', function() {
            let info = table.page.info();

            $(".dt-info-custom").html(
                `Showing ${info.start + 1 > info.recordsTotal ? 0 : info.start + 1} to ${info.end} posts of ${info.recordsTotal}`
            );
            let totalPages = info.pages > 0 ? info.pages : 1;

            let paginationHtml = `
                <button id="prevPage" ${info.page === 0 ? "disabled" : ""}>
                    <i class="fas fa-chevron-left" style="font-size: 12px;"></i>
                    Previous
                </button>

                <span>
                    Page ${info.page + 1} of ${totalPages}
                </span>

                <button id="nextPage" ${(info.page + 1 === totalPages) ? "disabled" : ""}>
                    Next
                    <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
                </button>
            `;

            $(".dt-pagination-custom").html(paginationHtml);

            $("#prevPage").on("click", function() {
                table.page("previous").draw("page");
            });
            $("#nextPage").on("click", function() {
                table.page("next").draw("page");
            });
        });

    let currentPage = 0;
    table.on('page.dt', function () {
        currentPage = table.page.info().page;
        });
        table.on('order.dt', function () {
            setTimeout(function () {
                updateSortIcons();
            }, 10);
        });
        // table.on('preDraw.dt', function (e, settings) {
        //     if (settings.aaSorting && settings.aaSorting.length > 0) {
        //         settings._iDisplayStart = currentPage * settings._iDisplayLength;
        //     }
        // });

        // Search
        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Date filters
        $('#filterFromDate, #filterToDate').on('change', function() {
            updateFilterDisplay();
            table.ajax.reload();
        });

        // Toggle filter section
        $('#filterToggleBtn').on('click', function() {
            const section = $('#filterSection');
            const isVisible = section.is(':visible');
            section.slideToggle();
            $('#filterBtnText').text(isVisible ? 'Filter' : 'Close Filters');
        });

        // Filter dropdown toggle
        $('.filter-dropdown-btn').on('click', function(e) {
            e.stopPropagation();
            const filterType = $(this).data('filter');
            const menu = $(`.filter-dropdown-menu[data-filter="${filterType}"]`);
            
            // Close other dropdowns
            $('.filter-dropdown-menu').not(menu).hide();
            
            // Toggle current dropdown
            menu.toggle();
        });

        // Close dropdowns when clicking outside
        $(document).on('click', function() {
            $('.filter-dropdown-menu').hide();
        });

        // Prevent dropdown from closing when clicking inside
        $('.filter-dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Clear all filters
        $('#clearAllFiltersBtn').on('click', function() {
            selectedFilters = {
                statuses: []
            };
            $('#filterFromDate').val('');
            $('#filterToDate').val('');
            $('.filter-dropdown-menu input[type="checkbox"]').prop('checked', false);
            updateFilterDisplay();
            table.ajax.reload();
        });

        // Apply filters when chips are updated
        window.applyFilters = function() {
            table.ajax.reload();
        };
    });

    function loadFilterOptions() {
        $.ajax({
            url: "{{ route('admin.forums.filter.options') }}",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    // Populate Statuses
                    populateFilterMenu('statuses', response.statuses, 'Status');
                }
            },
            error: function(xhr) {
                console.error('Error loading filter options:', xhr);
            }
        });
    }

    function populateFilterMenu(filterType, options, label) {
        const menu = $(`.filter-dropdown-menu[data-filter="${filterType}"]`);
        menu.empty();
        
        options.forEach(option => {
            const isChecked = selectedFilters[filterType].includes(option);
            const item = $(`
                <label style="display: flex; align-items: center; padding: 10px 16px; cursor: pointer; transition: background 0.2s;"
                    onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                    <input type="checkbox" value="${option}" ${isChecked ? 'checked' : ''}
                        style="margin-right: 10px; width: 16px; height: 16px; cursor: pointer;">
                    <span style="font-size: 14px; color: #374151; text-transform: capitalize;">${option.replace(/_/g, ' ')}</span>
                </label>
            `);
            
            item.find('input').on('change', function() {
                toggleFilter(filterType, option, this.checked);
            });
            
            menu.append(item);
        });
    }

    function toggleFilter(filterType, value, isChecked) {
        if (isChecked) {
            if (!selectedFilters[filterType].includes(value)) {
                selectedFilters[filterType].push(value);
            }
        } else {
            selectedFilters[filterType] = selectedFilters[filterType].filter(v => v !== value);
        }
        
        updateFilterDisplay();
        applyFilters();
    }

    function updateFilterDisplay() {
        // Update count badges
        ['statuses'].forEach(filterType => {
            const count = selectedFilters[filterType].length;
            const badge = $(`.filter-count[data-filter="${filterType}"]`);
            
            if (count > 0) {
                badge.text(count).css('display', 'flex');
            } else {
                badge.css('display', 'none');
            }
        });
        
        // Update active filters chips
        const chipsContainer = $('#activeFiltersChips');
        const activeContainer = $('#activeFiltersContainer');
        chipsContainer.empty();
        
        let hasFilters = false;
        
        // Add chips for status filters
        selectedFilters.statuses.forEach(value => {
            hasFilters = true;
            const chip = $(`
                <div style="background: #fbbf24; color: #000; padding: 6px 12px; border-radius: 16px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                    <span>Status: ${value.replace(/_/g, ' ')}</span>
                    <button onclick="removeFilter('statuses', '${value}')" 
                        style="background: none; border: none; color: #000; cursor: pointer; padding: 0; font-size: 16px; line-height: 1; font-weight: 700;">
                        ×
                    </button>
                </div>
            `);
            chipsContainer.append(chip);
        });
        
        // Add chips for date filters
        const fromDateRaw = $('#filterFromDate').val();
        const toDateRaw = $('#filterToDate').val();
        function formatDateDDMMYYYY(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }
        const fromDate = formatDateDDMMYYYY(fromDateRaw);
        const toDate = formatDateDDMMYYYY(toDateRaw);
        
        if (fromDate) {
            hasFilters = true;
            const chip = $(`
                <div style="background: #fbbf24; color: #000; padding: 6px 12px; border-radius: 16px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                    <span>From: ${fromDate}</span>
                    <button onclick="removeFilter('from_date', '')" 
                        style="background: none; border: none; color: #000; cursor: pointer; padding: 0; font-size: 16px; line-height: 1; font-weight: 700;">
                        ×
                    </button>
                </div>
            `);
            chipsContainer.append(chip);
        }
        
        if (toDate) {
            hasFilters = true;
            const chip = $(`
                <div style="background: #fbbf24; color: #000; padding: 6px 12px; border-radius: 16px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                    <span>To: ${toDate}</span>
                    <button onclick="removeFilter('to_date', '')" 
                        style="background: none; border: none; color: #000; cursor: pointer; padding: 0; font-size: 16px; line-height: 1; font-weight: 700;">
                        ×
                    </button>
                </div>
            `);
            chipsContainer.append(chip);
        }
        
        if (hasFilters) {
            activeContainer.show();
        } else {
            activeContainer.hide();
        }
    }

    function removeFilter(filterType, value) {
        if (filterType === 'from_date') {
            $('#filterFromDate').val('');
        } else if (filterType === 'to_date') {
            $('#filterToDate').val('');
        } else {
            selectedFilters[filterType] = selectedFilters[filterType].filter(v => v !== value);
            
            // Update checkbox in dropdown
            $(`.filter-dropdown-menu[data-filter="${filterType}"] input[value="${value}"]`).prop('checked', false);
        }
        
        updateFilterDisplay();
        applyFilters();
    }

    function viewPost(postId) {
        $.ajax({
            url: "{{ route('admin.forums.post.details', '') }}/" + postId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const post = response.post;
                    
                    // Set post title
                    $('#postTitle').text(post.title || 'No Title');
                    
                    const description = post.description ?
                    post.description.replace(/<\/?[^>]+>/g, "") :
                    'No description available';
                    $('#postDescription').text(description);
                    
                    // Set labels
                    const labelsContainer = $('#postLabels');
                    labelsContainer.empty();
                    
                    if (post.labels && post.labels.length > 0) {
                        post.labels.forEach(label => {
                            const labelBadge = $(`
                                <span style="background: #fcd176; color: #000; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                    ${label}
                                </span>
                            `);
                            labelsContainer.append(labelBadge);
                        });
                    } else {
                        labelsContainer.html('<span style="color: #9ca3af; font-style: italic;">No labels</span>');
                    }
                    
                    const commentsCount = post.comments_count || 0;
                    $('#commentsText').text(`View Comments (${commentsCount})`);
                    
                    $('#viewCommentsBtn').data('post-id', postId);
                    
                    // Show modal
                    document.getElementById('postDetailsModal').style.display = 'block';
                    document.body.style.overflow = 'hidden';
                }
            },
            error: function(xhr) {
                alert('Error loading post details');
            }
        });
    }

    function closePostModal() {
        document.getElementById('postDetailsModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function viewComments() {
        const postId = $('#viewCommentsBtn').data('post-id');
        window.location.href = "{{ route('admin.forums.comments', '') }}/" + postId;
    }

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('postDetailsModal');
        if (event.target === modal) {
            closePostModal();
        }
    });

    let currentRejectPostId = null;
    let currentRejectPostTitle = '';
    let currentRemovePostId = null;

    let statusUpdating = false;
    function updatePostStatus(postId, status, remarks = null) {
        if (statusUpdating) return;
        statusUpdating = true;
        $.ajax({
            url: "{{ route('forums.change.status') }}",
            type: 'POST',
            data: {
                id: postId,
                status: status,
                remarks: remarks,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                showToast(response.message);
                $('#forumsTable').DataTable().ajax.reload(null, false);
            },
            error: function(xhr) {
                showToast('Error updating post status', 'error');
            },
            complete: function () {
                statusUpdating = false;
            }
        });
    }

    function statusChange(id, status) {
        if (status === 'rejected') {
            openRejectModal(id);
            return;
        }

        if (status === 'removed_by_admin') {
            openRemoveModal(id);
            return;
        }

        confirmBox("Are you sure you want approve this post?", function() {
            updatePostStatus(id, status);
        });
    }

    function openRejectModal(postId) {
        $.ajax({
            url: "{{ route('admin.forums.post.details', '') }}/" + postId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    currentRejectPostId = postId;
                    currentRejectPostTitle = response.post.title || 'Untitled Post';
                    
                    $('#rejectPostTitle').text(currentRejectPostTitle);
                    
                    $('#rejectionRemarks').val('');
                    $('#remarksError').hide();
                    
                    updateRejectButtonState();
                    
                    document.getElementById('rejectPostModal').style.display = 'block';
                    document.body.style.overflow = 'hidden';
                }
            },
            error: function(xhr) {
                alert('Error loading post details');
            }
        });
    }

    // Enable/disable reject button based on textarea input
    $(document).on('input', '#rejectionRemarks', function() {
        updateRejectButtonState();
    });

    function updateRejectButtonState() {
        const remarks = $('#rejectionRemarks').val().trim();
        const rejectBtn = $('#rejectPostBtn');
        
        if (remarks.length > 0) {
            // Enable button
            rejectBtn.prop('disabled', false);
            rejectBtn.css({
                'background': '#dc2626',
                'cursor': 'pointer',
                'opacity': '1'
            });
            rejectBtn.attr('onmouseover', "this.style.background='#b91c1c'");
            rejectBtn.attr('onmouseout', "this.style.background='#dc2626'");
        } else {
            // Disable button
            rejectBtn.prop('disabled', true);
            rejectBtn.css({
                'background': '#f11d1dff',
                'cursor': 'not-allowed',
                'opacity': '0.6'
            });
            rejectBtn.removeAttr('onmouseover');
            rejectBtn.removeAttr('onmouseout');
        }
    }

    function closeRejectModal() {
        document.getElementById('rejectPostModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        currentRejectPostId = null;
        currentRejectPostTitle = '';
    }

    function confirmRejectPost() {
        const remarks = $('#rejectionRemarks').val().trim();
        
        // Validate remarks
        if (!remarks) {
            $('#remarksError').show();
            $('#rejectionRemarks').css('border-color', '#dc2626');
            return;
        }
        
        $('#remarksError').hide();
        $('#rejectionRemarks').css('border-color', '#d1d5db');
        
        // Use unified API call
        updatePostStatus(currentRejectPostId, 'rejected', remarks);
        closeRejectModal();
    }

    document.addEventListener('click', function(event) {
        const rejectModal = document.getElementById('rejectPostModal');
        const removeModal = document.getElementById('removePostModal');
        
        if (event.target === rejectModal) {
            closeRejectModal();
        }
        if (event.target === removeModal) {
            closeRemoveModal();
        }
    });

    
    function openRemoveModal(postId) {
        currentRemovePostId = postId;
        
        $('#removalRemarks').val('');
        
        updateRemoveButtonState();
        document.getElementById('removePostModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    $(document).on('input', '#removalRemarks', function() {
        updateRemoveButtonState();
    });

    function updateRemoveButtonState() {
        const remarks = $('#removalRemarks').val().trim();
        const removeBtn = $('#removePostBtn');
        
        if (remarks.length > 0) {
            // Enable button
            removeBtn.prop('disabled', false);
            removeBtn.css({
                'background': '#dc2626',
                'cursor': 'pointer',
                'opacity': '1'
            });
            removeBtn.attr('onmouseover', "this.style.background='#b91c1c'");
            removeBtn.attr('onmouseout', "this.style.background='#dc2626'");
        } else {
            // Disable button
            removeBtn.prop('disabled', true);
            removeBtn.css({
                'background': '#f73333ff',
                'cursor': 'not-allowed',
                'opacity': '0.6'
            });
            removeBtn.removeAttr('onmouseover');
            removeBtn.removeAttr('onmouseout');
        }
    }

    function closeRemoveModal() {
        document.getElementById('removePostModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        currentRemovePostId = null;
    }

    function confirmRemovePost() {
        const remarks = $('#removalRemarks').val().trim();
        
        // Validate remarks
        if (!remarks) {
            $('#removalRemarks').css('border-color', '#dc2626');
            return;
        }
        
        // Reset border
        $('#removalRemarks').css('border-color', '#d1d5db');
        
        // Use unified API call
        updatePostStatus(currentRemovePostId, 'removed_by_admin', remarks);
        closeRemoveModal();
    }
</script>
@endpush
@endsection