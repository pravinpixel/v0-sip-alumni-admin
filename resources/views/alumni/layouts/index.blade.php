<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Alumni Dashboard</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo/favicon.png') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/alumni/style.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .main-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .header {
            width: 100%;
            background-color: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            height: 70px;
        }

        /* Main Layout */
        .body-wrapper {
            display: flex;
            flex: 1;
        }

        /* Sidebar - Fixed position version */
        .sidebar {
            width: 250px;
            /* background: white; */
            /* border-right: 1px solid #e5e7eb; */
            position: fixed;
            left: 0;
            top: 127px;
            /* Below header */
            bottom: 0;
            overflow-y: auto;
            z-index: 10;
            display: flex;
            padding: 20px 0;
            flex-direction: column;
            transition: left 0.3s ease, width 0.3s ease;
            height: 96%;
        }

        /* Sidebar collapsed state - mini sidebar */
        .sidebar.collapsed {
            width: 80px;
            overflow: visible;
        }

        /* Profile Section in Sidebar */
        .profile-section {
            text-align: center;
            padding: 10px;
        }

        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 15px;
            border: 3px solid #f3f4f6;
        }

        .profile-name {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .profile-info {
            padding: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 4px 0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 20px;
            height: 20px;
            color: #ef4444;
            font-size: 13px;
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            display: block;
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 3px;
        }

        .info-value {
            color: #1f2937;
            font-weight: 500;
            font-size: 16px;
            line-height: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 170px;
            padding-bottom: 4px;
        }

        .edit-profile-btn {
            margin: 0 20px 50px 20px;
            padding: 8px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .edit-profile-btn:hover {
            background: #b91c1c;
        }

        /* Main Content Area - Adjusted for fixed sidebar */
        .main-content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: 250px;
            /* Make space for fixed sidebar */
            min-width: 0;
        }

        /* Navbar */
        .navbar {
            background-color: #f5f5f5;
            padding: 15px 30px;
            flex-shrink: 0;
        }

        /* Content */
        .content {
            flex: 1;
            padding: 30px;
            background-color: #f5f5f5;
            overflow-y: auto;
            min-height: calc(100vh - 140px);
            /* Account for header + navbar */
        }

        /* Scrollbar styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .announcements-global-container {
            z-index: 100;
        }

        .announcement-content {
            display: inline-block;
            background: linear-gradient(30deg, #E2001D 0%, #FCD116 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 600;
        }


        .announcement-banner {
            background:  #faebed;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Announcements Styles */
        @keyframes scroll-announcements {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }
        
        .announcement-scroll-content {
            display: flex;
            align-items: center;
            top: 10px;
        }
        
        .announcement-item {
            font-size: 16px;
            font-weight: 500;
            margin-right: 80px;
            white-space: nowrap;
        }
        
        .announcement-banner:hover .announcement-scroll-content {
            animation-play-state: paused;
        }
        
        
        .announcement-scroll-content.paused {
            animation-play-state: paused;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {

            .header {
                padding: 12px 20px;
                height: 60px;
            }

            .sidebar {
                width: 230px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                top: 60px;
                height: 98%;
                overflow: hidden;
            }

            .profile-img {
                width: 70px;
                height: 70px;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 60px;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .main-content-area {
                margin-left: 0;
                width: 100%;
            }

            .navbar {
                padding: 12px 20px;
                top: 10px;
            }

            .content {
                padding: 20px;
                min-height: calc(100vh - 120px);
            }

            .info-label {
                font-size: 10px;
            }

            .info-value {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <!-- Header -->
        @include('alumni.layouts.header')

        @include('alumni.layouts.announcement_banner')
        <!-- Main Layout -->
        <div class="body-wrapper">
            <!-- Sidebar -->
            <div class="sidebar">
                @include('alumni.layouts.sidebar')
            </div>

            <!-- Sidebar Overlay for Mobile -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>

            <!-- Main Content Area -->
            <div class="main-content-area">
                <!-- Navbar -->
                @if(!request()->routeIs('alumni.forums.activity'))
                <div class="navbar">
                    @include('alumni.layouts.navbar')
                </div>
                @endif

                <!-- Content -->
                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('/js/alumniCommon.js') }}"></script>

    <script>
        // Disable back button after logout
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, null, window.location.href);
        };
    </script>



    <script>
        // Mobile sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const menuToggle = document.getElementById('menuToggle'); // Add this to your header

            // Function to open sidebar
            function openSidebar() {
                sidebar.classList.add('active');
                sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            // Function to close sidebar
            function closeSidebar() {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            // Toggle sidebar when menu button is clicked
            if (menuToggle) {
                menuToggle.addEventListener('click', openSidebar);
            }

            // Close sidebar when overlay is clicked
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 &&
                    !event.target.closest('.sidebar') &&
                    !event.target.closest('#menuToggle')) {
                    closeSidebar();
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>