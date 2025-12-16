<style>
    .alumni-navbar {
        background: #ffffffff;
        padding: 6px;
        display: flex;
        gap: 12px;
        align-items: center;
        width: 100%;
        margin: 0;
        box-sizing: border-box;
        border-radius: 8px;
        display: flex;        
        justify-content: space-around;
    }

    .nav-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 6px 20px;
        color: #6b7280;
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        background: transparent;
        cursor: pointer;
        position: relative;
        border-radius: 6px;
        border-bottom: 3px solid transparent;
        flex: 1;
    }

    .nav-link i {
        font-size: 16px;
    }

    .nav-link:hover {
        background-color: #f4f5f7ff;
        color: #fd3324ff;
    }
    .nav-link:hover i {
        scale: 1.1;
    }

    .nav-link.active {
        background: linear-gradient(90deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        font-weight: 600;
        border-bottom: 4px solid #fbbf24;
        border-radius: 6px;
    }

    .nav-link.active i {
        color: white;
        scale: 1;
    }

    /* Medium screens (md) - 768px to 991px */
    @media (min-width: 768px) and (max-width: 991px) {
        .alumni-navbar {
            padding: 8px;
            gap: 8px;
        }

        .nav-link {
            font-size: 13px;
            padding: 8px 15px;
            gap: 8px;
        }
        
        .nav-link i {
            font-size: 14px;
        }
    }

    /* Small screens (sm) - 576px to 767px */
    @media (min-width: 576px) and (max-width: 767px) {
        .alumni-navbar {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding: 10px;
            gap: 8px;
        }

        .nav-link {
            white-space: nowrap;
            font-size: 12px;
            padding: 8px 12px;
            gap: 6px;
            flex: 0 0 auto;
        }
        
        .nav-link i {
            font-size: 13px;
        }
    }

    /* Extra small screens (xs) - <576px */
    @media (max-width: 575px) {
        .alumni-navbar {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding: 6px;
            gap: 2px;
        }

        .nav-link {
            white-space: nowrap;
            font-size: 12px;
            gap: 4px;
            flex: 0 0 auto;
        }
        
        .nav-link i {
            font-size: 10px;
        }

        .nav-link.active {
            border-bottom: 3px solid #fbbf24;
        }
    }

    /* Scrollbar styling for mobile */
    .alumni-navbar::-webkit-scrollbar {
        height: 4px;
    }

    .alumni-navbar::-webkit-scrollbar-track {
        background: #f3f4f6;
    }

    .alumni-navbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 2px;
    }
</style>

<nav class="alumni-navbar">
    <a href="{{ route('alumni.dashboard') }}"
        class="nav-link @if(request()->routeIs('alumni.dashboard')) active @endif">
        <i class="fas fa-th-large"></i>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('alumni.directory') }}"
        class="nav-link @if(request()->routeIs('alumni.directory')) active @endif">
        <i class="fas fa-address-book"></i>
        <span>Directory</span>
    </a>

    <a href="{{ route('alumni.connections') }}"
        class="nav-link @if(request()->routeIs('alumni.connections')) active @endif">
        <i class="fas fa-link"></i>
        <span>Connections</span>
    </a>

    <a href="{{ route('alumni.forums') }}" class="nav-link @if(request()->routeIs('alumni.forums')) active @endif">
        <i class="fas fa-comments"></i>
        <span>Forums</span>
    </a>
</nav>