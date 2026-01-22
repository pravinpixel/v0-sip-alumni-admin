<style>
    .app-sidebar {
        background-color: #f0f4f9;
        color: #1f2937;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .menu-link {
        color: #1f2937 !important;
        transition: all 0.2s ease-in-out;
    }

    .menu-link .custom_icon {
        color: #1f2937 !important;
    }

    .menu-link:not(.active):hover {
        background-color: #dc2626 !important;
        color: white !important;
        border-radius: 0.65rem;
    }

    .menu-link:not(.active):hover .custom_icon {
        color: white !important;
    }

    .menu-link:not(.active):hover .menu-title {
        color: white !important;
    }

    .menu-link.active {
        background-color: #d11234 !important;
        color: white !important;
        border-radius: 0.65rem;
    }

    .menu-link.active .custom_icon {
        color: white !important;
    }

    .menu-item {
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .menu-title b {
        font-weight: 600 !important;
    }
</style>

<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

    <div class="app-sidebar-logo px-6 pt-6 pb-2" id="kt_app_sidebar_logo"
        style="height:130px; display:flex; justify-content:center; align-items:center;">>
        <a href="{{ route('dashboard.view') }}">
            <img alt="Logo" style="align-items: center;" src="{{ asset('images/logo/sip_logo.png') }}"
                class="h-80px app-sidebar-logo-default theme-light-show" />
        </a>
    </div>
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5 px-3"
            data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu"
                data-kt-menu="true" data-kt-menu-expand="false">

                <div class="menu-item">
                    <a class="menu-link {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}"
                        href="{{ url('admin/dashboard') }}">
                        <i class="custom_icon fas fa-th-large"></i>
                        <span class="menu-title"><b>Dashboard</b></span>
                    </a>
                </div>

                @can('directory.view')
                    <div class="menu-item">
                        <a class="menu-link {{ (request()->is('admin/directory*')) ? 'active' : '' }}"
                            href="{{ route('admin.directory.index') }}">
                            <i class="custom_icon fas fa-address-book"></i>
                            <span class="menu-title"><b>Directory</b></span>
                        </a>
                    </div>
                @endcan

                @can('forum.view')
                    <div class="menu-item">
                        <a class="menu-link {{ (request()->is('admin/forums*')) ? 'active' : '' }}"
                            href="{{ route('admin.forums.index') }}">
                            <i class="custom_icon fas fa-comments"></i>
                            <span class="menu-title"><b>Forums</b></span>
                        </a>
                    </div>
                @endcan
                @can('forum.view')
                    <div class="menu-item">
                        <a class="menu-link {{ (request()->is('admin/announcements*')) ? 'active' : '' }}"
                            href="{{ route('admin.announcements.index') }}">
                            <i class="custom_icon fas fa-comments"></i>
                            <span class="menu-title"><b>Announcements</b></span>
                        </a>
                    </div>
                @endcan

                @can('user.view')
                    <div class="menu-item">
                        <a class="menu-link {{ (request()->is('admin/user*')) ? 'active' : '' }}"
                            href="{{ url('admin/user') }}">
                            <i class="custom_icon fas fa-user"></i>
                            <span class="menu-title"><b>User</b></span>
                        </a>
                    </div>
                @endcan

                @can('role.view')
                    <div class="menu-item">
                        <a class="menu-link {{ (request()->is('admin/role*')) ? 'active' : '' }}"
                            href="{{ url('admin/role') }}">
                            <i class="custom_icon fas fa-user-tag"></i>
                            <span class="menu-title"><b>Roles</b></span>
                        </a>
                    </div>
                @endcan

            </div>
        </div>
    </div>
    <div class="app-sidebar-footer px-6 pb-2 text-center">
        <p style="color: rgba(31, 41, 55, 0.6); font-size: 10px;">&copy; 2025 SIP Academy</p>
    </div>

</div>