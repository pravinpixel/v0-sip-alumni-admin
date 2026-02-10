@extends('alumni.layouts.index')

@section('content')
<style>
    /* Responsive Styles for Activity Page */
    @media (max-width: 991px) {
        .activity-container {
            padding: 16px !important;
        }


        /* Stats cards - 2 columns on tablet */
        #statsCardsContainer > div {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 16px !important;
        }

        .stat-card {
            padding: 16px !important;
        }

        .stat-card h2 {
            font-size: 28px !important;
        }

        .stat-card span {
            font-size: 12px !important;
        }

        .tab-button {
            padding: 10px 20px !important;
            font-size: 13px !important;
        }

        .post-item {
            padding: 16px !important;
        }
    }

    @media (max-width: 767px) {
        .activity-container {
            padding: 12px !important;
        }


        .back-link {
            font-size: 13px !important;
        }

        /* Stats cards - 2 columns on small tablet */
        #statsCardsContainer > div {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
        }

        .stat-card {
            padding: 14px !important;
        }

        .stat-card h2 {
            font-size: 24px !important;
        }

        .stat-card span {
            font-size: 11px !important;
        }

        .stat-card i {
            font-size: 14px !important;
        }

        /* Tabs and search row - stack vertically */
        div[style*="display: flex"][style*="justify-content: space-between"][style*="gap: 20px"][style*="margin-bottom: 20px"] {
            flex-direction: column !important;
            gap: 12px !important;
        }

        /* Tabs container */
        div[style*="display: flex"][style*="gap: 0"][style*="background: #f3f4f6"] {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Individual tab buttons */
        #activePostsTab,
        #postStatusTab,
        #archiveTab {
            padding: 10px 24px !important;
            font-size: 13px !important;
        }

        /* Search bar - full width */
        div[style*="flex: 0 0 350px"] {
            flex: 1 1 100% !important;
            max-width: 100% !important;
            width: 100% !important;
        }

        div[style*="flex: 0 0 350px"] > div {
            width: 100% !important;
        }

        #searchInput {
            width: 100% !important;
        }

        .post-item {
            padding: 14px !important;
        }

        .post-item h3 {
            font-size: 16px !important;
        }

        .post-item p {
            font-size: 13px !important;
        }

        #postDetailModal > div {
            padding: 15px !important;
        }

        #postDetailModal .modal-content {
            padding: 30px 20px !important;
        }
    }

    @media (max-width: 575px) {
        .activity-container {
            padding: 10px !important;
        }


        .back-link {
            font-size: 12px !important;
        }

        /* Stats cards - 1 column on mobile */
        #statsCardsContainer > div {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
        }

        .stat-card {
            padding: 12px !important;
        }

        .stat-card h2 {
            font-size: 22px !important;
        }

        .stat-card span {
            font-size: 11px !important;
        }

        .stat-card i {
            font-size: 13px !important;
        }

        /* Tabs and search row */
        div[style*="display: flex"][style*="justify-content: space-between"][style*="gap: 20px"][style*="margin-bottom: 20px"] {
            flex-direction: column !important;
            gap: 10px !important;
        }

        /* Tabs container - horizontal scroll */
        div[style*="display: flex"][style*="gap: 0"][style*="background: #f3f4f6"] {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        div[style*="display: flex"][style*="gap: 0"][style*="background: #f3f4f6"]::-webkit-scrollbar {
            height: 4px;
        }

        div[style*="display: flex"][style*="gap: 0"][style*="background: #f3f4f6"]::-webkit-scrollbar-thumb {
            background: #dc2626;
            border-radius: 4px;
        }

        /* Individual tab buttons - smaller on mobile */
        #activePostsTab,
        #postStatusTab,
        #archiveTab {
            padding: 8px 16px !important;
            font-size: 12px !important;
            flex-shrink: 0;
        }

        /* Search bar - full width */
        div[style*="flex: 0 0 350px"] {
            flex: 1 1 100% !important;
            max-width: 100% !important;
            width: 100% !important;
        }

        div[style*="flex: 0 0 350px"] > div {
            width: 100% !important;
        }

        #searchInput {
            width: 100% !important;
            font-size: 13px !important;
            padding: 9px 16px 9px 40px !important;
        }

        .post-item {
            padding: 12px !important;
        }

        .post-item h3 {
            font-size: 15px !important;
        }

        .post-item p {
            font-size: 12px !important;
        }

        #postDetailModal > div {
            padding: 10px !important;
        }

        #postDetailModal .modal-content {
            padding: 25px 15px !important;
        }

        #deleteConfirmModal .modal-content {
            padding: 24px 16px !important;
        }
    }
</style>

    <div class="activity-container" style="max-width: 1400px; margin: 0 auto; padding: 20px; background: white">
        {{-- Back to Forum Link --}}
        <div style="margin-bottom: 20px;">
            <a href="{{ route('alumni.forums') }}"
                class="back-link"
                style="color: #dc2626; text-decoration: none; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;"
                onmouseover="this.style.color='#b91c1c'" onmouseout="this.style.color='#dc2626'">
                <i class="fas fa-arrow-left"></i>
                Back to Forum
            </a>
        </div>

        {{-- Header --}}
        <div class="activity-header" style="margin-bottom: 30px;">
            <h1 style=" font-weight: 700; color: #111827; margin-bottom: 8px;" class="main-title">Your Activity</h1>
            <p style="color: #6b7280;" class="sub-title">Track your posts and engagement</p>
        </div>

        {{-- Dynamic Stats Cards --}}
        <div id="statsCardsContainer" style="margin-bottom: 30px;">
            {{-- Cards will be dynamically loaded based on active tab --}}
        </div>

        {{-- Post Detail Modal --}}
        <div id="postDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; overflow-y: auto;">
            <div style="min-height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
                <div style="background: white; border-radius: 16px; max-width: 800px; width: 100%; position: relative; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
                    {{-- Close Button --}}
                    <button onclick="closePostModal()" style="position: absolute; top: 20px; right: 20px; width: 40px; height: 40px; border-radius: 50%; background: #fee2e2; border: none; color: #dc2626; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 20px; z-index: 10;"
                        onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                        <i class="fas fa-times"></i>
                    </button>

                    {{-- Modal Content --}}
                    <div id="modalContent" style="padding: 40px;">
                        {{-- Content will be loaded here --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div id="deleteConfirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; overflow-y: auto;">
            <div style="min-height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
                <div style="background: white; border-radius: 16px; max-width: 500px; width: 100%; position: relative; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); padding: 32px;">
                    {{-- Close Button --}}
                    <button onclick="closeDeleteModal()" style="position: absolute; top: 16px; right: 16px; width: 32px; height: 32px; border-radius: 50%; background: transparent; border: none; color: #9ca3af; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px;"
                        onmouseover="this.style.background='#f3f4f6'; this.style.color='#111827'" onmouseout="this.style.background='transparent'; this.style.color='#9ca3af'">
                        <i class="fas fa-times"></i>
                    </button>

                    {{-- Modal Content --}}
                    <div>
                        <h3 style="font-size: 24px; font-weight: 700; color: #111827; margin: 0 0 12px 0;">Delete Post</h3>
                        <p style="color: #6b7280; font-size: 15px; margin: 0 0 24px 0;">Are you sure you want to delete this post?</p>
                        
                        <div style="display: flex; gap: 12px; justify-content: flex-end;">
                            <button onclick="closeDeleteModal()" style="padding: 10px 24px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; color: #374151; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb'">
                                Cancel
                            </button>
                            <button onclick="confirmDeletePost(event)" id="deletePostButton" style="padding: 10px 24px; background: #dc2626; border: none; border-radius: 8px; color: white; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Remarks Modal --}}
        <div id="remarksModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; overflow-y: auto;">
            <div style="min-height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px;">
                <div style="background: white; border-radius: 16px; max-width: 600px; width: 100%; position: relative; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); padding: 32px;">
                    {{-- Close Button --}}
                    <button onclick="closeRemarksModal()" style="position: absolute; top: 16px; right: 16px; width: 32px; height: 32px; border-radius: 50%; background: #fee2e2; border: none; color: #dc2626; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px;"
                        onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                        <i class="fas fa-times"></i>
                    </button>

                    {{-- Modal Content --}}
                    <div>
                        <h3 style="font-size: 24px; font-weight: 700; color: #111827; margin: 0 0 20px 0;">Admin Remarks</h3>
                        
                        <div id="remarksContent" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; color: #6b7280; font-size: 15px; line-height: 1.6; min-height: 80px;">
                            {{-- Remarks will be loaded here --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs and Search in One Row --}}
        <div style="background: white; border-radius: 12px; overflow: hidden;">
            <div
                style="display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 20px;">
                <div
                    style="display: inline-flex; gap: 0; background: #f3f4f6; border-radius: 8px; overflow: hidden; flex: 0 0 auto; justify-content: space-between;">
                    {{-- Tabs --}}
                    <button id="activePostsTab" onclick="switchTab('activePosts')"
                        style="padding: 12px 40px; background: #dc2626; color: white; border: none; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                        Active Posts
                    </button>
                    <button id="postStatusTab" onclick="switchTab('postStatus')"
                        style="padding: 12px 40px; background: transparent; color: #6b7280; border: none; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; display: none;">
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
            <div id="postsContainer">
                {{-- Posts will be loaded here --}}
            </div>
        </div>
    </div>

    {{-- Include Create/Edit Post Modal --}}
    @include('alumni.modals.create-post-modal', ['alumni' => session('alumni')])
    
    {{-- Include View Thread Modal --}}
    @include('alumni.modals.view-thread-modal')
    
    {{-- Include common thread modal JavaScript --}}
    <script src="{{ asset('js/thread-modal-common.js') }}"></script>

    <style>
        .stat-card:hover {
            transform: scale(1.05);
            opacity: 0.9;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>

    <script>
        // Configuration for common thread modal
        window.viewThreadRoute = "{{ route('alumni.view.thread', ':id') }}";
        window.createReplyRoute = "{{ route('alumni.create.reply') }}";
        window.currentAlumni = @json($alumni);
        window.reloadPageData = function() {
            if (typeof loadActivityData === 'function') {
                loadActivityData();
            }
        };
        
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
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Active Posts</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-file-alt" style="color: #ef4444; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #ef4444; margin: 0;">${stats.activePosts || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #f59e0b; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Total Likes</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-heart" style="color: #f59e0b; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #f59e0b; margin: 0;">${stats.totalLikes || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #3b82f6; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Total Views</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-eye" style="color: #3b82f6; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #3b82f6; margin: 0;">${stats.totalViews || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #a855f7; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
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
                // 4 cards for Post Status tab
                html = `
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                                    <div class="stat-card" style="background: white; border: 2px solid #ef4444; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Total Posts</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-file-alt" style="color: #ef4444; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #ef4444; margin: 0;">${stats.totalStatusPosts || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #008f58ff; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Approved</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-check" style="color: #008f58ff; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #008f58ff; margin: 0;">${stats.activePosts || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #f59e0b; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Pending</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-clock" style="color: #f59e0b; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #f59e0b; margin: 0;">${stats.pendingPosts || 0}</h2>
                                    </div>
                                    <div class="stat-card" style="background: white; border: 2px solid #ef4444; border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
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
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Total Posts</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-file-alt" style="color: #9ca3af; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #6b7280; margin: 0;">${stats.archivedPosts || 0}</h2>
                                    </div>
                                    <div style="background: white; border: 2px solid #d1d5db; border-radius: 12px; padding: 20px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Active</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-check-circle" style="color: #9ca3af; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #6b7280; margin: 0;">${stats.activePosts || 0}</h2>
                                    </div>
                                    <div style="background: white; border: 2px solid #d1d5db; border-radius: 12px; padding: 20px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Pending</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-clock" style="color: #9ca3af; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #6b7280; margin: 0;">${stats.pendingPosts || 0}</h2>
                                    </div>
                                    <div style="background: white; border: 2px solid #d1d5db; border-radius: 12px; padding: 20px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="color: #9ca3af; font-size: 13px; font-weight: 500;">Rejected</span>
                                            <div style="width: 32px; height: 32px; background: transparent; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-times-circle" style="color: #9ca3af; font-size: 16px;"></i>
                                            </div>
                                        </div>
                                        <h2 style="font-size: 32px; font-weight: 700; color: #6b7280; margin: 0;">${stats.rejectedPosts || 0}</h2>
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
                        currentStats.totalStatusPosts = currentStats.pendingPosts + currentStats.rejectedPosts + currentStats.activePosts;

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

            // Use simple layout for Post Status tab only
            if (viewType === 'postStatus') {
                renderSimplePosts(posts, false);
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
                const fullDescription = post.description ? post.description.replace(/<\/?[^>]+>/g, "") : 'No description available';
                const description = fullDescription.length > 150 ? 
                    fullDescription.substring(0, 150) + '...' : 
                    fullDescription;

                const date = post.updated_at ?
                    new Date(post.updated_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) + ' at ' + new Date(post.updated_at).toLocaleTimeString('en-US', {
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
                const tags = post.labels ?? [];

                html += `
                                                                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 16px; position: relative;">
                                                                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 8px;">
                                                                            <div style="flex: 1; min-width: 0;">
                                                                                    <h3 onclick="openPostModal(${post.id})"
                                                                                        style="font-size: 18px; font-weight: 700; color: #dc2626; margin: 0; cursor: pointer; 
                                                                                            transition: color 0.2s; line-height: 1.4; word-wrap: break-word; padding-right: 8px; 
                                                                                            overflow-wrap: break-word; word-break: break-word;"
                                                                                            onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none'; this.style.color='#dc2626';">
                                                                                        ${escapeHtml(title)}
                                                                                    </h3>
                                                                                </div>
                                                                                <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                                                                    <span style="background: ${statusColor}; color: ${statusTextColor}; padding: 4px 10px; border-radius: 50px; font-size: 12px; font-weight: 600; white-space: nowrap;">
                                                                                        ${statusText}
                                                                                    </span>
                                                                                    ${viewType !== 'archive' ? `
                                                                                        <button onclick="openDeleteModal(${post.id})" style="background: transparent; border: none; color: #dc2626; width: 32px; height: 32px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"
                                                                                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'"
                                                                                            title="Delete post">
                                                                                            <i class="fas fa-trash"></i>
                                                                                        </button>
                                                                                    ` : ''}
                                                                                </div>
                                                                            </div>

                                                                            <p style="color: #9ca3af; font-size: 13px; margin-bottom: 12px;">${date}</p>

                                                                            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin-bottom: 16px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;" 
                                                                               title="${escapeHtml(fullDescription)}">
                                                                                ${escapeHtml(fullDescription)}
                                                                            </p>

                                                                            ${viewType === 'archive' && post.status === 'removed_by_admin' && post.remarks ? `
                                                                                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 6px; margin-bottom: 16px; display: flex; gap: 12px;">
                                                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                                                        <span style="color: #b32626ff; font-weight: 600; font-size: 13px;">Removal Reason:</span>
                                                                                    </div>
                                                                                    <p style="color: #d64747ff; font-size: 13px; line-height: 1.5; margin: 0;">
                                                                                        ${escapeHtml(post.remarks)}
                                                                                    </p>
                                                                                </div>
                                                                            ` : ''}

                                                                            ${tags.length > 0 ? `
                                                                                <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
                                                                                    ${tags.map(tag => `
                                                                                        <span style="background: #fbbf24; color: #000; padding: 4px 12px; border-radius: 14px; font-size: 10px; font-weight: 600;">
                                                                                            ${escapeHtml(tag.trim())}
                                                                                        </span>
                                                                                    `).join('')}
                                                                                </div>
                                                                            ` : ''}
                                                                            <hr style="color: #45536eff;">

                                                                            <div style="display: flex; align-items: center; gap: 20px; color: #6b7280; font-size: 14px;">
                                                                                <span style="display: flex; align-items: center; gap: 6px;">
                                                                                    <i class="far fa-eye"></i> ${post.views_count || 0}
                                                                                </span>
                                                                                <span style="display: flex; align-items: center; gap: 6px;">
                                                                                    <i class="far fa-heart"></i> ${post.likes_count || 0}
                                                                                </span>
                                                                                <span style="display: flex; align-items: center; gap: 6px;">
                                                                                    <i class="far fa-comment"></i> ${post.reply_count || 0} replies
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    `;
            });

            container.innerHTML = html;
        }

        function renderSimplePosts(posts, isArchive = false) {
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
                const date = post.updated_at ?
                    'Posted: ' + new Date(post.updated_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) + ' at ' + new Date(post.updated_at).toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) :
                    'Unknown date';

                // Status display for Post Status tab
                let statusColor, statusTextColor, statusText;
                if (post.status === 'pending') {
                    statusColor = '#fef3c7';
                    statusTextColor = '#d97706';
                    statusText = 'Pending';
                } else if (post.status === 'rejected') {
                    statusColor = '#fee2e2';
                    statusTextColor = '#dc2626';
                    statusText = 'Rejected';
                } else if (post.status === 'post_deleted') {
                    statusColor = '#fee2e2';
                    statusTextColor = '#dc2626';
                    statusText = 'Post Deleted';
                } else if (post.status === 'removed_by_admin') {
                    statusColor = '#fee2e2';
                    statusTextColor = '#cb2431';
                    statusText = 'Removed by Admin';
                } else {
                    statusColor = '#f3f4f6';
                    statusTextColor = '#6b7280';
                    statusText = 'Archived';
                }

                html += `
                                                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 6px;">
                                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 12px;">
                                                        <div style="flex: 1; min-width: 0;">
                                                            <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 8px 0; transition: color 0.2s; line-height: 1.4; word-wrap: break-word; padding-right: 8px;"
                                                                >${escapeHtml(title)}</h3>
                                                            <p style="color: #9ca3af; font-size: 13px; margin: 0;">${date}</p>
                                                        </div>
                                                        <div style="flex-shrink: 0;"> 
                                                        <div style="margin-bottom: 10px; text-align: center;"> 
                                                        <span style="background: ${statusColor}; color: ${statusTextColor}; padding: 2px 10px; border-radius: 50px; font-size: 12px; font-weight: 600; white-space: nowrap;">
                                                            ${statusText}
                                                        </span>
                                                        </div>
                                                        <div style="display: flex; gap: 4px; justify-content: flex-end;">
                                                            ${post.status === 'rejected' ? `
                                                            <button onclick="openRemarksModal('${escapeHtml(post.remarks || 'No remarks provided').replace(/'/g, "\\'")}', ${post.id})" style="background: transparent; border: 1px solid #3b82f6; color: #3b82f6; width: 36px; height: 36px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                                onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='transparent'"
                                                                title="View Remarks">
                                                                <i class="fas fa-file-alt"></i>
                                                            </button>
                                                            <button onclick="openRepostModal(${post.id})" style="background: transparent; border: 1px solid #10b981; color: #10b981; width: 36px; height: 36px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                                onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='transparent'"
                                                                title="Resubmit">
                                                                <i class="fas fa-redo"></i>
                                                            </button>
                                                        ` : ''}
                                                        ${post.status === 'pending' ? `
                                                            <button onclick="openEditModal(${post.id})" style="background: transparent; border: 1px solid #f59e0b; color: #f59e0b; width: 36px; height: 36px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                                onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'"
                                                                title="Edit post">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        ` : ''}
                                                        ${!isArchive ? `
                                                            <button onclick="openDeleteModal(${post.id})" style="background: transparent; border: 1px solid #dc2626; color: #dc2626; width: 36px; height: 36px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                                                onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'"
                                                                title="Delete post">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        ` : ''}
                                                         </div>
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

        function openPostModal(postId) {
            // Determine if replies should be enabled based on current tab
            const canReply = currentFilter === 'activePosts';
            
            // Set reply permissions for common thread modal
            window.canReplyToComments = canReply;
            
            // Use the view-thread-modal instead of postDetailModal
            openThreadModal(postId, canReply);
        }

        function closePostModal() {
            const modal = document.getElementById('postDetailModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Thread modal functions are now in common file: thread-modal-common.js



        function renderPostDetail(post, replies) {
            const modalContent = document.getElementById('modalContent');
            
            const title = post.title || 'Untitled Post';
            const description = post.description || 'No description available';
            const authorName = (post.alumni && post.alumni.full_name) ? post.alumni.full_name : 'Current User';
            const authorInitials = authorName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
            const hasConnection = post.has_connection || false;
            const profilePicture = post.alumni?.image_url || '';
            
            const date = post.updated_at ? 
                new Date(post.updated_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }) : 'N/A';
            
            const tags = post.labels ? post.labels.split(',').filter(tag => tag.trim() !== '') : [];
            
            let html = `
                <div>
                    <h2 style="font-size: 24px; font-weight: 700; color: #111827; margin: 0 0 20px 0; padding-right: 60px; line-height: 1.4; word-wrap: break-word;">${escapeHtml(title)}</h2>
                    
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #e5e7eb;">
                        ${hasConnection && profilePicture ? `
                            <img src="${profilePicture}" alt="${escapeHtml(authorName)}" 
                                style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #dc2626;">
                        ` : `
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px;">
                                ${authorInitials}
                            </div>
                        `}
                        <div>
                            <div style="font-weight: 600; color: #111827; font-size: 15px;">${escapeHtml(authorName)}</div>
                            <div style="color: #9ca3af; font-size: 13px;">${date}</div>
                        </div>
                    </div>
                    
                    <div style="color: #374151; font-size: 15px; line-height: 1.7; margin-bottom: 20px; word-wrap: break-word; overflow-wrap: break-word;">
                        ${description}
                    </div>
                    
                    ${tags.length > 0 ? `
                        <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
                            ${tags.map(tag => `
                                <span style="background: #fbbf24; color: #000; padding: 4px 10px; border-radius: 16px; font-size: 13px; font-weight: 600;">
                                    ${escapeHtml(tag.trim())}
                                </span>
                            `).join('')}
                        </div>
                    ` : ''}
                    
                    <div style="display: flex; align-items: center; gap: 24px; padding: 16px 0; border-top: 2px solid #e5e7eb; border-bottom: 2px solid #e5e7eb; color: #6b7280; font-size: 14px;">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <i class="far fa-eye"></i> ${post.views_count || 0} views
                        </span>
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <i class="far fa-heart"></i> ${post.likes_count || 0} likes
                        </span>
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <i class="far fa-comment"></i> ${post.reply_count || 0} replies
                        </span>
                    </div>
                </div>
            `;
            
            modalContent.innerHTML = html;
        }

        // Delete Modal Functions
        let postToDelete = null;

        function openDeleteModal(postId) {
            postToDelete = postId;
            const modal = document.getElementById('deleteConfirmModal');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            postToDelete = null;
            const modal = document.getElementById('deleteConfirmModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            const deleteBtn = document.getElementById('deletePostButton');
            if (deleteBtn) {
                deleteBtn.innerHTML = 'Delete';
                deleteBtn.disabled = false;
            }
        }

        function confirmDeletePost(event) {
            if (!postToDelete) return;
            
            // Show loading state in the delete button
            const deleteBtn = event.target;
            if (deleteBtn.disabled) return;
            const originalText = deleteBtn.innerHTML;
            deleteBtn.innerHTML = 'Deleting...';
            deleteBtn.disabled = true;

            // Make API call to update post status to 'post_deleted'
            fetch("{{ route('alumni.update.status') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    post_id: postToDelete,
                    status: 'post_deleted'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDeleteModal();
                    // Reload the activity data
                    loadActivityData(currentFilter);
                    
                    // Show success message
                    showToast('Post deleted successfully!', 'success');
                } else {
                    // Reset button state
                    deleteBtn.innerHTML = originalText;
                    deleteBtn.disabled = false;
                    showToast(data.message || 'Failed to delete post', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting post:', error);
                // Reset button state
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
                showToast('An error occurred while deleting the post', 'error');
            });
        }

        

        // Edit and Repost Modal Functions
        let currentEditPostId = null;
        let isRepostMode = false;

        function openEditModal(postId) {
            currentEditPostId = postId;
            isRepostMode = false;
            
            // Find the post data
            const post = allUserPosts.find(p => p.id === postId);
            if (!post) {
                showToast('Post not found', 'error');
                return;
            }
            
            // Update modal title and button
            const modal = document.getElementById('createPostModal');
            const modalTitle = modal.querySelector('h2');
            const submitBtn = modal.querySelector('.btn-submit');
            
            modalTitle.textContent = 'Edit Post';
            submitBtn.textContent = 'Save Changes';
            submitBtn.onclick = updatePost;
            
            // Open modal and populate fields
            openCreatePostModal();
            
            // Populate form fields
            setTimeout(() => {
                const form = document.getElementById('createPostForm');
                form.querySelector('input[name="title"]').value = post.title || '';
                form.querySelector('input[name="labels"]').value = post.labels || '';
                
                if (quill && post.description) {
                    quill.root.innerHTML = post.description;
                }
            }, 100);
        }

        function openRepostModal(postId) {
            currentEditPostId = postId;
            isRepostMode = true;
            
            // Find the post data
            const post = allUserPosts.find(p => p.id === postId);
            if (!post) {
                showToast('Post not found', 'error');
                return;
            }
            
            // Update modal title and button
            const modal = document.getElementById('createPostModal');
            const modalTitle = modal.querySelector('h2');
            const submitBtn = modal.querySelector('.btn-submit');
            
            modalTitle.textContent = 'Repost';
            submitBtn.textContent = 'Resubmit Post';
            submitBtn.onclick = updatePost;
            
            // Open modal and populate fields
            openCreatePostModal();
            
            // Populate form fields
            setTimeout(() => {
                const form = document.getElementById('createPostForm');
                form.querySelector('input[name="title"]').value = post.title || '';
                form.querySelector('input[name="labels"]').value = post.labels || '';
                
                if (quill && post.description) {
                    quill.root.innerHTML = post.description;
                }
            }, 100);
        }

        function updatePost() {
            const form = document.getElementById('createPostForm');
            const titleInput = form.querySelector('input[name="title"]');
            const labelsInput = form.querySelector('input[name="labels"]');
            const title = titleInput.value.trim();
            const labels = labelsInput.value.trim();
            const description = quill.getText().trim();

            // Clear previous errors
            form.querySelectorAll('.error-message').forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
            form.querySelectorAll('.form-input').forEach(el => el.classList.remove('input-error'));
            document.getElementById('editor').classList.remove('editor-error');

            // Validation
            let hasError = false;

            if (!title) {
                showFieldError(titleInput, 'Post title is required');
                hasError = true;
            }
            const invalidPattern = /[^A-Za-z0-9 ]/;
            if (invalidPattern.test(title)) {
                showFieldError(titleInput, 'Special characters are not allowed in the title');
                hasError = true;
            }

            if (!description || description.length === 0) {
                showFieldError(document.getElementById('editor'), 'Post description is required');
                hasError = true;
            } else if (description.length > MAX_DESCRIPTION_LENGTH) {
                showFieldError(document.getElementById('editor'), `Description must be less than ${MAX_DESCRIPTION_LENGTH} characters`);
                hasError = true;
            }

            if (!labels) {
                showFieldError(labelsInput, 'At least one label is required');
                hasError = true;
            }

            if (hasError) {
                return;
            }

            const submitBtn = event.target;
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Updating...';
            submitBtn.disabled = true;

            // Prepare form data
            const formData = new FormData();
            formData.append('post_id', currentEditPostId);
            formData.append('title', title);
            formData.append('description', quill.root.innerHTML);
            formData.append('labels', labels);
            
            // If repost mode, set status to pending
            if (isRepostMode) {
                formData.append('status', 'pending');
            }

            fetch("{{ route('alumni.forums.update.post') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(isRepostMode ? 'Post resubmitted successfully' : 'Post updated successfully', 'success');
                    closeCreatePostModal();
                    currentEditPostId = null;
                    isRepostMode = false;
                    
                    // Reset modal
                    const modal = document.getElementById('createPostModal');
                    modal.querySelector('h2').textContent = 'Create New Post';
                    modal.querySelector('.btn-submit').textContent = 'Submit Post';
                    modal.querySelector('.btn-submit').onclick = submitPost;
                    
                    // Reload activity data
                    loadActivityData(currentFilter);
                } else {
                    showToast(data.message || 'Failed to update post', 'error');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('Error updating post', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        }

        // Remarks Modal Functions
        function openRemarksModal(remarks, postId) {
            const modal = document.getElementById('remarksModal');
            const remarksContent = document.getElementById('remarksContent');
            
            remarksContent.textContent = remarks || 'No remarks provided';
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeRemarksModal() {
            const modal = document.getElementById('remarksModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const postModal = document.getElementById('postDetailModal');
            const deleteModal = document.getElementById('deleteConfirmModal');
            const remarksModal = document.getElementById('remarksModal');
            
            if (event.target === postModal) {
                closePostModal();
            }
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
            if (event.target === remarksModal) {
                closeRemarksModal();
            }
        });
    </script>

@endsection