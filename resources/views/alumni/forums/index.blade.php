@extends('alumni.layouts.index')

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
        color: #000000ff;
        flex: 1;
        font-size: 14px;
        font-weight: 400;
    }
    .placeholder {
        background-color: #ffffffff;
    }

    .multi-select-display:hover {
        border-color: #9ca3af;
        background-color: #eebc4a;
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
        accent-color: #dc2626;
    }

    .multi-select-option label {
        flex: 1;
        font-size: 14px;
        color: #374151;
        cursor: pointer;
        user-select: none;
    }

    .selected-tag {
        background: #dc2626;
        color: white;
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
        color: white;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        padding: 0;
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
    <div style="max-width: 1400px; margin: 0 auto; padding: 20px; background: white">
        {{-- Header --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
            <div>
                <h1 style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">Forum Posts</h1>
                <p style="color: #6b7280; font-size: 15px;">Share and engage with the SIP Academy community</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('alumni.forums.activity') }}"
                    style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; text-decoration: none;"
                    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                    Your Activity
                </a>
                <button
                    style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;"
                    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'"
                    onclick="openCreatePostModal()">
                    <i class="fas fa-plus"></i>
                    Create Post
                </button>
            </div>
        </div>
        @include('alumni.modals.create-post-modal')

        {{-- Search and Filter --}}
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
            <div style="flex: 1; position: relative; max-width: 400px;">
                <i class="fas fa-search"
                    style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                <input type="text" id="searchInput" placeholder="Search posts..."
                    style="width: 100%; padding: 11px 16px 11px 45px; border: 1px solid #d1d5db; border-radius: 30px; font-size: 14px; outline: none; height: 42px;"
                    onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#d1d5db'">
            </div>
            <button id="filterToggleBtn"
                style="color: #374151; border: 1px solid #d1d5db; padding: 11px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px; height: 42px; white-space: nowrap;"
                onmouseover="this.style.background='#eebc4a'" onmouseout="this.style.background='#fbf9fa'">
                <i class="bi bi-funnel" style="font-size: 18px;"></i>
                <span id="filterBtnText">Filter</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <button id="clearAllFiltersBtn" onclick="clearAllFilters()" 
                style="background: white; color: #dc2626; border: 1px solid #dc2626; padding: 11px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: none; height: 42px; white-space: nowrap;"
                onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='white'">
                Clear All Filters
            </button>
        </div>

        {{-- Filter Section --}}
        <div id="filterSection"
            style="display: none; background: #fbf9fa; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                <div>
                    <label style="font-weight: 600; font-size: 13px; color: #111827; display: block; margin-bottom: 8px;">
                        Date Range
                    </label>
                    <div class="multi-select-container" data-filter="dateRange">
                        <div class="multi-select-display">
                            <span class="placeholder">Select date range</span>
                            <i class="fas fa-chevron-down" style="color: #9ca3af; font-size: 11px;"></i>
                        </div>
                        <div class="multi-select-dropdown"></div>
                    </div>
                </div>
                <div>
                    <label style="font-weight: 600; font-size: 13px; color: #111827; display: block; margin-bottom: 8px;">
                        Sort By
                    </label>
                    <div class="multi-select-container" data-filter="sortBy">
                        <div class="multi-select-display">
                            <span class="placeholder">Select sorting</span>
                            <i class="fas fa-chevron-down" style="color: #151616ff; font-size: 11px;"></i>
                        </div>
                        <div class="multi-select-dropdown"></div>
                    </div>
                </div>
                <div>
                    <label style="font-weight: 600; font-size: 13px; color: #111827; display: block; margin-bottom: 8px;">
                        Batch Year
                    </label>
                    <div class="multi-select-container" data-filter="batch">
                        <div class="multi-select-display">
                            <span class="placeholder">Select batch years</span>
                            <i class="fas fa-chevron-down" style="color: #9ca3af; font-size: 11px;"></i>
                        </div>
                        <div class="multi-select-dropdown"></div>
                    </div>
                </div>
                <div>
                    <label style="font-weight: 600; font-size: 13px; color: #111827; display: block; margin-bottom: 8px;">
                        Post Type
                    </label>
                    <div class="multi-select-container" data-filter="postType">
                        <div class="multi-select-display">
                            <span class="placeholder">Select post type</span>
                            <i class="fas fa-chevron-down" style="color: #9ca3af; font-size: 11px;"></i>
                        </div>
                        <div class="multi-select-dropdown"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Selected Filters Display --}}
        <div id="selectedFiltersDisplay" style="display: none; margin-bottom: 20px;">
            <div class="selected-tags" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
        </div>

        <div id="forumPostsContainer"></div>
    </div>
    <script>
        let selectedFilters = {
            dateRange: [],
            batch: [],
            postType: [],
            sortBy: []
        };

        document.addEventListener("DOMContentLoaded", function () {
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
            const section = document.getElementById('filterSection');
            const isVisible = section.style.display !== 'none';
            section.style.display = isVisible ? 'none' : 'block';

            const icon = this.querySelector('.fa-chevron-up, .fa-chevron-down');
            const btnText = document.getElementById('filterBtnText');
            if (isVisible) {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
                btnText.textContent = 'Filter';
            } else {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
                btnText.textContent = 'Close Filters';
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
                            dateRange: [
                                { key: "today", label: "Today" },
                                { key: "week", label: "Last 7 Days" },
                                { key: "month", label: "Last 30 Days" }
                            ],
                            sortBy: [
                                { key: "most_recent", label: "Most Recent" },
                                { key: "most_liked", label: "Most Liked" },
                                { key: "most_viewed", label: "Most Viewed" },
                                { key: "most_commented", label: "Most Commented" }
                            ],
                            batch: data.batchYears || [],
                            postType: [
                                { key: "pinned", label: "Pinned Posts" },
                                { key: "regular", label: "Regular Posts" }
                            ],
                        };
                        
                        populateFilters(filterData);
                    }
                })
                .catch(error => {
                    console.error('Error loading filter options:', error);
                    // Fallback to default data
                    const filterData = {
                        dateRange: [
                            { key: "today", label: "Today" },
                            { key: "week", label: "Last 7 Days" },
                            { key: "month", label: "Last 30 Days" }
                        ],
                        sortBy: [
                                { key: "most_recent", label: "Most Recent" },
                                { key: "most_liked", label: "Most Liked" },
                                { key: "most_viewed", label: "Most Viewed" },
                                { key: "most_commented", label: "Most Commented" }
                            ],
                        batch: [],
                        postType: [
                                { key: "pinned", label: "Pinned Posts" },
                                { key: "regular", label: "Regular Posts" }
                            ],
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
                            data-type="${filterType}">
                        <label for="${filterType}-${value}">${label}</label>
                    `;
                    dropdown.appendChild(option);

                    const checkbox = option.querySelector('input');
                    checkbox.addEventListener('change', function () {
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

                    updateSelectedFiltersDisplay();
                    loadForumPosts();
                }


                function toggleFilter(filterType, value, isChecked) {

            // Ensure key exists before use
            if (!selectedFilters[filterType]) {
                selectedFilters[filterType] = [];
            }

            if (isChecked) {
                if (!selectedFilters[filterType].includes(value)) {
                    selectedFilters[filterType].push(value);
                }
            } else {
                selectedFilters[filterType] = selectedFilters[filterType].filter(v => v !== value);
            }

            updateSelectedFiltersDisplay();
            loadForumPosts();
            }


        function updateSelectedFiltersDisplay() {
            const container = document.getElementById('selectedFiltersDisplay');
            const tagsContainer = container.querySelector('.selected-tags');
            const clearBtn = document.getElementById('clearAllFiltersBtn');
            tagsContainer.innerHTML = '';

            let hasFilters = false;

            const filterLabels = {
                dateRange: 'Date',
                batch: 'Batch',
                sortBy: 'Sort',
                postType: 'Type'
            };

            Object.keys(selectedFilters).forEach(filterType => {
                selectedFilters[filterType].forEach(value => {
                    hasFilters = true;
                    const tag = document.createElement('div');
                    tag.className = 'selected-tag';
                    tag.innerHTML = `
                        <span>${filterLabels[filterType]}: ${value}</span>
                        <button onclick="removeFilter('${filterType}', '${value}')">×</button>
                    `;
                    tagsContainer.appendChild(tag);
                });
            });

            container.style.display = hasFilters ? 'block' : 'none';
            
            // Show/hide clear all filters button
            if (clearBtn) {
                clearBtn.style.display = hasFilters ? 'flex' : 'none';
            }
        }

        function removeFilter(filterType, value) {
            selectedFilters[filterType] = selectedFilters[filterType].filter(v => v !== value);
            
            // Uncheck the checkbox
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
                sortBy: []
            };

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
                const description = post.description ?
                    post.description.replace(/<\/?[^>]+>/g, "").substring(0, 200) +
                    (post.description.length > 200 ? '...' : '') :
                    'No description available';

                const tags = post.labels ?
                    post.labels.split(',').filter(tag => tag.trim() !== '') : [];

                const author = post.alumni ?
                    (post.alumni.full_name || 'Unknown') :
                    (post.user ? (post.user.full_name || 'Unknown') : 'Unknown');

                const authorInitial = author.substring(0, 2).toUpperCase();
                const date = post.created_at ?
                    new Date(post.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) :
                    'Unknown date';

                html += `
                    <div style="background: white; border: 1px solid ${post.is_pinned_by_user ? '#e5e7eb' : '#e5e7eb'}; border-radius: 12px; padding: 24px; margin-bottom: 20px; transition: all 0.3s ease; position: relative;"
                         onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'"
                         onmouseout="this.style.boxShadow='none'">

                        ${post.is_pinned_by_user ? `
                            <div style="position: absolute; top: 16px; right: 16px; display: flex; align-items: center; gap: 8px;">
                                <div style="background: #F7C744; color: #000; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                                    <i class="fas fa-thumbtack"></i> Pinned
                                </div>
                                <button onclick="togglePin(${post.id}, this)" 
                                    style="background: transparent; border: none; color: #dc2626; cursor: pointer; font-size: 18px; padding: 4px;"
                                    title="Unpin this post">
                                    <i class="fas fa-thumbtack"></i>
                                </button>
                            </div>
                        ` : `
                            <button onclick="togglePin(${post.id}, this)" 
                                style="position: absolute; top: 16px; right: 16px; background: transparent; border: none; color: #9ca3af; cursor: pointer; font-size: 18px; padding: 4px;"
                                onmouseover="this.style.color='#6b7280'"
                                onmouseout="this.style.color='#9ca3af'"
                                title="Pin this post">
                                <i class="fas fa-thumbtack"></i>
                            </button>
                        `}

                        <div style="margin-bottom: 16px; padding-right: 80px;">
                            <a onclick="openThreadModal(${post.id})">
                              <h2 
                                style="font-size: 20px; font-weight: 700; color: #dc2626; margin: 0; line-height: 1.4;"
                                onmouseover="this.style.textDecoration='underline';"
                                onmouseout="this.style.textDecoration='none'; this.style.color='#dc2626';"
                              >
                                ${escapeHtml(title)}
                              </h2>
                            </a>
                        </div>


                        <p style="color: #6b7280; font-size: 15px; line-height: 1.6; margin-bottom: 20px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                             ${escapeHtml(description)}
                        </p>

                        ${tags.length > 0 ? `
                            <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
                                ${tags.map(tag => `
                                    <span style="background: #F7C744; color: #000000ff; padding: 4px 12px; border-radius: 14px; font-size: 10px; font-weight: 600;">
                                        ${escapeHtml(tag.trim())}
                                    </span>
                                `).join('')}
                            </div>
                        ` : ''}

                        <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 16px; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700;">
                                ${authorInitial}
                            </div>
                            <div>
                                <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 2px 0;">${escapeHtml(author)}</p>
                                <p style="font-size: 12px; color: #6b7280; margin: 0;">${date}</p>
                            </div>
                        </div>

                        {{-- Added engagement stats and action buttons with reply toggle --}}
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0px; margin-bottom: 16px;border-top: 1px solid #e5e7eb;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div style="display: flex; align-items: center; gap: 8px; color: #6b7280; font-size: 14px;">
                                    <i class="far fa-eye"></i>
                                    <span>${post.views_count || 0}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; color: #6b7280; font-size: 14px;">
                                    <i class="far fa-heart"></i>
                                    <span>${post.likes_count || 0}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; color: #6b7280; font-size: 14px;">
                                    <i class="far fa-comment"></i>
                                    <span>${post.reply_count || 0}</span>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 16px;">
                                ${post.is_liked_by_user ? `
                                    <button onclick="toggleLike(${post.id}, this)" 
                                        style="border: none; color: #dc2626; background: #ffffffff; cursor: pointer; font-size: 14px; padding: 8px 16px; border-radius: 6px; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: all 0.2s;"
                                        onmouseover="this.style.background='#F7C744';this.style.color='#000000ff';this.style.opacity='0.9'"
                                        onmouseout="this.style.background='#ffffffff';this.style.color='#000000ff';this.style.opacity='1'">
                                        <i class="fas fa-heart" style="color: #dc2626;"></i>
                                        Unlike
                                    </button>
                                ` : `
                                    <button onclick="toggleLike(${post.id}, this)" 
                                        style="border: none; color: #756b80ff; background: #ffffffff; cursor: pointer; font-size: 14px; padding: 8px 12px; border-radius: 6px; display: flex; align-items: center; gap: 6px; transition: all 0.2s;"
                                        onmouseover="this.style.background='#F7C744';this.style.color='#000000ff'"
                                        onmouseout="this.style.background='transparent'; this.style.color='#6b7280'">
                                        <i class="far fa-heart"></i>
                                        Like
                                    </button>
                                `}
                                <button 
                                         style="background: transparent; border: none; color: #6b7280; cursor: pointer; font-size: 14px; padding: 8px 12px; border-radius: 6px; display: flex; align-items: center; gap: 6px; transition: 0.2s;"
                                            onmouseover="replyHover(this)"
                                            onmouseout="replyUnhover(this)"
                                            onclick="toggleReplyForm(this, ${post.id})">
                                            <i class="fas fa-reply"></i> Reply
                                            </button>

                                <button onclick="openThreadModal(${post.id})"
                                    style="background: #dc2626; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px;"
                                        onmouseover="this.style.background='#b91c1c'; this.style.transform='translateY(-1px)'"
                                        onmouseout="this.style.background='#dc2626'; this.style.transform='translateY(0)'">
                                    <i class="fas fa-eye"></i>
                                    View Thread
                                </button>
                            </div>
                        </div>

                        {{-- Added reply input form that shows/hides on button click --}}
                        <div id="replyForm-${post.id}" style="display: none; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px;">
                            <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">
                                    DU
                                </div>
                                <input type="text" placeholder="Write your reply..."
                                    id="replyInput-${post.id}"
                                    style="flex: 1; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none;"
                                    onfocus="this.style.borderColor='#dc2626'" 
                                    onblur="this.style.borderColor='#e5e7eb'">
                            </div>
                            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                                <button onclick="toggleReplyForm(document.querySelector('[onclick*=toggleReplyForm][data-post-id=\\'${post.id}\\']'), ${post.id})"
                                        style="background: white; color: #374151; border: 2px solid #e5e7eb; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                        onmouseover="this.style.background='#f3f4f6'"
                                        onmouseout="this.style.background='white'">
                                    Cancel
                                </button>
                                <button onclick="submitReply(${post.id})"
                                        style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;"
                                        onmouseover="this.style.background='#b91c1c'; this.style.transform='translateY(-1px)'"
                                        onmouseout="this.style.background='#dc2626'; this.style.transform='translateY(0)'">
                                    <i class="fas fa-paper-plane"></i>
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
            const replyForm = document.getElementById(`replyForm-${postId}`);

            // Remove active style from all buttons
            document.querySelectorAll("button.reply-active").forEach(btn => {
                btn.classList.remove("reply-active");
                btn.style.background = "transparent";
                btn.style.color = "#6b7280";
                btn.style.border = "none";
            });

            if (replyForm.style.display === "none" || replyForm.style.display === "") {
                replyForm.style.display = "block";

                // Activate current button
                button.classList.add("reply-active");
                button.style.background = "#ffffff";
                button.style.color = "#dc2626";

                document.getElementById(`replyInput-${postId}`).focus();
            } else {
                replyForm.style.display = "none";

                // Deactivate
                button.classList.remove("reply-active");
                button.style.background = "transparent";
                button.style.color = "#6b7280";
                button.style.border = "none";
            }
        }




        function submitReply(postId) {
            const replyInput = document.getElementById(`replyInput-${postId}`);
            const replyText = replyInput.value.trim();

            if (!replyText) {
                showToast('Please write a reply before posting', 'error');
                return;
            }

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
                        showToast('Failed to post reply: ' + (data.message || 'Unknown error'), 'error');
                    }
                })
                .catch(error => {
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
                body: JSON.stringify({ post_id: postId })
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
                body: JSON.stringify({ post_id: postId })
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
    </script>

    <!-- view thread modal script -->
    <script>
        let activePostId = null;
        let replyingToReplyId = null;
        let replyingToUserName = null;

        window.openThreadModal = function (postId) {
            window.currentThreadPostId = postId;

            const url = "{{ route('alumni.view.thread', ['id' => 0]) }}"
                .replace('/0', '/' + postId);

            // Change URL without reload
            history.pushState({}, "", "{{ route('alumni.view.thread', ['id' => 0]) }}".replace('/0', '/' + postId));

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        showToast("Failed to load thread.", 'error');
                        return;
                    }

                    populateThreadModal(data.data);
                    loadForumPosts();

                    document.getElementById('threadModal').style.display = 'block';
                    document.body.style.overflow = 'hidden';
                })
                .catch(err => {
                    console.error(err);
                    showToast("Error loading thread.", 'error');
                });
        };


        function populateThreadModal(threadData) {
            const post = threadData.post;
            activePostId = window.currentThreadPostId;
            const replies = threadData.replies || [];
            window.currentThreadReplies = replies;

            // Reset reply state
            resetReplyState();

            document.getElementById('threadTitle').textContent = post.title || 'Untitled Post';
            document.getElementById('threadDescription').textContent = post.description ?
                post.description.replace(/<\/?[^>]+>/g, "").substring(0, 200) +
                (post.description.length > 200 ? '...' : '') :
                'No description available';

            const author = post.alumni?.full_name || 'Unknown';
            document.getElementById('threadAuthor').textContent = author;
            document.getElementById('threadAuthorAvatar').textContent = author.substring(0, 2).toUpperCase();

            const date = new Date(post.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('threadDate').textContent = date;

            const tagsContainer = document.getElementById('threadTags');
            tagsContainer.innerHTML = '';
            if (post.labels) {
                post.labels.split(',').forEach(tag => {
                    const tagElement = document.createElement('span');
                    tagElement.style.cssText = 'background: #F7C744; color: #000000; padding: 4px 12px; border-radius: 14px; font-size: 10px; font-weight: 600;';
                    tagElement.textContent = tag.trim();
                    tagsContainer.appendChild(tagElement);
                });
            }

            const commentsContainer = document.getElementById('threadComments');
            const noCommentsMessage = document.getElementById('noCommentsMessage');
            const commentsHeading = document.getElementById('commentsHeading');

            commentsContainer.innerHTML = '';

            if (replies && replies.length > 0) {
                commentsHeading.textContent = `Comments (${replies.length})`;
                noCommentsMessage.style.display = 'none';

                replies.forEach(reply => {
                    const commentElement = createCommentElement(reply);
                    commentsContainer.appendChild(commentElement);
                });
            } else {
                commentsHeading.textContent = 'Comments (0)';
                noCommentsMessage.style.display = 'block';
            }
        }

        function createCommentElement(reply) {
            const replyAuthor = reply.alumni?.full_name || 'Unknown';
            const replyInitial = replyAuthor.substring(0, 2).toUpperCase();
            const replyDate = new Date(reply.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const commentElement = document.createElement('div');
            commentElement.setAttribute('data-reply-id', reply.id);
            commentElement.style.cssText = '';

            commentElement.innerHTML = `
                <div>
                    <div style="display: flex; gap: 12px;background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 12px; transition: all 0.2s;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">
                            ${replyInitial}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <div>
                                    <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 4px 0;">${escapeHtml(replyAuthor)}</p>
                                    <p style="font-size: 12px; color: #6b7280; margin: 0;">${replyDate}</p>
                                </div>
                                <button 
                                    onclick="setReplyTo(${reply.id}, '${escapeHtml(replyAuthor)}')"
                                    style="background: transparent; border: none; color: #dc2626; cursor: pointer; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 600; transition: all 0.2s;"
                                    onmouseover="this.style.background='#fef2f2'"
                                    onmouseout="this.style.background='transparent'">
                                    <i class="fas fa-reply"></i> Reply
                                </button>
                            </div>
                            <p style="font-size: 14px; color: #374151; line-height: 1.6; margin: 0 0 12px 0; word-wrap: break-word;">
                                ${escapeHtml(reply.message || 'No message')}
                            </p>
                            ${reply.child_replies && reply.child_replies.length > 0 ? `
                                <button 
                                    id="toggle-btn-${reply.id}" 
                                    onclick="toggleReplies(${reply.id})"
                                    style="background: transparent; border: none; color: #2563eb; cursor: pointer; font-size: 13px; padding: 6px 0; font-weight: 600; transition: color 0.2s;"
                                    onmouseover="this.style.color='#1d4ed8'"
                                    onmouseout="this.style.color='#2563eb'">
                                    View Thread ${reply.child_replies.length} <i class="fas fa-chevron-down"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                    <div id="reply-box-${reply.id}" style="display: none; margin-top: 16px; margin-left: 52px; border-left: 1px solid #fbbf24; padding-left: 16px;">
                        <!-- Nested replies will be inserted here -->
                    </div>
                </div>
                `;

            return commentElement;
        }

        function setReplyTo(replyId, userName) {
            // Remove previous highlights
            document.querySelectorAll('[data-reply-id]').forEach(item => {
                item.style.background = 'white';
                item.style.borderColor = '#e5e7eb';
            });

            // Highlight selected comment
            const commentElement = document.querySelector(`[data-reply-id="${replyId}"]`);
            if (commentElement) {
                commentElement.style.background = '#fef2f2';
                commentElement.style.borderColor = '#fca5a5';
                commentElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }

            replyingToReplyId = replyId;
            replyingToUserName = userName;
            activePostId = window.currentThreadPostId;

            // Show replying indicator
            document.getElementById('replyingToIndicator').style.display = 'flex';
            document.getElementById('replyingToName').textContent = userName;

            // Focus input
            const input = document.getElementById('replyInput');
            input.focus();
            input.placeholder = `Replying to ${userName}...`;
        }

        function cancelReply() {
            resetReplyState();
        }

        function resetReplyState() {
            // Remove all highlights
            document.querySelectorAll('[data-reply-id]').forEach(item => {
                item.style.background = 'white';
                item.style.borderColor = '#e5e7eb';
            });

            replyingToReplyId = null;
            replyingToUserName = null;

            document.getElementById('replyingToIndicator').style.display = 'none';
            document.getElementById('replyingToName').textContent = '';

            const input = document.getElementById('replyInput');
            input.placeholder = 'Write your reply...';
            input.value = '';
        }

        function submitThreadReply() {
            const replyInput = document.getElementById('replyInput');
            const replyText = replyInput.value.trim();

            if (!replyText) {
                showToast('Please write a reply before posting', 'error');
                return;
            }

            const postId = window.currentThreadPostId;

            const requestData = {
                forum_post_id: postId,
                parent_reply_id: replyingToReplyId,
                message: replyText
            };

            fetch("{{ route('alumni.create.reply') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(requestData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Reply posted successfully!', 'success');
                        resetReplyState();
                        openThreadModal(postId);
                        if (typeof loadForumPosts === 'function') {
                            loadForumPosts();
                        }
                    } else {
                        showToast('Failed to post reply: ' + (data.message || 'Unknown error'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error posting reply', 'error');
                });
        }

        function closeThreadModal() {
            document.getElementById('threadModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            history.pushState({}, "", "{{ route('alumni.forums') }}");
            resetReplyState();
        }

        document.addEventListener('click', function (event) {
            const modal = document.getElementById('threadModal');
            if (event.target === modal) {
                closeThreadModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeThreadModal();
            }
        });

        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function toggleReplies(parentId) {
            const box = document.getElementById(`reply-box-${parentId}`);
            const btn = document.getElementById(`toggle-btn-${parentId}`);

            if (!box || !btn) return;

            if (box.style.display === "none" || box.style.display === "") {
                box.style.display = "block";
                btn.innerHTML = `Hide Thread <i class="fas fa-chevron-up"></i>`;

                // Load child replies if not already loaded
                if (box.children.length === 0) {
                    const parentReply = window.currentThreadReplies.find(r => r.id === parentId);
                    if (parentReply && parentReply.child_replies) {
                        box.innerHTML = '';
                        parentReply.child_replies.forEach(child => {
                            box.appendChild(renderChildComment(child, 0));
                        });
                    }
                }
            } else {
                box.style.display = "none";
                const count = box.children.length ?? 0;
                btn.innerHTML = `View Thread (${count}) <i class="fas fa-chevron-down"></i>`;
            }
        }

        function renderChildComment(reply, level = 0) {
            const div = document.createElement('div');

            const marginLeft = level > 0 ? `${level * 2}px` : '0px';
            const bgColor = level % 2 === 0 ? '#fffbeb' : '#fef3c7';

            div.style.cssText = `
                    margin-left: ${marginLeft};
                    margin-bottom: 12px;
                    padding: 16px;
                    background: ${bgColor};
                    border-radius: 8px;
                    transition: all 0.2s;
                `;

            const author = reply.alumni?.full_name || "Unknown";
            const initials = author.substring(0, 2).toUpperCase();
            const date = new Date(reply.created_at).toLocaleDateString('en-US', {
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            });

            div.innerHTML = `
                    <div style="display: flex; gap: 12px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0;">
                            ${initials}
                        </div>

                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <div>
                                    <p style="margin: 0; font-weight: 600; font-size: 14px; color: #111827;">${escapeHtml(author)}</p>
                                    <p style="margin: 0; color: #6b7280; font-size: 12px;">${date}</p>
                                </div>

                                <button 
                                    onclick="setReplyTo(${reply.id}, '${escapeHtml(author)}')"
                                    style="background: transparent; border: none; color: #dc2626; cursor: pointer; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 600; transition: all 0.2s;"
                                    onmouseover="this.style.background='#fef2f2'"
                                    onmouseout="this.style.background='transparent'">
                                    <i class="fas fa-reply"></i> Reply
                                </button>
                            </div>

                            <p style="margin: 0; font-size: 14px; color: #374151; line-height: 1.6; word-wrap: break-word;">
                                ${escapeHtml(reply.message || 'No message')}
                            </p>
                        </div>
                    </div>
                `;

            // Recursively render nested children
            if (reply.child_replies && reply.child_replies.length > 0) {
                const nestedContainer = document.createElement('div');
                nestedContainer.style.cssText = 'margin-top: 12px; border-left: 1px solid #fbbf24; padding-left: 12px;';

                reply.child_replies.forEach(child => {
                    nestedContainer.appendChild(renderChildComment(child, level + 1));
                });

                div.appendChild(nestedContainer);
            }

            return div;
        }

        // Add required animations
        const style = document.createElement('style');
        style.textContent = `
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }

                @keyframes slideOutRight {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
            `;
        document.head.appendChild(style);
    </script>
@endsection