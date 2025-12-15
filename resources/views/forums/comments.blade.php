@extends('layouts.index')
@section('title', 'Post Comments - Alumni Tracking')

@section('content')
<div style="margin-bottom: 30px;">
    <!-- Back Button and Header -->
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
        <button onclick="window.history.back()" style="background: transparent; border: none; cursor: pointer; padding: 8px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: background 0.2s;"
            onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-arrow-left" style="font-size: 20px; color: #374151;"></i>
        </button>
        <div>
            <h1 id="postTitleHeader" style="font-size: 28px; font-weight: 700; color: #333; margin: 0;">Discussion Topic connections with the Alumni Peoples</h1>
            <p style="color: #666; font-size: 14px; margin: 4px 0 0 0;">
                View and manage all comments on this post
            </p>
        </div>
    </div>

    <!-- Post Details Card -->
    <div style="background-color: #fff; padding: 32px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <!-- Post Title Section -->
        <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e5e7eb;">
            <label style="display: block; font-weight: 700; font-size: 11px; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Post Title</label>
            <div id="postTitle" style="font-size: 20px; font-weight: 600; color: #111827; line-height: 1.4;"></div>
        </div>

        <!-- Post Description Section -->
        <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e5e7eb;">
            <label style="display: block; font-weight: 700; font-size: 11px; color: #6b7280; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Post Description</label>
            <div id="postDescription" style="font-size: 14px; color: #4b5563; line-height: 1.7;"></div>
        </div>

        <!-- Labels Section -->
        <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e5e7eb;">
            <label style="display: block; font-weight: 700; font-size: 11px; color: #6b7280; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Labels</label>
            <div id="postLabels" style="display: flex; gap: 8px; flex-wrap: wrap;">
                <!-- Labels will be added here -->
            </div>
        </div>

        <!-- Post Stats -->
        <div style="display: flex; gap: 32px; flex-wrap: wrap; align-items: center;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-heart" style="color: #ef4444; font-size: 20px;"></i>
                    <span id="likesCount" style="font-weight: 700; color: #111827; font-size: 18px;">0</span>
                </div>
                <span style="color: #6b7280; font-size: 12px; font-weight: 500;">Likes</span>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-comment" style="color: #3b82f6; font-size: 20px;"></i>
                    <span id="commentsCount" style="font-weight: 700; color: #111827; font-size: 18px;">0</span>
                </div>
                <span style="color: #6b7280; font-size: 12px; font-weight: 500;">Comments</span>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-eye" style="color: #10b981; font-size: 20px;"></i>
                    <span id="viewsCount" style="font-weight: 700; color: #111827; font-size: 18px;">0</span>
                </div>
                <span style="color: #6b7280; font-size: 12px; font-weight: 500;">Views</span>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-thumbtack" style="color: #f59e0b; font-size: 20px;"></i>
                    <span id="awardsCount" style="font-weight: 700; color: #111827; font-size: 18px;">0</span>
                </div>
                <span style="color: #6b7280; font-size: 12px; font-weight: 500;">Pinned</span>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 700; color: #333; margin: 0;">Comments (<span id="totalComments">0</span>)</h2>
            <div style="position: relative; flex: 1; max-width: 400px; margin-left: 20px;">
                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                <input type="text" id="searchComments" placeholder="Search comments..."
                    style="width: 100%; padding: 8px 15px 8px 40px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>
        </div>

        <!-- Comments Table -->
        <table id="commentsTable" class="display" style="width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 6px; border: 1px solid #e0e0e0;">
            <thead>
                <tr style="background: #ba0028; color: white; font-weight: 700; font-size: 12px;">
                    <th style="padding: 15px; text-align: left;">Alumni Profile</th>
                    <th style="padding: 15px; text-align: left;">Alumni Name</th>
                    <th style="padding: 15px; text-align: left;">Comment</th>
                    <th style="padding: 15px; text-align: left;">Time Commented</th>
                    <th style="padding: 15px; text-align: left;">Threads</th>
                    <th style="padding: 15px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<style>
    #commentsTable tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        box-sizing: border-box;
        border-bottom: 1px solid #f0f0f0;
    }

    #commentsTable thead th {
        border-bottom: 2px solid #e0e0e0;
    }

    table.dataTable th,
    table.dataTable td {
        white-space: nowrap;
    }

    .dataTables_wrapper {
        margin-top: 25px !important;
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
        const table = $('#commentsTable').DataTable({
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
            dom: 't<"row mt-10"<"col-6 dt-info-custom"><"col-6 dt-pagination-custom text-end">>',
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ comments"
            }
        });

        table.on('draw', function() {
            let info = table.page.info();

            $(".dt-info-custom").html(
                `Showing ${info.start + 1} to ${info.end} comments of ${info.recordsTotal}`
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
        $('#searchComments').on('keyup', function() {
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
        confirmBox('By deleting this comment, it will be removed from the post.', function() {
            $.ajax({
                url: "{{ route('admin.forums.comment.delete', '') }}/" + commentId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showToast('Comment deleted successfully');
                    $('#commentsTable').DataTable().ajax.reload();
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
