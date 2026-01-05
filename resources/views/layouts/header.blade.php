<style>
  .icon_wrapper {
    display: flex;
    justify-content: space-between;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown functionality
    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');
    const profileChevron = document.getElementById('profileChevron');
    
    // Toggle dropdown on click
    if (profileToggle) {
      profileToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        const isVisible = profileDropdown.style.display === 'block';
        
        if (isVisible) {
          profileDropdown.style.display = 'none';
          if (profileChevron) profileChevron.style.transform = 'rotate(0deg)';
        } else {
          profileDropdown.style.display = 'block';
          if (profileChevron) profileChevron.style.transform = 'rotate(180deg)';
        }
      });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (profileToggle && profileDropdown && !profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
        profileDropdown.style.display = 'none';
        if (profileChevron) profileChevron.style.transform = 'rotate(0deg)';
      }
    });

    // Sidebar toggle functionality with slide animation and content expansion
    const sidebarToggleBtn = document.getElementById('kt_app_sidebar_mobile_toggle');
    const sidebarToggleBtnDesktop = document.getElementById('sidebarToggleDesktop');
    const sidebar = document.getElementById('kt_app_sidebar');
    const header = document.getElementById('kt_app_header');
    const wrapper = document.getElementById('kt_app_wrapper');
    const body = document.body;
    let sidebarHidden = false;
    
    // Add transition styles
    if (sidebar) {
      sidebar.style.transition = 'transform 0.3s ease-in-out';
    }
    if (header) {
      header.style.transition = 'margin-left 0.3s ease-in-out';
    }
    if (wrapper) {
      wrapper.style.transition = 'margin-left 0.3s ease-in-out';
    }
    
    // Function to toggle sidebar with slide animation and expand content
    function toggleSidebar() {
      if (sidebar) {
        const sidebarWidth = sidebar.offsetWidth;
        
        if (sidebarHidden) {
          // Show sidebar - slide in from left
          sidebar.style.transform = 'translateX(0)';
          
          // Restore margins for header and content
          if (header) header.style.marginLeft = '';
          if (wrapper) wrapper.style.marginLeft = '';
          
          sidebarHidden = false;
        } else {
          // Hide sidebar - slide out to left
          sidebar.style.transform = `translateX(-${sidebarWidth}px)`;
          
          // Expand header and content to fill the space
          if (header) header.style.marginLeft = '0';
          if (wrapper) wrapper.style.marginLeft = '0';
          
          sidebarHidden = true;
        }
      }
    }
    
    // Mobile toggle button
    if (sidebarToggleBtn) {
      sidebarToggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSidebar();
      });
    }
    
    // Desktop toggle button (hamburger icon in header)
    if (sidebarToggleBtnDesktop) {
      sidebarToggleBtnDesktop.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSidebar();
      });
    }
  });
</script>
<!--begin::Header-->
<div id="kt_app_header" class="app-header">
  <!--begin::Header container-->
  <div class="app-container container-fluid d-flex align-items-stretch justify-content-between"
    id="kt_app_header_container" style="background-color: white;">
    <!--begin::Sidebar mobile toggle-->
    <div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
      <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
        <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
        <span class="svg-icon svg-icon-2 svg-icon-md-1">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
              fill="currentColor" />
            <path opacity="0.3"
              d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
              fill="currentColor" />
          </svg>
        </span>
        <!--end::Svg Icon-->
      </div>
    </div>
    <!--end::Sidebar mobile toggle-->
    <!--begin::Mobile logo-->
    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
      <a href="" class="d-lg-none">
        <img alt="Logo" style="width:30%;" src="" class="h-30px" />
      </a>
    </div>
    <!--end::Mobile logo-->
    <!--begin::Header wrapper-->
    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper"
      style="background-color: white;">

      <!--begin::Sidebar Toggle (Desktop)-->
      <div class="d-flex align-items-center">
        <button id="sidebarToggleDesktop" style="background: transparent; border: none; cursor: pointer; padding: 8px; margin-right: 16px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: background 0.2s;"
          onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="#374151" />
            <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="#374151" />
          </svg>
        </button>
      </div>
      <!--end::Sidebar Toggle-->

      <!--begin::Menu wrapper-->
      <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true"
        data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}"
        data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true"
        data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
        data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
        <!--begin::Menu-->
        <div class="menu-title mt-2">
          <h3 class="mt-5">

          </h3>
        </div>
        <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
          id="kt_app_header_menu" data-kt-menu="true">
        </div>
        <!--end::Menu-->
      </div>
      <!--end::Menu wrapper-->

      <!--begin::Navbar-->
      <div class="app-navbar flex-shrink-0">
        <!--begin::User menu-->
        <div class="app-navbar-item ms-1 ms-md-3" style="position: relative;">
          <!--begin::Profile Display-->
          <div id="profileToggle" style="display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 8px 12px; border-radius: 8px; transition: background 0.2s;"
            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: #ba0028; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px; flex-shrink: 0;">
              @if(Auth::user()->profile_image ?? '')
                <img src="{{ url('storage/' . Auth::user()->profile_image) }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" alt="Profile">
              @else
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
              @endif
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-start;">
              <span style="font-weight: 700; font-size: 16px; color: #111827; line-height: 1.2;">{{Auth::user()->name ?? 'Admin User'}}</span>
              <span style="font-size: 14px; color: #9ca3af; line-height: 1.2;">{{Auth::user()->role->name ?? 'Super Admin'}}</span>
            </div>
          </div>
          <!--end::Profile Display-->

          <!--begin::Dropdown Menu-->
          <div id="profileDropdown" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 8px; background: white; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); min-width: 220px; z-index: 1000; overflow: hidden;">
            <a href="{{route('logout')}}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; text-decoration: none; color: #dc2626; transition: background 0.2s;"
              onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='white'">
              <i class="fas fa-sign-out-alt" style="color: #dc2626; width: 16px;"></i>
              <span style="font-size: 14px; font-weight: 600;">Logout</span>
            </a>
          </div>
          <!--end::Dropdown Menu-->
        </div>
        <!--end::User menu-->
      </div>
      <!--end::Navbar-->
    </div>
    <!--end::Header wrapper-->
  </div>
  <!--end::Header container-->
</div>
<!--end::Header-->

<!--begin::Chat drawer-->
<div id="kt_drawer_chat" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="chat" data-kt-drawer-activate="true"
  data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'md': '500px'}" data-kt-drawer-direction="end"
  data-kt-drawer-toggle="#kt_drawer_chat_toggle" data-kt-drawer-close="#kt_drawer_chat_close">
  <!--begin::Messenger-->
  <div class="card w-100 rounded-0 border-0" id="kt_drawer_chat_messenger">
    <!--begin::Card header-->
    <div class="card-header pe-5" id="kt_drawer_chat_messenger_header">
      <!--begin::Title-->
      <div class="card-title">
        <!--begin::User-->
        <div class="d-flex justify-content-center flex-column me-3">
          <a href="#" class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 mb-2 lh-1">Brian Cox</a>
          <!--begin::Info-->
          <div class="mb-0 lh-1">
            <span class="badge badge-success badge-circle w-10px h-10px me-1"></span>
            <span class="fs-7 fw-semibold text-muted">Active</span>
          </div>
          <!--end::Info-->
        </div>
        <!--end::User-->
      </div>
      <!--end::Title-->
      <!--begin::Card toolbar-->
      <div class="card-toolbar">

        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_chat_close">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-2">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)"
                fill="currentColor" />
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)"
                fill="currentColor" />
            </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body" id="kt_drawer_chat_messenger_body">
      <!--begin::Messages-->
      <div class="scroll-y me-n5 pe-5" data-kt-element="messages" data-kt-scroll="true" data-kt-scroll-activate="true"
        data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_drawer_chat_messenger_header, #kt_drawer_chat_messenger_footer"
        data-kt-scroll-wrappers="#kt_drawer_chat_messenger_body" data-kt-scroll-offset="0px">
        <!--begin::Message(in)-->
        <div class="d-flex justify-content-start mb-10">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column align-items-start">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
              <!--begin::Avatar-->
              <div class="symbol symbol-35px symbol-circle">
                <!-- <img alt="Pic" src="assets/media/avatars/300-25.jpg" /> -->
              </div>
              <!--end::Avatar-->
              <!--begin::Details-->
              <div class="ms-3">
                <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary me-1">Brian Cox</a>
                <span class="text-muted fs-7 mb-1">2 mins</span>
              </div>
              <!--end::Details-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start"
              data-kt-element="message-text">How likely are you to recommend our company to your friends and family ?
            </div>
            <!--end::Text-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Message(in)-->
        <!--begin::Message(out)-->
        <div class="d-flex justify-content-end mb-10">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column align-items-end">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
              <!--begin::Details-->
              <div class="me-3">
                <span class="text-muted fs-7 mb-1">5 mins</span>
                <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary ms-1">You</a>
              </div>
              <!--end::Details-->
              <!--begin::Avatar-->
              <div class="symbol symbol-35px symbol-circle">
                <!-- <img alt="Pic" src="assets/media/avatars/300-1.jpg" /> -->
              </div>
              <!--end::Avatar-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-5 rounded bg-light-primary text-dark fw-semibold mw-lg-400px text-end"
              data-kt-element="message-text">Hey there, we’re just writing to let you know that you’ve been subscribed
              to a repository on GitHub.</div>
            <!--end::Text-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Message(out)-->
        <!--begin::Message(in)-->
        <div class="d-flex justify-content-start mb-10">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column align-items-start">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
              <!--begin::Avatar-->
              <div class="symbol symbol-35px symbol-circle">
                <!-- <img alt="Pic" src="assets/media/avatars/300-25.jpg" /> -->
              </div>
              <!--end::Avatar-->
              <!--begin::Details-->
              <div class="ms-3">
                <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary me-1">Brian Cox</a>
                <span class="text-muted fs-7 mb-1">1 Hour</span>
              </div>
              <!--end::Details-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start"
              data-kt-element="message-text">Ok, Understood!</div>
            <!--end::Text-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Message(in)-->
        <!--begin::Message(out)-->
        <div class="d-flex justify-content-end mb-10">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column align-items-end">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
              <!--begin::Details-->
              <div class="me-3">
                <span class="text-muted fs-7 mb-1">2 Hours</span>
                <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary ms-1">You</a>
              </div>
              <!--end::Details-->
              <!--begin::Avatar-->
              <div class="symbol symbol-35px symbol-circle">
                <!-- <img alt="Pic" src="assets/media/avatars/300-1.jpg" /> -->
              </div>
              <!--end::Avatar-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-5 rounded bg-light-primary text-dark fw-semibold mw-lg-400px text-end"
              data-kt-element="message-text">You’ll receive notifications for all issues, pull requests!</div>
            <!--end::Text-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Message(out)-->
        <!--begin::Message(in)-->
        <div class="d-flex justify-content-start mb-10">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column align-items-start">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
              <!--begin::Avatar-->
              <div class="symbol symbol-35px symbol-circle">
                <!-- <img alt="Pic" src="assets/media/avatars/300-25.jpg" /> -->
              </div>
              <!--end::Avatar-->
              <!--begin::Details-->
              <div class="ms-3">
                <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary me-1">Brian Cox</a>
                <span class="text-muted fs-7 mb-1">3 Hours</span>
              </div>
              <!--end::Details-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start"
              data-kt-element="message-text">You can unwatch this repository immediately by clicking here:
              <a href="https://keenthemes.com">Keenthemes.com</a>
            </div>
            <!--end::Text-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Message(in)-->
        <!--begin::Message(out)-->
        <div class="d-flex justify-content-end mb-10">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column align-items-end">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
              <!--begin::Details-->
              <div class="me-3">
                <span class="text-muted fs-7 mb-1">4 Hours</span>
                <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary ms-1">You</a>
              </div>
              <!--end::Details-->
              <!--begin::Avatar-->
              <div class="symbol symbol-35px symbol-circle">
                <!-- <img alt="Pic" src="assets/media/avatars/300-1.jpg" /> -->
              </div>
              <!--end::Avatar-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-5 rounded bg-light-primary text-dark fw-semibold mw-lg-400px text-end"
              data-kt-element="message-text">Most purchased Business courses during this sale!</div>
            <!--end::Text-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Message(out)-->
        <!--begin::Message(in)-->
        <div class="d-flex justify-content-start mb-10">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column align-items-start">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
              <!--begin::Avatar-->
              <div class="symbol symbol-35px symbol-circle">
                <!-- <img alt="Pic" src="assets/media/avatars/300-25.jpg" /> -->
              </div>
              <!--end::Avatar-->
              <!--begin::Details-->
              <div class="ms-3">
                <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary me-1">Brian Cox</a>
                <span class="text-muted fs-7 mb-1">5 Hours</span>
              </div>
              <!--end::Details-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start"
              data-kt-element="message-text">Company BBQ to celebrate the last quater achievements and goals. Food and
              drinks provided</div>
            <!--end::Text-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Message(in)-->
        <!--begin::Message(template for out)-->
        <div class="d-flex justify-content-end mb-10 d-none" data-kt-element="template-out">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column align-items-end">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
              <!--begin::Details-->
              <div class="me-3">
                <span class="text-muted fs-7 mb-1">Just now</span>
                <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary ms-1">You</a>
              </div>
              <!--end::Details-->
              <!--begin::Avatar-->
              <div class="symbol symbol-35px symbol-circle">
                <!-- <img alt="Pic" src="assets/media/avatars/300-1.jpg" /> -->
              </div>
              <!--end::Avatar-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-5 rounded bg-light-primary text-dark fw-semibold mw-lg-400px text-end"
              data-kt-element="message-text"></div>
            <!--end::Text-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Message(template for out)-->
        <!--begin::Message(template for in)-->
        <div class="d-flex justify-content-start mb-10 d-none" data-kt-element="template-in">
          <!--begin::Wrapper-->
          <div class="d-flex flex-column align-items-start">
            <!--begin::User-->
            <div class="d-flex align-items-center mb-2">
              <!--begin::Avatar-->
              <div class="symbol symbol-35px symbol-circle">
                <!-- <img alt="Pic" src="assets/media/avatars/300-25.jpg" /> -->
              </div>
              <!--end::Avatar-->
              <!--begin::Details-->
              <div class="ms-3">
                <a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary me-1">Brian Cox</a>
                <span class="text-muted fs-7 mb-1">Just now</span>
              </div>
              <!--end::Details-->
            </div>
            <!--end::User-->
            <!--begin::Text-->
            <div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start"
              data-kt-element="message-text">Right before vacation season we have the next Big Deal for you.</div>
            <!--end::Text-->
          </div>
          <!--end::Wrapper-->
        </div>
        <!--end::Message(template for in)-->
      </div>
      <!--end::Messages-->
    </div>
    <!--end::Card body-->
    <!--begin::Card footer-->
    <div class="card-footer pt-4" id="kt_drawer_chat_messenger_footer">
      <!--begin::Input-->
      <textarea class="form-control form-control-flush mb-3" rows="1" data-kt-element="input"
        placeholder="Type a message"></textarea>
      <!--end::Input-->
      <!--begin:Toolbar-->
      <div class="d-flex flex-stack">
        <!--begin::Actions-->
        <div class="d-flex align-items-center me-2">
          <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip"
            title="Coming soon">
            <i class="bi bi-paperclip fs-3"></i>
          </button>
          <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip"
            title="Coming soon">
            <i class="bi bi-upload fs-3"></i>
          </button>
        </div>
        <!--end::Actions-->
        <!--begin::Send-->
        <button class="btn btn-primary" type="button" data-kt-element="send">Send</button>
        <!--end::Send-->
      </div>
      <!--end::Toolbar-->
    </div>
    <!--end::Card footer-->
  </div>
  <!--end::Messenger-->
</div>
<!--end::Chat drawer-->