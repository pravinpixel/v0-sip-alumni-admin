@extends('alumni.layouts.index')

@section('content')
<div class="container mt-5">
    <!-- Improved header styling with proper spacing and typography -->
    <div style="margin-bottom:30px;">
        <h2 style="font-weight:700;color:#000;font-size:28px;margin:0;">My Connections</h2>
        <p style="color:#666;font-size:14px;margin-top:4px;">Manage your alumni network and connection requests</p>
    </div>

    <!-- Added search bar with red border styling -->
    <div style="margin-bottom:20px;">
        <input type="text" id="globalSearch" class="form-control" placeholder="Search connections..."
            style="max-width:300px;border:2px solid #c41e3a;border-radius:20px;padding:10px 16px;">
    </div>

    <!-- Tab Navigation -->
    <div style="display:flex;gap:10px;margin-bottom:20px;border-bottom:1px solid #eee;padding-bottom:0;">
        <button class="tab-btn active" data-tab="connections"
            style="background-color:#c41e3a;color:white;border:none;padding:12px 24px;border-radius:4px 4px 0 0;font-weight:600;cursor:pointer;font-size:14px;">
            Connections <span style="background-color:rgba(255,255,255,0.3);padding:2px 8px;border-radius:12px;margin-left:8px;">12</span>
        </button>
        <button class="tab-btn" data-tab="requests"
            style="background-color:#f0f0f0;color:#666;border:none;padding:12px 24px;border-radius:4px 4px 0 0;font-weight:600;cursor:pointer;font-size:14px;">
            Requests <span style="background-color:#ff6b6b;color:white;padding:2px 8px;border-radius:12px;margin-left:8px;">10</span>
        </button>
    </div>

    <div style="margin-top:20px;">
        <!-- Connections Tab -->
        <div id="connections" class="tab-content active">
            <div style="background:white;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                <table id="connectionsTable" class="table table-hover" style="width:100%;margin:0;border-collapse:collapse;">
                    <thead>
                        <tr style="background: linear-gradient(90deg, #c41e3a 0%, #e67e22 100%);color:white;">
                            <th style="padding:16px;font-weight:600;text-align:left;border:none;">Alumni</th>
                            <th style="padding:16px;font-weight:600;text-align:left;border:none;">Email</th>
                            <th style="padding:16px;font-weight:600;text-align:left;border:none;">Batch</th>
                            <th style="padding:16px;font-weight:600;text-align:left;border:none;">Location</th>
                            <th style="padding:16px;font-weight:600;text-align:left;border:none;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Requests Tab -->
        <div id="requests" class="tab-content" style="display:none;">
            <div style="background:white;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                <table id="requestsTable" class="table table-hover" style="width:100%;margin:0;border-collapse:collapse;">
                    <thead>
                        <tr style="background:linear-gradient(90deg, #c41e3a 0%, #e67e22 100%);color:white;">
                            <th style="padding:16px;font-weight:600;text-align:left;border:none;">Alumni</th>
                            <th style="padding:16px;font-weight:600;text-align:left;border:none;">Email</th>
                            <th style="padding:16px;font-weight:600;text-align:left;border:none;">Batch</th>
                            <th style="padding:16px;font-weight:600;text-align:left;border:none;">Location</th>
                            <th style="padding:16px;font-weight:600;text-align:center;border:none;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="alumniProfileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#c41e3a;color:white;">
                <h5 class="modal-title" id="profileModalLabel">Alumni Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="profileModalBody" style="padding:20px;">
                <div class="text-center">
                    <img id="profileImage" src="" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;">
                    <h5 id="profileName" style="font-weight:700;"></h5>
                    <p id="profileEmail" style="color:#666;"></p>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Full Name</strong> <br> <span id="profileName"></span></p>
                        <p><strong>City & State</strong> <br><span id="profileLocation"></span></p>
                        <p><strong>Contact Number</strong> <br><span id="profileLocation"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Year of Completion</strong><br> <span id="profileOccupation"></span></p>
                        <p><strong>Email Address</strong> <br><span id="profileEmail"></span></p>
                        <p><strong>Current Occupation/Field of Study</strong><br> <span id="profileCompany"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@push('scripts')
<script>
    let connectionsTable, requestsTable;

    $(function() {
        // CONNECTION LIST
        connectionsTable = $('#connectionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("alumni.connections.list") }}',
            searching: false,
            paging: true,
            lengthChange: false,
            columns: [{
                    data: 'alumni',
                    name: 'alumni',
                    orderable: false
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: false
                },
                {
                    data: 'batch',
                    name: 'batch'
                },
                {
                    data: 'location',
                    name: 'location',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        // REQUEST LIST
        requestsTable = $('#requestsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("alumni.connections.requests") }}',
            searching: false,
            paging: true,
            lengthChange: false,
            columns: [{
                    data: 'alumni',
                    name: 'alumni',
                    orderable: false
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: false
                },
                {
                    data: 'batch',
                    name: 'batch'
                },
                {
                    data: 'location',
                    name: 'location',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        // Tab switching
        $('.tab-btn').on('click', function() {
            $('.tab-btn').css({
                'background-color': '#f0f0f0',
                'color': '#666'
            });
            $(this).css({
                'background-color': '#c41e3a',
                'color': 'white'
            });

            $('.tab-content').hide();
            $('#' + $(this).data('tab')).show();
        });

        // Global search
        $('#globalSearch').on('keyup', function() {
            connectionsTable.search($(this).val()).draw(false);
            requestsTable.search($(this).val()).draw(false);
        });
    });

    function viewProfile(id) {
        $.ajax({
            url: '/connections/profile/' + id,
            type: 'GET',
            success: function(data) {
                // Fill modal fields
                $('#profileImage').attr('src', data.image);
                $('#profileName').text(data.name);
                $('#profileEmail').text(data.email);
                $('#profileBatch').text(data.batch);
                $('#profileLocation').text(data.location);
                $('#profileOccupation').text(data.occupation);
                $('#profileCompany').text(data.company);

                // Show Bootstrap 5 modal
                var alumniModalEl = document.getElementById('alumniProfileModal');
                var alumniModal = new bootstrap.Modal(alumniModalEl);
                alumniModal.show();
            },
            error: function() {
                alert('Unable to load profile details.');
            }
        });
    }

    function acceptRequest(id) {
        $.ajax({
            url: '/connections/accept/' + id,
            type: 'POST',
            success: function(data) {
                connectionsTable.ajax.reload();
                requestsTable.ajax.reload();
            },
            error: function() {
                alert('Unable to accept connection request.');
            }
        });
    }

    function rejectRequest(id) {
        $.ajax({
            url: '/connections/reject/' + id,
            type: 'POST',
            success: function(data) {
                requestsTable.ajax.reload();
            },
            error: function() {
                alert('Unable to reject connection request.');
            }
        });
    }
</script>
@endpush