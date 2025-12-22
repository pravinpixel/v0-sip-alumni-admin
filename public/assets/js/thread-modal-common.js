// Global variables for thread modal
let replyingToReplyId = null;
let replyingToUserName = null;
let activePostId = null;

function openThreadModal(postId, canReply = true) {
    const modal = document.getElementById('threadModal');
    
    window.currentThreadPostId = postId;
    activePostId = postId;
    
    // Show modal
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    const replySection = document.getElementById('replySection');
    if (replySection) {
        replySection.style.display = canReply ? 'block' : 'none';
    }
    
    setCurrentUserAvatar();
    
    // Load thread data
    loadThreadData(postId);
}

function setCurrentUserAvatar() {
    const currentUserAvatar = document.getElementById('currentUserAvatar');
    if (currentUserAvatar && window.currentAlumni) {
        const userInitials = window.currentAlumni.full_name ? 
            window.currentAlumni.full_name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) : 
            'U';
        
        if (window.currentAlumni.image_url) {
            currentUserAvatar.innerHTML = `<img src="${window.currentAlumni.image_url}" alt="${window.currentAlumni.full_name}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
        } else {
            currentUserAvatar.textContent = userInitials;
        }
    }
}

function loadThreadData(postId) {
    // Show loading state
    document.getElementById('threadTitle').textContent = 'Loading...';
    document.getElementById('threadDescription').textContent = 'Loading post details...';
    document.getElementById('threadComments').innerHTML = `
        <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
            <i class="fas fa-spinner fa-spin" style="font-size: 32px; margin-bottom: 16px;"></i>
            <p style="font-size: 14px;">Loading comments...</p>
        </div>
    `;
    
    // Fetch thread data
    fetch(window.viewThreadRoute.replace(':id', postId))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.post) {
                window.currentThreadReplies = data.data.replies || [];
                renderThreadData(data.data.post, data.data.replies || []);
            } else {
                document.getElementById('threadTitle').textContent = 'Error';
                document.getElementById('threadDescription').textContent = 'Failed to load post details';
                document.getElementById('threadComments').innerHTML = `
                    <div style="text-align: center; padding: 40px 20px; color: #dc2626;">
                        <i class="fas fa-exclamation-circle" style="font-size: 32px; margin-bottom: 16px;"></i>
                        <p style="font-size: 14px;">Failed to load post details</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading thread:', error);
            document.getElementById('threadTitle').textContent = 'Error';
            document.getElementById('threadDescription').textContent = 'Error loading post details';
            document.getElementById('threadComments').innerHTML = `
                <div style="text-align: center; padding: 40px 20px; color: #dc2626;">
                    <i class="fas fa-exclamation-circle" style="font-size: 32px; margin-bottom: 16px;"></i>
                    <p style="font-size: 14px;">Error loading post details</p>
                </div>
            `;
        });
}

function renderThreadData(post, replies) {
    // Populate post details
    document.getElementById('threadTitle').textContent = post.title || 'Untitled Post';
    document.getElementById('threadDescription').innerHTML = post.description || 'No description available';
    
    // Author info
    const authorName = (post.alumni && post.alumni.full_name) ? post.alumni.full_name : 'Current User';
    const authorInitials = authorName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    const profilePicture = post.alumni?.image_url || '';
    const hasConnection = post.has_connection || false;
    
    document.getElementById('threadAuthor').textContent = authorName;
    
    const date = post.created_at
                ? (() => {
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
                })()
                : 'Unknown date';
    document.getElementById('threadDate').textContent = date;
    
    // Author avatar
    const avatarEl = document.getElementById('threadAuthorAvatar');
    if (hasConnection && profilePicture) {
        avatarEl.innerHTML = `<img src="${profilePicture}" alt="${escapeHtml(authorName)}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
    } else {
        avatarEl.textContent = authorInitials;
    }
    
    // Tags
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
    
    // Comments
    const commentsContainer = document.getElementById('threadComments');
    const commentsHeading = document.getElementById('commentsHeading');
    const noCommentsMessage = document.getElementById('noCommentsMessage');
    
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
    const hasConnection = reply.has_connection || false;
    const profilePicture = reply.alumni?.image_url || '';
    
    const replyDate = reply.created_at
                ? (() => {
                    const d = new Date(reply.created_at);

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
                })()
                : 'Unknown date';

    const commentElement = document.createElement('div');
    commentElement.setAttribute('data-reply-id', reply.id);
    commentElement.style.cssText = '';

    // Check if replies are allowed based on page context
    const canReply = window.canReplyToComments !== false;

    commentElement.innerHTML = `
        <div>
            <div style="display: flex; gap: 12px;background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 12px; transition: all 0.2s;">
                ${hasConnection && profilePicture ? `
                    <img src="${profilePicture}" alt="${escapeHtml(replyAuthor)}" 
                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                ` : `
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(90deg, #E2001D, #B1040E); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">
                        ${replyInitial}
                    </div>
                `}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <div>
                            <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 2px 0;">${escapeHtml(replyAuthor)}</p>
                            <p style="font-size: 12px; color: #6b7280; margin: 0;">${replyDate}</p>
                        </div>
                        ${canReply ? `
                            <button 
                                onclick="setReplyTo(${reply.id}, '${escapeHtml(replyAuthor)}')"
                                style="background: transparent; border: none; color: #dc2626; cursor: pointer; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 600; transition: all 0.2s;"
                                onmouseover="this.style.background='#fef2f2'"
                                onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-arrow-turn-up fa-rotate-270 fa-sm me-1"></i> Reply
                            </button>
                        ` : ''}
                    </div>
                    <p style="font-size: 14px; color: #374151; line-height: 1.6; margin: 0 0 12px 0; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                        ${escapeHtml(reply.message || 'No message')}
                    </p>
                    ${reply.child_replies && reply.child_replies.length > 0 ? `
                        <button 
                            id="toggle-btn-${reply.id}" 
                            onclick="toggleReplies(${reply.id})"
                            style="background: transparent; border: none; color: #2563eb; cursor: pointer; font-size: 13px; padding: 6px 0; font-weight: 600; transition: color 0.2s;"
                            onmouseover="this.style.color='#1d4ed8'"
                            onmouseout="this.style.color='#2563eb'">
                            View Thread (${reply.child_replies.length}) <i class="fas fa-chevron-down"></i>
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
        // commentElement.style.background = '#fef2f2';
        // commentElement.style.borderColor = '#fca5a5';
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
    input.placeholder = `Write a comment...`;
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
    const replyButton = document.getElementById('replyButton');
    const replyText = replyInput.value.trim();
    if (replyButton.disabled) return;

    if (!replyText) {
        showToast('Please enter a valid reply', 'error');
        return;
    }

    const postId = window.currentThreadPostId;

    const requestData = {
        forum_post_id: postId,
        parent_reply_id: replyingToReplyId,
        message: replyText
    };
    replyButton.disabled = true;
    replyButton.innerText = 'Posting...';

    fetch(window.createReplyRoute, {
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
                // Reload thread with current reply permissions
                const canReply = window.canReplyToComments !== false;
                openThreadModal(postId, canReply);
                
                // Call page-specific reload function if available
                if (typeof window.reloadPageData === 'function') {
                    window.reloadPageData();
                }
            } else {
                replyButton.disabled = false;
                showToast('Failed to post reply: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            replyButton.disabled = false;
            console.error('Error:', error);
            showToast('Error posting reply', 'error');
        });
}

function closeThreadModal() {
    document.getElementById('threadModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    resetReplyState();
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
    const hasConnection = reply.has_connection || false;
    const profilePicture = reply.alumni?.image_url || '';
    
    const date = reply.created_at
                ? (() => {
                    const d = new Date(reply.created_at);

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
                })()
                : 'Unknown date';

    const showReplyButton = level < 1 && window.canReplyToComments !== false;
    div.innerHTML = `
            <div style="display: flex; gap: 12px;">
                ${hasConnection && profilePicture ? `
                    <img src="${profilePicture}" alt="${escapeHtml(author)}" 
                        style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #dc2626; flex-shrink: 0;">
                ` : `
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0;">
                        ${initials}
                    </div>
                `}

                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <div>
                            <p style="margin: 0; font-weight: 600; font-size: 14px; color: #111827;">${escapeHtml(author)}</p>
                            <p style="margin: 0; color: #6b7280; font-size: 12px;">${date}</p>
                        </div>

                        ${showReplyButton ? `
                        <button 
                            onclick="setReplyTo(${reply.id}, '${escapeHtml(author)}')"
                            style="background: transparent; border: none; color: #dc2626; cursor: pointer; font-size: 12px; padding: 6px 12px; border-radius: 6px; font-weight: 600; transition: all 0.2s;"
                            onmouseover="this.style.background='#fef2f2'"
                            onmouseout="this.style.background='transparent'">
                            <i class="fa-solid fa-arrow-turn-up fa-rotate-270 fa-sm me-1"></i> Reply
                        </button>
                        ` : ''}
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

// Event listeners for modal
// document.addEventListener('click', function (event) {
//     const modal = document.getElementById('threadModal');
//     if (event.target === modal) {
//         closeThreadModal();
//     }
// });

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeThreadModal();
    }
});

// Utility function for HTML escaping
function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}