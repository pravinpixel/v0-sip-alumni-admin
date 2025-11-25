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
        background-color: #e5e7eb;
        color: #374151;
        border-bottom: 3px solid #fbbf24;
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
    }

    @media (max-width: 768px) {
        .alumni-navbar {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding: 15px 20px;
            gap: 12px;
        }

        .nav-link {
            white-space: nowrap;
            padding: 10px 20px;
            font-size: 14px;
        }
    }
</style>

<nav class="alumni-navbar">
    <a href="{{ route('alumni.dashboard') }}"
        class="nav-link @if(request()->routeIs('alumni.dashboard')) active @endif">
        <i class="fas fa-th-large"></i>
        Dashboard
    </a>

    <a href="{{ route('alumni.directory') }}"
        class="nav-link @if(request()->routeIs('alumni.directory')) active @endif">
        <i class="fas fa-address-book"></i>
        Directory
    </a>

    <a href="{{ route('alumni.connections') }}"
        class="nav-link @if(request()->routeIs('alumni.connections')) active @endif">
        <i class="fas fa-link"></i>
        Connections
    </a>

    <a href="{{ route('alumni.forums') }}" class="nav-link @if(request()->routeIs('alumni.forums')) active @endif">
        <i class="fas fa-comments"></i>
        Forums
    </a>
</nav>