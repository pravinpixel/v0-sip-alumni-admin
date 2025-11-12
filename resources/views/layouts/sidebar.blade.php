<style>
    /* 1. Sidebar Background Color (Light Blue/Grey from design) */
    .app-sidebar {
        background-color: #f0f4f9; 
        color: #1f2937; /* Default text color */
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }
    
    /* 2. Default Link Styling */
    .menu-link {
        color: #1f2937 !important; /* Dark text for inactive links */
        transition: all 0.2s ease-in-out;
    }
    
    /* 3. Inactive Icon Color (Matches the dark text) */
    .menu-link .custom_icon {
        color: #1f2937 !important;
    }

    /* 4. ACTIVE State: Dashboard (Red background, White text/icon) */
    .menu-link.active {
        background-color: #d11234 !important; /* Deep red for active link */
        color: white !important; /* White text when active */
        border-radius: 0.65rem; /* Match the rounded corners in the design */
    }

    .menu-link.active .custom_icon {
        color: white !important; /* White icon when active */
    }
    
    .menu-item {
        /* Add some padding to match the spacing in the design */
        padding-top: 5px;
        padding-bottom: 5px;
    }

    /* Override for bold text in the menu */
    .menu-title b {
        font-weight: 600 !important;
    }

    
</style>

<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    
    <div class="app-sidebar-logo px-6 pt-6 pb-2" id="kt_app_sidebar_logo" style="height:130px; display:flex; justify-content:center; align-items:center;">>
        <a href="{{ url('admin/') }}">
            <img alt="Logo" style="align-items: center;" src="{{ asset('images/logo/sip_logo.png') }}" class="h-80px app-sidebar-logo-default theme-light-show" />
        </a>
    </div>
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5 px-3" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">

                <div class="menu-item">
                    <a class="menu-link {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}" href="{{ url('admin/dashboard') }}">
                        <i class="custom_icon fas fa-th-large"></i>
                        <span class="menu-title"><b>Dashboard</b></span>
                    </a>
                </div>

                @can('employee.view')
                <div class="menu-item">
                    <a class="menu-link {{ (request()->is('admin/directory*')) ? 'active' : '' }}" href="{{ route('admin.directory.index') }}">
                        <i class="custom_icon fas fa-address-book"></i>
                        <span class="menu-title"><b>Directory</b></span>
                    </a>
                </div>
                @endcan

                @can('organization.view')
                <div class="menu-item">
                    <a class="menu-link {{ (request()->is('admin/forums*')) ? 'active' : '' }}" href="{{ route('admin.forums.index') }}">
                        <i class="custom_icon fas fa-comments"></i>
                        <span class="menu-title"><b>Forums</b></span>
                    </a>
                </div>
                @endcan

                @can('user.view')
                <div class="menu-item">
                    <a class="menu-link {{ (request()->is('admin/user') && !request()->is('admin/user/*')) ? 'active' : '' }}" href="{{ url('admin/user') }}">
                        <i class="custom_icon fas fa-user"></i>
                        <span class="menu-title"><b>User</b></span>
                    </a>
                </div>
                @endcan

                @can('role.view')
                <div class="menu-item">
                    <a class="menu-link {{ (request()->is('admin/role*')) ? 'active' : '' }}" href="{{ url('admin/role') }}">
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
