<style>
    .alumni-navbar {
        background: white;
        padding: 0;
        display: flex;
        gap: 0;
        align-items: center;
        flex-wrap: wrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .nav-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 28px;
        color: #6b7280;
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        border-bottom: none;
        background: transparent;
        cursor: pointer;
        position: relative;
        box-shadow: none;
    }

    .nav-link i {
        font-size: 18px;
    }

    .nav-link:hover {
        background-color: #f9fafb;
        color: #374151;
    }

    .nav-link.active {
        background-color: #dc2626;
        color: white;
        font-weight: 600;
        border-radius: 8px 8px 0 0;
        border: none;
        box-shadow: none;
        position: relative;
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background-color: #fbbf24;
        border-radius: 0 0 2px 2px;
    }

    .nav-link.active i {
        color: white;
    }

    @media (max-width: 768px) {
        .alumni-navbar {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .nav-link {
            white-space: nowrap;
            padding: 14px 20px;
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