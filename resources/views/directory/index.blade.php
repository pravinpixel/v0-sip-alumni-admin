@extends('layouts.index')
@section('title', 'Directory - Alumni Tracking')

@section('content')
<div style="margin-bottom: 30px;">
    <h1 style="font-size: 40px; font-weight: 700; color: #333; margin-bottom: 8px;">Alumni Directory</h1>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
        Manage and view all alumni profiles
    </p>
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <!-- Search and Filter -->
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                <input type="text" id="searchInput" placeholder="Search by name or email..."
                    style="width: 100%; padding: 10px 15px 10px 40px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>
            <button id="filterToggleBtn"
                style="background-color: white; color: black; border: 1px solid #ccc; border-radius: 6px; padding: 10px 20px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; font-weight: 500;"
                onmouseover="this.style.backgroundColor='#ba0028'; this.style.color='#fff';"
                onmouseout="this.style.backgroundColor='white'; this.style.color='#000000ff';">
                <i class="fas fa-filter"></i>
                <span id="filterBtnText">Filter</span>
            </button>
            <!-- Export Dropdown -->
            <div style="position: relative;">
                <button id="exportBtn"
                    style="background-color: white; border: 1px solid #ccc; border-radius: 6px; padding: 10px 20px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; font-weight: 500; position: relative;"
                    onmouseover="this.style.backgroundColor='#ba0028'; this.style.color='#fff';"
                    onmouseout="this.style.backgroundColor='white'; this.style.color='#000000ff';"
                    onclick="toggleExportDropdown()">
                    <i class="fas fa-download"></i>
                    <span>Export</span>
                </button>
                
                <!-- Export Dropdown Menu -->
                <div id="exportDropdown" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 8px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 200px; z-index: 1000;">
                    <div onclick="exportDirectory('csv')" 
                        style="padding: 12px 16px; cursor: pointer; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f3f4f6; transition: background 0.2s;"
                        onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        <i class="fas fa-file-csv" style="color: #10b981; font-size: 16px;"></i>
                        <span style="font-size: 12px; color: #374151; font-weight: 500;">Export as CSV</span>
                    </div>
                    <div onclick="exportDirectory('excel')" 
                        style="padding: 12px 16px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: background 0.2s;"
                        onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        <i class="fas fa-file-excel" style="color: #059669; font-size: 16px;"></i>
                        <span style="font-size: 12px; color: #374151; font-weight: 500;">Export as Excel</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div id="filterSection" style="display: none; margin-bottom: 20px;">
            <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <!-- Year Filter Dropdown -->
                    <div class="filter-dropdown" style="position: relative;">
                        <button type="button" class="filter-dropdown-btn" data-filter="years"
                            style="background: white; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; min-width: 180px; justify-content: space-between;"
                            onmouseover="this.style.background='#ba0028'; this.style.color='#fff';" onmouseout="this.style.background='white'; this.style.color='#000000ff';">
                            <span>Year of Completion</span>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span class="filter-count" data-filter="years" style="background: #ba0028; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; font-weight: 600; align-items: center; justify-content: center; display: none;">0</span>
                                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                            </div>
                        </button>
                        <div class="filter-dropdown-menu" data-filter="years" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 4px; background: white; border: 1px solid #d1d5db; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 1000; min-width: 200px; max-height: 300px; overflow-y: auto;">
                            <!-- Options will be loaded here -->
                        </div>
                    </div>

                    <!-- City Filter Dropdown -->
                    <div class="filter-dropdown" style="position: relative;">
                        <button type="button" class="filter-dropdown-btn" data-filter="cities"
                            style="background: white; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; min-width: 180px; justify-content: space-between;"
                            onmouseover="this.style.background='#ba0028'; this.style.color='#fff';" onmouseout="this.style.background='white'; this.style.color='#000000ff';">
                            <span>City</span>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span class="filter-count" data-filter="cities" style="background: #ba0028; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; font-weight: 600; align-items: center; justify-content: center; display: none;">0</span>
                                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                            </div>
                        </button>
                        <div class="filter-dropdown-menu" data-filter="cities" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 4px; background: white; border: 1px solid #d1d5db; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 1000; min-width: 200px; max-height: 300px; overflow-y: auto;">
                            <!-- Options will be loaded here -->
                        </div>
                    </div>

                    <!-- Occupation Filter Dropdown -->
                    <div class="filter-dropdown" style="position: relative;">
                        <button type="button" class="filter-dropdown-btn" data-filter="occupations"
                            style="background: white; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; min-width: 180px; justify-content: space-between;"
                            onmouseover="this.style.background='#ba0028'; this.style.color='#fff';" onmouseout="this.style.background='white'; this.style.color='#000000ff';">
                            <span>Occupation</span>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span class="filter-count" data-filter="occupations" style="background: #ba0028; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; font-weight: 600; align-items: center; justify-content: center; display: none;">0</span>
                                <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                            </div>
                        </button>
                        <div class="filter-dropdown-menu" data-filter="occupations" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 4px; background: white; border: 1px solid #d1d5db; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 1000; min-width: 200px; max-height: 300px; overflow-y: auto;">
                            <!-- Options will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Filters Display (Outside Filter Section) -->
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

        <!-- Alumni Table Container -->
        <div class="table-container" style="border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; margin-top: 0; margin-bottom: 0;">
            <!-- Table Wrapper (Scrollable) -->
            <div class="table-responsive" style="overflow-x: auto;">
                <table id="directoryTable" class="display directory-table" style="width: 100%; min-width: 1200px; border-collapse: collapse; background-color: white; margin: 0;">
                    <thead>
                        <tr style="background: #ba0028; color: white; font-weight: 700; font-size: 12px;">
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Created On</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Profile Picture</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Name</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Year</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">City & State</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Email</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Contact</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Occupation</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Status</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Connections</th>
                            <th style="padding: 15px; text-align: left; white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
        </div>
        <!-- Pagination Area (Bottom of Table Design) -->
        <div class="pagination-bottom-area" style="background: #ffffff; border-top: 1px solid #e5e7eb; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
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
    #directoryTable tbody td {
        padding: 12px 15px;
        /* Adjust as needed */
        vertical-align: middle;
        box-sizing: border-box;
    }

    table.dataTable th,
    table.dataTable td {
        white-space: nowrap;
        /* Prevent wrapping */
    }

    .dataTables_wrapper {
        margin: 0 !important;
        padding: 0 !important;
        /* completely removed all gaps */
    }

    table.dataTable thead th {
        box-sizing: border-box;
        /* Ensure proper width calculation */
    }

    #directoryTable tbody td {
        border-bottom: 1px solid #f0f0f0;
        /* soft line between rows */
    }

    #directoryTable {
        margin: 0 !important;
        padding: 0 !important;
    }

    #directoryTable thead th {
        border-bottom: 2px solid #e0e0e0;
        /* slightly thicker under header */
        position: relative;
        margin: 0 !important;
        padding: 15px !important;
    }

    /* Ensure only one header row */
    #directoryTable thead {
        display: table-header-group;
    }

    /* Hide any duplicate headers that might be created by DataTables */
    #directoryTable thead:not(:first-child) {
        display: none !important;
    }

    /* Prevent DataTables from creating additional header elements */
    .dataTables_wrapper .dataTables_scroll .dataTables_scrollHead {
        display: none !important;
    }

    /* Ensure the original header stays visible */
    #directoryTable > thead {
        display: table-header-group !important;
    }

    /* Hide any cloned headers */
    .dataTables_scrollHead table thead {
        display: none !important;
    }

    /* Table container */
    .table-container {
        position: relative;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Table responsive wrapper */
    .table-responsive {
        -webkit-overflow-scrolling: touch;
        position: relative;
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

    /* Bottom pagination area (matches original table design) */
    .pagination-bottom-area {
        position: relative;
        z-index: 10;
        background: #ffffff !important;
        border-top: 1px solid #e5e7eb !important;
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

    /* DataTables sorting icons */
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_desc:after {
        color: white !important;
    }
</style>

<!-- Profile Picture Modal -->
<div class="modal fade" id="profilePicModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alumni Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="alumniProfileImage" src="" class="img-fluid rounded" alt="Profile Picture">
            </div>
        </div>
    </div>
</div>

<!-- Block Alumni Modal -->
<div id="blockAlumniModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; overflow-y: auto;">
    <div style="min-height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; border-radius: 12px; max-width: 500px; width: 100%; position: relative; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); padding: 32px;">
            <button onclick="closeBlockModal()" style="position: absolute; top: 16px; right: 16px; width: 32px; height: 32px; border-radius: 50%; background: transparent; border: none; color: #9ca3af; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px;"
                onmouseover="this.style.background='#f3f4f6'; this.style.color='#111827'" onmouseout="this.style.background='transparent'; this.style.color='#9ca3af'">
                <i class="fas fa-times"></i>
            </button>

            <div>
                <h3 style="font-size: 24px; font-weight: 700; color: #111827; margin: 0 0 12px 0;">Block Alumni</h3>
                <p style="color: #6b7280; font-size: 15px; margin: 0 0 20px 0;">Please provide a reason for blocking this alumni.</p>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; color: #374151; margin-bottom: 8px;">
                        Remarks <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea id="blockRemarks" rows="4" placeholder="Enter reason for blocking..."
                        style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; font-family: inherit; resize: vertical;"
                        onfocus="this.style.borderColor='#ba0028'" onblur="this.style.borderColor='#d1d5db'"></textarea>
                    <small id="remarksError" style="color: #dc2626; font-size: 12px; display: none; margin-top: 4px;">Remarks are required</small>
                </div>
                
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button onclick="closeBlockModal()" style="padding: 10px 24px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; color: #374151; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb'">
                        Cancel
                    </button>
                    <button onclick="confirmBlock()" id="confirmBlockBtn" style="padding: 10px 24px; background: #dc2626; border: none; border-radius: 8px; color: white; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                        Block Alumni
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
        years: [],
        cities: [],
        occupations: []
    };

    $(document).ready(function() {
        // Load filter options
        loadFilterOptions();

        // Destroy existing DataTable if it exists to prevent duplicates
        if ($.fn.DataTable.isDataTable('#directoryTable')) {
            $('#directoryTable').DataTable().destroy();
        }

        const table = $('#directoryTable').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: "{{ route('admin.directory.data') }}",
                type: 'GET',
                data: function(d) {
                    d.years = selectedFilters.years;
                    d.cities = selectedFilters.cities;
                    d.occupations = selectedFilters.occupations;
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
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'full_name',
                    name: 'full_name',
                    orderable: true
                },
                {
                    data: 'year_of_completion',
                    name: 'year_of_completion',
                    orderable: true
                },
                {
                    data: 'location',
                    name: 'location',
                    orderable: true
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: true
                },
                {
                    data: 'mobile_number',
                    name: 'mobile_number',
                    orderable: true
                },
                {
                    data: 'occupation',
                    name: 'occupation',
                    orderable: true
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false
                },
                {
                    data: 'connections',
                    name: 'connections',
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
                info: "Showing _START_ to _END_ of _TOTAL_ alumni"
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

        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

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
                years: [],
                cities: [],
                occupations: []
            };
            $('.filter-dropdown-menu input[type="checkbox"]').prop('checked', false);
            updateFilterDisplay();
            table.ajax.reload();
        });

        // Apply filters when chips are updated
        window.applyFilters = function() {
            table.ajax.reload();
        };
    });

    function toggleExportDropdown() {
        const dropdown = document.getElementById('exportDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const exportBtn = document.getElementById('exportBtn');
        const dropdown = document.getElementById('exportDropdown');
        
        if (!exportBtn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    function exportDirectory(format) {
        // Close dropdown
        document.getElementById('exportDropdown').style.display = 'none';
        
        let years = selectedFilters.years.join(',');
        let cities = selectedFilters.cities.join(',');
        let occupations = selectedFilters.occupations.join(',');
        let search = $('#directoryTable').DataTable().search();

        let url = "{{ route('admin.directory.export') }}" 
                    + "?years=" + years 
                    + "&cities=" + cities 
                    + "&occupations=" + occupations
                    + "&search=" + encodeURIComponent(search)
                    + "&format=" + format;

        window.location.href = url;
    }

    function loadFilterOptions() {
        $.ajax({
            url: "{{ route('admin.directory.filter.options') }}",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    // Populate Years
                    populateFilterMenu('years', response.years, 'Year');
                    
                    // Populate Cities
                    populateFilterMenu('cities', response.cities, 'City');
                    
                    // Populate Occupations
                    populateFilterMenu('occupations', response.occupations, 'Occupation');
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
                    <span style="font-size: 14px; color: #374151;">${option}</span>
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
        ['years', 'cities', 'occupations'].forEach(filterType => {
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
        
        // Add chips for each filter type
        const filterLabels = {
            years: 'Year',
            cities: 'City',
            occupations: 'Occupation'
        };
        
        Object.keys(selectedFilters).forEach(filterType => {
            selectedFilters[filterType].forEach(value => {
                hasFilters = true;
                const chip = $(`
                    <div style="background: #fbbf24; color: #000; padding: 6px 12px; border-radius: 16px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                        <span>${filterLabels[filterType]}: ${value}</span>
                        <button onclick="removeFilter('${filterType}', '${value}')" 
                            style="background: none; border: none; color: #000; cursor: pointer; padding: 0; font-size: 16px; line-height: 1; font-weight: 700;">
                            ×
                        </button>
                    </div>
                `);
                chipsContainer.append(chip);
            });
        });
        
        if (hasFilters) {
            activeContainer.show();
        } else {
            activeContainer.hide();
        }
    }

    function removeFilter(filterType, value) {
        selectedFilters[filterType] = selectedFilters[filterType].filter(v => String(v) !== String(value));
        
        // Update checkbox in dropdown
        $(`.filter-dropdown-menu[data-filter="${filterType}"] input[value="${value}"]`).prop('checked', false);
        
        updateFilterDisplay();
        applyFilters();
    }
    function viewConnections(id) {
        window.location.href = "{{ route('admin.directory.view.connections.page', '') }}/" + id;
    }

    let alumniToBlock = null;

    function updateStatus(id, status) {
        if (status === 'blocked') {
            // Open modal for block with remarks
            alumniToBlock = id;
            document.getElementById('blockAlumniModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
            document.getElementById('blockRemarks').value = '';
            document.getElementById('remarksError').style.display = 'none';
        } else {
            // Unblock without remarks
            confirmBox("Are you sure you want to unblock this user?", function() {
            
            $.ajax({
                url: "{{ route('directory.update.status') }}",
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showToast(response.message);
                    $('#directoryTable').DataTable().ajax.reload( null, false);
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    showToast(res && res.message ? res.message : 'An error occurred while updating status.', 'error');
                }
            });
        });

        }
    }

    function closeBlockModal() {
        alumniToBlock = null;
        document.getElementById('blockAlumniModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('blockRemarks').value = '';
        document.getElementById('remarksError').style.display = 'none';
        document.getElementById('confirmBlockBtn').disabled = false;
    }

    function confirmBlock() {
        const remarks = document.getElementById('blockRemarks').value.trim();
        const errorEl = document.getElementById('remarksError');
        const confirmBlockBtn = document.getElementById('confirmBlockBtn');

        if (confirmBlockBtn.disabled) return;
        
        if (!remarks) {
            errorEl.style.display = 'block';
            return;
        }
        
        if (!alumniToBlock) return;

        const originalText = confirmBlockBtn.innerHTML;
            confirmBlockBtn.innerHTML = 'Blocking...';
            confirmBlockBtn.disabled = true;
        
        $.ajax({
            url: "{{ route('directory.update.status') }}",
            type: 'POST',
            data: {
                id: alumniToBlock,
                status: 'blocked',
                remarks: remarks,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                confirmBlockBtn.innerHTML = originalText;
                confirmBlockBtn.disabled = false;
                showToast('User Blocked Successfully.' ||response.message);
                closeBlockModal();
                $('#directoryTable').DataTable().ajax.reload(null, false);
            },
            error: function(xhr) {
                confirmBlockBtn.innerHTML = originalText;
                confirmBlockBtn.disabled = false;
                const res = xhr.responseJSON;
                showToast(res && res.message ? res.message : 'An error occurred while blocking alumni.', 'error');
            }
        });
    }

    function viewProfilePic(imageUrl) {
        window.open(imageUrl, '_blank');
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('blockAlumniModal');
        if (event.target === modal) {
            closeBlockModal();
        }
    });
</script>
@endpush
@endsection