@extends('alumni.layouts.index')

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
        {{-- Back to Forum Link --}}
        <div style="margin-bottom: 20px;">
            <a href="{{ route('alumni.forums') }}"
                style="color: #dc2626; text-decoration: none; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;"
                onmouseover="this.style.color='#b91c1c'" onmouseout="this.style.color='#dc2626'">
                <i class="fas fa-arrow-left"></i>
                Back to Forum
            </a>
        </div>

        {{-- Header --}}
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">Your Activity</h1>
            <p style="color: #6b7280; font-size: 15px;">Track your posts and engagement</p>
        </div>

        {{-- Dynamic Stats Cards --}}
        <div id="statsCardsContainer" style="margin-bottom: 30px;">
            {{-- Cards will be dynamically loaded based on active tab --}}
        </div>

        {{-- Tabs and Search in One Row --}}
        <div style="background: white; border-radius: 12px; border: 2px solid #e5e7eb; overflow: hidden;">
            <div
                style="display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 20px; border-bottom: 2px solid #e5e7eb;">
                <div
                    style="display: flex; gap: 0; background: #f3f4f6; border-radius: 8px; overflow: hidden; flex: 0 0 auto;">
                    <button id="activePostsTab" onclick="switchTab('activePosts')"
                        style="padding: 12px 40px; background: #dc2626; color: white; border: none; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                        Active Posts
                    </button>
                    <button id="postStatusTab" onclick="switchTab('postStatus')"
                        style="padding: 12px 40px; background: transparent; color: #6b7280; border: none; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                        Post Status
                    </button>
                    <button id="archiveTab" onclick="switchTab('archive')"
                        style="padding: 12px 40px; background: transparent; color: #6b7280; border: none; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                        Archive
                    </button>
                </div>

                {{-- Search Bar --}}
                <div style="flex: 0 0 350px; max-width: 350px;">
                    <div style="position: relative;">
                        <i class="fas fa-search"
                            style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="text" id="searchInput" placeholder="Search posts..."
                            style="width: 100%; padding: 10px 16px 10px 45px; border: 1px solid #e5e7eb; border-radius: 20px; font-size: 14px; outline: none; background: #f9fafb;"
                            onfocus="this.style.borderColor='#dc2626'; this.style.background='white'"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'"
                            oninput="filterPosts()">
                    </div>
                </div>
            </div>

            {{-- Posts Container --}}
            <div id="postsContainer" style="padding: 20px;">
                {{-- Posts will be loaded here --}}
            </div>
        </div>
    </div>

    <style>
        .stat-card:hover {
            transform: scale(1.05);
            opacity: 0.9;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
    </style>

    <script>
        // Global variables to store data
        let allUserPosts = [];
        let currentFilter = 'activePosts';
        let currentStats = {};

        document.addEventListener("DOMContentLoaded", function () {
            loadActivityData();
        });

        function renderStatsCards(tabName, stats) {
            const container = document.getElementById('statsCardsContainer');
            let html = '';

            if (tabName === 'activePosts') {
                // 4 cards for Active Posts tab
                html = `
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                                    <div class="stat-card" style="background: white; border: 2px solid #ef4444; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Active Posts</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-file-alt" style="color: #ef4444; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #ef4444; margin: 0;">${stats.activePosts || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #f59e0b; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Total Likes</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-heart" style="color: #f59e0b; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #f59e0b; margin: 0;">${stats.totalLikes || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #3b82f6; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Total Views</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-eye" style="color: #3b82f6; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #3b82f6; margin: 0;">${stats.totalViews || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #a855f7; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Total Replies</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-reply" style="color: #a855f7; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #a855f7; margin: 0;">${stats.totalComments || 0}</h2>
                                    </div>
                                </div>
                            `;
            } else if (tabName === 'postStatus') {
                // 3 cards for Post Status tab
                html = `
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                                    <div class="stat-card" style="background: white; border: 2px solid #ef4444; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Total</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-file-alt" style="color: #ef4444; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #ef4444; margin: 0;">${stats.totalStatusPosts || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #f59e0b; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Pending</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-clock" style="color: #f59e0b; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #f59e0b; margin: 0;">${stats.pendingPosts || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #ef4444; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Rejected</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-times-circle" style="color: #ef4444; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #ef4444; margin: 0;">${stats.rejectedPosts || 0}</h2>
                                    </div>
                                </div>
                            `;
            } else if (tabName === 'archive') {
                // 4 cards for Archive tab
                html = `
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                                    <div style="background: white; border: 2px solid #d1d5db; border-radius: 12px; padding: 20px;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Total Posts</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-file-alt" style="color: #9ca3af; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #6b7280; margin: 0;">${stats.archivedPosts || 0}</h2>
                                    </div>
                                    <div style="background: white; border: 2px solid #d1d5db; border-radius: 12px; padding: 20px;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Active</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-check-circle" style="color: #9ca3af; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #6b7280; margin: 0;">${stats.archivedPosts || 0}</h2>
                                    </div>
                                    <div style="background: white; border: 2px solid #d1d5db; border-radius: 12px; padding: 20px;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Pending</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-clock" style="color: #9ca3af; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #6b7280; margin: 0;">0</h2>
                                    </div>
                                    <div style="background: white; border: 2px solid #d1d5db; border-radius: 12px; padding: 20px;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Rejected</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-times-circle" style="color: #9ca3af; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #6b7280; margin: 0;">0</h2>
                                    </div>
                                </div>
                            `;
            }

            container.innerHTML = html;
        }

        function switchTab(tabName) {
            // Update tab styles
            const tabs = ['activePostsTab', 'postStatusTab', 'archiveTab'];
            tabs.forEach(tab => {
                const element = document.getElementById(tab);
                if (tab === tabName + 'Tab') {
                    element.style.background = '#dc2626';
                    element.style.color = 'white';
                } else {
                    element.style.background = 'transparent';
                    element.style.color = '#6b7280';
                }
            });

            // Update current filter
            currentFilter = tabName;
            
            // Clear search input
            document.getElementById('searchInput').value = '';
            
            // Load content based on tab
            loadActivityData(tabName);
        }

        function loadActivityData(filter = 'activePosts') {
            // Show loading state
            const container = document.getElementById('postsContainer');
            container.innerHTML = `
                <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 48px; margin-bottom: 20px;"></i>
                    <p style="font-size: 16px;">Loading your activity...</p>
                </div>
            `;

            fetch("{{ route('alumni.forums.activity.data') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        // Store data globally
                        allUserPosts = data.data.posts;
                        currentStats = data.data.stats;
                        currentFilter = filter;

                        // Update stats for Post Status tab
                        currentStats.totalStatusPosts = currentStats.pendingPosts + currentStats.rejectedPosts;

                        // Render stat cards based on current tab
                        renderStatsCards(filter, currentStats);

                        // Render posts based on filter
                        let filteredPosts = allUserPosts;
                        if (filter === 'activePosts') {
                            filteredPosts = allUserPosts.filter(post => post.status === 'approved');
                        } else if (filter === 'postStatus') {
                            filteredPosts = allUserPosts.filter(post => post.status === 'pending' || post.status === 'rejected');
                        } else if (filter === 'archive') {
                            filteredPosts = allUserPosts.filter(post => post.status === 'post_deleted' || post.status === 'removed_by_admin');
                        }

                        renderPosts(filteredPosts, filter);
                    } else {
                        container.innerHTML = `
                            <div style="text-align: center; padding: 60px 20px; color: #dc2626;">
                                <i class="fas fa-exclamation-circle" style="font-size: 48px; margin-bottom: 20px;"></i>
                                <p style="font-size: 16px;">Failed to load activity data</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading activity data:', error);
                    container.innerHTML = `
                        <div style="text-align: center; padding: 60px 20px; color: #dc2626;">
                            <i class="fas fa-exclamation-circle" style="font-size: 48px; margin-bottom: 20px;"></i>
                            <p style="font-size: 16px;">Error loading activity data</p>
                            <p style="font-size: 14px; color: #6b7280; margin-top: 8px;">Please try again later</p>
                        </div>
                    `;
                });
        }

        function renderPosts(posts, viewType = 'activePosts') {
            const container = document.getElementById('postsContainer');

            // Use simple layout for Post Status tab
            if (viewType === 'postStatus') {
                renderSimplePosts(posts);
                return;
            }

            if (!posts || posts.length === 0) {
                container.innerHTML = `
                                                                        <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
                                                                            <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 20px; opacity: 0.5;"></i>
                                                                            <h3 style="font-size: 20px; margin-bottom: 8px; color: #374151;">No posts found</h3>
                                                                            <p style="color: #6b7280;">Your posts will appear here</p>
                                                                        </div>
                                                                    `;
                return;
            }

            let html = '';
            posts.forEach(post => {
                const title = post.title || 'Untitled Post';
                const description = post.description ?
                    post.description.replace(/<\/?[^>]+>/g, "").substring(0, 150) +
                    (post.description.length > 150 ? '...' : '') :
                    'No description available';

                const date = post.created_at ?
                    new Date(post.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) + ' at ' + new Date(post.created_at).toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) :
                    'Unknown date';

                // Status display logic
                let statusColor, statusTextColor, statusText;
                
                if (post.status === 'approved') {
                    statusColor = '#dcfce7';
                    statusTextColor = '#16a34a';
                    statusText = 'Active';
                } else if (post.status === 'pending') {
                    statusColor = '#fef3c7';
                    statusTextColor = '#d97706';
                    statusText = 'Pending';
                } else if (post.status === 'post_deleted') {
                    statusColor = '#d3d0d0ff';
                    statusTextColor = '#1f1b1bff';
                    statusText = 'Post Deleted';
                } else if (post.status === 'removed_by_admin') {
                    statusColor = '#fee2e2';
                    statusTextColor = '#dc2626';
                    statusText = 'Removed by Admin';
                } else {
                    statusColor = '#f3f4f6';
                    statusTextColor = '#6b7280';
                    statusText = 'Archived';
                }

                // Parse tags/labels
                const tags = post.labels ? post.labels.split(',').filter(tag => tag.trim() !== '') : [];

                html += `
                                                                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 16px; position: relative;">
                                                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                                                                <h3 style="font-size: 18px; font-weight: 700; color: #dc2626; margin: 0; flex: 1;">${escapeHtml(title)}</h3>
                                                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                                                    <span style="background: ${statusColor}; color: ${statusTextColor}; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                                                                        ${statusText}
                                                                                    </span>
                                                                                    <button style="background: transparent; border: none; color: #dc2626; width: 32px; height: 32px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                                                        onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'"
                                                                                        title="Delete post">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>

                                                                            <p style="color: #9ca3af; font-size: 13px; margin-bottom: 12px;">${date}</p>

                                                                            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin-bottom: 16px;">
                                                                                ${escapeHtml(description)}
                                                                            </p>

                                                                            ${tags.length > 0 ? `
                                                                                <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
                                                                                    ${tags.map(tag => `
                                                                                        <span style="background: #fbbf24; color: #000; padding: 4px 12px; border-radius: 14px; font-size: 12px; font-weight: 600;">
                                                                                            ${escapeHtml(tag.trim())}
                                                                                        </span>
                                                                                    `).join('')}
                                                                                </div>
                                                                            ` : ''}

                                                                            <div style="display: flex; align-items: center; gap: 20px; color: #6b7280; font-size: 14px;">
                                                                                <span style="display: flex; align-items: center; gap: 6px;">
                                                                                    <i class="fas fa-eye"></i> ${post.views_count || 0}
                                                                                </span>
                                                                                <span style="display: flex; align-items: center; gap: 6px;">
                                                                                    <i class="fas fa-heart"></i> ${post.likes_count || 0}
                                                                                </span>
                                                                                <span style="display: flex; align-items: center; gap: 6px;">
                                                                                    <i class="fas fa-comment"></i> ${post.reply_count || 0} replies
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    `;
            });

            container.innerHTML = html;
        }

        function renderSimplePosts(posts) {
            const container = document.getElementById('postsContainer');

            if (!posts || posts.length === 0) {
                container.innerHTML = `
                                                <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
                                                    <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 20px; opacity: 0.5;"></i>
                                                    <h3 style="font-size: 20px; margin-bottom: 8px; color: #374151;">No posts found</h3>
                                                    <p style="color: #6b7280;">Your posts will appear here</p>
                                                </div>
                                            `;
                return;
            }

            let html = '';
            posts.forEach(post => {
                const title = post.title || 'Untitled Post';
                const date = post.created_at ?
                    'Posted: ' + new Date(post.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) + ' at ' + new Date(post.created_at).toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) :
                    'Unknown date';

                const statusColor = post.status === 'pending' ? '#fef3c7' : '#fee2e2';
                const statusTextColor = post.status === 'pending' ? '#d97706' : '#dc2626';
                const statusText = post.status === 'pending' ? 'Pending' : 'Rejected';

                html += `
                                                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                                                    <div style="flex: 1;">
                                                        <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 8px 0;">${escapeHtml(title)}</h3>
                                                        <p style="color: #9ca3af; font-size: 13px; margin: 0;">${date}</p>
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <span style="background: ${statusColor}; color: ${statusTextColor}; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                                                            ${statusText}
                                                        </span>
                                                        <div style="display: flex; gap: 8px;">
                                                            ${post.status === 'pending' ? `
                                                                <button style="background: transparent; border: 2px solid #3b82f6; color: #3b82f6; width: 36px; height: 36px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                                    onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='transparent'"
                                                                    title="View post">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </button>
                                                            ` : ''}
                                                            ${post.status === 'rejected' ? `
                                                                <button style="background: transparent; border: 2px solid #3b82f6; color: #3b82f6; width: 36px; height: 36px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                                    onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='transparent'"
                                                                    title="View post">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </button>
                                                                <button style="background: transparent; border: 2px solid #10b981; color: #10b981; width: 36px; height: 36px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                                    onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='transparent'"
                                                                    title="Resubmit">
                                                                    <i class="fas fa-redo"></i>
                                                                </button>
                                                            ` : ''}
                                                            ${post.status === 'pending' ? `
                                                                <button style="background: transparent; border: 2px solid #f59e0b; color: #f59e0b; width: 36px; height: 36px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                                    onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'"
                                                                    title="Edit post">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            ` : ''}
                                                            <button style="background: transparent; border: 2px solid #dc2626; color: #dc2626; width: 36px; height: 36px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                                onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'"
                                                                title="Delete post">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
            });

            container.innerHTML = html;
        }

        function filterPosts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
            
            // Filter posts based on current tab
            let filteredPosts = allUserPosts;
            
            // Apply tab filter first
            if (currentFilter === 'activePosts') {
                filteredPosts = allUserPosts.filter(post => post.status === 'approved');
            } else if (currentFilter === 'postStatus') {
                filteredPosts = allUserPosts.filter(post => post.status === 'pending' || post.status === 'rejected');
            } else if (currentFilter === 'archive') {
                filteredPosts = allUserPosts.filter(post => post.status === 'post_deleted' || post.status === 'removed_by_admin');
            }
            
            // Apply search filter if search term exists
            if (searchTerm) {
                filteredPosts = filteredPosts.filter(post => {
                    const title = (post.title || '').toLowerCase();
                    return title.includes(searchTerm);
                });
            }
            
            // Render filtered posts
            renderPosts(filteredPosts, currentFilter);
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
    </script>

@endsection