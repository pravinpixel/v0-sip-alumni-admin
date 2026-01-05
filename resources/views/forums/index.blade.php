@extends('layouts.index')
@section('title', 'Directory - Alumni Tracking')

@section('content')
<div class="content-container">
    <h1 class="main-title">Forums</h1>
    <p class="main-subtitle">
        Manage community discussions and forum posts
    </p>
    <div class="table-box-container">
        <!-- Search and Filter -->
        <div class="search-filter-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search by alumni name or post title...">
            </div>
            <button id="filterToggleBtn">
                <i class="fas fa-filter"></i>
                <span id="filterBtnText">Filter</span>
            </button>
        </div>

        @include('forums.filtersection')

        <!-- Forums Table Container -->
        <div class="table-container">
            <!-- Table Wrapper (Scrollable) -->
            <div class="table-responsive" style="overflow-x: auto;">
                <table id="dataTable">
                    <thead>
                        <tr id="tableHeaderRow">
                            <th class="table-header">Created On</th>
                            <th class="table-header">Alumni</th>
                            <th class="table-header">Contact</th>
                            <th class="table-header">View Post</th>
                            <th class="table-header">Action Taken On</th>
                            <th class="table-header">Status</th>
                            <th class="table-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination Area (Bottom of Table Design) -->
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
<style>

    /* Prevent DataTables from creating additional header elements */
    .dataTables_wrapper .dataTables_scroll .dataTables_scrollHead {
        display: none !important;
    }

    /* Ensure the original header stays visible */
    #dataTable > thead {
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


    /* Ensure DataTables doesn't interfere with our fixed pagination */
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        display: none !important;
    }
</style>

<!-- Post Details Modal -->
<div id="postDetailsModal" class="modal-overlay">
    <div class="modal-wrapper">
        <div class="modal-card">

            <!-- Header -->
            <div class="modal-header">
                <button class="modal-close-btn" onclick="closePostModal()">×</button>
                <h2 class="modal-title">Post Details</h2>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Post Title -->
                <div class="modal-section">
                    <label class="modal-label">POST TITLE</label>
                    <div id="postTitle" class="modal-content-box"></div>
                </div>

                <hr>

                <!-- Post Description -->
                <div class="modal-section">
                    <label class="modal-label">POST DESCRIPTION</label>
                    <div id="postDescription" class="modal-content-box description-box"></div>
                </div>

                <hr>

                <!-- Labels -->
                <div class="modal-section">
                    <label class="modal-label">LABELS</label>
                    <div id="postLabels" class="modal-labels"></div>
                </div>

                <hr>

                <!-- View Comments Button -->
                <button id="viewCommentsBtn" class="view-comments-btn" onclick="viewComments()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square mr-2 h-5 w-5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
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
<div id="removePostModal" class="modal-overlay">
    <div class="modal-wrapper">
        <div class="modal-card modal-card-lg">

            <!-- Body -->
            <div class="modal-body-lg">

                <!-- Description -->
                <div class="modal-desc">
                    <h2 class="modal-heading">Remove Post</h2>
                    <p class="modal-subtext">
                        Removing this post will make it no longer available to any of the alumni.
                        Please provide a reason for removal.
                    </p>
                </div>

                <!-- Removal Remarks -->
                <div class="modal-field">
                    <textarea
                        id="removalRemarks"
                        rows="3"
                        class="modal-textarea"
                        placeholder="Enter remarks for removing this post...">
                    </textarea>
                </div>

                <!-- Action Buttons -->
                <div class="modal-actions">
                    <button class="btn-cancel" onclick="closeRemoveModal()">
                        Cancel
                    </button>

                    <button
                        id="removePostBtn"
                        class="btn-danger"
                        onclick="confirmRemovePost()"
                        disabled>
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
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }

        const table = $('#dataTable').DataTable({
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
                <div class="filter-chip">
                    <span>Status: ${value.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase())}</span>
                    <button onclick="removeFilter('statuses', '${value}')" >
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
                $('#dataTable').DataTable().ajax.reload(null, false);
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

        confirmBox("Approve Post","Are you sure you want approve this post ?", function() {
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