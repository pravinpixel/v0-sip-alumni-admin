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
    <!-- Profile Section -->
    <div class="profile-section" style="position: relative;">
        <button class="close-btn" onclick="toggleSidebar()">×</button>
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
    }

    /* Ensure sidebar has clean white background */
    .sidebar {
        background: white !important;
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