<style>
    .top-header {
        background: white;
        padding: 15px 30px 5px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .header-logo img {
        height: 60px;
        width: auto;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .user-profile:hover {
        background: #f9fafb;
    }

    .user-info {
        text-align: right;
    }

    .user-name {
        font-weight: 600;
        font-size: 14px;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .user-batch {
        background: #fef2f2;
        color: #dc2626;
        padding: 2px 10px;
        border-radius: 14px;
        font-size: 11px;
        font-weight: 700;
        display: inline-block;
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f3f4f6;
    }

    .menu-toggle {
        display: none !important;
        background: transparent;
        border: none;
        font-size: 24px;
        color: #6b7280;
        cursor: pointer;
        padding: 8px;
    }

    @media (max-width: 768px) {
        .top-header {
            padding: 12px 15px;
        }

        .header-logo img {
            height: 40px;
        }

        .user-info {
            display: none;
        }

        .user-avatar {
            width: 40px !important;
            height: 40px !important;
        }

        .menu-toggle {
            display: none !important;
        }

        .header-right {
            gap: 8px;
        }

        .top-header>div:nth-child(2) {
            position: static !important;
            transform: none !important;
        }

        #profileDropdown {
            top: 60px;
            right: 10px;
        }
    }

    @media (max-width: 480px) {
        .top-header {
            padding: 10px 12px;
        }

        .header-logo img {
            height: 35px;
        }

        .user-avatar {
            width: 36px !important;
            height: 36px !important;
        }

        .menu-toggle {
            font-size: 20px;
            padding: 6px;
        }
    }
</style>

<!-- Header -->
<div class="top-header"> <!-- Left: Menu Toggle + Logo -->
    <div style="display: flex; align-items: center; gap: 15px;">
        <button class="menu-toggle" id="menuToggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="header-logo">
            <a href="{{ route('alumni.dashboard') }}"><img src=" {{ asset('images/logo/sip_logo.png') }}" alt="Logo"></a>
        </div>
    </div>

    <!-- Center: User Profile -->
    <div
        style="position: absolute; left: 50%; transform: translateX(-50%); display: flex; align-items: center; gap: 16px;">
    </div>

    <!-- Right: User Profile -->
    <div class="header-right" style="cursor: pointer; gap: 12px;" onclick="toggleProfileDropdown()">
        <img src="{{ $alumni->image_url ?? asset('images/avatar/blank.png') }}" alt="User" class="user-avatar"
            style="width: 45px; height: 45px; border: 2px solid #e5e7eb;">
        <div style="text-align: left;">
            <div class="user-name" style="font-size: 15px; margin-bottom: 2px;">{{ $alumni->full_name ?? 'Rohit' }}
            </div>
            <span class="user-batch" style="font-size: 12px; padding: 3px 10px;">Batch
                {{ $alumni->year_of_completion ?? '-' }}</span>
        </div>
    </div>
</div>
<div id="profileDropdown"
    class="dropdown-menu dropdown-menu-end shadow"
    style="position: absolute; top: 70px; right: 20px; display: none; border-radius: 10px; padding: 10px;">

    <a onclick="viewSettingModal()" class="dropdown-item" style="padding: 10px 15px;">
        <i class="fas fa-cog me-2"></i> Settings
    </a>

    <form action="{{ route('alumni.logout') }}" method="POST">
        @csrf
        <button class="dropdown-item text-danger" style="padding: 10px 15px;">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
    </form>

</div>

<!-- SETTINGS MODAL -->
<div id="settingModal"
    style="position:fixed; top:0; left:0; width:100%; height:100%; 
            background:rgba(0,0,0,0.45); display:none; 
            justify-content:center; align-items:center; z-index:9999;">

    <div style="background:white; width:450px; border-radius:12px; 
                padding:20px; box-shadow:0 4px 20px rgba(0,0,0,0.2);">

        <!-- Header -->
        <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:10px; margin-bottom:4px;">
            <div>
                <h2 style="margin:0; font-size:20px; font-weight:600;">Settings</h2>
                <p style="margin:3px 0;color:#666;font-size:12px;">Manage your notification preferences</p>
            </div>
            <button onclick="closeSettingModal()"
                style="background:none; border:none; font-size:16px; cursor:pointer;">
                ✖
            </button>
        </div>

        <!-- Body -->
        <div>

            <!-- Setting 1 -->
            <div class="setting-row" style="display:flex;justify-content:space-between;align-items:center;
                  padding:12px 0;">
                <div>
                    <h4 style="margin:0;font-size:13px;">Receive email notifications for all Admin Approvals</h4>
                    <p style="margin:3px 0;color:#666;font-size:12px;">Get notified when admins approve or reject your requests</p>
                </div>

                <div style="display:flex;align-items:center;">
                    <input type="checkbox" id="adminToggle" style="display:none;"
                        {{ $alumni->notify_admin_approval ? 'checked' : '' }}>

                    <div id="adminSlider" style="width:46px;height:24px;background:#d1d5db;border-radius:20px;
                     position:relative;cursor:pointer;transition:0.3s;">
                        <div id="adminCircle" style="width:20px;height:20px;background:white;border-radius:50%;
                        position:absolute;top:2px;left:2px;transition:0.3s;"></div>
                    </div>
                </div>
            </div>


            <!-- Setting 2 -->
            <div class="setting-row" style="display:flex;justify-content:space-between;align-items:center;
                 padding:12px 0;">
                <div>
                    <h4 style="margin:0;font-size:13px;">Receive email notifications for post comments or updates</h4>
                    <p style="margin:3px 0;color:#666;font-size:12px;">Get notified when someone comments on your posts or replies to your comments</p>
                </div>

                <div style="display:flex;align-items:center;">
                    <input type="checkbox" id="commentToggle" style="display:none;"
                        {{ $alumni->notify_post_comments ? 'checked' : '' }}>

                    <div id="commentSlider" style="width:46px;height:24px;background:#d1d5db;border-radius:20px;
                  position:relative;cursor:pointer;transition:0.3s;">
                        <div id="commentCircle" style="width:20px;height:20px;background:white;border-radius:50%;
                   position:absolute;top:2px;left:2px;transition:0.3s;"></div>
                    </div>
                </div>
            </div>


        </div>

    </div>
</div>




<script>
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const profileBox = document.querySelector('.header-right');
        const dropdown = document.getElementById('profileDropdown');

        if (!profileBox.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    function viewSettingModal() {
        document.getElementById('settingModal').style.display = "flex";
    }


    function closeSettingModal() {
        document.getElementById('settingModal').style.display = "none";
    }

    function initSetting(inputId, sliderId, circleId) {
        const input = document.getElementById(inputId);
        const slider = document.getElementById(sliderId);
        const circle = document.getElementById(circleId);

        loadUI();

        function loadUI() {
            if (input.checked) {
                slider.style.background = "#2563eb"; // blue
                circle.style.transform = "translateX(22px)";
            } else {
                slider.style.background = "#d1d5db"; // grey
                circle.style.transform = "translateX(0)";
            }
        }

        slider.addEventListener("click", function() {
            input.checked = !input.checked;
            loadUI();
            saveAllSettings();
        });
    }

    function saveAllSettings() {
        let adminValue = document.getElementById("adminToggle").checked ? 1 : 0;
        let commentValue = document.getElementById("commentToggle").checked ? 1 : 0;

        $.ajax({
            url: "{{ route('alumni.update.settings') }}",
            type: "POST",
            data: {
                notify_admin_approval: adminValue,
                notify_post_comments: commentValue,
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                console.log("Saved", res);
            },
            error: function(xhr) {
                console.log("Error", xhr.responseText);
            }
        });
    }

    // Initialize both toggles
    initSetting("adminToggle", "adminSlider", "adminCircle");
    initSetting("commentToggle", "commentSlider", "commentCircle");
</script>