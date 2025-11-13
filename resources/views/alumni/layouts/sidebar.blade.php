@php
    $alumni = $alumni ?? null;
    $city = $alumni && isset($alumni->city) ? $alumni->city : null;
    $state = $city && isset($city->state) ? $city->state : null;
    $occupation = $alumni && isset($alumni->occupation) ? $alumni->occupation : null;
@endphp

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Drawer Toggle Button -->
    <button class="drawer-toggle" id="drawerToggle" onclick="toggleDrawer()">
        <i class="fas fa-chevron-left"></i>
    </button>

    <!-- Profile Section -->
    <div class="profile-section" style="position: relative;">
        <button class="close-btn" onclick="closeSidebar()">×</button>
        <img src="{{ $alumni->image ?? asset('images/avatar/blank.png') }}" alt="Profile" class="profile-img">
        <div class="profile-name">{{ $alumni->full_name ?? '-' }}</div>
    </div>

    <!-- Profile Info -->
    <div class="profile-info">
        <div class="info-item">
            <i class="fa fa-graduation-cap info-icon"></i>
            <div class="info-content">
                <span class="info-label">Year of Completion</span>
                <div class="info-value">{{ $alumni->year_of_completion ?? '-' }}</div>
            </div>
        </div>

        <div class="info-item">
            <i class="fa fa-map-marker info-icon"></i>
            <div class="info-content">
                <span class="info-label">Location</span>
                <div class="info-value">{{ $state->name ?? '-' }}, {{ $city->name ?? '-' }}</div>
            </div>
        </div>

        <div class="info-item">
            <i class="fa fa-envelope info-icon"></i>
            <div class="info-content">
                <span class="info-label">Email Address</span>
                <div class="info-value">{{ $alumni->email ?? '-' }}</div>
            </div>
        </div>

        <div class="info-item">
            <i class="fa fa-phone info-icon"></i>
            <div class="info-content">
                <span class="info-label">Contact Number</span>
                <div class="info-value">{{ $alumni->mobile_number ?? '-' }}</div>
            </div>
        </div>

        <div class="info-item">
            <i class="fa fa-briefcase info-icon"></i>
            <div class="info-content">
                <span class="info-label">Current Occupation</span>
                <div class="info-value">{{ $occupation->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Button -->
    <button class="edit-profile-btn" onclick="alert('Edit Profile clicked')">
        <i class="fa fa-pencil"></i>
        Edit Profile
    </button>
</div>

<style>
    /* Drawer Toggle Button */
    .drawer-toggle {
        position: absolute;
        top: 50%;
        right: -15px;
        transform: translateY(-50%);
        width: 30px;
        height: 60px;
        background: #dc2626;
        border: none;
        border-radius: 0 8px 8px 0;
        color: white;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1001;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .drawer-toggle:hover {
        background: #b91c1c;
        right: -18px;
    }

    .drawer-toggle i {
        transition: transform 0.3s ease;
    }

    .sidebar.collapsed .drawer-toggle i {
        transform: rotate(180deg);
    }

    #sidebar.collapsed {
        width: 80px !important;
        overflow: visible !important;
        transition: width 0.3s ease !important;
    }

    #sidebar.collapsed .profile-section {
        padding: 30px 10px 10px;
    }

    #sidebar.collapsed .profile-img {
        width: 55px;
        height: 55px;
        margin: 0 auto 5px;
    }

    #sidebar.collapsed .profile-name,
    #sidebar.collapsed .info-item,
    #sidebar.collapsed .profile-info {
        display: none;
    }

    #sidebar.collapsed .edit-profile-btn {
        width: 35px;
        height: 35px;
        margin: 10px auto 0;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0;
        position: relative;
    }

    #sidebar.collapsed .edit-profile-btn i {
        font-size: 14px;
        margin: 0;
    }

    #sidebar.collapsed .drawer-toggle {
        right: -15px;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: white;
        border: 2px solid #ef4444;
        color: #ef4444;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        line-height: 26px;
        text-align: center;
        z-index: 1000;
        display: none;
    }

    @media (max-width: 768px) {
        .close-btn {
            display: block;
        }

        .drawer-toggle {
            display: none;
        }
    }

    /* Ensure sidebar has clean white background */
    #sidebar {
        background: white !important;
        transition: left 0.3s ease !important;
    }

    /* Ensure edit button is red, not yellow */
    .edit-profile-btn {
        background: #dc2626 !important;
        border: none !important;
    }

    .edit-profile-btn:hover {
        background: #b91c1c !important;
    }

    /* Remove any yellow/orange tints */
    .info-item {
        background: transparent !important;
    }
</style>

<script>
    function toggleDrawer() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content-area');

        console.log('Toggle drawer clicked', sidebar, mainContent);

        if (!sidebar) {
            console.error('Sidebar not found');
            return;
        }

        const isCollapsed = sidebar.classList.contains('collapsed');

        if (isCollapsed) {
            // Expand sidebar
            sidebar.classList.remove('collapsed');
            sidebar.style.width = '250px';
            sidebar.style.padding = '20px 0';
            if (mainContent) {
                mainContent.style.marginLeft = '250px';
            }
        } else {
            // Collapse sidebar to mini version
            sidebar.classList.add('collapsed');
            sidebar.style.width = '80px';
            sidebar.style.padding = '20px 0';
            if (mainContent) {
                mainContent.style.marginLeft = '80px';
            }
        }

        console.log('Sidebar collapsed:', !isCollapsed, 'Width:', sidebar.style.width);
    }

    function closeSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebar) sidebar.classList.remove('active');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
</script>