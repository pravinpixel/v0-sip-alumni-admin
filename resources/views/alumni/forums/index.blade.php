@extends('alumni.layouts.index')

@section('content')
@include('alumni.modals.view-thread-modal')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px; background: white;">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">Forum Posts</h1>
            <p style="color: #6b7280; font-size: 15px;">Share and engage with the SIP Academy community</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <button
                style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;"
                onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                Your Activity
            </button>
            <button
                style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;"
                onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'" onclick="openCreatePostModal()">
                <i class="fas fa-plus"></i>
                Create Post
            </button>
        </div>
    </div>
    @include('alumni.modals.create-post-modal')

    {{-- Search and Filter --}}
    <div style="display: flex; gap: 12px; margin-bottom: 24px;">
        <div style="flex: 1; position: relative;">
            <i class="fas fa-search"
                style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
            <input type="text" placeholder="Search posts..."
                style="width: 100%; padding: 12px 16px 12px 45px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none;"
                onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#e5e7eb'">
        </div>
        <button
            style="background: white; color: #374151; border: 2px solid #e5e7eb; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px;"
            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
            <i class="fas fa-filter"></i>
            Filter
        </button>
    </div>

    <div id="forumPostsContainer"></div>
</div>
<style>
    .toast-notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideInUp 0.3s ease;
        z-index: 3000;
        display: flex;
        align-items: center;
        gap: 12px;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        loadForumPosts();
    });

    function loadForumPosts() {
        const container = document.getElementById('forumPostsContainer');

        fetch("{{ route('alumni.forums.data') }}")
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
                <button onclick="openCreatePostModal()" style="background: #dc2626; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-plus"></i> Create First Post
                </button>
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
            <div style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px; transition: all 0.3s ease;"
                 onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; this.style.borderColor='#dc2626'"
                 onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e5e7eb'">

                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <h2 style="font-size: 20px; font-weight: 700; color: #dc2626; margin: 0; line-height: 1.4;">
                        ${escapeHtml(title)}
                    </h2>
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
                            <i class="fas fa-eye"></i>
                            <span>${post.views_count || 0}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; color: #6b7280; font-size: 14px;">
                            <i class="fas fa-heart"></i>
                            <span>${post.likes_count || 0}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; color: #6b7280; font-size: 14px;">
                            <i class="fas fa-comment"></i>
                            <span>${post.reply_count || 0}</span>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 16px;">
                        <button style="background: transparent; border: none; color: #6b7280; cursor: pointer; font-size: 14px; padding: 8px 12px; border-radius: 6px; transition: all 0.2s; display: flex; align-items: center; gap: 6px;"
                                onmouseover="this.style.background='#f3f4f6'; this.style.color='#dc2626'"
                                onmouseout="this.style.background='transparent'; this.style.color='#6b7280'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                             <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                            </svg>
                            Like
                        </button>
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

    function replyHover(button) {
        button.style.background = "#F7C744"; // yellow
        button.style.color = "#374151"; // dark
    }


    function replyUnhover(button) {
        if (button.classList.contains("reply-active")) {
            // return to active style
            button.style.background = "#ffdbdbff";
            button.style.color = "#dc2626";
        } else {
            // return to default
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

    window.openThreadModal = function(postId) {
        window.currentThreadPostId = postId;
        const url = "{{ route('alumni.view.thread', ':id') }}".replace(':id', postId);

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    showToast("Failed to load thread.", 'error');
                    return;
                }

                populateThreadModal(data.data);
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
                tagElement.style.cssText = 'background: #F7C744; color: #000000; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;';
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
        resetReplyState();
    }

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('threadModal');
        if (event.target === modal) {
            closeThreadModal();
        }
    });

    document.addEventListener('keydown', function(event) {
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

    // Toast notification function (if not already defined)
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? '#10b981' : '#ef4444';
        
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${bgColor};
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            z-index: 10000;
            font-size: 14px;
            font-weight: 600;
            animation: slideInRight 0.3s ease-out;
        `;
        
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
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