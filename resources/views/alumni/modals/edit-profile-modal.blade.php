@php
$alumni = $alumni ?? null;
$city = $alumni && isset($alumni->city) ? $alumni->city : null;
$state = $city && isset($city->state) ? $city->state : null;
$occupation = $alumni && isset($alumni->occupation) ? $alumni->occupation : null;
@endphp

<!-- Edit Profile Modal Popup -->
<div id="editProfileModal" class="modal-overlay" data-alumni-id="{{ $alumni->id ?? '' }}">
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

            <form id="editProfileForm">
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

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" class="form-input" data-field="email" value="{{ $alumni->email ?? '' }}">
                    <small class="error-message" style="color:red;font-size:12px;"></small>
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
                        <div style="flex: 1;">
                            <input type="text" class="form-input" data-field="mobile_number" id="mobileNumberInput"
                                value="{{ $alumni->mobile_number ?? '' }}"
                                maxlength="10"
                                placeholder="Enter 10 digit mobile number"
                                oninput="validateMobileNumber(this)"
                                readonly>
                            <small class="error-message" style="color:red;font-size:12px;display:block;margin-top:4px;"></small>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                            <button type="button" id="verifyMobileBtn" class="btn-verify" disabled onclick="sendOTP()"
                                style="padding: 10px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: not-allowed; opacity: 0.5; white-space: nowrap;">
                                Verify
                            </button>
                        </div>
                    </div>
                    <div id="otpSection" style="display: none; margin-top: 12px; padding: 12px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb;">
                        <label style="font-size: 13px; font-weight: 600; color: #1f2937; margin-bottom: 8px; display: block;">Enter OTP</label>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="otpInput" class="form-input" maxlength="6" placeholder="Enter 6 digit OTP"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                style="flex: 1;">
                            <button type="button" onclick="verifyOTP()" class="btn-verify"
                                style="padding: 10px 16px; background: #10b981; color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                                Verify OTP
                            </button>
                        </div>
                        <small id="otpTimer" style="color: #6b7280; font-size: 12px; display: block; margin-top: 6px;"></small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Current Occupation</label>
                    <select class="form-input" data-field="occupation_id" id="occupationSelect">
                        <option value="">Select Occupation</option>
                    </select>
                    <small class="error-message" style="color:red;font-size:12px;"></small>
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
        max-width: 500px;
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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
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
    let isMobileVerified = false;
    let otpTimer = null;

    // Initialize original mobile number when modal opens
    function initializeModal() {
        const mobileInput = document.getElementById('mobileNumberInput');
        originalMobileNumber = mobileInput.value;
        isMobileVerified = true; // Original number is considered verified
    }

    function closeEditProfileModal() {
        const modal = document.getElementById('editProfileModal');
        if (modal) {
            modal.classList.remove('open');
        }

        // Reset mobile input to readonly
        const mobileInput = document.getElementById('mobileNumberInput');
        mobileInput.readOnly = true;
        mobileInput.disabled = false;
        mobileInput.value = originalMobileNumber;

        // Reset OTP section
        document.getElementById('otpSection').style.display = 'none';
        document.getElementById('otpInput').value = '';
        if (otpTimer) clearInterval(otpTimer);

        // Reset edit/cancel button
        const editCancelBtn = document.getElementById('editCancelMobileBtn');
        const verifyBtn = document.getElementById('verifyMobileBtn');
        if (editCancelBtn) {
            editCancelBtn.textContent = 'Edit';
            editCancelBtn.style.color = '#dc2626';
        }
        verifyBtn.textContent = 'Verify';
        verifyBtn.disabled = true;
        verifyBtn.style.opacity = '0.5';
        verifyBtn.style.cursor = 'not-allowed';

        // Reset save button
        const saveBtn = document.querySelector('.btn-save');
        saveBtn.disabled = false;
        saveBtn.style.opacity = '1';
        saveBtn.style.cursor = 'pointer';

        // Reset verification flag
        isMobileVerified = false;
        selectedFile = null;
        window.removeImage = false;

        document.querySelectorAll('#editProfileModal .error-message').forEach(el => {
            el.textContent = '';
        });
        document.querySelectorAll('#editProfileModal .form-input').forEach(input => {
            input.style.borderColor = '#ddd';
        });
    }

    // Toggle mobile number editing
    function toggleMobileEdit() {
        const mobileInput = document.getElementById('mobileNumberInput');
        const editCancelBtn = document.getElementById('editCancelMobileBtn');
        const verifyBtn = document.getElementById('verifyMobileBtn');
        const saveBtn = document.querySelector('.btn-save');

        if (mobileInput.readOnly) {
            // Enable editing mode
            mobileInput.readOnly = false;
            mobileInput.focus();
            editCancelBtn.textContent = 'Cancel';
            editCancelBtn.style.color = '#6b7280';

            // Reset verification state
            isMobileVerified = false;

            // Disable save button until verified
            saveBtn.disabled = true;
            saveBtn.style.cursor = 'not-allowed';
            saveBtn.style.opacity = '0.5';
        } else {
            // Cancel editing mode
            mobileInput.readOnly = true;
            mobileInput.disabled = false;
            mobileInput.value = originalMobileNumber;
            editCancelBtn.textContent = 'Edit';
            editCancelBtn.style.color = '#dc2626';

            // Reset verify button
            verifyBtn.disabled = true;
            verifyBtn.style.cursor = 'not-allowed';
            verifyBtn.style.opacity = '0.5';
            verifyBtn.textContent = 'Verify';
            verifyBtn.style.background = '#dc2626';

            // Hide OTP section
            document.getElementById('otpSection').style.display = 'none';
            document.getElementById('otpInput').value = '';
            if (otpTimer) clearInterval(otpTimer);

            // Enable save button
            saveBtn.disabled = false;
            saveBtn.style.cursor = 'pointer';
            saveBtn.style.opacity = '1';

            isMobileVerified = true;

            const errorEl = mobileInput.parentElement.querySelector('.error-message');
            if (errorEl) {
                errorEl.textContent = '';
            }
        }
    }

    // Mobile number validation
    function validateMobileNumber(input) {
        // Allow only numbers
        input.value = input.value.replace(/[^0-9]/g, '');

        const mobileNumber = input.value;
        const verifyBtn = document.getElementById('verifyMobileBtn');
        const saveBtn = document.querySelector('.btn-save');

        // Check if number changed and is 10 digits
        if (mobileNumber.length === 10 && mobileNumber !== originalMobileNumber) {
            verifyBtn.disabled = false;
            verifyBtn.style.cursor = 'pointer';
            verifyBtn.style.opacity = '1';
            verifyBtn.style.background = '#dc2626';
            verifyBtn.textContent = 'Verify';
            isMobileVerified = false;

            // Disable save button until verified
            saveBtn.disabled = true;
            saveBtn.style.cursor = 'not-allowed';
            saveBtn.style.opacity = '0.5';
        } else if (mobileNumber === originalMobileNumber) {
            // Same number - no verification needed
            verifyBtn.disabled = true;
            verifyBtn.style.cursor = 'not-allowed';
            verifyBtn.style.opacity = '0.5';
            isMobileVerified = true;

            // Enable save button
            saveBtn.disabled = false;
            saveBtn.style.cursor = 'pointer';
            saveBtn.style.opacity = '1';
        } else {
            verifyBtn.disabled = true;
            verifyBtn.style.cursor = 'not-allowed';
            verifyBtn.style.opacity = '0.5';
        }
    }

    // Send OTP
    function sendOTP() {
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
                    is_login: 0
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'OTP sent successfully');
                    document.getElementById('otpSection').style.display = 'block';
                    document.getElementById('mobileNumberInput').disabled = true;
                    startOTPTimer();
                } else {
                    showToast(data.message || 'Failed to send OTP', 'error');
                    verifyBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('Failed to send OTP', 'error');
                verifyBtn.disabled = false;
            })
            .finally(() => {
                verifyBtn.textContent = 'Verify';
            });
    }

    // Verify OTP
    function verifyOTP() {
        const mobileNumber = document.getElementById('mobileNumberInput').value;
        const otp = document.getElementById('otpInput').value;

        if (otp.length !== 6) {
            showToast('Please enter a valid 6 digit OTP', 'error');
            return;
        }

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
                    document.getElementById('otpSection').style.display = 'none';

                    const verifyBtn = document.getElementById('verifyMobileBtn');
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

        // Check if mobile number changed but not verified
        if (mobileNumber !== originalMobileNumber && !isMobileVerified) {
            showToast('Please verify your new mobile number before saving', 'error');
            return;
        }

        const formData = new FormData();

        // Clear old error messages
        form.querySelectorAll('.error-message').forEach(el => el.textContent = '');

        const inputs = form.querySelectorAll('.form-input');
        inputs.forEach(input => {
            const fieldName = input.getAttribute('data-field');
            formData.append(fieldName, input.value.trim());
        });

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
                                const errorEl = input.parentElement.querySelector('.error-message');
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
</script>