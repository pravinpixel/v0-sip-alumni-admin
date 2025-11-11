<style>
    .alumni-navbar {
        background: linear-gradient(90deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 20px;
        padding: 12px;
        margin: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        border: 1px solid #e9ecef;
    }

    .nav-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        color: #555;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        border-radius: 12px;
        transition: all 0.3s ease;
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .nav-link:hover {
        background-color: #f0f0f0;
        color: #333;
    }

    /* Active tab now has red background with white text */
    .nav-link.active {
        background-color: #d63031;
        color: white;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(214, 48, 49, 0.3);
    }

    @media (max-width: 768px) {
        .alumni-navbar {
            flex-direction: column;
            align-items: stretch;
        }

        .nav-link {
            justify-content: center;
        }
    }
</style>

<nav class="alumni-navbar">
    <!-- Dashboard link with active state -->
    <a href="{{ route('alumni.dashboard') }}" class="nav-link @if(request()->routeIs('alumni.dashboard')) active @endif">
        <span>📊</span>
        Dashboard
    </a>

    <!-- Directory link with active state -->
    <a href="{{ route('alumni.directory') }}" class="nav-link @if(request()->routeIs('alumni.directory')) active @endif">
        <span>👥</span>
        Directory
    </a>

    <!-- Connections link with active state -->
    <a href="{{ route('alumni.connections') }}" class="nav-link @if(request()->routeIs('alumni.connections')) active @endif">
        <span>🤝</span>
        Connections
    </a>

    <!-- Forums link with active state -->
    <a href="{{ route('alumni.forums') }}" class="nav-link @if(request()->routeIs('alumni.forums')) active @endif">
        <span>💬</span>
        Forums
    </a>
</nav>
