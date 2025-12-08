@extends('alumni.layouts.index')
@section('title', 'Directory - Alumni Tracking')

@section('content')
<style>
    /* Responsive Styles */
    @media (max-width: 991px) {
        .search-filter-container {
            gap: 10px !important;
        }

        .search-box {
            min-width: 150px !important;
        }

        .filter-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 16px !important;
        }

        #filterSection {
            padding: 16px !important;
        }
    }

    @media (max-width: 767px) {
        .search-filter-container {
            gap: 8px !important;
        }

        .search-box {
            width: 100% !important;
            min-width: 100% !important;
        }

        .filter-grid {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }

        #filterSection {
            padding: 12px !important;
        }

        #filterToggleBtn,
        #clearFiltersBtn {
            font-size: 13px !important;
            padding: 8px 14px !important;
        }

        .table-container {
            border-radius: 8px !important;
        }

        #alumniTable thead th {
            padding: 12px !important;
            font-size: 13px !important;
        }

        #alumniTable tbody td {
            padding: 12px !important;
            font-size: 13px !important;
        }
    }

    @media (max-width: 575px) {
        .search-filter-container {
            flex-direction: column;
            align-items: stretch !important;
        }

        #filterToggleBtn,
        #clearFiltersBtn {
            width: 100%;
            justify-content: center;
        }

        #filterSection {
            padding: 10px !important;
        }

        #alumniTable thead th {
            padding: 10px !important;
            font-size: 12px !important;
        }

        #alumniTable tbody td {
            padding: 10px !important;
            font-size: 12px !important;
        }

        .multi-select-display {
            font-size: 13px !important;
            padding: 8px 10px !important;
        }
    }

    /* Table horizontal scroll indicator */
    .table-container::-webkit-scrollbar {
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 0 0 12px 12px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Pagination Responsive */
    @media (max-width: 767px) {
        .pagination-container {
            padding: 8px 5px !important;
        }

        .pagination-info {
            font-size: 12px !important;
            width: 100%;
            text-align: center;
        }

        .pagination-controls {
            width: 100%;
            justify-content: center !important;
        }

        .pagination-controls button {
            font-size: 12px !important;
            padding: 6px 12px !important;
        }

        .pagination-controls span {
            font-size: 12px !important;
            padding: 6px 12px !important;
        }
    }

    @media (max-width: 575px) {
        .pagination-info {
            font-size: 11px !important;
        }

        .pagination-controls button {
            padding: 5px 10px !important;
        }

        .pagination-controls span {
            padding: 5px 10px !important;
        }
    }

    /* Override DataTables default styles */
    #alumniTable thead tr {
        background: linear-gradient(90deg, #dc2626 0%, #f59e0b 100%) !important;
    }

    #alumniTable thead th {
        background: transparent !important;
        color: white !important;
        border: none !important;
        position: relative !important;
        padding-right: 30px !important;
    }

    /* Hide default DataTables arrows */
    table.dataTable thead th.sorting:before,
    table.dataTable thead th.sorting_asc:before,
    table.dataTable thead th.sorting_desc:before,
    table.dataTable thead th.sorting:after,
    table.dataTable thead th.sorting_asc:after,
    table.dataTable thead th.sorting_desc:after {
        display: none !important;
    }

    /* Bootstrap Icons for sorting */
    #alumniTable thead th.sorting::after {
        content: "⇅";
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: rgba(255, 255, 255, 0.6);
    }


    table.dataTable.order-column>tbody tr>.sorting_1,
table.dataTable.order-column>tbody tr>.sorting_2,
table.dataTable.order-column>tbody tr>.sorting_3,
table.dataTable.display>tbody tr>.sorting_1,
table.dataTable.display>tbody tr>.sorting_2,
table.dataTable.display>tbody tr>.sorting_3 {
    box-shadow: none !important;
    background-color: transparent !important;
}

table.dataTable.stripe tbody tr.odd,
table.dataTable.display tbody tr.odd,
table.dataTable.stripe tbody tr.even,
table.dataTable.display tbody tr.even {
    background-color: transparent !important;
}
table.dataTable tbody tr > .sorting_1,
table.dataTable tbody tr > .sorting_2,
table.dataTable tbody tr > .sorting_3 {
    background-color: transparent !important;
    box-shadow: none !important;
}



    /* Sortable columns styling */
    #alumniTable thead th.sorting,
    #alumniTable thead th.sorting_asc,
    #alumniTable thead th.sorting_desc {
        cursor: pointer !important;
        padding-right: 5px !important;
    }

    #alumniTable tbody tr,
#alumniTable tbody td {
    background: none !important;
}
#alumniTable tbody tr:hover td {
    background: none !important;
}



    /* CRITICAL: Remove sorted column background - all variations */
    table.dataTable.display tbody tr.odd > .sorting_1,
    table.dataTable.order-column.stripe tbody tr.odd > .sorting_1,
    table.dataTable.display tbody tr.even > .sorting_1,
    table.dataTable.order-column.stripe tbody tr.even > .sorting_1,
    table.dataTable tbody td.sorting_1,
    table.dataTable tbody td.sorting_2,
    table.dataTable tbody td.sorting_3,
    table.dataTable.display tbody tr > .sorting_1,
    table.dataTable.display tbody tr > .sorting_2,
    table.dataTable.display tbody tr > .sorting_3,
    #alumniTable tbody td {
        background-color: inherit !important;
    }

    #alumniTable tbody tr:hover {
        background-color: #f9fafb !important;
    }

    #alumniTable tbody tr:hover td {
        background-color: transparent !important;
    }

    /* Remove DataTables default elements */
    #alumniTable_length {
        display: none !important;
    }

    #alumniTable_filter {
        display: none !important;
    }

    /* Ensure table wrapper has proper border radius */
    .dataTables_wrapper {
        border-radius: 12px !important;
        overflow: visible !important;
    }

    /* Force table container to allow horizontal scroll */
    .table-container {
        overflow-x: auto !important;
        overflow-y: visible !important;
        width: 100% !important;
        display: block !important;
    }

    /* Style pagination */
    .dataTables_paginate {
        padding: 16px !important;
        text-align: center !important;
    }

    .dataTables_info {
        padding: 16px !important;
        color: #6b7280 !important;
        font-size: 14px !important;
    }

    /* Multi-select dropdown styles */
    .multi-select-container {
        position: relative;
        cursor: pointer !important;
    }

    .multi-select-display {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 40px;
        transition: border-color 0.2s;
    }

    .multi-select-display * {
        cursor: pointer !important;
    }

    .multi-select-display .placeholder {
        color: #111213ff;
        flex: 1;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
    }

    .multi-select-display span {
        background: none;
    }

    .multi-select-display:hover {
        border-color: #9ca3af;
        background-color: #eebc4a;
    }

    .multi-select-display:focus-within {
        border-color: #dc2626;
        outline: none;
    }

    .multi-select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        margin-top: 4px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .multi-select-dropdown.active {
        display: block;
    }

    .multi-select-option {
        padding: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background-color 0.2s;
    }

    .multi-select-option:hover {
        background-color: #f3f4f6;
    }

    .multi-select-option input[type="checkbox"] {
        cursor: pointer;
        width: 16px;
        height: 16px;
        accent-color: #dc2626;
    }

    .multi-select-option label {
        flex: 1;
        font-size: 14px;
        color: #374151;
        user-select: none;
    }

    .selected-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
        min-height: 24px;
    }

    .selected-tag {
        background: #dc2626;
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .selected-tag .remove {
        cursor: pointer;
        font-weight: bold;
        font-size: 16px;
        line-height: 1;
        margin-left: 2px;
    }

    .selected-tag .remove:hover {
        opacity: 0.8;
    }

    #filterCountBadge {
        background: #dc2626;
        color: white;
        border-radius: 50%;
        min-width: 20px;
        height: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        margin-left: 4px;
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 20px; background: white">
    {{-- Header --}}
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">Alumni Directory</h1>
        <p style="color: #6b7280; font-size: 15px;">Connect with {{ $totalAlumni}} alumni from SIP Academy</p>
    </div>

    {{-- Search and Filter --}}
    <div class="search-filter-container" style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px; flex-wrap: wrap;">
        <div class="search-box" style="flex: 1; position: relative; min-width: 200px;">
            <i class="fas fa-search"
                style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
            <input type="text" id="searchInput" placeholder="Search alumni..."
                style="width: 100%; padding: 8px 16px 8px 45px; border: 1px solid #d1d5db; border-radius: 30px; font-size: 14px; outline: none;"
                onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#d1d5db'">
        </div>
        <button id="filterToggleBtn"
            style=" color: #374151; border: 1px solid #d1d5db; padding: 4px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px; white-space: nowrap; position: relative;"
            onmouseover="this.style.background='#eebc4a'" onmouseout="this.style.background='#fbf9fa'">
            <i class="bi bi-funnel" style="font-size: 18px;"></i>
            <span id="filterBtnText">Filter<i class="fa-solid fa-chevron-down" style="margin-left: 10px;"></i></span>
        </button>
        <button id="clearFiltersBtn"
            style="background: white; color: #dc2626; border: 1px solid #dc2626; padding: 11px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: none; white-space: nowrap;"
            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='white'">
            Clear All Filters
        </button>
    </div>

    {{-- Filter Section --}}
    <div id="filterSection"
        style="display: none; background: #fbf9fa; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <div class="filter-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div>
                <label style="font-weight: 600; font-size: 13px; color: #111827; display: block; margin-bottom: 8px;">
                    Batch Year
                </label>
                <div class="multi-select-container" data-filter="batch">
                    <div class="multi-select-display">
                        <span class="placeholder">Select batch years</span>
                        <i class="fas fa-chevron-down" style="color: #151616ff; font-size: 11px;"></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
            <div>
                <label style="font-weight: 600; font-size: 13px; color: #111827; display: block; margin-bottom: 8px;">
                    Location
                </label>
                <div class="multi-select-container" data-filter="location">
                    <div class="multi-select-display">
                        <span class="placeholder">Select locations</span>
                        <i class="fas fa-chevron-down" style="color: #9ca3af; font-size: 11px;"></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
            <div>
                <label style="font-weight: 600; font-size: 13px; color: #111827; display: block; margin-bottom: 8px;">
                    Status
                </label>
                <div class="multi-select-container" data-filter="status">
                    <div class="multi-select-display">
                        <span class="placeholder">Select status</span>
                        <i class="fas fa-chevron-down" style="color: #9ca3af; font-size: 11px;"></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Selected Filters Display (Always visible when filters are selected) --}}
    <div id="selectedFiltersDisplay" style="display: none; margin-bottom: 20px;">
        <div class="selected-tags" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
    </div>

    {{-- Info Banner --}}
    <div id="directoryRibbon" data-ribbon-state="{{ $isDirectoryRibbon ?? 0 }}"
        style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px; margin-bottom: 14px; display: {{ ($isDirectoryRibbon ?? 0) == 1 ? 'flex' : 'none' }}; justify-content: space-between; align-items: center;">
        <p style="color: #1a0505ff; font-size: 14px; margin: 0;">
            You can share your contact with alumni. Once they accept, you can view their profile and contact info in the
            Connections menu.
        </p>
        <button id="closeDirectoryRibbon"
            style="background: transparent; border: none; color: #160606ff; cursor: pointer; font-size: 20px; padding: 0 8px; line-height: 1;">
            ×
        </button>
    </div>


    {{-- Alumni Table --}}
    <div style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
        <div class="table-container" style="overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%;">
            <table id="alumniTable" class="display" style="width: 100%; margin: 0; border-collapse: collapse; min-width: 700px;">
                <thead>
                    <tr style="background: linear-gradient(90deg, #dc2626 0%, #f59e0b 100%); color: white;">
                        <th style="padding: 16px; font-weight: 600; text-align: left; border: none; min-width: 250px;">
        Alumni 
    </th>

    <th style="padding: 16px; font-weight: 600; text-align: left; border: none; min-width: 120px;">
        Batch 
    </th>

    <th style="padding: 16px; font-weight: 600; text-align: left; border: none; min-width: 180px;">
        Location 
    </th>

    <th style="padding: 16px; font-weight: 600; text-align: left; border: none; min-width: 150px;">Action</th>

                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div id="customPaginationContainer" class="mt-10"></div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        let selectedFilters = {
            batch: [],
            location: [],
            status: []
        };

        let filterOptions = {
            batch: [],
            location: [],
            status: []
        };

        // Function to load filter options
        function loadFilterOptions() {
            $.ajax({
                url: "{{ route('alumni.directory.filter-options') }}",
                method: 'GET',
                success: function(data) {
                    filterOptions.batch = data.batchYears.map(year => ({ id: year, name: year }));
                    filterOptions.location = data.locations;
                    filterOptions.status = data.connectionStatuses;

                    populateDropdown('batch', filterOptions.batch);
                    populateDropdown('location', filterOptions.location);
                    populateDropdown('status', filterOptions.status);
                }
            });
        }

        // Load filter options on page load
        loadFilterOptions();

        function populateDropdown(type, options) {
            const container = $(`.multi-select-container[data-filter="${type}"]`);
            const dropdown = container.find('.multi-select-dropdown');
            
            // Get currently selected values
            const selectedValues = selectedFilters[type].map(item => item.id);
            
            dropdown.empty();
            options.forEach(option => {
                const isChecked = selectedValues.includes(option.id.toString());
                const optionHtml = `
                    <div class="multi-select-option" data-value="${option.id}">
                        <input type="checkbox" id="${type}_${option.id}" value="${option.id}" ${isChecked ? 'checked' : ''}>
                        <label for="${type}_${option.id}" style="cursor: pointer; margin: 0;">${option.name}</label>
                    </div>
                `;
                dropdown.append(optionHtml);
            });
        }

        // Toggle dropdown
        $(document).on('click', '.multi-select-display', function(e) {
            e.stopPropagation();
            const dropdown = $(this).siblings('.multi-select-dropdown');
            $('.multi-select-dropdown').not(dropdown).removeClass('active');
            dropdown.toggleClass('active');
        });

        // Prevent dropdown from closing when clicking inside
        $(document).on('click', '.multi-select-dropdown', function(e) {
            e.stopPropagation();
        });

        // Close dropdowns when clicking outside
        $(document).on('click', function() {
            $('.multi-select-dropdown').removeClass('active');
        });

        // Handle checkbox selection
        $(document).on('change', '.multi-select-option input[type="checkbox"]', function() {
            const container = $(this).closest('.multi-select-container');
            const filterType = container.data('filter');
            const value = $(this).val();
            const label = $(this).siblings('label').text();

            if ($(this).is(':checked')) {
                selectedFilters[filterType].push({ id: value, name: label });
            } else {
                selectedFilters[filterType] = selectedFilters[filterType].filter(item => item.id != value);
            }

            updatePlaceholder(filterType);
            table.ajax.reload();
            updateClearButton();
        });

        function updatePlaceholder(type) {
            const container = $(`.multi-select-container[data-filter="${type}"]`);
            const placeholder = container.find('.placeholder');
            const count = selectedFilters[type].length;

            if (count > 0) {
                placeholder.text(`${count} selected`);
            } else {
                const placeholders = {
                    batch: 'Select batch years',
                    location: 'Select locations',
                    status: 'Select status'
                };
                placeholder.text(placeholders[type]);
            }
        }

        // Remove tag
        $(document).on('click', '.selected-tag .remove', function() {
            const type = $(this).data('type');
            const value = $(this).data('value');

            selectedFilters[type] = selectedFilters[type].filter(item => item.id != value);
            
            // Uncheck the checkbox
            $(`.multi-select-container[data-filter="${type}"] input[value="${value}"]`).prop('checked', false);
            
            updatePlaceholder(type);
            table.ajax.reload();
            updateClearButton();
        });

        // Clear all filters
        $('#clearFiltersBtn').on('click', function() {
            selectedFilters = { batch: [], location: [], status: [] };
            
            $('.multi-select-option input[type="checkbox"]').prop('checked', false);
            $('.selected-tags').empty();
            
            updatePlaceholder('batch');
            updatePlaceholder('location');
            updatePlaceholder('status');
            
            table.ajax.reload();
            updateClearButton();
        });

        function updateClearButton() {
            const totalCount = selectedFilters.batch.length + 
                             selectedFilters.location.length + 
                             selectedFilters.status.length;
            
            const hasFilters = totalCount > 0;
            
            // Update count badge
            const badge = $('#filterCountBadge');
            if (hasFilters) {
                badge.text(totalCount).show();
                $('#clearFiltersBtn').show();
            } else {
                badge.hide();
                $('#clearFiltersBtn').hide();
            }

            // Update selected filters display when section is closed
            updateSelectedFiltersDisplay();
        }

        function updateSelectedFiltersDisplay() {
            const displayContainer = $('#selectedFiltersDisplay');
            const displayTags = displayContainer.find('.selected-tags');
            
            displayTags.empty();
            
            // Collect all selected filters
            const allFilters = [
                ...selectedFilters.batch.map(item => ({ ...item, type: 'batch' })),
                ...selectedFilters.location.map(item => ({ ...item, type: 'location' })),
                ...selectedFilters.status.map(item => ({ ...item, type: 'status' }))
            ];

            if (allFilters.length > 0) {
                // Show selected filters always when there are filters
                allFilters.forEach(item => {
                    // Get filter type label
                    let typeLabel = '';
                    if (item.type === 'batch') {
                        typeLabel = 'Batch: ';
                    } else if (item.type === 'location') {
                        typeLabel = 'Location: ';
                    } else if (item.type === 'status') {
                        typeLabel = 'Status: ';
                    }
                    
                    const tag = `
                        <div class="selected-tag">
                            <span>${typeLabel}${item.name}</span>
                            <span class="remove" data-type="${item.type}" data-value="${item.id}">×</span>
                        </div>
                    `;
                    displayTags.append(tag);
                });
                displayContainer.show();
            } else {
                displayContainer.hide();
            }
        }

        const table = $('#alumniTable').DataTable({
            processing: true,
            serverSide: true,
            stripeClasses: [],         
            orderClasses: false,
            ajax: {
                url: "{{ route('alumni.directory.data') }}",
                data: function(d) {
                    d.batch_years = selectedFilters.batch.map(item => item.id).join(',');
                    d.locations = selectedFilters.location.map(item => item.id).join(',');
                    d.connection_statuses = selectedFilters.status.map(item => item.id).join(',');
                }
            },
            columns: [{
                    data: 'alumni',
                    name: 'full_name',
                },
                {
                    data: 'batch',
                    name: 'year_of_completion',
                },
                {
                    data: 'location',
                    name: 'city_id',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                { data: 'created_at', name: 'created_at', visible: false },
            ],
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: false,
            pagelength: 10,
            order: [[4, 'desc']],
            dom: 't',
        });

        // Function to update sorting icons
        function updateSortIcons() {
            $('#alumniTable thead th').each(function() {
                const $th = $(this);
                
                $th.find('.sort-icon').remove(); // Remove existing icons
                
                // Check specific sorting states first (before generic 'sorting')
                if ($th.hasClass('sorting_asc')) {
                    $th.append(' <i class="bi bi-arrow-up sort-icon" style="color:white;font-size:14px;margin-left:6px;"></i>');
                } else if ($th.hasClass('sorting_desc')) {
                    $th.append(' <i class="bi bi-arrow-down sort-icon" style="color:white;font-size:14px;margin-left:6px;"></i>');
                } else if ($th.hasClass('sorting')) {
                    $th.append(' <i class="bi bi-arrow-down-up sort-icon" style="color:white;font-size:13px;margin-left:6px;"></i>');
                }
            });
        }

        // Update icons on table draw
        table.on('draw', function() {
            // Force remove sorted column background colors
            $('#alumniTable tbody td').css('background-color', '');
            $('#alumniTable tbody tr.odd td').css('background-color', '');
            $('#alumniTable tbody tr.even td').css('background-color', '');
            
            updateSortIcons();
            
            let info = table.page.info();

            let html = `
          <div class="pagination-container d-flex justify-content-between align-items-center flex-wrap" style="padding: 10px 5px; gap: 10px;">
            
            <div class="pagination-info text-muted" style="font-size: 14px;">
                Showing ${info.start + 1}-${info.end} of ${info.recordsTotal} alumni
            </div>

            <div class="pagination-controls d-flex align-items-center gap-2">
                <button class="btn btn-light btn-sm" id="prevPage" ${info.page === 0 ? "disabled" : ""}>
                    <span class="d-none d-sm-inline">‹ Previous</span>
                    <span class="d-inline d-sm-none">‹</span>
                </button>

                <span class="px-3 py-1 bg-danger text-white rounded" style="font-weight:600; font-size: 14px;">
                    ${info.page + 1}
                </span>

                <button class="btn btn-light btn-sm" id="nextPage" ${(info.page + 1 === info.pages) ? "disabled" : ""}>
                    <span class="d-none d-sm-inline">Next ›</span>
                    <span class="d-inline d-sm-none">›</span>
                </button>
            </div>

           </div>
           `;

            $("#customPaginationContainer").html(html);

            // Attach events again
            $("#prevPage").on("click", function() {
                table.page("previous").draw("page");
            });

            $("#nextPage").on("click", function() {
                table.page("next").draw("page");
            });
        });

        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Update icons after sorting
        table.on('order.dt', function() {
            updateSortIcons();
        });

        // Initial icon setup
        setTimeout(function() {
            updateSortIcons();
        }, 500);

        $('#filterToggleBtn').on('click', function() {
            const section = $('#filterSection');
            const isVisible = section.is(':visible');
            section.slideToggle(300, function() {
                updateSelectedFiltersDisplay();
            });

            const icon = $(this).find('i');
            const btnText = $('#filterBtnText');
            if (isVisible) {
                icon.removeClass('fa-times').addClass('bi-funnel');
                $('#filterBtnText').html('Filter <i class="fa-solid fa-chevron-down" style="margin-left: 10px;"></i>');
            } else {
                $('#filterBtnText').html('Close Filters <i class="fa-solid fa-chevron-up" style="margin-left: 10px;"></i>');
            }
        });

        // Close directory ribbon button - update database
        $('#closeDirectoryRibbon').on('click', function() {
            $('#directoryRibbon').slideUp();
            
            // Update database via API
            $.ajax({
                url: "{{ route('alumni.update.ribbon') }}",
                type: 'POST',
                data: {
                    is_directory_ribbon: 0,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#directoryRibbon').data('ribbon-state', 0);
                        // showToast(response.message);
                    }
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    showToast(res && res.message ? res.message : 'An error occurred while updating status.', 'error');
                }
            });
        });
        $(document).on("click", ".sendRequestBtn", function (e) {
            e.preventDefault();
            let url = $(this).data("url");
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function (res) {
                    if (res.success) {
                        showToast(res.message);
                    }

                    loadFilterOptions();
                    $('#alumniTable').DataTable().ajax.reload(null, false);
                },
                error: function (xhr) {
                    const res = xhr.responseJSON;
                    showToast(res && res.message ? res.message : 'An error occurred while updating status.', 'error');
                }
            });
        });
    });
</script>
@endpush
@endsection