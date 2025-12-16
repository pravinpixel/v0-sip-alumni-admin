<div id="threadModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 1000; overflow-y: auto; padding: 20px; z-index: 1000;">
    <div style="background: white; border-radius: 12px; max-width: 700px; margin: 20px auto; padding: 0; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);">

        

        {{-- Thread Content --}}
        <div style="padding: 24px;">
            {{-- Original Post --}}
            <div style="margin-bottom: 4px; border-bottom: 1px solid #e5e7eb; padding-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 id="threadTitle" style="font-size: 20px; font-weight: 700; color: #dc2626; margin: 0 0 16px 0;"></h2>
                    <button onclick="closeThreadModal()" style="background: transparent; border: none; font-size: 14px; cursor: pointer; color: #6b7280;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 16px;border-bottom: 1px solid #e5e7eb;">
                    <div id="threadAuthorAvatar" style="width: 40px; height: 40px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700;"></div>
                    <div>
                        <p id="threadAuthor" style="font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 2px 0;"></p>
                        <p id="threadDate" style="font-size: 12px; color: #6b7280; margin: 0;"></p>
                    </div>
                </div>

                <p id="threadDescription" style="color: #6b7280; font-size: 15px; line-height: 1.6; margin: 10px 0 16px 0; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;"></p>

                <div id="threadTags" style="display: flex; gap: 8px; flex-wrap: wrap;"></div>
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
            <div id="noCommentsMessage" style="display: none; text-align: center; padding: auto; color: #6b7280;">
                <i class="fas fa-comments" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                <p style="font-size: 14px; margin: 0;">No comments yet. Be the first to reply!</p>
            </div>

            {{-- Reply Input Section --}}
            <div id="replySection">
                {{-- Replying To Indicator --}}
                <div id="replyingToIndicator" style="display: none; border: 1px solid #fff9a1ff; background: #fff4f6ff; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <span style="font-size: 14px; color: #374151; font-weight: 600;">
                            <i class="fa-solid fa-arrow-turn-up fa-rotate-270 fa-sm me-1 text-danger"></i> Replying to <span id="replyingToName" style="color: #dc2626;"></span>
                        </span>
                        <button onclick="cancelReply()" class="btn hover:text-danger" style="background: transparent; border: none; color: #1e1f20ff; cursor: pointer; font-size: 14px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 6px;">
                        <!-- <input
                            type="text"
                            placeholder="Write your reply..."
                            id="replyInput"
                            style="width: 100%; padding: 8px 10px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none;"
                            onfocus="this.style.borderColor='#dc2626'"
                            onblur="this.style.borderColor='#e5e7eb'"> -->
                        <textarea
                            id="replyInput"
                            placeholder="Write your reply..."
                            rows="1"
                            class="py-2 px-3"
                            style="width:100%;
                                border:2px solid #e5e7eb;
                                border-radius:8px;
                                font-size:14px;
                                outline:none;
                                resize:none;
                                overflow:hidden;"
                            oninput="this.style.height='auto'; this.style.height=this.scrollHeight+'px';"
                            onfocus="this.style.borderColor='#dc2626'"
                            onblur="this.style.borderColor='#e5e7eb'"></textarea>
                            <button
                                onclick="submitThreadReply()"
                                class="py-2 px-3"
                                style="background: linear-gradient(90deg, #E2001D, #B1040E); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;"
                                onmouseover="this.style.background='linear-gradient(90deg, #B1040E, #E2001D)'"
                                onmouseout="this.style.background='linear-gradient(90deg, #E2001D, #B1040E)'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send h-2 w-2"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                                Post
                            </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #noCommentsMessage {
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

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

    .comment-item.highlighted {
        background: #fff7ed;
        border-left: 3px solid #f59e0b;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
    }

    .replying-indicator {
        background: #fef3c7;
        color: #92400e;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 8px;
        display: inline-block;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        #threadModal {
            padding: 10px !important;
        }

        #threadModal > div {
            max-width: 85% !important;
            margin: 10px auto !important;
            max-height: 95vh !important;
            border-radius: 8px !important;
        }

        /* Modal content padding */
        #threadModal > div > div {
            padding: 16px !important;
        }

        /* Thread title */
        #threadTitle {
            font-size: 18px !important;
            margin-bottom: 12px !important;
            line-height: 1.3 !important;
        }

        /* Close button */
        #threadModal button[onclick="closeThreadModal()"] {
            font-size: 16px !important;
            padding: 8px !important;
        }

        /* Author section */
        #threadAuthorAvatar {
            width: 36px !important;
            height: 36px !important;
            font-size: 12px !important;
        }

        #threadAuthor {
            font-size: 13px !important;
        }

        #threadDate {
            font-size: 11px !important;
        }

        /* Thread description */
        #threadDescription {
            font-size: 14px !important;
            line-height: 1.5 !important;
            margin: 8px 0 12px 0 !important;
        }

        /* Tags */
        #threadTags span {
            font-size: 10px !important;
            padding: 4px 8px !important;
        }

        /* Comments heading */
        #commentsHeading {
            font-size: 15px !important;
            margin-bottom: 12px !important;
        }

        /* Comment items */
        .comment-item {
            padding: 12px !important;
            margin-bottom: 12px !important;
        }

        .comment-item p {
            font-size: 13px !important;
            line-height: 1.4 !important;
        }

        .comment-item .comment-author {
            font-size: 12px !important;
        }

        .comment-item .comment-date {
            font-size: 10px !important;
        }

        /* Reply section */
        #replySection {
            padding-top: 16px !important;
        }

        #replyingToIndicator {
            padding: 10px 12px !important;
            margin-bottom: 12px !important;
        }

        #replyingToIndicator span {
            font-size: 12px !important;
        }

        /* Current user avatar in reply */
        #currentUserAvatar {
            width: 36px !important;
            height: 36px !important;
            font-size: 12px !important;
        }

        /* Reply input */
        #replyInput {
            padding: 10px 12px !important;
            font-size: 13px !important;
            margin-bottom: 10px !important;
        }

        /* Reply buttons */
        #replySection button {
            padding: 8px 16px !important;
            font-size: 13px !important;
        }

        /* Stack buttons vertically on very small screens */
        #replySection > div > div > div:last-child {
            flex-direction: column !important;
            gap: 8px !important;
        }

        #replySection > div > div > div:last-child button {
            width: 100% !important;
            justify-content: center !important;
        }

        /* No comments message */
        #noCommentsMessage {
            padding: 30px 15px !important;
        }

        #noCommentsMessage i {
            font-size: 36px !important;
            margin-bottom: 8px !important;
        }

        #noCommentsMessage p {
            font-size: 13px !important;
        }
    }

    @media (max-width: 480px) {
        #threadModal {
            padding: 5px !important;
        }

        #threadModal > div {
            margin: 5px auto !important;
            border-radius: 6px !important;
            max-width: 96% !important;
        }

        /* Modal content padding */
        #threadModal > div > div {
            padding: 12px !important;
        }

        /* Thread title */
        #threadTitle {
            font-size: 16px !important;
            margin-bottom: 10px !important;
        }

        /* Author section */
        #threadAuthorAvatar {
            width: 32px !important;
            height: 32px !important;
            font-size: 11px !important;
        }

        #threadAuthor {
            font-size: 12px !important;
        }

        #threadDate {
            font-size: 10px !important;
        }

        /* Thread description */
        #threadDescription {
            font-size: 13px !important;
            margin: 6px 0 10px 0 !important;
        }

        /* Tags */
        #threadTags span {
            font-size: 9px !important;
            padding: 3px 6px !important;
        }

        /* Comments heading */
        #commentsHeading {
            font-size: 14px !important;
            margin-bottom: 10px !important;
        }

        /* Comment items */
        .comment-item {
            padding: 10px !important;
        }

        .comment-item p {
            font-size: 12px !important;
        }

        .comment-item .comment-author {
            font-size: 11px !important;
        }

        .comment-item .comment-date {
            font-size: 9px !important;
        }

        /* Current user avatar in reply */
        #currentUserAvatar {
            width: 32px !important;
            height: 32px !important;
            font-size: 11px !important;
        }

        /* Reply input */
        #replyInput {
            padding: 8px 10px !important;
            font-size: 12px !important;
        }

        /* Reply buttons */
        #replySection button {
            padding: 8px 14px !important;
            font-size: 12px !important;
        }
    }
</style>
