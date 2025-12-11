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
            <div id="noCommentsMessage" style="display: none; text-align: center; padding: 40px 20px; color: #6b7280;">
                <i class="fas fa-comments" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                <p style="font-size: 14px; margin: 0;">No comments yet. Be the first to reply!</p>
            </div>

            {{-- Reply Input Section --}}
            <div id="replySection" style="border-top: 1px solid #e5e7eb; padding-top: 24px;">
                {{-- Replying To Indicator --}}
                <div id="replyingToIndicator" style="display: none; background: #f3f4f6; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #dc2626;">
                    <div style="display: flex; justify-content: between; align-items: center;">
                        <span style="font-size: 14px; color: #374151; font-weight: 600;">
                            Replying to <span id="replyingToName" style="color: #dc2626;"></span>
                        </span>
                        <button onclick="cancelReply()" style="background: transparent; border: none; color: #6b7280; cursor: pointer; font-size: 14px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div id="currentUserAvatar" style="width: 40px; height: 40px; border-radius: 50%; background: #dc2626; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; flex-shrink: 0;">
                        <!-- Will be populated by JavaScript -->
                    </div>
                    <div style="flex: 1;">
                        <input
                            type="text"
                            placeholder="Write your reply..."
                            id="replyInput"
                            style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none; margin-bottom: 12px;"
                            onfocus="this.style.borderColor='#dc2626'"
                            onblur="this.style.borderColor='#e5e7eb'">

                        <div style="display: flex; justify-content: flex-end; gap: 12px;">
                            <button
                                onclick="closeThreadModal()"
                                style="background: white; color: #374151; border: 2px solid #e5e7eb; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.background='#f3f4f6'"
                                onmouseout="this.style.background='white'">
                                Cancel
                            </button>
                            <button
                                onclick="submitThreadReply()"
                                style="background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;"
                                onmouseover="this.style.background='#b91c1c'"
                                onmouseout="this.style.background='#dc2626'">
                                <i class="fas fa-paper-plane"></i>
                                Post Reply
                            </button>
                        </div>
                    </div>
                </div>
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
</style>
