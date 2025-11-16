<div id="threadModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 1000; overflow-y: auto; padding: 20px; z-index: 1000;">
    <div style="background: white; border-radius: 12px; max-width: 700px; margin: 20px auto; padding: 0; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);">

        {{-- Close Button --}}


        {{-- Thread Content --}}
        <div style="padding: 24px;">
            <div style="position: sticky; top: 0; right: 0; display: flex; justify-content: flex-end; background: white;">
                <button onclick="closeThreadModal()" style="background: transparent; border: none; font-size: 14px; cursor: pointer; color: #6b7280;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Original Post --}}
            <div style="margin-bottom: 14px; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px;">
                <h2 id="threadTitle" style="font-size: 20px; font-weight: 700; color: #dc2626; margin: 0 0 16px 0;"></h2>
                <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; margin-bottom: 16px;">
                    <div id="threadAuthorAvatar" style="width: 40px; height: 40px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700;"></div>
                    <div>
                        <p id="threadAuthor" style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 2px 0;"></p>
                        <p id="threadDate" style="font-size: 12px; color: #6b7280; margin: 0;"></p>
                    </div>
                </div>

                <p id="threadDescription" style="color: #6b7280; font-size: 15px; line-height: 1.6; margin: 0 0 16px 0;"></p>

                <div id="threadTags" style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;"></div>


            </div>

            {{-- Comments Count --}}
            <div style="margin-bottom: 20px;">
                <h3 id="commentsHeading" style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 16px 0;">Comments (0)</h3>
            </div>

            {{-- Comments Loop --}}
            <div id="threadComments" style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                {{-- Comments will be rendered here --}}
            </div>

            {{-- Empty Comments Message --}}
            <div id="noCommentsMessage" style="display: none; text-align: center; padding: 40px 20px; color: #6b7280;">
                <i class="fas fa-comments" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                <p style="font-size: 14px; margin: 0;">No comments yet. Be the first to reply!</p>
            </div>
            <div style="display: flex; align-items: center;">
                <input
                    type="text"
                    placeholder="Write your reply..."
                    id="replyInput-${post.id}"
                    style="flex: 1; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none;"
                    onfocus="this.style.borderColor='#dc2626'"
                    onblur="this.style.borderColor='#e5e7eb'">

                <button
                    onclick=""
                    style="margin-left: 12px; padding: 12px 20px; background: #dc2626; color: white; border: none; border-radius: 8px; font-size: 14px; cursor: pointer;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

        </div>
    </div>
</div>

<style>
    #threadModal {
        animation: fadeIn 0.3s ease;
    }

    #threadModal>div {
        animation: slideUp 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            background: rgba(0, 0, 0, 0);
        }

        to {
            background: rgba(0, 0, 0, 0.5);
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .comment-item {
        background: #f9fafb;
        border-left: 3px solid #dc2626;
        padding: 16px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .comment-item:hover {
        background: #f3f4f6;
    }
</style>

<script>
    window.openThreadModal = function(postId) {

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
                alert("Error loading thread.", 'error');
            });
    };



    function populateThreadModal(threadData) {
        const post = threadData.post;
        const replies = threadData.replies || [];

        document.getElementById('threadTitle').textContent = post.title || 'Untitled Post';
        document.getElementById('threadDescription').textContent = post.description ?
            post.description.replace(/<\/?[^>]+>/g, "").substring(0, 200) +
            (post.description.length > 200 ? '...' : '') :
            'No description available';
        document.getElementById('threadAuthor').textContent = post.alumni?.full_name || 'Unknown';

        const author = post.alumni?.full_name || 'Unknown';
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
                tagElement.style.cssText = 'background: #F7C744; color: #000000ff; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;';
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
                commentElement.className = 'comment-item';
                commentElement.innerHTML = `
                <div style="display: flex; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">
                        ${replyInitial}
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                        <div>
                            <p style="font-size: 14px; font-weight: 600; color: #111827; margin: 0;">${escapeHtml(replyAuthor)}</p>
                            <p style="font-size: 12px; color: #6b7280; margin: 0;">${replyDate}</p>
                        </div>
                        <div>
                            <button style="background: transparent; border: none; color: #dc2626; cursor: pointer; font-size: 12px; padding: 8px 0; margin-top: 8px; font-weight: 600;">
                            <i class="fas fa-reply"></i> Reply
                        </button>
                        </div>
                        </div>
                        <p style="font-size: 14px; color: #374151; line-height: 1.5; margin: 0; word-wrap: break-word;">
                            ${escapeHtml(reply.message || 'No message')}
                        </p>
                        <button style="background: transparent; border: none; color: #dc2626; cursor: pointer; font-size: 12px; padding: 8px 0; margin-top: 8px; font-weight: 600;">
                            <i class="fas fa-reply"></i> View Thread
                        </button>
                    </div>
                </div>
            `;
                commentsContainer.appendChild(commentElement);
            });
        } else {
            commentsHeading.textContent = 'Comments (0)';
            noCommentsMessage.style.display = 'block';
        }
    }


    function closeThreadModal() {
        document.getElementById('threadModal').style.display = 'none';
        document.body.style.overflow = 'auto';
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
</script>