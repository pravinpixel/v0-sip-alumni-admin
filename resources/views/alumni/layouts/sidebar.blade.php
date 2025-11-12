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

    <!-- Profile Info -->
    <div class="profile-info">
        <div class="profile-section" style="position: relative;">
            <button class="close-btn" onclick="closeSidebar()">×</button>
            <img src="{{ $alumni->image ?? asset('images/avatar/blank.png') }}"
                alt="Profile"
                class="profile-img">
            <div class="profile-name">{{ $alumni->full_name ?? '-' }}</div>
        </div>
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
        <button class="edit-profile-btn" onclick="alert('Edit Profile clicked')">
            <i class="fa fa-pencil"></i>
            Edit Profile
        </button>
    </div>

    <!-- Edit Profile Button -->
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
}



</style>

<script>
function closeSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    sidebar.classList.remove('active');
    sidebarOverlay.classList.remove('active');
    document.body.style.overflow = '';
}
</script>
