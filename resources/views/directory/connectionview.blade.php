@extends('layouts.index')
@section('title', 'Alumni Connections - Alumni Tracking')

@section('content')
<style>
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
        transition: all 0.2s;
        background: white;
    }

    .multi-select-display * {
        cursor: pointer !important;
    }

    .multi-select-display .placeholder {
        flex: 1;
        font-size: 14px;
        font-weight: 400;
    }
    .placeholder {
        background-color: #ffffffff !important;
        opacity: 1 !important;
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
        padding: 10px 12px;
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
        accent-color: #ba0028;
    }

    .multi-select-option label {
        flex: 1;
        font-size: 14px;
        color: #374151;
        cursor: pointer;
        user-select: none;
    }

    .selected-tag {
        background: #fbbf24;
        color: #000;
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .selected-tag button {
        background: none;
        border: none;
        color: #000;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        padding: 0;
        font-weight: 700;
    }

    .selected-tag button:hover {
        opacity: 0.8;
    }

    /* Count badge in dropdown */
    .filter-count-badge {
        background: #dc2626;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: 6px;
    }

     table.dataTable {
        margin: 0 !important;
    }
</style>

<div class="content-container mt-4">
    <div class="connection-header">
        <button onclick="history.back()" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
        </button>
        <div>
            <h1 class="main-title">Alumni 1 connections with the Alumni Peoples</h1>
            <p class="main-subtitle">
                View and manage alumni connections
            </p>
        </div>
    </div>

    <div class="table-box-container">
        <!-- Search and Filter -->
        <div class="search-filter-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search by name or location...">
            </div>
            <button id="filterToggleBtn">
                <i class="fas fa-filter"></i>
                <span id="filterBtnText">Filter</span>
            </button>
        </div>

        @include('directory.connectionfilter')



        <!-- Alumni Table -->
        <div class="table-container">
            <div class="table-responsive" style="overflow-x: auto;">
                <table id="dataTable">
                    <thead>
                        <tr id="tableHeaderRow">
                            <th class="table-header">Alumni Name</th>
                            <th class="table-header">Batch</th>
                            <th class="table-header">Location</th>
                            <th class="table-header">View profile</th>
                            <th class="table-header">Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="pagination-bottom-area">
                <div class="dt-info-custom">
                    <!-- Info will be populated here -->
                </div>
                <div class="dt-pagination-custom">
                    <!-- Pagination will be populated here -->
                </div>
            </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="alumniProfileDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="text-end p-4">
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="profileModalBody" style="padding:10px 30px;">
                <div style="padding: 10px;">
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <div id="profileImageContainer" style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 4px solid #dc2626; flex-shrink: 0; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);">
                            <img id="profileImage" src="" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <h2 id="profileName" style="margin: 0 0 8px 0; font-size: 24px; font-weight: 700; color: #111827; line-height: 1.2;">Alumni Connection 1</h2>
                            <p id="profileOccupation" style="margin: 0; font-size: 15px; color: #6b7280; font-weight: 500;">Software Engineer</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div style="padding: 24px 32px 32px 32px;">
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">Full Name:</label>
                            <p id="profileFullName" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1;">John Doe</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">Year:</label>
                            <p id="profileBatch" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1;">2000</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">City & State:</label>
                            <p id="profileLocation" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1;">Mumbai, Tamil Nadu</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">Email:</label>
                            <p id="profileEmail" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1; word-break: break-all;">connection1@example.com</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">Contact:</label>
                            <p id="profileNumber" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1;">+91 4616661464</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 0;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">Occupation:</label>
                            <p id="profileOccupationDetails" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1;">Software Engineer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    let selectedFilters = {
        batch: [],
        location: []
    };

    let alumniId = "{{ $id }}";
    let table;

    $(document).ready(function() {
        // Initialize multi-select dropdowns
        initializeMultiSelect();

        // Initialize DataTable
        table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.directory.view.connections.list', ':id') }}".replace(':id', alumniId),
                type: 'GET',
                data: function(d) {
                    d.batch = selectedFilters.batch;
                    d.location = selectedFilters.location;
                    d.search = $('#searchInput').val();
                }
            },
            columns: [
                { data: 'alumni', name: 'alumni' },
                { data: 'batch', name: 'batch' },
                { data: 'location', name: 'location' },
                { data: 'viewProfile', name: 'viewProfile' },
                { data: 'status', name: 'status' }
            ],
            pageLength: 10,
            lengthChange: false,
            searching: false,
            ordering: false,
            scrollX: true,
            dom: 't',
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ connections"
            }
        });

        // Custom Pagination
        table.on('draw', function() {
            let info = table.page.info();
            $(".dt-info-custom").html(
                `Showing ${info.start + 1 > info.recordsTotal ? 0 : info.start + 1} to ${info.end} connections`
            );
            let totalPages = info.pages > 0 ? info.pages : 1;

            let paginationHtml = `
                <button id="prevPage" ${info.page === 0 ? "disabled" : ""}>
                    <i class="fas fa-chevron-left"></i>
                    Previous
                </button>

                <span>
                    Page ${info.page + 1} of ${totalPages}
                </span>

                <button id="nextPage" ${(info.page + 1 === totalPages) ? "disabled" : ""}>
                    Next
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;
            $(".dt-pagination-custom").html(paginationHtml);
            
            $("#prevPage").on("click", function() { table.page("previous").draw("page"); });
            $("#nextPage").on("click", function() { table.page("next").draw("page"); });
        });

        // Search functionality
        let searchTimeout;
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                table.ajax.reload();
            }, 500);
        });

        // Filter toggle
        $('#filterToggleBtn').on('click', function() {
            const section = $('#filterSection');
            const isVisible = section.is(':visible');
            section.slideToggle();
            $('#filterBtnText').text(isVisible ? 'Filter' : 'Close Filters');
        });
    });

    function initializeMultiSelect() {
        // Fetch filter options
        $.ajax({
            url: "{{ route('admin.directory.view.connections.filter-options', ':id') }}".replace(':id', alumniId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    populateDropdown('batch', response.batches);
                    populateDropdown('location', response.locations);
                }
            },
            error: function() {
                console.error('Failed to load filter options');
            }
        });

        // Setup dropdown toggles
        $('.multi-select-display').on('click', function(e) {
            e.stopPropagation();
            const dropdown = $(this).siblings('.multi-select-dropdown');
            $('.multi-select-dropdown').not(dropdown).removeClass('active');
            dropdown.toggleClass('active');
        });

        // Close dropdowns when clicking outside
        $(document).on('click', function() {
            $('.multi-select-dropdown').removeClass('active');
        });

        $('.multi-select-dropdown').on('click', function(e) {
            e.stopPropagation();
        });
    }

    function populateDropdown(type, options) {
        const container = $(`.multi-select-container[data-filter="${type}"]`);
        const dropdown = container.find('.multi-select-dropdown');
        dropdown.empty();

        options.forEach(option => {
            const value = option.value || option;
            const label = option.label || option;
            const isChecked = selectedFilters[type].includes(value);

            const optionHtml = `
                <div class="multi-select-option">
                    <input type="checkbox" id="${type}-${value}" value="${value}" ${isChecked ? 'checked' : ''}>
                    <label for="${type}-${value}">${label}</label>
                </div>
            `;
            dropdown.append(optionHtml);
        });

        // Attach change event
        dropdown.find('input[type="checkbox"]').on('change', function() {
            toggleFilter(type, this.value, this.checked);
        });
    }

    function toggleFilter(type, value, isChecked) {
        if (isChecked) {
            if (!selectedFilters[type].includes(value)) {
                selectedFilters[type].push(value);
            }
        } else {
            selectedFilters[type] = selectedFilters[type].filter(v => v !== value);
        }
        updatePlaceholder(type);
        updateSelectedFiltersDisplay();
        table.ajax.reload();
    }

    function updatePlaceholder(type) {
        const container = $(`.multi-select-container[data-filter="${type}"]`);
        const display = container.find('.multi-select-display');
        const count = selectedFilters[type].length;
        
        const placeholderTexts = {
            batch: 'Batch',
            location: 'Location'
        };
        
        // Remove existing badge if any
        display.find('.filter-count-badge').remove();
        
        if (count > 0) {
            display.html(`
                <span>${placeholderTexts[type]}</span>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span class="filter-count-badge">${count}</span>
                    <i class="fas fa-chevron-down" style="color: #9ca3af; font-size: 10px;"></i>
                </div>
            `);
        } else {
            display.html(`
                <span>${placeholderTexts[type]}</span>
                <i class="fas fa-chevron-down" style="color: #9ca3af; font-size: 10px;"></i>
            `);
        }
    }

    function updateSelectedFiltersDisplay() {
        const container = $('#selectedFiltersDisplay');
        const tagsContainer = container.find('.selected-tags');
        tagsContainer.empty();

        let hasFilters = false;
        const filterLabels = { batch: 'Batch', location: 'Location' };

        Object.keys(selectedFilters).forEach(filterType => {
            selectedFilters[filterType].forEach(value => {
                hasFilters = true;
                const tag = $(`
                    <div class="selected-tag">
                        <span>${filterLabels[filterType]}: ${value}</span>
                        <button onclick="removeFilter('${filterType}', '${value}')">×</button>
                    </div>
                `);
                tagsContainer.append(tag);
            });
        });

        container.toggle(hasFilters);
    }

    function removeFilter(filterType, value) {
        selectedFilters[filterType] = selectedFilters[filterType].filter(v => v !== value);
        $(`.multi-select-container[data-filter="${filterType}"] input[value="${value}"]`).prop('checked', false);
        updatePlaceholder(filterType);
        updateSelectedFiltersDisplay();
        table.ajax.reload();
    }

    function clearAllFilters() {
        selectedFilters = { batch: [], location: [] };
        $('.multi-select-option input[type="checkbox"]').prop('checked', false);
        
        // Reset all placeholders
        updatePlaceholder('batch');
        updatePlaceholder('location');
        
        updateSelectedFiltersDisplay();
        table.ajax.reload();
    }

    function viewProfileDetails(id) {
        $.ajax({
            url: "{{ route('admin.directory.view.profile', ':id') }}".replace(':id', id),
            type: 'GET',
            success: function(response) {
                const data = response.data;
                $('#profileImage').attr('src', data.image_url);
                $('#profileName').text(data.name);
                $('#profileFullName').text(data.name);
                $('#profileEmail').text(data.email);
                $('#profileBatch').text(data.batch);
                $('#profileLocation').text(data.location);
                $('#profileOccupation').text(data.occupation);
                $('#profileOccupationDetails').text(data.occupation);
                $('#profileNumber').text(data.mobile_number);
                $('#alumniProfileDetailsModal').modal('show');
            },
            error: function() {
                alert('Failed to fetch profile details.');
            }
        });
    }
</script>
@endpush
@endsection
