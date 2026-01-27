<!-- Alumni Details Modal -->
<div class="modal fade" id="alumniDetailsModal" tabindex="-1" aria-labelledby="alumniDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mb-4">
                <h2 class="modal-title fs-1" id="alumniDetailsModalLabel">
                    Alumni Details
                </h2>
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="alumniDetailsContent">
                <!-- Loading state -->
                <div id="detailsLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading alumni details...</p>
                </div>

                <!-- Content will be loaded here -->
                <div id="detailsContent" style="display: none;">
                    <div class="row">
                        <!-- Profile Picture Section -->
                        <div class="col-md-4 text-center border-end border-2 border-gray-300">
                            <div class="profile-picture-container" style="position: relative; display: inline-block;">
                                <img id="modalProfilePic" src="" alt="Profile Picture" 
                                     class="img-fluid rounded-circle shadow" 
                                     style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #ba0028;">
                                <div class="profile-status-badge" id="modalStatusBadge" 
                                     style="position: absolute; bottom: 10px; right: 10px; 
                                            padding: 4px 8px; border-radius: 12px; 
                                            font-size: 10px; font-weight: 600; color: white;">
                                </div>
                            </div>
                            <h4 id="modalAlumniName" class="mt-3 mb-1" style="color: #ba0028; font-weight: 600;"></h4>
                            <p id="modalAlumniBatch" class="text-muted mb-0" style="font-size: 14px;"></p>
                        </div>

                        <!-- Details Section -->
                        <div class="col-md-8">
                            <div class="details-grid">
                                <!-- Contact Information -->
                                <div class="detail-section mb-4">
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    Email
                                                </label>
                                                <p id="modalAlumniEmail" class="detail-value"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    Mobile Number
                                                </label>
                                                <p id="modalAlumniMobile" class="detail-value"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Academic Information -->
                                <div class="detail-section mb-4">
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    Year of Completion
                                                </label>
                                                <p id="modalAlumniBatchDetail" class="detail-value"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    Occupation
                                                </label>
                                                <p id="modalAlumniOccupation" class="detail-value"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Professional Information -->
                                <div class="detail-section mb-4">
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    State
                                                </label>
                                                <p id="modalAlumniState" class="detail-value"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    City
                                                </label>
                                                <p id="modalAlumniCity" class="detail-value"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="detail-section mb-4">
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    Pincode
                                                </label>
                                                <p id="modalAlumniPincode" class="detail-value"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    Center Location
                                                </label>
                                                <p id="modalAlumniCenterLocation" class="detail-value"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="detail-section mb-4">
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    Current Location
                                                </label>
                                                <p id="modalAlumniCurrentLocation" class="detail-value"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    Level Completed
                                                </label>
                                                <p id="modalAlumniLevelCompleted" class="detail-value"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="detail-section mb-4">
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    LinkedIn Profile
                                                </label>
                                                <p id="modalAlumniLinkedInProfile" class="detail-value"></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    Organization
                                                </label>
                                                <p id="modalAlumniOrganization" class="detail-value"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="detail-section mb-4">
                                    <div class="row">
                                        <div class="col-sm-6 mb-3">
                                            <div class="detail-item">
                                                <label class="detail-label">
                                                    University
                                                </label>
                                                <p id="modalAlumniUniversity" class="detail-value"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error state -->
                <div id="detailsError" style="display: none;" class="text-center py-4">
                    <div class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                    <h5 class="text-danger">Error Loading Details</h5>
                    <p class="text-muted" id="errorMessage">Failed to load alumni details. Please try again.</p>
                    <button type="button" class="btn btn-outline-primary" onclick="retryLoadDetails()">
                        <i class="fas fa-redo me-2"></i>Retry
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detail-label {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    display: block;
}

.detail-value {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin: 0;
    padding: 8px 12px;
    background-color: #f8f9fa;
    border-radius: 6px;
    border-left: 3px solid #ba0028;
}

.detail-item {
    margin-bottom: 15px;
}

.section-title {
    font-size: 16px;
    margin-bottom: 20px;
}

.profile-picture-container {
    margin-bottom: 20px;
}

#alumniDetailsModal .modal-content {
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

#alumniDetailsModal .modal-header {
    border-bottom: none;
}

#alumniDetailsModal .modal-footer {
    border-top: 1px solid #dee2e6;
}

.details-grid {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 10px;
}

.details-grid::-webkit-scrollbar {
    width: 6px;
}

.details-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.details-grid::-webkit-scrollbar-thumb {
    background: #ba0028;
    border-radius: 3px;
}

.details-grid::-webkit-scrollbar-thumb:hover {
    background: #9a0022;
}
</style>

<script>
let currentAlumniId = null;

function openDetailsModal(alumniId) {
    currentAlumniId = alumniId;
    
    // Reset modal state
    $('#detailsLoading').show();
    $('#detailsContent').hide();
    $('#detailsError').hide();
    
    // Show modal
    $('#alumniDetailsModal').modal('show');
    
    // Load alumni details
    loadAlumniDetails(alumniId);
}

function loadAlumniDetails(alumniId) {
    $.ajax({
        url: "{{ route('admin.directory.view.profile', '') }}/" + alumniId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                populateModalWithData(response.data);
                $('#detailsLoading').hide();
                $('#detailsContent').show();
            } else {
                showError(response.message || 'Failed to load alumni details');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Failed to load alumni details';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showError(errorMessage);
        }
    });
}

function populateModalWithData(data) {
    // Profile picture and basic info
    $('#modalProfilePic').attr('src', data.image_url || "{{ asset('images/avatar/blank.png') }}");
    $('#modalAlumniName').text(data.name || 'N/A');
    $('#modalAlumniBatch').text('Batch ' + (data.batch || 'N/A'));
    
    // Status badge
    const statusBadge = $('#modalStatusBadge');
    if (data.status) {
        const status = data.status.toLowerCase();
        let badgeStyle = '';
        let badgeText = '';
        
        switch(status) {
            case 'active':
                badgeStyle = 'background-color: #28a745;';
                badgeText = 'Active';
                break;
            case 'blocked':
                badgeStyle = 'background-color: #dc3545;';
                badgeText = 'Blocked';
                break;
            default:
                badgeStyle = 'background-color: #6c757d;';
                badgeText = 'Inactive';
        }
        
        statusBadge.attr('style', statusBadge.attr('style').replace(/background-color:[^;]*;?/, '') + badgeStyle).text(badgeText).show();
    } else {
        statusBadge.hide();
    }
    
    // Contact information
    $('#modalAlumniEmail').text(data.email || 'N/A');
    $('#modalAlumniMobile').text(data.mobile_number || 'N/A');
    
    // Academic information
    $('#modalAlumniBatchDetail').text(data.batch || 'N/A');
    $('#modalAlumniCenter').text(data.center_location || 'N/A');

    // Academic information
    $('#modalAlumniState').text(data.state || 'N/A');
    $('#modalAlumniCity').text(data.city || 'N/A');
    $('#modalAlumniPincode').text(data.pincode || 'N/A');
    $('#modalAlumniCenterLocation').text(data.center_location || 'N/A');
    $('#modalAlumniCurrentLocation').text(data.current_location || 'N/A');
    $('#modalAlumniLevelCompleted').text(data.level_completed || 'N/A');
    $('#modalAlumniLinkedInProfile').text(data.linkedin_profile || 'N/A');
    $('#modalAlumniOrganization').text(data.organization || 'N/A');
    $('#modalAlumniUniversity').text(data.university || 'N/A');
    
    // Professional information
    $('#modalAlumniOccupation').text(data.occupation || 'N/A');
    $('#modalAlumniCompany').text(data.company || 'N/A');
    
    // Location information
    $('#modalAlumniLocation').text(data.location || 'N/A');
}

function showError(message) {
    $('#detailsLoading').hide();
    $('#detailsContent').hide();
    $('#errorMessage').text(message);
    $('#detailsError').show();
}

function retryLoadDetails() {
    if (currentAlumniId) {
        $('#detailsError').hide();
        $('#detailsLoading').show();
        loadAlumniDetails(currentAlumniId);
    }
}

// Reset modal when closed
$('#alumniDetailsModal').on('hidden.bs.modal', function () {
    currentAlumniId = null;
    $('#detailsLoading').show();
    $('#detailsContent').hide();
    $('#detailsError').hide();
});
</script>