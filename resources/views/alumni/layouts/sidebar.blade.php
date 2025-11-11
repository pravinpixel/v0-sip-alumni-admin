@php
    $alumni = $alumni ?? null;
@endphp

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Profile Section -->
    <div class="profile-section">
        <img src="https://via.placeholder.com/100" 
             alt="Profile" 
             class="profile-img">
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

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }
</script>