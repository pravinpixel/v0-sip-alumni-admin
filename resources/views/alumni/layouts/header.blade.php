<style>
    .top-header {
        background: white;
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 101;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .header-logo img {
        height: 40px;
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
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
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
        display: none;
        background: transparent;
        border: none;
        font-size: 24px;
        color: #6b7280;
        cursor: pointer;
        padding: 8px;
    }

    @media (max-width: 768px) {
        .top-header {
            padding: 12px 20px;
            flex-direction: column;
            gap: 12px;
        }

        .header-logo img {
            height: 32px;
        }

        .user-info {
            display: block;
        }

        .user-avatar {
            width: 50px !important;
            height: 50px !important;
        }

        .menu-toggle {
            display: block;
        }

        .top-header>div:nth-child(2) {
            position: static !important;
            transform: none !important;
        }
    }
</style>

<!-- Header -->
<div class="top-header" > <!-- Left: Menu Toggle + Logo -->
    <div style="display: flex; align-items: center; gap: 15px;">
        <button class="menu-toggle" id="menuToggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="header-logo">
            <img src=" {{ asset('images/logo/sip_logo.png') }}" alt="Logo">
        </div>
    </div>

    <!-- Center: User Profile -->
    <div
        style="position: absolute; left: 50%; transform: translateX(-50%); display: flex; align-items: center; gap: 16px;">
        <img src="{{ $alumni->image ?? asset('images/avatar/blank.png') }}" alt="User" class="user-avatar"
            style="width: 60px; height: 60px; border: 3px solid #e5e7eb;">
        <div style="text-align: left;">
            <div class="user-name" style="font-size: 24px; margin-bottom: 4px;">{{ $alumni->full_name ?? 'Rohit' }}
            </div>
            <span class="user-batch" style="font-size: 16px; padding: 4px 16px;">Batch
                {{ $alumni->year_of_completion ?? '2019' }}</span>
        </div>
    </div>

    <!-- Right: Icons/Actions -->
    <div class="header-right">
        <button style="background: transparent; border: none; cursor: pointer; padding: 8px;"
            onclick="toggleProfileDropdown()">
            <i class="fas fa-user-circle" style="font-size: 24px; color: #6b7280;"></i>
        </button>
    </div>
</div>

<script>
    function toggleProfileDropdown() {
        // Add dropdown functionality here if needed
        console.log('Profile clicked');
    }
</script>