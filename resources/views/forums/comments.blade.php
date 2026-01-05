@extends('layouts.index')
@section('title', 'Post Comments - Alumni Tracking')

@section('content')
<div class="content-container mt-4">
    <!-- Back Button and Header -->
    <div class="connection-header">
        <button onclick="window.history.back()" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
        </button>
        <div>
            <h1 id="postTitleHeader" class="main-title">Discussion Topic connections with the Alumni Peoples</h1>
            <p class="main-subtitle">
                View and manage all comments on this post
            </p>
        </div>
    </div>

    <!-- Post Details Card -->
    <div class="post-card">
    <!-- Post Title -->
    <div class="post-section">
        <label class="post-label">Post Title</label>
        <div id="postTitle" class="post-title text-wrap"></div>
    </div>

    <!-- Post Description -->
    <div class="post-section">
        <label class="post-label">Post Description</label>
        <div id="postDescription" class="post-description text-wrap"></div>
    </div>

    <!-- Labels -->
    <div class="post-section">
        <label class="post-label">Labels</label>
        <div id="postLabels" class="post-labels">
            <!-- Labels here -->
        </div>
    </div>

    <!-- Stats -->
    <div class="post-stats">
        <div class="stat-item">
            <div class="stat-value">
                <i class="fas fa-heart stat-icon like"></i>
                <span id="likesCount">0</span>
            </div>
            <span class="stat-label">Likes</span>
        </div>

        <div class="stat-item">
            <div class="stat-value">
                <i class="fas fa-comment stat-icon comment"></i>
                <span id="commentsCount">0</span>
            </div>
            <span class="stat-label">Comments</span>
        </div>

        <div class="stat-item">
            <div class="stat-value">
                <i class="fas fa-eye stat-icon view"></i>
                <span id="viewsCount">0</span>
            </div>
            <span class="stat-label">Views</span>
        </div>

        <div class="stat-item">
            <div class="stat-value">
                <i class="fas fa-thumbtack stat-icon pin"></i>
                <span id="awardsCount">0</span>
            </div>
            <span class="stat-label">Pinned</span>
        </div>
    </div>
</div>


    <!-- Comments Section -->
    <div class="table-box-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 700; color: #333; margin: 0;">Comments (<span id="totalComments">0</span>)</h2>
            <div class="comment-search">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search comments...">
            </div>
        </div>

        <!-- Comments Table -->
        <div class="table-container">
            <div class="table-responsive" style="overflow-x: auto;">
                <table id="dataTable">
                    <thead>
                        <tr id="tableHeaderRow">
                            <th class="table-header">Alumni Profile</th>
                            <th class="table-header">Alumni Name</th>
                            <th class="table-header">Comment</th>
                            <th class="table-header">Time Commented</th>
                            <th class="table-header">Threads</th>
                            <th class="table-header">Actions</th>
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

<style>
    table.dataTable {
        margin: 0 !important;
    }
</style>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    const postId = {{ $postId }};

    $(document).ready(function() {
        // Load post details
        loadPostDetails();

        // Initialize DataTable
        const table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.forums.comments.data', '') }}/" + postId,
                type: 'GET'
            },
            columns: [
                {
                    data: 'alumni_profile',
                    name: 'alumni_profile',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'alumni_name',
                    name: 'alumni_name'
                },
                {
                    data: 'comment',
                    name: 'comment'
                },
                {
                    data: 'time_commented',
                    name: 'time_commented'
                },
                {
                    data: 'threads',
                    name: 'threads',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            paging: true,
            searching: true,
            ordering: false,
            pageLength: 10,
            lengthChange: false,
            scrollX: true,
            dom: 't',
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ comments"
            }
        });

        table.on('draw', function() {
            let info = table.page.info();

            $(".dt-info-custom").html(
                `Showing ${info.start + 1 > info.recordsTotal ? 0 : info.start + 1} to ${info.end} comments of ${info.recordsTotal}`
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

        // Search
        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    function loadPostDetails() {
        $.ajax({
            url: "{{ route('admin.forums.post.details', '') }}/" + postId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const post = response.post;
                    
                    
                    // Set post details
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
                                <span style="background: #fbbf24; color: #000; padding: 6px 14px; border-radius: 16px; font-size: 13px; font-weight: 600;">
                                    ${label}
                                </span>
                            `);
                            labelsContainer.append(labelBadge);
                        });
                    } else {
                        labelsContainer.html('<span style="color: #9ca3af; font-style: italic;">No labels</span>');
                    }
                    
                    // Set stats
                    $('#likesCount').text(post.likes_count || 0);
                    $('#commentsCount').text(post.comments_count || 0);
                    $('#viewsCount').text(post.views_count || 0);
                    $('#awardsCount').text(post.awards_count || 0);
                    $('#totalComments').text(post.comments_count || 0);
                }
            },
            error: function(xhr) {
                console.error('Error loading post details');
            }
        });
    }

    function toggleReplies(commentId) {
        const repliesRow = $('#replies-' + commentId);
        const icon = $('#icon-' + commentId);
        
        if (repliesRow.length) {
            // Replies already loaded, just toggle visibility
            repliesRow.toggle();
            icon.toggleClass('fa-chevron-right fa-chevron-down');
        } else {
            // Load replies from server
            $.ajax({
                url: "{{ route('admin.forums.comment.replies', '') }}/" + commentId,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.replies.length > 0) {
                        const parentRow = $('button[onclick="toggleReplies(' + commentId + ')"]').closest('tr');
                        let repliesHtml = '<tr id="replies-' + commentId + '" class="replies-row"><td colspan="6" style="background:#f9fafb;padding:0;"><div style="padding:20px 20px 20px 60px;"><div style="background:white;border-left:1px solid #dc2626;padding:15px;border-radius:6px;"><h4 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:15px;">Replies to this comment:</h4>';
                        
                        response.replies.forEach(reply => {
                            const img = reply.alumni_image || "{{ asset('images/avatar/blank.png') }}";
                            const name = reply.alumni_name || 'Unknown';
                            const message = reply.message || '';
                            const time = reply.created_at || '';
                            
                            repliesHtml += `
                                <div style="display:flex;gap:12px;padding:12px;border-bottom:1px solid #e5e7eb;background:#fafafa;border-radius:6px;margin-bottom:10px;">
                                    <img src="${img}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                    <div style="flex:1;">
                                        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:6px;">
                                            <span style="font-weight:600;color:#111827;font-size:13px;">${name}</span>
                                            <span style="color:#6b7280;font-size:11px;">${time}</span>
                                        </div>
                                        <p style="color:#374151;font-size:13px;margin:0;line-height:1.5;">${message}</p>
                                    </div>
                                </div>
                            `;
                            
                            // Add nested child replies if they exist
                            if (reply.child_replies && reply.child_replies.length > 0) {
                                repliesHtml += `<div style="margin-left:44px;border-left:2px solid #e5e7eb;padding-left:16px;">`;
                                
                                reply.child_replies.forEach(childReply => {
                                    const childImg = childReply.alumni_image || "{{ asset('images/avatar/blank.png') }}";
                                    const childName = childReply.alumni_name || 'Unknown';
                                    const childMessage = childReply.message || '';
                                    const childTime = childReply.created_at || '';
                                    
                                    repliesHtml += `
                                        <div style="display:flex;gap:10px;padding:10px;border-bottom:1px solid #f3f4f6;background:#f9fafb;border-radius:4px;margin-bottom:8px;">
                                            <img src="${childImg}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                            <div style="flex:1;">
                                                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:4px;">
                                                    <span style="font-weight:600;color:#111827;font-size:12px;">${childName}</span>
                                                    <span style="color:#6b7280;font-size:10px;">${childTime}</span>
                                                </div>
                                                <p style="color:#374151;font-size:12px;margin:0;line-height:1.4;">${childMessage}</p>
                                            </div>
                                        </div>
                                    `;
                                });
                                
                                repliesHtml += `</div>`;
                            }
                        });
                        
                        repliesHtml += '</div></div></td></tr>';
                        parentRow.after(repliesHtml);
                        icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                    }
                },
                error: function(xhr) {
                    showToast('Error loading replies', 'error');
                }
            });
        }
    }

    function deleteComment(commentId) {
        confirmBox('Delete Comment','By deleting this comment, it will be removed from the post.', function() {
            $.ajax({
                url: "{{ route('admin.forums.comment.delete', '') }}/" + commentId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showToast('Comment deleted successfully');
                    $('#dataTable').DataTable().ajax.reload();
                    loadPostDetails(); 
                },
                error: function(xhr) {
                    showToast('Error deleting comment', 'error');
                }
            });
        });

    }
</script>
@endpush
@endsection
