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
        }

        .header-logo img {
            height: 32px;
        }

        .user-info {
            display: none;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
        }

        .menu-toggle {
            display: block;
        }
    }
</style>

<!-- Header -->
<div class="top-header">
    <!-- Left: Logo -->
    <div class="header-logo">
        <img src="{{ asset('images/logo/sip_logo.png') }}" alt="Logo">
    </div>

    <!-- Right: User Profile -->
    <div class="header-right">
        <div class="user-profile" onclick="toggleProfileDropdown()">
            <div class="user-info">
                <div class="user-name">Rohit</div>
                <span class="user-batch">Batch 2019</span>
            </div>
            <img src="https://via.placeholder.com/45" alt="User" class="user-avatar">
        </div>
    </div>
</div>

<script>
    function toggleProfileDropdown() {
        // Add dropdown functionality here if needed
        console.log('Profile clicked');
    }
</script>