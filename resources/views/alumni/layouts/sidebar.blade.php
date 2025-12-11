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
    <!-- Drawer Toggle Button -->
    <button class="drawer-toggle" id="drawerToggle" onclick="toggleDrawer()">
        <i class="fas fa-chevron-left"></i>
    </button>

    <!-- Profile Section -->
    <div class="profile-section" style="position: relative;">
        <button class="close-btn" onclick="closeSidebar()">×</button>
        <img src="{{ $alumni->image_url ?? asset('images/avatar/blank.png') }}" alt="Profile" class="profile-img">
        <div class="profile-name">{{ $alumni->full_name ?? '-' }}</div>
    </div>

    <!-- Profile Info -->
    <div class="profile-info">
        <div class="info-item">
            <div>
                <i class="bi bi-mortarboard info-icon"></i>
            </div>
            <div class="info-content">
                <span class="info-label">Year of Completion</span>
                <div class="info-value">{{ $alumni->year_of_completion ?? '-' }}</div>
            </div>
        </div>

        <div class="info-item">
            <i class="bi bi-geo-alt info-icon"></i>
            <div class="info-content">
                <span class="info-label">Location</span>
                <div class="info-value">{{ $state->name ?? '-' }}, {{ $city->name ?? '-' }}</div>
            </div>
        </div>

        <div class="info-item">
            <i class="bi bi-envelope info-icon"></i>
            <div class="info-content">
                <span class="info-label">Email Address</span>
                <div class="info-value">{{ $alumni->email ?? '-' }}</div>
            </div>
        </div>

        <div class="info-item">
            <i class="bi bi-telephone info-icon"></i>
            <div class="info-content">
                <span class="info-label">Contact Number</span>
                <div class="info-value">{{ $alumni->mobile_number ?? '-' }}</div>
            </div>
        </div>

        <div class="info-item">
            <i class="bi bi-briefcase info-icon"></i>
            <div class="info-content">
                <span class="info-label">Current Occupation</span>
                <div class="info-value">{{ $occupation->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Button -->
    <button class="edit-profile-btn" onclick="openEditProfileModal()">
        <i class="fa-regular fa-pen-to-square"></i>
        Edit Profile
    </button>
</div>

<!-- Include Modal -->
@include('alumni.modals.edit-profile-modal')

<style>
    /* Drawer Toggle Button */
    .drawer-toggle {
        position: absolute;
        top: 4%;
        right: 0px;
        transform: translateY(-50%);
        width: 30px;
        height: 30px;
        background: #ffffffff;
        border: 1px solid #ff0000ff;
        border-radius: 20px;
        color: black;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1001;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .drawer-toggle:hover {
        background: #d30f0fff;
        right: 2px;
    }

    .drawer-toggle i {
        transition: transform 0.3s ease;
    }

    .sidebar.collapsed .drawer-toggle i {
        transform: rotate(180deg);
    }

    #sidebar.collapsed {
        width: 80px !important;
        overflow: visible !important;
        transition: width 0.3s ease !important;
    }

    #sidebar.collapsed .profile-section {
        padding: 30px 10px 10px;
    }

    #sidebar.collapsed .profile-img {
        width: 55px;
        height: 55px;
        margin: 0 auto 5px;
    }

    #sidebar.collapsed .profile-name,
    #sidebar.collapsed .info-item,
    #sidebar.collapsed .profile-info {
        display: none;
    }

    #sidebar.collapsed .edit-profile-btn {
        width: 35px;
        height: 35px;
        margin: 10px auto 0;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0;
        position: relative;
    }

    #sidebar.collapsed .edit-profile-btn i {
        font-size: 14px;
        margin-left: 10px;
        color: #fa1717ff;
    }
    #sidebar.collapsed .edit-profile-btn {
        background: #ffffffff;
        border-radius: 6px;
    }
    #sidebar.collapsed .edit-profile-btn:hover {
        background: #f1e5e5ff;
    }

    #sidebar.collapsed .edit-profile-btn:hover i {
        color: #333;
    }

    #sidebar.collapsed .drawer-toggle {
        right: -15px;
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.95);
        border: 2px solid #ef4444;
        color: #ef4444;
        font-size: 22px;
        font-weight: bold;
        cursor: pointer;
        line-height: 30px;
        text-align: center;
        z-index: 1002;
        display: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
    }

    .close-btn:hover {
        background: #ef4444;
        color: white;
        transform: scale(1.1);
    }

    .close-btn:active {
        transform: scale(0.95);
    }

    @media (max-width: 768px) {
        .close-btn {
            display: block;
        }

        .drawer-toggle {
            display: none;
        }
    }

    /* Ensure sidebar has clean white background */
    #sidebar {
        background: white !important;
        transition: left 0.3s ease !important;
    }

    /* Ensure edit button is red, not yellow */
    .edit-profile-btn {
        background: #dc2626;
        border: none !important;
    }

    .edit-profile-btn:hover {
        background: #b91c1c;
    }

    /* Remove any yellow/orange tints */
    .info-item {
        background: transparent !important;
    }

    /* Modal Overlay */
    .modal-overlay {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.open {
        display: flex;
    }

    /* Modal Popup */
    .modal-popup {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        animation: slideIn 0.3s ease;
    }

    .modal-header {
        display: block;
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
        position: relative;
        background: white;
    }

    .modal-header h2 {
        margin: 0 0 5px 0;
        font-size: 20px;
        color: #333;
    }

    .modal-subtitle {
        margin: 0;
        font-size: 14px;
        color: #999;
    }

    .modal-close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 28px;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close-btn:hover {
        color: #333;
    }

    .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        padding-bottom: 30px;
    }

    .profile-image-section {
        text-align: center;
        margin-bottom: 30px;
        background: white;
        padding-bottom: 15px;
    }

    .modal-profile-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid #f0f0f0;
        margin-bottom: 15px;
        object-fit: cover;
    }

    .profile-action-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }




    .form-group {
        margin-bottom: 15px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }

    .form-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .btn-save {
        background: #dc2626;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-save:hover {
        background: #b91c1c;
    }

    .btn-cancel {
        background: white;
        color: #333;
        padding: 10px 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-cancel:hover {
        background: #f9fafb;
    }
</style>

<script>
    function toggleDrawer() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content-area');

        console.log('Toggle drawer clicked', sidebar, mainContent);

        if (!sidebar) {
            console.error('Sidebar not found');
            return;
        }

        const isCollapsed = sidebar.classList.contains('collapsed');

        if (isCollapsed) {
            // Expand sidebar
            sidebar.classList.remove('collapsed');
            sidebar.style.width = '250px';
            sidebar.style.padding = '20px 0';
            if (mainContent) {
                mainContent.style.marginLeft = '250px';
            }
        } else {
            // Collapse sidebar to mini version
            sidebar.classList.add('collapsed');
            sidebar.style.width = '80px';
            sidebar.style.padding = '20px 0';
            if (mainContent) {
                mainContent.style.marginLeft = '80px';
            }
        }

        console.log('Sidebar collapsed:', !isCollapsed, 'Width:', sidebar.style.width);
    }

    function closeSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebar) sidebar.classList.remove('active');
        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    async function openEditProfileModal() {
        const modal = document.getElementById('editProfileModal');
        if(!modal) return;
        if (modal) {
            // Reset form state
            isMobileVerified = false;
            selectedFile = null;
            window.removeImage = false;
            
            // Reset mobile input to readonly
            const mobileInput = document.getElementById('mobileNumberInput');
            if (mobileInput) {
                mobileInput.readOnly = true;
            }
            
            // Reset edit/cancel button
            const editCancelBtn = document.getElementById('editCancelMobileBtn');
            const verifyBtn = document.getElementById('verifyMobileBtn');
            if (editCancelBtn) {
                editCancelBtn.textContent = 'Edit';
                editCancelBtn.style.color = '#dc2626';
            }
            if (verifyBtn) {
                verifyBtn.textContent = 'Verify';
                verifyBtn.style.background = '#dc2626';
                verifyBtn.disabled = true;
                verifyBtn.style.opacity = '0.5';
                verifyBtn.style.cursor = 'not-allowed';
            }
            
            // Reset save button
            const saveBtn = document.querySelector('.btn-save');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.style.opacity = '1';
                saveBtn.style.cursor = 'pointer';
            }
            
            // Hide OTP section
            const otpSection = document.getElementById('otpSection');
            if (otpSection) {
                otpSection.style.display = 'none';
            }
            
            const dropdownData = await loadStates();
            const alumni = await loadAlumniData();
            
            // Initialize modal state
            if (typeof initializeModal === 'function') {
                initializeModal();
            }
            
            modal.classList.add('open');
        }
    }

    function loadAlumniData() {
        const modal = document.getElementById('editProfileModal');
        const alumniId = modal.getAttribute('data-alumni-id');

        if (!alumniId) {
            console.error('Alumni ID not found');
            return;
        }

        return fetch(`{{ route('alumni.profile.view', '') }}/${alumniId}`)
            .then(res => res.json())
            .then(data => {
            if (data.success) {
                populateFormData(data.alumni);
                return data.alumni;  // RETURN DATA HERE ✔
            }
            return null;
            });
    }

    function populateFormData(alumni) {
        const form = document.getElementById('editProfileForm');
        const stateSelect = document.getElementById('stateSelect');
        const citySelect = document.getElementById('citySelect');

        form.querySelector('[data-field="full_name"]').value = alumni.full_name || '';
        form.querySelector('[data-field="year_of_completion"]').value = alumni.year_of_completion || '';
        form.querySelector('[data-field="email"]').value = alumni.email || '';
        form.querySelector('[data-field="mobile_number"]').value = alumni.mobile_number || '';
        form.querySelector('[data-field="occupation_id"]').value = alumni.occupation?.id || '';

        // Store original mobile number
        originalMobileNumber = alumni.mobile_number || '';

        if (stateSelect) {
            stateSelect.value = alumni.city?.state?.id || '';
            loadCities(alumni.city?.state?.id, alumni.city?.id); // load cities based on selected state
        }

        const profileImg = document.querySelector('.modal-profile-img');
        if (profileImg) profileImg.src = alumni.image_url || "{{ asset('images/avatar/blank.png') }}";
    }

    // Load all states
        function loadStates(retry = true) {
            const url = "{{ route('alumni.states') }}?t=" + Date.now();

            return fetch(url, { credentials: "include" })
                .then(res => {
                    if (!res.ok) throw new Error('Failed to load states');
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        populateSelect('stateSelect', data.states, 'Select State');
                        populateSelect('occupationSelect', data.occupations, 'Select Occupation');
                        return data;
                    } else {
                        throw new Error(data.message || 'Failed to load states');
                    }
                })
                .catch(err => {
                    console.error('Error loading states:', err);

                    if (retry) {
                        return new Promise((resolve) => {
                            setTimeout(() => {
                                loadStates(false).then(resolve);
                            }, 500);
                        });
                    } else {
                        return { success: false };
                }
            });
        }


        function loadCities(stateId, selectedCityId = null) {

            return new Promise((resolve) => { 
                if (!stateId) {
                    populateSelect('citySelect', [], 'Select City');
                    return resolve();
                }

                fetch(`{{ route('alumni.cities', '') }}/${stateId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            populateSelect('citySelect', data.cities, 'Select City', selectedCityId);
                        }
                        resolve();  
                    })
                    .catch(err => {
                        console.error('City load error:', err);
                        resolve();  
                    });
            });
        }
    

    function populateSelect(selectId, items, placeholder, selectedValue = null) {
        const select = document.getElementById(selectId);
        if (!select) return;

        select.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            if (selectedValue && selectedValue == item.id) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

    // When user changes state, load its cities dynamically
    document.addEventListener('DOMContentLoaded', function() {
        const stateSelect = document.getElementById('stateSelect');
        if (stateSelect) {
            stateSelect.addEventListener('change', function() {
                loadCities(this.value);
            });
        }
    });
</script>