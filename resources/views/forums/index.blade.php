@extends('layouts.index')
@section('title', 'Directory - Alumni Tracking')

@section('content')
<div style="margin-bottom: 30px;">
    <h1 style="font-size: 40px; font-weight: 700; color: #333; margin-bottom: 8px;">Forums</h1>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
        Manage community discussions and forum posts
    </p>
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <!-- Search and Filter -->
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                <input type="text" id="searchInput" placeholder="Search by alumni name or post title..."
                    style="width: 100%; padding: 10px 15px 10px 40px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>
            <button id="filterToggleBtn"
                style="background-color: #ba0028; color: white; border: none; border-radius: 6px; padding: 10px 20px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; font-weight: 500;">
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
                            style="background: white; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; min-width: 180px; justify-content: space-between;">
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

        <!-- Alumni Table -->
        <table id="forumsTable" class="display" style="width: 100%; border-collapse: collapse; border: 1px solid #dedede; background-color: #f5f0f0ff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 6px;">
            <thead>
                <tr style="background: #ba0028; color: white; font-weight: 700; font-size: 14px;">
                    <th style="padding: 15px; text-align: left;">Created On</th>
                    <th style="padding: 15px; text-align: left;">Alumni</th>
                    <th style="padding: 15px; text-align: left;">Contact</th>
                    <th style="padding: 15px; text-align: left;">View Post</th>
                    <th style="padding: 15px; text-align: left;">Action Taken On</th>
                    <th style="padding: 15px; text-align: left;">Status</th>
                    <th style="padding: 15px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
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
</style>

<!-- Profile Modal -->
<div class="modal fade" id="viewPostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#c41e3a;color:white;">
                <h5 class="modal-title">Alumni Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="profileModalBody" style="padding:20px;">
                <div class="text-center">
                    <img id="profileImage" src="" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;">
                    <h5 id="profileName" style="font-weight:700;"></h5>
                    <p id="profileEmail" style="color:#666;"></p>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Batch:</strong> <span id="profileBatch"></span></p>
                        <p><strong>Location:</strong> <span id="profileLocation"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Occupation:</strong> <span id="profileOccupation"></span></p>
                        <p><strong>Company:</strong> <span id="profileCompany"></span></p>
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
        statuses: []
    };

    $(document).ready(function() {
        // Load filter options
        loadFilterOptions();

        const table = $('#forumsTable').DataTable({
            processing: true,
            serverSide: true,
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
                    name: 'created_at'
                },
                {
                    data: 'alumni',
                    name: 'alumni',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'contact',
                    name: 'contact'
                },
                {
                    data: 'view_post',
                    name: 'view_post',
                    orderable: false
                },
                {
                    data: 'action_taken_on',
                    name: 'action_taken_on'
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
            searching: false,
            ordering: false,
            pageLength: 10,
            lengthChange: false,
            scrollX: true,
            dom: 't<"row mt-10"<"col-6 dt-info-custom"><"col-6 dt-pagination-custom text-end">>',
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ posts"
            }
        });
        
        table.on('draw', function() {
            let info = table.page.info();

            $(".dt-info-custom").html(
                `Showing ${info.start + 1} to ${info.end} posts`
            );

            let paginationHtml = `
            <button class="btn btn-light btn-sm me-2" id="prevPage" ${info.page === 0 ? "disabled" : ""}>
                ‹ Previous
            </button>

            <span class="mx-2" style="font-weight:500;">
                Page ${info.page + 1} of ${info.pages}
            </span>

            <button class="btn btn-light btn-sm ms-2" id="nextPage" ${(info.page + 1 === info.pages) ? "disabled" : ""}>
                Next ›
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
        const fromDate = $('#filterFromDate').val();
        const toDate = $('#filterToDate').val();
        
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

    function statusChange(id, status) {
        confirmBox("Are you sure you want to change the status?", function() {
            $.ajax({
                url: "{{ route('forums.change.status') }}",
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#forumsTable').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    alert('An error occurred while updating the status.');
                }
            });
        });
    }
</script>
@endpush
@endsection