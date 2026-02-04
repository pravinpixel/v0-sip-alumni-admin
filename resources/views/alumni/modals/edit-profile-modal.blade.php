@php
$alumni = $alumni ?? null;
$city = $alumni && isset($alumni->city) ? $alumni->city : null;
$state = $city && isset($city->state) ? $city->state : null;
$occupation = $alumni && isset($alumni->occupation) ? $alumni->occupation : null;
$locationTypeChecked = $alumni && $alumni->location_type == 1 ? '' : 'checked';
$outsideIndiaChecked = $alumni && $alumni->location_type == 1 ? 'checked' : '';
@endphp

<!-- Edit Profile Modal Popup -->
<div id="editProfileModal" class="modal-overlay" data-alumni-id="{{ session('alumni.id') }}">
    <div class="modal-popup">
        <div class="modal-header">
            <h2>Edit Profile</h2>
            <p class="modal-subtitle">Update your profile information below</p>
            <button class="modal-close-btn" onclick="closeEditProfileModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Profile Image Section -->
            <div class="profile-image-section">
                <img src="{{ $alumni->image ?? asset('images/avatar/blank.png') }}" alt="Profile" class="modal-profile-img">
                <div class="profile-action-buttons">
                    <button class="btn-change-profile" onclick="changeProfileImage()">
                        <i class="fa fa-camera"></i> Change Profile
                    </button>
                    <button class="btn-remove-profile" onclick="removeProfileImage()">
                        <i class="fa fa-trash"></i> Remove Profile
                    </button>
                </div>
            </div>
            <div class="text-center mb-3 d-none">
                <label class="me-3">
                    <input type="radio" name="location_type" value="0" {{ $locationTypeChecked }} onchange="toggleVerificationMethod()">
                    Inside India
                </label>
                <label>
                    <input type="radio" name="location_type" value="1" {{ $outsideIndiaChecked }} onchange="toggleVerificationMethod()">
                    Outside India
                </label>
            </div>

            <form id="editProfileForm">
                <div class="form-row">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="form-input" data-field="full_name" value="{{ $alumni->full_name ?? '' }}">
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>

                <div class="form-group">
                    <label>Year of Completion</label>
                    <input type="number" class="form-input" data-field="year_of_completion" value="{{ $alumni->year_of_completion ?? '' }}">
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>
                </div>
                <div class="form-row">
                <div class="form-group">
                    <label>State</label>
                    <select class="form-input" data-field="state_id" id="stateSelect">
                        <option value="">Select State</option>
                    </select>
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>

                <div class="form-group">
                    <label>City</label>
                    <select class="form-input" data-field="city_id" id="citySelect">
                        <option value="">Select City</option>
                    </select>
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>
                </div>
                <div class="form-row">
                <div class="form-group">
                    <label>Pincode</label>
                    <select class="form-input" data-field="pincode_id" id="pincodeSelect">
                        <option value="">Select Pincode</option>
                    </select>
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>

                <div class="form-group">
                    <label>Center Location</label>
                    <select class="form-input" data-field="center_id" id="centerLocationSelect">
                        <option value="">Select Center Location</option>
                    </select>
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>
                </div>
                <!-- <div class="form-row"> -->
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label>Email Address</label>
                        <button type="button" id="editCancelEmailBtn" class="btn-edit-cancel-mobile" onclick="toggleEmailEdit()" 
                            style="background: none; border: none; color: #dc2626; font-size: 11px; font-weight: 600; cursor: pointer; padding: 0 8px; text-decoration: underline; display: none;">
                            Edit
                        </button>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: flex-start;">
                        <div style="flex: 1;">
                            <input type="email" class="form-input" data-field="email" id="emailInput" value="{{ $alumni->email ?? '' }}"
                                oninput="validateEmailVerification(this)" readonly>
                            <small class="error-message" style="color:red;font-size:12px;display:block;margin-top:4px;"></small>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                            <button type="button" id="verifyEmailBtn" class="btn-verify" disabled onclick="sendEmailOTP()"
                                style="padding: 10px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: not-allowed; opacity: 0.5; white-space: nowrap; display: none;">
                                Verify
                            </button>
                        </div>
                    </div>
                    <div id="emailOtpSection" style="display: none; margin-top: 12px; padding: 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb;">
                        <label style="font-size: 13px; font-weight: 600; color: #1f2937; margin-bottom: 8px; display: block;">Enter OTP</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="emailOtpInput" class="form-input" maxlength="6" placeholder="Enter 6 digit OTP"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                style="flex: 1;">
                            <button type="button" onclick="verifyEmailOTP()" class="btn-verify"
                                style="padding: 10px 16px; background: #10b981; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                                Verify OTP
                            </button>
                        </div>
                        <small id="emailOtpTimer" style="color: #6b7280; font-size: 12px; display: block; margin-top: 6px;"></small>
                    </div>
                </div>
                
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label>Contact Number</label>
                        <button type="button" id="editCancelMobileBtn" class="btn-edit-cancel-mobile" onclick="toggleMobileEdit()"
                            style="background: none; border: none; color: #dc2626; font-size: 11px; font-weight: 600; cursor: pointer; padding: 0 8px; text-decoration: underline;">
                            Edit
                        </button>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: flex-start;">
                        <select class="form-input" data-field="country_code" id="countryCodeSelect" 
                            style="width: 80px; font-size: 14px; padding: 10px 8px; background-color: #f8f9fa;">
                            <option value="">+91</option>
                        </select>
                        <div style="position: relative; display: flex; flex: 1;">
                            <input type="text" class="form-input" data-field="mobile_number" id="mobileNumberInput"
                                value="{{ $alumni->mobile_number ?? '' }}"
                                maxlength="10"
                                placeholder="Enter 10-digit mobile number"
                                oninput="validateMobileNumber(this)"
                                style="flex: 1; padding-left: 15px;"
                                readonly>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                            <button type="button" id="verifyMobileBtn" class="btn-verify" disabled onclick="sendMobileOTP()"
                                style="padding: 10px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: not-allowed; opacity: 0.5; white-space: nowrap; display: none;">
                                Verify
                            </button>
                        </div>
                    </div>
                    <div style="display: flex;">
                        <small class="error-message" style="color:red;font-size:12px;width: 80px;"></small>
                        <small class="error-message" style="color:red;font-size:12px;flex: 1;margin-left: 15px;"></small>
                    </div>
                    <div id="mobileOtpSection" style="display: none; margin-top: 12px; padding: 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb;">
                        <label style="font-size: 13px; font-weight: 600; color: #1f2937; margin-bottom: 8px; display: block;">Enter OTP</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="mobileOtpInput" class="form-input" maxlength="6" placeholder="Enter 6 digit OTP"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                style="flex: 1;">
                            <button type="button" onclick="verifyMobileOTP()" class="btn-verify"
                                style="padding: 10px 16px; background: #10b981; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                                Verify OTP
                            </button>
                        </div>
                        <small id="mobileOtpTimer" style="color: #6b7280; font-size: 12px; display: block; margin-top: 6px;"></small>
                    </div>
                </div>
                <!-- </div> -->
                <div class="form-row">
                <div class="form-group">
                    <label>Current Occupation</label>
                    <select class="form-input" data-field="occupation_id" id="occupationSelect">
                        <option value="">Select Occupation</option>
                    </select>
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>
                <div class="form-group">
                    <label>Current Location</label>
                    <input type="text" class="form-input" data-field="current_location" value="{{ $alumni->current_location ?? '' }}">
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>
                </div>
                <div class="form-row">
                <div class="form-group">
                    <label>Level Completed</label>
                    <select class="form-input" data-field="level_completed">
                        <option value="Advanced 4 / Level 8">Advanced 4 / Level 8</option>
                        <option value="Grandmaster A">Grandmaster A</option>
                        <option value="Grandmaster B">Grandmaster B</option>
                        <option value="Grandmaster C">Grandmaster C</option>
                    </select>
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>
                <div class="form-group">
                    <label>LinkedIn Profile</label>
                    <input type="text" class="form-input" data-field="linkedin_profile" value="{{ $alumni->linkedin_profile ?? '' }}">
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>
                </div>
                <div class="form-row">
                <div class="form-group">
                    <label>Organization</label>
                    <input type="text" class="form-input" data-field="organization" value="{{ $alumni->organization ?? '' }}">
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>
                <div class="form-group">
                    <label>University</label>
                    <input type="text" class="form-input" data-field="university" value="{{ $alumni->university ?? '' }}">
                    <small class="error-message" style="color:red;font-size:12px;"></small>
                </div>
                </div>

            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeEditProfileModal()">Cancel</button>
            <button class="btn-save" onclick="saveProfile()">Save Changes</button>
        </div>
    </div>
</div>

<style>
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
        border-radius: 12px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        width: 90%;
        max-width: 800px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        animation: slideIn 0.3s ease;
        overflow: hidden;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        display: block;
        padding: 20px;
        border-bottom: 1px solid #ffffffff;
        position: relative;
        background: white;
        border-radius: 12px 12px 0 0;
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
        border: 5px solid #dc2626;
        margin-bottom: 15px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .profile-action-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-change-profile {
        background: #ffffffff;
        color: red;
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
        border: 2px solid #dc2626;
    }

    .btn-change-profile:hover {
        background: #ff0c0cff;
        color: white;
        box-shadow: 0 4px 8px rgba(185, 28, 28, 0.3);
    }

    .btn-remove-profile {
        background: white;
        color: #aaaaaaff;
        padding: 10px 16px;
        border: 2px solid;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    }

    .btn-remove-profile:hover {
        background: #fef2f2;
        border-color: #b91c1c;
        color: #b91c1c;
    }

    .form-group {
        margin-bottom: 15px;
    }


    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1f2937;
        font-size: 13px;
    }

    .form-input {
        width: 100%;
        padding: 10px;
        border: 2px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
        background-color: #fafafa;
    }

    .form-input:focus {
        outline: none;
        border-color: #dc2626;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px;
        border-top: 1px solid #e5e7eb;
        background-color: #f9fafb;
        flex-shrink: 0;
        position: sticky;
        bottom: 0;
        border-radius: 0 0 12px 12px;
    }

    .btn-save {
        background: #dc2626;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
    }

    .btn-save:hover {
        background: #b91c1c;
        box-shadow: 0 4px 8px rgba(185, 28, 28, 0.3);
    }

    .btn-cancel {
        background: white;
        color: #6b7280;
        padding: 12px 24px;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .btn-edit-cancel-mobile {
        transition: all 0.2s ease;
    }

    .btn-edit-cancel-mobile:hover {
        opacity: 0.8;
    }

    .btn-edit-cancel-mobile:active {
        transform: scale(0.95);
    }

    #emailInput[readonly] {
        background-color: #f9fafb;
        cursor: not-allowed;
    }

    #emailInput:not([readonly]) {
        background-color: white;
        cursor: text;
    }

    #mobileNumberInput[readonly] {
        background-color: #f9fafb;
        cursor: not-allowed;
    }

    #mobileNumberInput:not([readonly]) {
        background-color: white;
        cursor: text;
    }
</style>

<script>
    let selectedFile = null;
    let originalMobileNumber = '';
    let originalEmailAddress = '';
    let isMobileVerified = false;
    let isEmailVerified = false;
    let mobileOtpTimer = null;
    let emailOtpTimer = null;
    let mobileCooldownTimer = null;
    let emailCooldownTimer = null;
    let currentLocationType = 0; // 0 = Inside India, 1 = Outside India

    // Initialize original values when modal opens
    function initializeModal() {
        const mobileInput = document.getElementById('mobileNumberInput');
        const emailInput = document.getElementById('emailInput');
        const locationType = document.querySelector('input[name="location_type"]:checked').value;
        
        currentLocationType = parseInt(locationType);
        
        // Only set original values if they haven't been set already by populateFormData
        if (!originalMobileNumber) {
            originalMobileNumber = mobileInput.value;
        }
        if (!originalEmailAddress) {
            originalEmailAddress = emailInput.value;
        }
        
        isMobileVerified = true; // Original values are considered verified
        isEmailVerified = true;
        
        toggleVerificationMethod(true); // Set up the UI based on location type, preserve country code
    }

    // Toggle verification method based on location type
    function toggleVerificationMethod(preserveCountryCode = false) {
        const locationType = document.querySelector('input[name="location_type"]:checked').value;
        currentLocationType = parseInt(locationType);
        
        // Only load country codes if not preserving existing selection
        if (!preserveCountryCode) {
            // Get current selected country code before reloading
            const countryCodeSelect = document.getElementById('countryCodeSelect');
            const currentSelection = countryCodeSelect ? countryCodeSelect.value : null;
            
            // Load country codes based on location type, preserving current selection
            loadCountryCodesByLocation(currentLocationType, currentSelection);
        }
        
        const editCancelEmailBtn = document.getElementById('editCancelEmailBtn');
        const verifyEmailBtn = document.getElementById('verifyEmailBtn');
        const emailInput = document.getElementById('emailInput');
        
        const editCancelMobileBtn = document.getElementById('editCancelMobileBtn');
        const verifyMobileBtn = document.getElementById('verifyMobileBtn');
        const mobileInput = document.getElementById('mobileNumberInput');
        
        if (currentLocationType === 1) {
            // Outside India - Email verification required, mobile is free to edit
            editCancelEmailBtn.style.display = 'block';
            verifyEmailBtn.style.display = 'block';
            emailInput.readOnly = true; // Email requires verification like mobile does for Inside India
            
            // Hide all mobile verification controls
            editCancelMobileBtn.style.display = 'none';
            verifyMobileBtn.style.display = 'none';
            mobileInput.readOnly = false; // Mobile is free to edit
            
            // Mark mobile as verified since no verification needed
            isMobileVerified = true;
        } else {
            // Inside India - Mobile verification required, email is free to edit
            editCancelEmailBtn.style.display = 'none';
            verifyEmailBtn.style.display = 'none';
            emailInput.readOnly = false; // Email is free to edit
            
            // Show mobile verification controls
            editCancelMobileBtn.style.display = 'block';
            verifyMobileBtn.style.display = 'block';
            mobileInput.readOnly = true; // Mobile requires verification
            
            // Mark email as verified since no verification needed
            isEmailVerified = true;
        }
        
        // Reset verification states
        resetMobileVerificationState();
        resetEmailVerificationState();
    }

    // Add new function to load country codes by location
    function loadCountryCodesByLocation(locationType, selectedCountryCode = null) {
        const url = `{{ route('alumni.country-codes', '') }}/${locationType}`;
        
        return fetch(url, { credentials: "include" })
            .then(res => {
                if (!res.ok) throw new Error('Failed to load country codes');
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    const countryCodeSelect = document.getElementById('countryCodeSelect');
                    if (!countryCodeSelect) {
                        return;
                    }

                    // Clear existing options
                    countryCodeSelect.innerHTML = '';
                    
                    if (locationType == 0) {
                        // Inside India - show only +91
                        countryCodeSelect.innerHTML = '<option value="91" selected>+91</option>';
                        countryCodeSelect.disabled = true;
                        countryCodeSelect.style.backgroundColor = '#f8f9fa';
                    } else {
                        // Outside India - show all international codes
                        countryCodeSelect.disabled = false;
                        countryCodeSelect.style.backgroundColor = 'white';
                        
                        // Build options HTML
                        let optionsHTML = '<option value="">Select</option>';
                        let selectedFound = false;
                        
                        data.countryCodes.forEach(country => {
                            // Store value without +, but display with +
                            const valueWithoutPlus = country.dial_code.replace('+', '');
                            const displayWithPlus = country.dial_code.startsWith('+') ? country.dial_code : '+' + country.dial_code;
                            
                            const isSelected = selectedCountryCode && (
                                valueWithoutPlus === selectedCountryCode ||
                                valueWithoutPlus === selectedCountryCode.replace('+', '') ||
                                country.dial_code === selectedCountryCode ||
                                displayWithPlus === selectedCountryCode
                            );
                            
                            if (isSelected) {
                                selectedFound = true;
                            }
                            
                            optionsHTML += `<option value="${valueWithoutPlus}" ${isSelected ? 'selected' : ''} title="${country.country_name}">${displayWithPlus}</option>`;
                        });
                        
                        // Set all options at once
                        countryCodeSelect.innerHTML = optionsHTML;
                        
                        // Double-check selection
                        if (selectedCountryCode && selectedFound) {
                            // Force set value again
                            setTimeout(() => {
                                const cleanSelected = selectedCountryCode.replace('+', '');
                                for (let option of countryCodeSelect.options) {
                                    if (option.value === cleanSelected) {
                                        countryCodeSelect.value = option.value;
                                        break;
                                    }
                                }
                            }, 50);
                        }
                        
                    }
                }
                return data;
            })
            .catch(err => {
                console.error(' Error loading country codes:', err);
                throw err;
            });
    }

    function closeEditProfileModal() {
        const modal = document.getElementById('editProfileModal');
        if (modal) {
            modal.classList.remove('open');
        }

        // Clear all timers
        if (mobileOtpTimer) {
            clearInterval(mobileOtpTimer);
            mobileOtpTimer = null;
        }
        if (emailOtpTimer) {
            clearInterval(emailOtpTimer);
            emailOtpTimer = null;
        }
        if (mobileCooldownTimer) {
            clearInterval(mobileCooldownTimer);
            mobileCooldownTimer = null;
        }
        if (emailCooldownTimer) {
            clearInterval(emailCooldownTimer);
            emailCooldownTimer = null;
        }

        // Reset mobile verification
        resetMobileVerificationState();
        // Reset email verification
        resetEmailVerificationState();

        // Reset verification flags
        isMobileVerified = false;
        isEmailVerified = false;
        selectedFile = null;
        window.removeImage = false;

        document.querySelectorAll('#editProfileModal .error-message').forEach(el => {
            el.textContent = '';
        });
        document.querySelectorAll('#editProfileModal .form-input').forEach(input => {
            input.style.borderColor = '#ddd';
        });
    }

    // Reset mobile verification state
    function resetMobileVerificationState() {
        const mobileInput = document.getElementById('mobileNumberInput');
        const editCancelBtn = document.getElementById('editCancelMobileBtn');
        const verifyBtn = document.getElementById('verifyMobileBtn');
        const saveBtn = document.querySelector('.btn-save');

        // Clear mobile timers
        if (mobileOtpTimer) {
            clearInterval(mobileOtpTimer);
            mobileOtpTimer = null;
        }
        if (mobileCooldownTimer) {
            clearInterval(mobileCooldownTimer);
            mobileCooldownTimer = null;
        }

        // Reset mobile input value
        mobileInput.disabled = false;
        mobileInput.value = originalMobileNumber;
        
        if (currentLocationType === 0) {
            // Inside India - Mobile verification required
            editCancelBtn.textContent = 'Edit';
            editCancelBtn.style.color = '#dc2626';
            editCancelBtn.style.display = 'block';
            
            mobileInput.readOnly = true;
            verifyBtn.style.display = 'block';
            verifyBtn.disabled = true;
            verifyBtn.style.cursor = 'not-allowed';
            verifyBtn.style.opacity = '0.5';
            verifyBtn.textContent = 'Verify';
            verifyBtn.style.background = '#dc2626';
            
            isMobileVerified = true; // Original number is considered verified
        } else {
            // Outside India - No mobile verification needed
            editCancelBtn.style.display = 'none';
            verifyBtn.style.display = 'none';
            mobileInput.readOnly = false;
            
            isMobileVerified = true; // Always verified for outside India
        }

        // Hide OTP section
        document.getElementById('mobileOtpSection').style.display = 'none';
        document.getElementById('mobileOtpInput').value = '';

        // Update save button state
        updateSaveButtonState();
    }

    // Reset email verification state
    function resetEmailVerificationState() {
        const emailInput = document.getElementById('emailInput');
        const editCancelBtn = document.getElementById('editCancelEmailBtn');
        const verifyBtn = document.getElementById('verifyEmailBtn');
        const saveBtn = document.querySelector('.btn-save');

        // Clear email timers
        if (emailOtpTimer) {
            clearInterval(emailOtpTimer);
            emailOtpTimer = null;
        }
        if (emailCooldownTimer) {
            clearInterval(emailCooldownTimer);
            emailCooldownTimer = null;
        }

        // Reset email input value
        emailInput.disabled = false;
        emailInput.value = originalEmailAddress;
        
        if (currentLocationType === 1) {
            // Outside India - Email verification required
            editCancelBtn.textContent = 'Edit';
            editCancelBtn.style.color = '#dc2626';
            editCancelBtn.style.display = 'block';
            
            emailInput.readOnly = true;
            verifyBtn.style.display = 'block';
            verifyBtn.disabled = true;
            verifyBtn.style.cursor = 'not-allowed';
            verifyBtn.style.opacity = '0.5';
            verifyBtn.textContent = 'Verify';
            verifyBtn.style.background = '#dc2626';
            
            isEmailVerified = true; // Original email is considered verified
        } else {
            // Inside India - No email verification needed
            editCancelBtn.style.display = 'none';
            verifyBtn.style.display = 'none';
            emailInput.readOnly = false;
            
            isEmailVerified = true; // Always verified for inside India
        }

        // Hide OTP section
        document.getElementById('emailOtpSection').style.display = 'none';
        document.getElementById('emailOtpInput').value = '';

        // Update save button state
        updateSaveButtonState();
    }

    // Update save button state based on verification requirements
    function updateSaveButtonState() {
        const saveBtn = document.querySelector('.btn-save');
        
        let canSave = true;
        
        if (currentLocationType === 0) {
            // Inside India - Only mobile verification required
            canSave = isMobileVerified;
        } else {
            // Outside India - Only email verification required
            canSave = isEmailVerified;
        }
        
        if (canSave) {
            saveBtn.disabled = false;
            saveBtn.style.cursor = 'pointer';
            saveBtn.style.opacity = '1';
        } else {
            saveBtn.disabled = true;
            saveBtn.style.cursor = 'not-allowed';
            saveBtn.style.opacity = '0.5';
        }
    }

    // Toggle mobile editing
    function toggleMobileEdit() {
        // This function should only work for Inside India (location_type = 0)
        if (currentLocationType !== 0) {
            return; // Do nothing for Outside India
        }
        
        const mobileInput = document.getElementById('mobileNumberInput');
        const editCancelBtn = document.getElementById('editCancelMobileBtn');
        const verifyBtn = document.getElementById('verifyMobileBtn');

        if (mobileInput.readOnly) {
            // Enable editing mode
            mobileInput.readOnly = false;
            mobileInput.focus();
            editCancelBtn.textContent = 'Cancel';
            editCancelBtn.style.color = '#6b7280';

            // Reset verification state
            isMobileVerified = false;
            updateSaveButtonState();
        } else {
            // Cancel editing mode
            resetMobileVerificationState();
        }
    }

    // Toggle email editing
    function toggleEmailEdit() {
        // This function should only work for Outside India (location_type = 1)
        if (currentLocationType !== 1) {
            return; // Do nothing for Inside India
        }
        
        const emailInput = document.getElementById('emailInput');
        const editCancelBtn = document.getElementById('editCancelEmailBtn');
        const verifyBtn = document.getElementById('verifyEmailBtn');

        if (emailInput.readOnly) {
            // Enable editing mode
            emailInput.readOnly = false;
            emailInput.focus();
            editCancelBtn.textContent = 'Cancel';
            editCancelBtn.style.color = '#6b7280';

            // Reset verification state
            isEmailVerified = false;
            updateSaveButtonState();
        } else {
            // Cancel editing mode
            resetEmailVerificationState();
        }
    }

    // Mobile number validation
    function validateMobileNumber(input) {
        // Allow only numbers
        input.value = input.value.replace(/[^0-9]/g, '');

        const mobileNumber = input.value;
        const verifyBtn = document.getElementById('verifyMobileBtn');

        // For Outside India (location_type = 1), no mobile verification needed
        if (currentLocationType === 1) {
            // Just mark as verified since no verification is required
            isMobileVerified = true;
            updateSaveButtonState();
            return;
        }

        // For Inside India (location_type = 0), mobile verification is required
        // Check if number changed and is 10 digits
        if (mobileNumber.length === 10 && mobileNumber !== originalMobileNumber) {
            verifyBtn.disabled = false;
            verifyBtn.style.cursor = 'pointer';
            verifyBtn.style.opacity = '1';
            verifyBtn.style.background = '#dc2626';
            verifyBtn.textContent = 'Verify';
            isMobileVerified = false;
        } else if (mobileNumber === originalMobileNumber) {
            // Same number - no verification needed
            verifyBtn.disabled = true;
            verifyBtn.style.cursor = 'not-allowed';
            verifyBtn.style.opacity = '0.5';
            isMobileVerified = true;
        } else {
            verifyBtn.disabled = true;
            verifyBtn.style.cursor = 'not-allowed';
            verifyBtn.style.opacity = '0.5';
            isMobileVerified = false;
        }
        
        updateSaveButtonState();
    }

    // Email validation
    function validateEmailVerification(input) {
        const emailValue = input.value;
        const verifyBtn = document.getElementById('verifyEmailBtn');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        // For Inside India (location_type = 0), email is free to edit - no verification needed
        if (currentLocationType === 0) {
            isEmailVerified = true;
            updateSaveButtonState();
            return;
        }
        
        // For Outside India (location_type = 1), email verification is required
        if (emailRegex.test(emailValue) && emailValue !== originalEmailAddress) {
            verifyBtn.disabled = false;
            verifyBtn.style.cursor = 'pointer';
            verifyBtn.style.opacity = '1';
            verifyBtn.style.background = '#dc2626';
            verifyBtn.textContent = 'Verify';
            isEmailVerified = false;
        } else if (emailValue === originalEmailAddress) {
            // Same email - no verification needed
            verifyBtn.disabled = true;
            verifyBtn.style.cursor = 'not-allowed';
            verifyBtn.style.opacity = '0.5';
            isEmailVerified = true;
        } else {
            verifyBtn.disabled = true;
            verifyBtn.style.cursor = 'not-allowed';
            verifyBtn.style.opacity = '0.5';
            isEmailVerified = false;
        }
        
        updateSaveButtonState();
    }

    // Send Mobile OTP
    function sendMobileOTP() {
        const mobileNumber = document.getElementById('mobileNumberInput').value;

        if (mobileNumber.length !== 10) {
            showToast('Please enter a valid 10 digit mobile number', 'error');
            return;
        }

        const verifyBtn = document.getElementById('verifyMobileBtn');
        verifyBtn.textContent = 'Sending...';
        verifyBtn.disabled = true;

        fetch('{{ route("send.otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    number: mobileNumber,
                    location_type: 0,
                    is_login: 0
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('OTP sent to your mobile number successfully');
                    document.getElementById('mobileOtpSection').style.display = 'block';
                    document.getElementById('mobileNumberInput').disabled = true;
                    
                    // Start 30-second cooldown timer
                    startMobileOTPCooldown();
                    startMobileOTPTimer();
                } else {
                    showToast(data.message || 'Failed to send OTP', 'error');
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify';
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('Failed to send OTP', 'error');
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify';
            });
    }

    // Send Email OTP
    function sendEmailOTP() {
        const emailAddress = document.getElementById('emailInput').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(emailAddress)) {
            showToast('Please enter a valid email address', 'error');
            return;
        }

        const verifyBtn = document.getElementById('verifyEmailBtn');
        verifyBtn.textContent = 'Sending...';
        verifyBtn.disabled = true;

        fetch('{{ route("send.otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    number: emailAddress,
                    location_type: 1,
                    is_login: 0
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('OTP sent to your email address successfully');
                    document.getElementById('emailOtpSection').style.display = 'block';
                    document.getElementById('emailInput').disabled = true;
                    
                    // Start 30-second cooldown timer
                    startEmailOTPCooldown();
                    startEmailOTPTimer();
                } else {
                    showToast(data.error || 'Failed to send OTP', 'error');
                    verifyBtn.disabled = false;
                    verifyBtn.textContent = 'Verify';
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('Failed to send OTP', 'error');
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify';
            });
    }

    // Verify OTP
    function verifyOTP() {
        const contactValue = document.getElementById('contactInput').value;
        const otp = document.getElementById('otpInput').value;

        if (otp.length !== 6) {
            showToast('Please enter a valid 6 digit OTP', 'error');
            return;
        }

        const requestData = {
            otp: otp
        };

        // Set the correct field based on location type
        if (currentLocationType === 0) {
            requestData.mobile = contactValue;
        } else {
            requestData.email = contactValue;
        }

        fetch('{{ route("alumni.edit.verify.otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const message = currentLocationType === 0 
                        ? 'Mobile number verified successfully'
                        : 'Email address verified successfully';
                    showToast(message);
                    isContactVerified = true;
                    document.getElementById('otpSection').style.display = 'none';

                    const verifyBtn = document.getElementById('verifyContactBtn');
                    verifyBtn.textContent = 'Verified ✓';
                    verifyBtn.style.background = '#10b981';
                    verifyBtn.disabled = true;

                    // Enable save button after verification
                    const saveBtn = document.querySelector('.btn-save');
                    saveBtn.disabled = false;
                    saveBtn.style.cursor = 'pointer';
                    saveBtn.style.opacity = '1';

                    if (otpTimer) clearInterval(otpTimer);
                } else {
                    showToast(data.message || 'Invalid OTP', 'error');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('Failed to verify OTP', 'error');
            });
    }

    // OTP Timer
    function startOTPTimer() {
        let timeLeft = 30;
        const timerEl = document.getElementById('otpTimer');

        if (otpTimer) clearInterval(otpTimer);

        otpTimer = setInterval(() => {
            timeLeft--;
            const seconds = timeLeft;
            timerEl.textContent = `OTP expires in ${seconds} seconds`;

            if (timeLeft <= 0) {
                clearInterval(otpTimer);
                timerEl.textContent = 'OTP expired. Please request a new one.';
                document.getElementById('otpSection').style.display = 'none';
                document.getElementById('mobileNumberInput').disabled = false;
            }
        }, 1000);
    }

    // Verify Mobile OTP
    function verifyMobileOTP() {
        const mobileNumber = document.getElementById('mobileNumberInput').value;
        const otp = document.getElementById('mobileOtpInput').value;

        if (otp.length !== 6) {
            showToast('Please enter a valid 6 digit OTP', 'error');
            return;
        }

        const verifyOtpBtn = document.querySelector('#mobileOtpSection button');
        const originalText = verifyOtpBtn.textContent;
        verifyOtpBtn.textContent = 'Verifying...';
        verifyOtpBtn.disabled = true;

        fetch('{{ route("alumni.edit.verify.otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    mobile: mobileNumber,
                    otp: otp
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Mobile number verified successfully');
                    isMobileVerified = true;
                    document.getElementById('mobileOtpSection').style.display = 'none';
                    document.getElementById('mobileOtpInput').value = ''; // Clear OTP input

                    const verifyBtn = document.getElementById('verifyMobileBtn');
                    verifyBtn.textContent = 'Verified ✓';
                    verifyBtn.style.background = '#10b981';
                    verifyBtn.disabled = true;

                    // Update save button state
                    updateSaveButtonState();

                    if (mobileOtpTimer) {
                        clearInterval(mobileOtpTimer);
                        mobileOtpTimer = null;
                    }
                    if (mobileCooldownTimer) {
                        clearInterval(mobileCooldownTimer);
                        mobileCooldownTimer = null;
                    }
                } else {
                    showToast(data.message || 'Invalid OTP', 'error');
                    verifyOtpBtn.textContent = originalText;
                    verifyOtpBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('Failed to verify OTP', 'error');
                verifyOtpBtn.textContent = originalText;
                verifyOtpBtn.disabled = false;
            });
    }

    // Verify Email OTP
    function verifyEmailOTP() {
        const emailAddress = document.getElementById('emailInput').value;
        const otp = document.getElementById('emailOtpInput').value;

        if (otp.length !== 6) {
            showToast('Please enter a valid 6 digit OTP', 'error');
            return;
        }

        const verifyOtpBtn = document.querySelector('#emailOtpSection button');
        const originalText = verifyOtpBtn.textContent;
        verifyOtpBtn.textContent = 'Verifying...';
        verifyOtpBtn.disabled = true;

        fetch('{{ route("alumni.edit.verify.otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: emailAddress,
                    otp: otp
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Email address verified successfully');
                    isEmailVerified = true;
                    document.getElementById('emailOtpSection').style.display = 'none';
                    document.getElementById('emailOtpInput').value = ''; // Clear OTP input

                    const verifyBtn = document.getElementById('verifyEmailBtn');
                    verifyBtn.textContent = 'Verified ✓';
                    verifyBtn.style.background = '#10b981';
                    verifyBtn.disabled = true;

                    // Update save button state
                    updateSaveButtonState();

                    if (emailOtpTimer) {
                        clearInterval(emailOtpTimer);
                        emailOtpTimer = null;
                    }
                    if (emailCooldownTimer) {
                        clearInterval(emailCooldownTimer);
                        emailCooldownTimer = null;
                    }
                } else {
                    showToast(data.message || 'Invalid OTP', 'error');
                    verifyOtpBtn.textContent = originalText;
                    verifyOtpBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('Failed to verify OTP', 'error');
                verifyOtpBtn.textContent = originalText;
                verifyOtpBtn.disabled = false;
            });
    }

    // Mobile OTP Timer
    function startMobileOTPTimer() {
        let timeLeft = 30;
        const timerEl = document.getElementById('mobileOtpTimer');

        if (mobileOtpTimer) clearInterval(mobileOtpTimer);

        mobileOtpTimer = setInterval(() => {
            timeLeft--;
            const seconds = timeLeft;
            timerEl.textContent = `OTP expires in ${seconds} seconds`;

            if (timeLeft <= 0) {
                clearInterval(mobileOtpTimer);
                timerEl.textContent = 'OTP expired. Please request a new one.';
                document.getElementById('mobileOtpSection').style.display = 'none';
                document.getElementById('mobileNumberInput').disabled = false;
            }
        }, 1000);
    }

    // Mobile OTP Cooldown Timer (30 seconds before allowing to send again)
    function startMobileOTPCooldown() {
        let cooldownTime = 30;
        const verifyBtn = document.getElementById('verifyMobileBtn');
        
        // Clear existing cooldown timer
        if (mobileCooldownTimer) {
            clearInterval(mobileCooldownTimer);
        }
        
        mobileCooldownTimer = setInterval(() => {
            cooldownTime--;
            verifyBtn.textContent = `Wait ${cooldownTime}s`;
            verifyBtn.disabled = true;
            verifyBtn.style.opacity = '0.5';
            verifyBtn.style.cursor = 'not-allowed';

            if (cooldownTime <= 0) {
                clearInterval(mobileCooldownTimer);
                mobileCooldownTimer = null;
                verifyBtn.textContent = 'Verify';
                verifyBtn.disabled = false;
                verifyBtn.style.opacity = '1';
                verifyBtn.style.cursor = 'pointer';
            }
        }, 1000);
    }

    // Email OTP Timer
    function startEmailOTPTimer() {
        let timeLeft = 30;
        const timerEl = document.getElementById('emailOtpTimer');

        if (emailOtpTimer) clearInterval(emailOtpTimer);

        emailOtpTimer = setInterval(() => {
            timeLeft--;
            const seconds = timeLeft;
            timerEl.textContent = `OTP expires in ${seconds} seconds`;

            if (timeLeft <= 0) {
                clearInterval(emailOtpTimer);
                timerEl.textContent = 'OTP expired. Please request a new one.';
                document.getElementById('emailOtpSection').style.display = 'none';
                document.getElementById('emailInput').disabled = false;
            }
        }, 1000);
    }

    // Email OTP Cooldown Timer (30 seconds before allowing to send again)
    function startEmailOTPCooldown() {
        let cooldownTime = 30;
        const verifyBtn = document.getElementById('verifyEmailBtn');
        
        // Clear existing cooldown timer
        if (emailCooldownTimer) {
            clearInterval(emailCooldownTimer);
        }
        
        emailCooldownTimer = setInterval(() => {
            cooldownTime--;
            verifyBtn.textContent = `Wait ${cooldownTime}s`;
            verifyBtn.disabled = true;
            verifyBtn.style.opacity = '0.5';
            verifyBtn.style.cursor = 'not-allowed';

            if (cooldownTime <= 0) {
                clearInterval(emailCooldownTimer);
                emailCooldownTimer = null;
                verifyBtn.textContent = 'Verify';
                verifyBtn.disabled = false;
                verifyBtn.style.opacity = '1';
                verifyBtn.style.cursor = 'pointer';
            }
        }, 1000);
    }

    function changeProfileImage() {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
                const maxSize = 2 * 1024 * 1024;
                if (!allowedTypes.includes(file.type)) {
                    showToast('Please select a valid image file', 'error');
                    return;
                }
                if (file.size > maxSize) {
                    showToast('Maximum image size allowed is 2 MB', 'error');
                    return;
                }
                selectedFile = file;
                const reader = new FileReader();
                reader.onload = function(event) {
                    const profileImg = document.querySelector('.modal-profile-img');
                    if (profileImg) {
                        profileImg.src = event.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        document.body.appendChild(fileInput);
        fileInput.click();
        document.body.removeChild(fileInput);
    }

    function removeProfileImage() {
        const profileImg = document.querySelector('.modal-profile-img');
        if (profileImg) {
            profileImg.src = "{{ asset('images/avatar/blank.png') }}";
            selectedFile = null;
            window.removeImage = true;
        }
    }

    function saveProfile() {
        const form = document.getElementById('editProfileForm');
        const mobileNumber = document.getElementById('mobileNumberInput').value;
        const emailAddress = document.getElementById('emailInput').value;

        // Check if mobile number changed but not verified (only for Inside India)
        if (currentLocationType === 0 && mobileNumber !== originalMobileNumber && !isMobileVerified) {
            showToast('Please verify your new mobile number before saving', 'error');
            return;
        }

        // Check if email changed but not verified (only for Outside India)
        if (currentLocationType === 1 && emailAddress !== originalEmailAddress && !isEmailVerified) {
            showToast('Please verify your new email address before saving', 'error');
            return;
        }

        const formData = new FormData();

        // Clear old error messages
        form.querySelectorAll('.error-message').forEach(el => el.textContent = '');

        const inputs = form.querySelectorAll('.form-input');
        inputs.forEach(input => {
            const fieldName = input.getAttribute('data-field');
            let value = input.value.trim();
            
            // Remove + symbol from country code before sending to backend
            if (fieldName === 'country_code' && value.startsWith('+')) {
                value = value.substring(1);
            }
            
            formData.append(fieldName, value);
        });

        // Add location_type from radio button
        const locationType = document.querySelector('input[name="location_type"]:checked').value;
        formData.append('location_type', locationType);

        if (selectedFile) {
            formData.append('image', selectedFile);
        }
        if (window.removeImage) {
            formData.append('remove_image', 1);
        }


        const modal = document.getElementById('editProfileModal');
        const alumniId = modal.getAttribute('data-alumni-id');
        if (!alumniId) {
            alert(' Alumni ID not found');
            return;
        }

        const saveBtn = document.querySelector('.btn-save');
        const originalText = saveBtn.textContent;
        saveBtn.textContent = 'Saving...';
        saveBtn.disabled = true;

        fetch(`{{ route('alumni.profile.update', '') }}/${alumniId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json().catch(() => null);

                if (!res.ok) {
                    if (res.status === 422 && data && data.errors) {
                        for (const [key, messages] of Object.entries(data.errors)) {
                            const input = form.querySelector(`.form-input[data-field="${key}"]`);
                            if (input) {
                                let errorEl;
                                
                                if (key === 'country_code') {
                                    // For country code, find the first error message in the contact number section
                                    const contactSection = input.closest('.form-group');
                                    const errorMessages = contactSection.querySelectorAll('.error-message');
                                    errorEl = errorMessages[0]; // First error message is for country code
                                } else if (key === 'mobile_number') {
                                    // For mobile number, find the second error message in the contact number section
                                    const contactSection = input.closest('.form-group');
                                    const errorMessages = contactSection.querySelectorAll('.error-message');
                                    errorEl = errorMessages[1]; // Second error message is for mobile number
                                } else {
                                    // For other fields, use the standard logic
                                    errorEl = input.parentElement.querySelector('.error-message');
                                }
                                
                                if (errorEl) {
                                    errorEl.textContent = messages[0];
                                }
                            }
                            if (key === 'image') {
                                showToast(messages[0], 'error');
                            }
                        }
                    } else {
                        showToast(` ${data?.message || 'Server error'}`, 'error');
                    }
                    throw new Error(data?.message || `HTTP ${res.status}`);
                }

                return data;
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    closeEditProfileModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(err => console.error('Error:', err))
            .finally(() => {
                saveBtn.textContent = originalText;
                saveBtn.disabled = false;
            });
    }

    // Make sure the modal uses the existing functions from sidebar
    // The dropdown population functions are already defined in the sidebar
</script>