@extends('alumni.layouts.index')

@section('content')
<style>
    /* Responsive Styles */
    @media (max-width: 991px) {

        /* Container and spacing */
        div[style*="max-width: 1400px"] {
            padding: 16px !important;
        }



        #filterSection>div {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 16px !important;
        }

        /* Post cards */
        #forumPostsContainer>div {
            padding: 18px !important;
        }

        #forumPostsContainer h2 {
            font-size: 18px !important;
        }

        #forumPostsContainer p {
            font-size: 14px !important;
        }
    }

    @media (max-width: 767px) {

        /* Container */
        div[style*="max-width: 1400px"] {
            padding: 12px !important;
        }



        /* Search and filter row - stack vertically */
        div[style*="display: flex"][style*="align-items: center"][style*="gap: 12px"][style*="margin-bottom: 20px"] {
            flex-direction: column !important;
            gap: 10px !important;
        }

        /* Search box - full width */
        div[style*="flex: 1"][style*="position: relative"][style*="max-width: 400px"] {
            max-width: 100% !important;
            width: 100% !important;
        }

        #searchInput {
            width: 100% !important;
        }

        #filterSection>div {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }

        /* Post cards */
        #forumPostsContainer>div {
            padding: 16px !important;
        }

        #forumPostsContainer h2 {
            font-size: 17px !important;
            padding-right: 60px !important;
        }

        #forumPostsContainer p {
            font-size: 13px !important;
        }

        /* Post action buttons - wrap on small screens */
        #forumPostsContainer>div>div:last-child {
            flex-wrap: wrap !important;
        }

        #forumPostsContainer button {
            font-size: 13px !important;
            padding: 7px 14px !important;
        }
    }

    @media (max-width: 575px) {

        /* Container */
        div[style*="max-width: 1400px"] {
            padding: 10px !important;
        }


        div[style*="display: flex"][style*="justify-content: space-between"]>div:first-child p {
            font-size: 12px !important;
        }



        /* Search input */
        #searchInput {
            font-size: 13px !important;
            padding: 10px 16px 10px 40px !important;
            height: 38px !important;
        }

        .multi-select-option {
            padding: 8px 10px !important;
            font-size: 13px !important;
        }

        .multi-select-option label {
            font-size: 13px !important;
        }

        /* Selected tags */
        .selected-tag {
            font-size: 11px !important;
            padding: 5px 10px !important;
        }

        /* Post cards */
        #forumPostsContainer>div {
            padding: 14px !important;
            margin-bottom: 16px !important;
        }

        #forumPostsContainer h2 {
            font-size: 16px !important;
            padding-right: 50px !important;
        }

        #forumPostsContainer p {
            font-size: 12px !important;
        }

        /* Post tags */
        #forumPostsContainer span[style*="background: #F7C744"] {
            font-size: 10px !important;
            padding: 3px 10px !important;
        }

        /* Post author section */
        #forumPostsContainer>div>div[style*="border-bottom"] img,
        #forumPostsContainer>div>div[style*="border-bottom"]>div:first-child {
            width: 36px !important;
            height: 36px !important;
            font-size: 13px !important;
        }

        #forumPostsContainer>div>div[style*="border-bottom"] p {
            font-size: 13px !important;
        }

        /* Post stats */
        #forumPostsContainer>div>div[style*="padding: 10px"] {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 12px !important;
        }

        #forumPostsContainer>div>div[style*="padding: 10px"]>div:first-child {
            width: 100%;
        }

        #forumPostsContainer>div>div[style*="padding: 10px"]>div:last-child {
            width: 100%;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        /* Post action buttons */
        #forumPostsContainer button {
            font-size: 12px !important;
            padding: 6px 8px !important;
        }

        /* Reply form */
        div[id^="replyForm-"] {
            padding: 16px !important;
        }

        div[id^="replyForm-"] input {
            font-size: 13px !important;
            padding: 10px 14px !important;
        }

        div[id^="replyForm-"] button {
            font-size: 13px !important;
            padding: 8px 16px !important;
        }

        /* Pin button */
        button[onclick^="togglePin"] {
            top: 12px !important;
            right: 12px !important;
            font-size: 16px !important;
        }
    }

    .multi-select-option {
        padding: 2px 4px;
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
        height: 15px;
        accent-color: #dc2626;
    }

    .multi-select-option label {
        flex: 1;
        font-size: 12px;
        position: relative;
        top: 2px;
        color: #374151;
        cursor: pointer;
        user-select: none;
    }

    .selected-tag {
        background: color-mix(in oklab, #F7C744 20%, transparent);
        color: #B1040E;
        padding: 4px 10px;
        border-radius: 16px;
        border: 1px solid #F7C744;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .selected-tag button {
        background: none;
        border: none;
        color: #B1040E;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
        font-weight: 700;
    }

    .selected-tag button:hover {
        opacity: 0.8;
    }

    /* Single-select dropdown styles - matching multi-select */
    .single-select-container {
        position: relative;
        cursor: pointer !important;
    }

    .single-select-display {
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
        background: white;
    }

    .single-select-display * {
        cursor: pointer !important;
    }

    .single-select-display .placeholder {
        color: #111213ff;
        flex: 1;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
    }

    .single-select-display:hover {
        border-color: #9ca3af;
        background-color: #eebc4a;
    }

    .single-select-display:focus-within {
        border-color: #dc2626;
        outline: none;
    }

    .single-select-dropdown {
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

    .single-select-dropdown.active {
        display: block;
    }

    .single-select-option {
        margin: 10px;
        padding: 10px 12px;
        cursor: pointer;
        font-size: 14px;
        color: #374151;
        transition: background-color 0.2s;
        border-radius: 4px;
    }

    .single-select-option:hover {
        background-color: #f3f4f6;
    }

    .single-select-option.selected {
        background-color: #fee2e2;
        color: #dc2626;
        font-weight: 600;
    }
</style>

@include('alumni.modals.view-thread-modal')

{{-- Report Post Modal --}}
<div id="reportPostModal" class="modal-overlay">
    <div class="report-modal-popup">
        <div class="modal-header">
            <h2>Report Post</h2>
            <button class="modal-close-btn" onclick="closeReportModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Reason for reporting</label>
                <textarea id="reportReason" class="form-textarea" placeholder="Please describe why you're reporting this post..." rows="3"></textarea>
                <small class="error-message" style="color: #dc2626; font-size: 12px; display: none;"></small>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeReportModal()">Cancel</button>
            <button class="btn-submit-report" onclick="submitReport()">Submit Report</button>
        </div>
    </div>
</div>

<style>
    /* Report Modal Styles */
    #reportPostModal.modal-overlay {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    #reportPostModal.modal-overlay.open {
        display: flex;
    }

    #reportPostModal .report-modal-popup {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        animation: slideIn 0.3s ease;
        overflow: hidden;
    }

    #reportPostModal .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        background: white;
        border-radius: 12px 12px 0 0;
    }

    #reportPostModal .modal-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #111827;
    }

    #reportPostModal .modal-close-btn {
        background: none;
        border: none;
        font-size: 28px;
        color: #6b7280;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #reportPostModal .modal-close-btn:hover {
        color: #111827;
    }

    #reportPostModal .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

    #reportPostModal .form-group {
        margin-bottom: 20px;
    }

    #reportPostModal .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #111827;
        font-size: 14px;
    }

    #reportPostModal .form-textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        box-sizing: border-box;
        transition: border-color 0.3s;
        resize: vertical;
        min-height: 100px;
    }

    #reportPostModal .form-textarea:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    #reportPostModal .form-textarea::placeholder {
        color: #9ca3af;
    }

    #reportPostModal .form-textarea.input-error {
        border-color: #dc2626;
        background-color: #fef2f2;
    }

    #reportPostModal .char-count {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        color: #6b7280;
        text-align: right;
    }

    #reportPostModal .error-message {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: #dc2626;
        font-weight: 500;
    }

    #reportPostModal .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px;
        border-top: 1px solid #e5e7eb;
        background-color: #f9fafb;
        flex-shrink: 0;
        border-radius: 0 0 12px 12px;
    }

    #reportPostModal .btn-cancel {
        background: white;
        color: #6b7280;
        padding: 10px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    #reportPostModal .btn-cancel:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    #reportPostModal .btn-submit-report {
        background: #dc2626;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    #reportPostModal .btn-submit-report:hover {
        background: #b91c1c;
    }

    #reportPostModal .btn-submit-report:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    /* Report Button Styles */
    .report-toggle-btn {
        background: transparent;
        color: #6b7280;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .report-toggle-btn:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    .report-toggle-btn i {
        font-size: 12px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        #reportPostModal .report-modal-popup {
            width: 95%;
            margin: 20px;
        }

        #reportPostModal .modal-header {
            padding: 16px;
        }

        #reportPostModal .modal-header h2 {
            font-size: 18px;
        }

        #reportPostModal .modal-body {
            padding: 16px;
        }

        #reportPostModal .modal-footer {
            padding: 16px;
            flex-direction: column;
        }

        #reportPostModal .btn-cancel,
        #reportPostModal .btn-submit-report {
            width: 100%;
            justify-content: center;
        }

        .report-toggle-btn {
            padding: 6px 8px;
            font-size: 12px;
        }
    }
</style>

{{-- Include common thread modal JavaScript --}}
<script src="{{ asset('js/thread-modal-common.js') }}"></script>

<div style="max-width: 1400px; margin: 0 auto; padding: 20px; background: white">
    {{-- Header --}}
    <div class="forum-header">
        <div class="forum-header-left">
            <h1 style=" font-weight: 700; color: #111827; margin-bottom: 8px;" class="main-title">Forum Posts</h1>
            <p style="color: #6b7280;" class="sub-title">Share and engage with the SIP Academy community</p>
        </div>
        <div style="display: flex; gap: 12px;" class="forum-header-actions">
            <button
                onclick="window.location='{{ route('alumni.forums.activity') }}'"
                class="forum-btn">
                Your Activity
            </button>
            <button
                class="forum-btn"
                onclick="openCreatePostModal()">
                <i class="fas fa-plus"></i>
                Create Post
            </button>
        </div>
    </div>
    @include('alumni.modals.create-post-modal')

    {{-- Search and Filter --}}
    <div class="search-filter-container">
        <div style="flex: 1; position: relative; max-width: 400px;">
            <i class="fas fa-search"
                style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
            <input type="text" id="searchInput" placeholder="Search posts...">
        </div>
        <!-- <button id="filterToggleBtn"
            style="color: #374151; border: 1px solid #d1d5db; padding: 11px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px; height: 42px; white-space: nowrap;"
            onmouseover="this.style.background='#eebc4a'" onmouseout="this.style.background='#fbf9fa'">
            <i class="bi bi-funnel" style="font-size: 18px;"></i>
            <span id="filterBtnText">Filter</span>
            <i class="fas fa-chevron-down"></i>
        </button>
        <button id="clearFiltersBtn" onclick="clearAllFilters()"
            style="background: white; color: #dc2626; border: 1px solid #dc2626; padding: 11px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: none; height: 42px; white-space: nowrap;"
            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='white'">
            Clear All Filters
        </button> -->
        <div style="display: flex; align-items: center; gap: 12px;">
            @include('alumni.filter')
        </div>
    </div>

    @include('alumni.forums.filters')
    

    <div id="forumPostsContainer"></div>
</div>
<script>
    // Configuration for common thread modal
    window.viewThreadRoute = "{{ route('alumni.view.thread', ':id') }}";
    window.createReplyRoute = "{{ route('alumni.create.reply') }}";
    window.currentAlumni = @json($currentUser);
    window.canReplyToComments = true; // Forums index allows replies
    window.reloadPageData = function() {
        if (typeof loadForumPosts === 'function') {
            loadForumPosts();
        }
    };

    // Current user data
    const currentUserName = "{{ $currentUser->full_name ?? 'User' }}";
    const currentUserInitials = currentUserName.substring(0, 2).toUpperCase();
    const currentUserImage = "{{ $currentUser->image_url ?? '' }}";

    let selectedFilters = {
        dateRange: [],
        batch: [],
        postType: [],
        sortBy: [],
        label: []
    };
    let selectedFiltersOrder = [];
    let labelNames = {}; // Store label ID to name mapping

    document.addEventListener("DOMContentLoaded", function() {
        initializeMultiSelect();
        loadForumPosts();

        // Check if there's a post ID in URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const postId = urlParams.get('post');
        if (postId) {
            // Wait a bit for the page to load, then open the modal
            setTimeout(() => {
                openThreadModal(postId);
                // Clean up URL without reloading
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 500);
        }
    });

    let searchTimeout;

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadForumPosts();
        }, 500); // Debounce for 500ms
    });

    // Filter toggle
    document.getElementById('filterToggleBtn').addEventListener('click', function() {
        const section = $('#filterSection');
            const isVisible = section.is(':visible');
            section.slideToggle(300, function() {
                updateSelectedFiltersDisplay();
            });

            const icon = $(this).find('i');
            const btnText = $('#filterBtnText');
            if (isVisible) {
                icon.removeClass('fa-times').addClass('bi-funnel');
                $('#filterBtnText').html('Filter <i class="fa-solid fa-chevron-down ms-2"></i>');
            } else {
                $('#filterBtnText').html('Close Filters <i class="fa-solid fa-chevron-up ms-2"></i>');
            }
    });

    // Initialize multi-select dropdowns
    function initializeMultiSelect() {
        // Fetch filter options from API
        fetch("{{ route('alumni.forums.filter-options') }}")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const filterData = {
                        dateRange: [{
                                key: "today",
                                label: "Today"
                            },
                            {
                                key: "week",
                                label: "Last 7 Days"
                            },
                            {
                                key: "month",
                                label: "Last 30 Days"
                            }
                        ],
                        sortBy: [{
                                key: "most_recent",
                                label: "Most Recent"
                            },
                            {
                                key: "most_liked",
                                label: "Most Liked"
                            },
                            {
                                key: "most_viewed",
                                label: "Most Viewed"
                            },
                            {
                                key: "most_commented",
                                label: "Most Commented"
                            }
                        ],
                        batch: data.batchYears || [],
                        postType: [{
                                key: "pinned",
                                label: "Pinned Posts"
                            },
                            {
                                key: "regular",
                                label: "Regular Posts"
                            }
                        ],
                        label: data.labels || [],
                    };

                    populateFilters(filterData);
                    
                    // Store label names for display
                    if (data.labels) {
                        data.labels.forEach(label => {
                            labelNames[label.key] = label.label;
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error loading filter options:', error);
                // Fallback to default data
                const filterData = {
                    dateRange: [{
                            key: "today",
                            label: "Today"
                        },
                        {
                            key: "week",
                            label: "Last 7 Days"
                        },
                        {
                            key: "month",
                            label: "Last 30 Days"
                        }
                    ],
                    sortBy: [{
                            key: "most_recent",
                            label: "Most Recent"
                        },
                        {
                            key: "most_liked",
                            label: "Most Liked"
                        },
                        {
                            key: "most_viewed",
                            label: "Most Viewed"
                        },
                        {
                            key: "most_commented",
                            label: "Most Commented"
                        }
                    ],
                    batch: [],
                    postType: [{
                            key: "pinned",
                            label: "Pinned Posts"
                        },
                        {
                            key: "regular",
                            label: "Regular Posts"
                        }
                    ],
                    label: [],
                };
                populateFilters(filterData);
            });
    }

    function populateFilters(filterData) {

        Object.keys(filterData).forEach(filterType => {
            const container = document.querySelector(`.multi-select-container[data-filter="${filterType}"]`);
            if (!container) return;

            const display = container.querySelector('.multi-select-display');
            const dropdown = container.querySelector('.multi-select-dropdown');

            // Populate dropdown
            filterData[filterType].forEach(item => {
                const value = item.key || item;
                const label = item.label || item;

                const option = document.createElement('div');
                option.className = 'multi-select-option';
                option.innerHTML = `
                        <input type="checkbox" 
                            id="${filterType}-${value}" 
                            value="${value}"
                            class="form-check-input border border-danger" 
                            data-type="${filterType}">
                        <label for="${filterType}-${value}" class="fw-bold">${label}</label>
                    `;
                dropdown.appendChild(option);

                const checkbox = option.querySelector('input');
                checkbox.addEventListener('change', function() {
                    if (filterType === "sortBy") {
                        handleSingleSelect(filterType, value, checkbox);
                    } else {
                        toggleFilter(filterType, value, checkbox.checked);
                    }
                });

            });


            // Toggle dropdown
            display.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('active');
                // Close other dropdowns
                document.querySelectorAll('.multi-select-dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.remove('active');
                });
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.multi-select-container')) {
                document.querySelectorAll('.multi-select-dropdown').forEach(d => {
                    d.classList.remove('active');
                });
            }
        });
    }

    function handleSingleSelect(filterType, value, checkbox) {
        // Uncheck all checkboxes inside this group
        const group = document.querySelectorAll(`input[data-type="${filterType}"]`);
        group.forEach(cb => {
            if (cb !== checkbox) cb.checked = false;
        });

        // Reset filter and set selected one
        selectedFilters[filterType] = checkbox.checked ? [value] : [];
        selectedFiltersOrder = selectedFiltersOrder.filter(i => i.type !== filterType);

        if (checkbox.checked) {
            selectedFiltersOrder.push({
                type: filterType,
                value: value
            });
        }

        updateSelectedFiltersDisplay();
        loadForumPosts();
    }

    function toggleFilter(filterType, value, isChecked) {
        if (!selectedFilters[filterType]) {
            selectedFilters[filterType] = [];
        }

        if (isChecked) {
            selectedFilters[filterType].push(value);
            selectedFiltersOrder.push({
                type: filterType,
                value
            });
        } else {
            selectedFilters[filterType] =
                selectedFilters[filterType].filter(v => v !== value);

            selectedFiltersOrder = selectedFiltersOrder.filter(
                i => !(i.type === filterType && i.value === value)
            );
        }

        updateSelectedFiltersDisplay();
        loadForumPosts();
    }



    function updateSelectedFiltersDisplay() {
        const container = document.getElementById('selectedFiltersDisplay');
        const tagsContainer = container.querySelector('.selected-tags');
        const clearBtn = document.getElementById('clearFiltersBtn');
        tagsContainer.innerHTML = '';

        let hasFilters = false;

        const filterLabels = {
            dateRange: 'Date',
            batch: 'Batch',
            sortBy: 'Sort',
            postType: 'Type',
            label: 'Label'
        };

        const valueLabels = {
            // Date Range
            'today': 'Today',
            'week': 'Last 7 Days',
            'month': 'Last 30 Days',
            // Sort By
            'most_recent': 'Most Recent',
            'most_liked': 'Most Liked',
            'most_viewed': 'Most Viewed',
            'most_commented': 'Most Commented',
            // Post Type
            'pinned': 'Pinned Posts',
            'regular': 'Regular Posts'
        };

        selectedFiltersOrder.forEach(item => {
            const filterType = item.type;
            const value = item.value;

            hasFilters = true;

            const tag = document.createElement('div');
            tag.className = 'selected-tag';

            let displayValue = valueLabels[value] || value;
            
            // Handle label names specially
            if (filterType === 'label' && labelNames[value]) {
                displayValue = labelNames[value];
            }

            tag.innerHTML = `
                    <span>${filterLabels[filterType]}: ${displayValue}</span>
                    <button onclick="removeFilter('${filterType}', '${value}')">×</button>
                `;

            tagsContainer.appendChild(tag);
        });


        container.style.display = hasFilters ? 'block' : 'none';

        // Show/hide clear all filters button
        if (clearBtn) {
            clearBtn.style.display = hasFilters ? 'flex' : 'none';
        }
    }

    function removeFilter(filterType, value) {
        if (selectedFilters[filterType]) {
            selectedFilters[filterType] =
                selectedFilters[filterType].filter(v => String(v) !== String(value));
        }
        selectedFiltersOrder = selectedFiltersOrder.filter(
            i => !(i.type === filterType && String(i.value) === String(value))
        );
        const checkbox = document.querySelector(`#${filterType}-${value}`);
        if (checkbox) checkbox.checked = false;
        updateSelectedFiltersDisplay();
        loadForumPosts();
    }

    function clearAllFilters() {
        // Reset all multi-select filters
        selectedFilters = {
            dateRange: [],
            batch: [],
            postType: [],
            sortBy: [],
            label: []
        };
        selectedFiltersOrder = [];

        // Uncheck all checkboxes
        document.querySelectorAll('.multi-select-option input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Update display and reload posts
        updateSelectedFiltersDisplay();
        loadForumPosts();
    }

    function loadForumPosts() {
        const container = document.getElementById('forumPostsContainer');

        // Build URL with all filters
        let url = "{{ route('alumni.forums.data') }}";
        const params = new URLSearchParams();

        const searchTerm = document.getElementById('searchInput').value;
        if (searchTerm) params.append('search', searchTerm);

        // Add sort by filter
        if (selectedFilters.sortBy.length > 0) {
            selectedFilters.sortBy.forEach(sort => {
                params.append('sort_by[]', sort);
            });
        }

        // Add multi-select filters
        if (selectedFilters.dateRange.length > 0) {
            selectedFilters.dateRange.forEach(range => {
                params.append('date_range[]', range);
            });
        }

        if (selectedFilters.batch.length > 0) {
            selectedFilters.batch.forEach(year => {
                params.append('batch_year[]', year);
            });
        }

        if (selectedFilters.postType.length > 0) {
            selectedFilters.postType.forEach(type => {
                params.append('post_type[]', type);
            });
        }

        // Add label filters
        if (selectedFilters.label.length > 0) {
            selectedFilters.label.forEach(labelId => {
                params.append('label[]', labelId);
            });
        }

        if (params.toString()) {
            url += '?' + params.toString();
        }

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Handle different response structures
                let posts = [];

                if (data && data.success && data.data && Array.isArray(data.data.posts)) {
                    posts = data.data.posts;
                } else if (Array.isArray(data)) {
                    posts = data;
                } else if (data && Array.isArray(data.posts)) {
                    posts = data.posts;
                } else {
                    throw new Error('Unexpected API response structure');
                }

                renderForumPosts(posts, container);
            })
            .catch(error => {
                console.error('Error loading forum posts:', error);
                showError(container, error.message);
            });
    }

    function renderForumPosts(posts, container) {
        if (!posts || posts.length === 0) {
            container.innerHTML = `
                    <div style="text-align: center; padding: 60px 20px; color: #6b7280; background: white; border-radius: 12px; border: 2px solid #e5e7eb;">
                        <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 20px; opacity: 0.5;"></i>
                        <h3 style="font-size: 20px; margin-bottom: 8px; color: #374151;">No forum posts yet</h3>
                        <p style="margin-bottom: 20px; color: #6b7280;">Be the first to create a post and start the discussion!</p>
                    </div>
                `;
            return;
        }

        let html = '';

        posts.forEach((post, index) => {
            const title = post.title || 'Untitled Post';
            const fullDescription = post.description ? post.description.replace(/<\/?[^>]+>/g, "") : 'No description available';

            const tags = post.labels ?? [];

            const author = post.alumni ?
                (post.alumni.full_name || 'Unknown') :
                (post.user ? (post.user.full_name || 'Unknown') : 'Unknown');

            const authorInitial = author.substring(0, 2).toUpperCase();
            const hasConnection = post.has_connection || false;
            const profilePicture = post.alumni?.image_url || '';

            const date = post.created_at ?
                (() => {
                    const d = new Date(post.created_at);

                    const formattedDate = d.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                    });

                    const formattedTime = d.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true,
                    });

                    return `${formattedDate} at ${formattedTime}`;
                })() :
                'Unknown date';

            html += `
                    <div class="forum-post-card">
                        <div class="post-header mb-2">
                            <div>
                                <a onclick="openThreadModal(${post.id})">
                                <h2 class="post-title">
                                    ${escapeHtml(title)}
                                </h2>
                                </a>
                            </div>

                            ${post.is_pinned_by_user ? `
                                <div class="post-header-right">
                                    <div class="pinned-tag">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            class="pin-icon">
                                            <line x1="12" x2="12" y1="17" y2="22"></line>
                                            <path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"></path>
                                        </svg>
                                        <span class="fw-bold">Pinned</span>
                                    </div>
                                    <button onclick="togglePin(${post.id}, this)" class="pin-button pinned"
                                        title="Unpin this post">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            class="pin-icon">
                                            <line x1="12" x2="12" y1="17" y2="22"></line>
                                            <path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"></path>
                                        </svg>
                                    </button>
                                    <button class="flag-button" onclick="openReportModal(${post.id}, this)" title="Report this post">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="flag-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" x2="4" y1="22" y2="15"></line></svg>
                                    </button>
                                </div>
                            ` : `
                                <div class="post-header-right">
                                <button onclick="togglePin(${post.id}, this)" class="pin-button"
                                    title="Pin this post">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        class="pin-icon">
                                        <line x1="12" x2="12" y1="17" y2="22"></line>
                                        <path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6h1a2 2 0 0 0 0-4H8a2 2 0 0 0 0 4h1v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"></path>
                                    </svg>
                                  </button>
                                  <button class="flag-button" onclick="openReportModal(${post.id}, this)" title="Report this post">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="flag-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" x2="4" y1="22" y2="15"></line></svg>
                                    </button>
                                </div>
                            `}
                        </div>

                        <p class="post-description mb-4"
                           title="${escapeHtml(fullDescription)}">
                             ${escapeHtml(fullDescription)}
                        </p>

                        ${tags.length > 0 ? `
                            <div class="post-tags mb-4">
                                ${tags.map(tag => `
                                    <span>
                                        ${escapeHtml(tag.trim())}
                                    </span>
                                `).join('')}
                            </div>
                        ` : ''}

                        <div class="post-author-section">
                            ${hasConnection && profilePicture ? `
                                <img src="${profilePicture}" alt="${escapeHtml(author)}" class="post-author-image">
                            ` : `
                                <div class="post-author-initial">
                                    ${authorInitial}
                                </div>
                            `}
                            <div class="post-author-info">
                                <p>${escapeHtml(author)}</p>
                                <span>•</span>
                                <p>${date}</p>
                            </div>
                        </div>

                        {{-- Added engagement stats and action buttons with reply toggle --}}

                        <div class="post-footer">
                            <!-- row 1: used on all screens; on mobile it has NO View Thread -->
                            <div class="post-footer-row-1">
                                <div class="post-footer-left">
                                    <div>
                                        <i class="far fa-eye"></i>
                                        <span>${post.views_count || 0}</span>
                                    </div>
                                    <div>
                                        <i class="far fa-heart"></i>
                                        <span>${post.likes_count || 0}</span>
                                    </div>
                                    <div>
                                        <i class="far fa-comment"></i>
                                        <span>${post.reply_count || 0}</span>
                                    </div>
                                </div>

                                <div class="post-footer-actions">
                                    ${post.is_liked_by_user ? `
                                        <button onclick="toggleLike(${post.id}, this)" class="like-toggle-btn liked">
                                            <i class="fas fa-heart"></i>
                                            Unlike
                                        </button>
                                    ` : `
                                        <button onclick="toggleLike(${post.id}, this)" class="like-toggle-btn">
                                            <i class="far fa-heart"></i>
                                            Like
                                        </button>
                                    `}

                                    <button data-post-id="${post.id}" class="reply-toggle-btn"
                                        onmouseover="replyHover(this)"
                                        onmouseout="replyUnhover(this)"
                                        onclick="toggleReplyForm(this, ${post.id})">
                                        <i class="fa-solid fa-arrow-turn-up fa-rotate-270 fa-sm"></i> Reply
                                    </button>

                                    <!-- Report Flag Button -->
                                    <button onclick="openReportModal(${post.id})" class="report-toggle-btn" title="Report Post">
                                        <i class="fas fa-flag"></i>
                                        Report
                                    </button>

                                    <!-- View Thread for desktop & tablet -->
                                    <button onclick="openThreadModal(${post.id})"
                                        class="view-thread-btn desktop-tablet-only">
                                        <i class="far fa-comment"></i>
                                        View Thread
                                    </button>
                                </div>
                            </div>

                            <!-- row 2: MOBILE-ONLY View Thread -->
                            <div class="post-footer-row-2">
                                <button onclick="openThreadModal(${post.id})"
                                    class="view-thread-btn mobile-only">
                                    <i class="far fa-comment"></i>
                                    View Thread
                                </button>
                            </div>
                        </div>

                        {{-- Added reply input form that shows/hides on button click --}}
                        <div id="replyForm-${post.id}" style="display: none; border-radius: 12px;">
                        <hr>
                            <div style="display: flex; gap: 16px; margin-bottom: 8px;">
                                ${currentUserImage ? `
                                    <img src="${currentUserImage}" alt="${currentUserName}" 
                                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #dc2626; flex-shrink: 0;">
                                ` : `
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">
                                        ${currentUserInitials}
                                    </div>
                                `}
                                <textarea placeholder="Write your reply..." id="replyInput-${post.id}" oninput="toggleReplyButton(${post.id})" style="flex: 1; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none; resize: vertical;"
                                onfocus="this.style.borderColor='#dc2626'" 
                                onblur="this.style.borderColor='#e5e7eb'"></textarea>

                            </div>
                            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                                <button onclick="closeReplyForm(${post.id})"
                                        class="px-3 py-1"
                                        style="background: #f7f7f7ff; color: #0d0d0eff; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                        onmouseover="this.style.background='#F7C744'"
                                        onmouseout="this.style.background='#f7f7f7ff'">
                                    Cancel
                                </button>
                                <button onclick="submitReply(${post.id})" id="replySubmit-${post.id}" disabled
                                        class="px-3 py-1 fs-6"
                                        style="background: #dc2626; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: not-allowed; opacity: 0.6; display: flex; align-items: center; gap: 8px; transition: all 0.2s;"
                                        onmouseover="this.style.background='#b91c1c';"
                                        onmouseout="this.style.background='#dc2626';">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send h-2 w-2"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                                    Post Reply
                                </button>
                            </div>
                        </div>
                    </div>
                `;
        });

        container.innerHTML = html;
    }

    function showError(container, message) {
        container.innerHTML = `
                <div style="text-align: center; padding: 60px 20px; color: #dc2626; background: white; border-radius: 12px; border: 2px solid #fecaca;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 64px; margin-bottom: 20px; opacity: 0.7;"></i>
                    <h3 style="font-size: 20px; margin-bottom: 12px; color: #dc2626;">Failed to Load Posts</h3>
                    <p style="margin-bottom: 20px; color: #6b7280;">${escapeHtml(message)}</p>
                    <button onclick="loadForumPosts()" style="background: #dc2626; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.background='#b91c1c'"
                            onmouseout="this.style.background='#dc2626'">
                        <i class="fas fa-redo"></i> Try Again
                    </button>
                </div>
            `;
    }

    function toggleReplyForm(button, postId) {
        document.querySelectorAll("[id^='replyForm-']").forEach(form => {
            if (form.id !== `replyForm-${postId}`) {
                form.style.display = "none";
            }
        });
        document.querySelectorAll("button[data-post-id]").forEach(btn => {
            btn.classList.remove("reply-active");
            btn.style.background = "transparent";
            btn.style.color = "#6b7280";
        });

        const replyForm = document.getElementById(`replyForm-${postId}`);

        // Remove active style from all buttons
        document.querySelectorAll("button.reply-active").forEach(btn => {
            btn.classList.remove("reply-active");
            btn.style.background = "transparent";
            btn.style.color = "#6b7280";
            btn.style.border = "none";
        });

        if (replyForm.style.display === "block") {
            replyForm.style.display = "none";
            button.classList.remove("reply-active");
            button.style.background = "transparent";
            button.style.color = "#6b7280";
        } else {
            replyForm.style.display = "block";
            button.classList.add("reply-active");
            button.style.background = "#ffffff";
            button.style.color = "#dc2626";
            document.getElementById(`replyInput-${postId}`).focus();
        }
    }

    function closeReplyForm(postId) {
        const replyForm = document.getElementById(`replyForm-${postId}`);
        replyForm.style.display = "none";

        const btn = document.querySelector(`button[data-post-id="${postId}"]`);
        if (btn) {
            btn.classList.remove("reply-active");
            btn.style.background = "transparent";
            btn.style.color = "#6b7280";
        }
    }

    function toggleReplyButton(postId) {
        let input = document.getElementById(`replyInput-${postId}`);
        let button = document.getElementById(`replySubmit-${postId}`);

        if (input.value.trim().length > 0) {
            button.disabled = false;
            button.style.opacity = "1";
            button.style.cursor = "pointer";
        } else {
            button.disabled = true;
            button.style.opacity = "0.6";
            button.style.cursor = "not-allowed";
        }
    }

    function submitReply(postId) {
        const replyInput = document.getElementById(`replyInput-${postId}`);
        const replyBtn = document.getElementById(`replySubmit-${postId}`);
        const replyText = replyInput.value.trim();
        if (replyBtn.disabled) return;

        if (!replyText) {
            showToast('Please enter a valid reply', 'error');
            return;
        }
        if (replyText.length > 255) {
            showToast('Reply exceeds maximum length of 255 characters', 'error');
            return;
        }

        replyBtn.disabled = true;
        replyBtn.innerText = 'Posting...';

        fetch("{{ route('alumni.create.reply') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    forum_post_id: postId,
                    parent_reply_id: null,
                    message: replyText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear input and hide form
                    replyInput.value = '';
                    document.getElementById(`replyForm-${postId}`).style.display = 'none';

                    // Update comments count
                    showToast('Reply posted successfully!', 'success');
                    loadForumPosts();
                } else {
                    replyBtn.disabled = false;
                    showToast('Failed to post reply: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                replyBtn.disabled = false;
                console.error('Error:', error);
                showToast('Error posting reply', 'error');
            });
    }


    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return String(unsafe)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Toggle pin/unpin post
    function togglePin(postId, button) {
        fetch("{{ route('alumni.pinned.post') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    post_id: postId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    loadForumPosts();
                } else {
                    showToast(data.message || 'Failed to update pin status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error updating pin status', 'error');
            });
    }

    // Toggle like/unlike post
    function toggleLike(postId, button) {
        fetch("{{ route('alumni.like.post') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    post_id: postId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    loadForumPosts(); // Reload posts to update UI
                } else {
                    showToast(data.message || 'Failed to update like status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error updating like status', 'error');
            });
    }

    function replyHover(button) {
        button.style.background = "#F7C744";
        button.style.color = "#374151";
    }


    function replyUnhover(button) {
        if (button.classList.contains("reply-active")) {
            button.style.background = "#ffdbdbff";
            button.style.color = "#dc2626";
        } else {
            button.style.background = "transparent";
            button.style.color = "#6b7280";
            button.style.border = "none";
        }
    }

    // Report Modal Functions
    let currentReportPostId = null;

    function openReportModal(postId) {
        currentReportPostId = postId;
        const modal = document.getElementById('reportPostModal');
        const textarea = document.getElementById('reportReason');
        
        // Reset form
        textarea.value = '';
        updateCharCount();
        clearReportError();
        
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        
        // Focus on textarea
        setTimeout(() => {
            textarea.focus();
        }, 100);
    }

    function closeReportModal() {
        const modal = document.getElementById('reportPostModal');
        modal.classList.remove('open');
        document.body.style.overflow = 'auto';
        currentReportPostId = null;
        
        // Reset form
        document.getElementById('reportReason').value = '';
        updateCharCount();
        clearReportError();
    }

    function updateCharCount() {
        const textarea = document.getElementById('reportReason');
        const charCount = document.querySelector('.char-count');
        const currentLength = textarea.value.length;
    }

    function clearReportError() {
        const textarea = document.getElementById('reportReason');
        const errorMsg = document.querySelector('#reportPostModal .error-message');
        
        textarea.classList.remove('input-error');
        errorMsg.textContent = '';
        errorMsg.style.display = 'none';
    }

    function showReportError(message) {
        const textarea = document.getElementById('reportReason');
        const errorMsg = document.querySelector('#reportPostModal .error-message');
        
        textarea.classList.add('input-error');
        errorMsg.textContent = message;
        errorMsg.style.display = 'block';
    }

    function submitReport() {
        const textarea = document.getElementById('reportReason');
        const reason = textarea.value.trim();
        const submitBtn = document.querySelector('.btn-submit-report');
        
        // Clear previous errors
        clearReportError();
        
        // Validation
        if (!reason) {
            showReportError('Please provide a reason for reporting this post.');
            return;
        }
        
        // Show loading state
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Submitting...';
        submitBtn.disabled = true;
        
        // Submit report
        fetch("{{ route('alumni.forums.report') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                post_id: currentReportPostId,
                report: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Report submitted successfully.', 'success');
                closeReportModal();
            } else {
                showReportError(data.message || 'Failed to submit report. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error submitting report:', error);
            showReportError('An error occurred while submitting the report. Please try again.');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }

    // Add event listeners for report modal
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('reportReason');
        if (textarea) {
            textarea.addEventListener('input', updateCharCount);
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('reportPostModal');
            if (event.target === modal) {
                closeReportModal();
            }
        });
    });
</script>
</script>
@endsection