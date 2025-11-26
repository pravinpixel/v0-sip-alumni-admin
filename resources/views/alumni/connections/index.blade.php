@extends('alumni.layouts.index')

@section('content')
<style>
    /* Override DataTables default styles */
    #connectionsTable thead tr,
    #requestsTable thead tr {
        background: linear-gradient(90deg, #dc2626 0%, #f59e0b 100%) !important;
    }

    #connectionsTable thead th,
    #requestsTable thead th {
        background: transparent !important;
        color: white !important;
        border: none !important;
    }

    #connectionsTable tbody tr:hover,
    #requestsTable tbody tr:hover {
        background-color: #f9fafb !important;
    }

    /* Small responsive tweak so table container doesn't hide the pagination area */
    .table-wrapper {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        padding: 0;
        /* keep original look */
    }

    /* Custom small styles for the custom pagination blocks */
    .custom-info {
        padding: 12px 16px;
        color: #6b7280;
        font-size: 14px;
    }

    .custom-pagination {
        padding: 12px 16px;
        text-align: right;
    }

    .custom-pagination .btn {
        border-radius: 8px;
    }

    /* Tab button active state */
    .tab-btn.active {
        background-color: #dc2626 !important;
        color: white !important;
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 20px; background: white;">
    {{-- Header with Search on Right --}}
    {{-- Header --}}
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">My Connections</h1>
        <p style="color: #6b7280; font-size: 15px;">Manage your alumni network and connection requests</p>
    </div>

    {{-- Search Bar (Right Aligned) --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 4px;">
        <div style="position: relative; width: 350px;">
            <i class="fas fa-search"
                style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
            <input type="text" id="globalSearch" placeholder="Search connections..."
                style="width: 100%; padding: 11px 16px 11px 45px; border: 1px solid #d1d5db; border-radius: 30px; font-size: 14px; outline: none;"
                onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#d1d5db'">
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div style="display: flex; gap: 0; margin-bottom: 20px;">
        <button class="tab-btn active" data-tab="connections"
            style="background-color: #dc2626; color: white; border: none; padding: 12px 28px; border-radius: 8px 8px 0 0; font-weight: 600; cursor: pointer; font-size: 14px; transition: all 0.3s;">
            Connections <span id="connectionsCount"
                style="background-color: rgba(255,255,255,0.3); padding: 2px 10px; border-radius: 12px; margin-left: 8px; font-size: 12px;">0</span>
        </button>
        <button class="tab-btn" data-tab="requests"
            style="background-color: #e5e7eb; color: #6b7280; border: none; padding: 12px 28px; border-radius: 8px 8px 0 0; font-weight: 600; cursor: pointer; font-size: 14px; transition: all 0.3s;">
            Requests <span id="requestsCount"
                style="background-color: #dc2626; color: white; padding: 2px 10px; border-radius: 12px; margin-left: 8px; font-size: 12px;">0</span>
        </button>
    </div>

    {{-- Info Ribbon (Only for Requests Tab) --}}
    <div id="infoRibbon" data-ribbon-state="{{ $isRequestRibbon ?? 0 }}" style="display: none; background: #dbeafe; border: 1px solid #93c5fd; border-radius: 8px; padding: 14px 20px; margin-bottom: 20px; position: relative;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-info-circle" style="color: #3b82f6; font-size: 18px;"></i>
            <p style="color: #1e40af; font-size: 14px; margin: 0; flex: 1;">
                <strong>Note:</strong> By approving a request, your contact information will also be shared with the person who sent the request.
            </p>
            <button id="closeRibbon" style="background: transparent; border: none; color: #3b82f6; font-size: 20px; cursor: pointer; padding: 0; line-height: 1;">
                ×
            </button>
        </div>
    </div>

    {{-- Connections Tab --}}
    <div id="connections" class="tab-content active">
        <div class="table-wrapper">
            <table id="connectionsTable" class="table table-hover" style="width: 100%; margin: 0; border-collapse: collapse;">
                <thead>
                    <tr style="background: linear-gradient(90deg, #dc2626 0%, #f59e0b 100%); color: white;">
                        <th style="padding: 16px; font-weight: 600; text-align: left; border: none;">
                            Alumni <i class="fas fa-sort" style="margin-left: 8px; font-size: 12px;"></i>
                        </th>
                        <th style="padding: 16px; font-weight: 600; text-align: left; border: none;">
                            Email <i class="fas fa-sort" style="margin-left: 8px; font-size: 12px;"></i>
                        </th>
                        <th style="padding: 16px; font-weight: 600; text-align: left; border: none;">
                            Batch <i class="fas fa-sort" style="margin-left: 8px; font-size: 12px;"></i>
                        </th>
                        <th style="padding: 16px; font-weight: 600; text-align: left; border: none;">
                            Location <i class="fas fa-sort" style="margin-left: 8px; font-size: 12px;"></i>
                        </th>
                        <th style="padding: 16px; font-weight: 600; text-align: left; border: none;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- custom info & pagination containers (below this table) -->
        <div class="d-flex justify-content-between align-items-center mt-2 w-100">
            <div id="connectionsInfo" class="custom-info"></div>
            <div id="connectionsPagination" class="custom-pagination"></div>
        </div>
    </div>

    {{-- Requests Tab --}}
    <div id="requests" class="tab-content" style="display: none;">
        <div class="table-wrapper">
            <table id="requestsTable" class="table table-hover" style="width: 100%; margin: 0; border-collapse: collapse;">
                <thead>
                    <tr style="background: linear-gradient(90deg, #dc2626 0%, #f59e0b 100%); color: white;">
                        <th style="padding: 16px; font-weight: 600; text-align: left; border: none;">
                            Alumni <i class="fas fa-sort" style="margin-left: 8px; font-size: 12px;"></i>
                        </th>
                        <th style="padding: 16px; font-weight: 600; text-align: left; border: none;">
                            Email <i class="fas fa-sort" style="margin-left: 8px; font-size: 12px;"></i>
                        </th>
                        <th style="padding: 16px; font-weight: 600; text-align: left; border: none;">
                            Batch <i class="fas fa-sort" style="margin-left: 8px; font-size: 12px;"></i>
                        </th>
                        <th style="padding: 16px; font-weight: 600; text-align: left; border: none;">
                            Location <i class="fas fa-sort" style="margin-left: 8px; font-size: 12px;"></i>
                        </th>
                        <th style="padding: 16px; font-weight: 600; text-align: center; border: none;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- custom info & pagination containers (below this table) -->
        <div class="d-flex justify-content-between align-items-center mt-2 w-100">
            <div id="requestsInfo" class="custom-info"></div>
            <div id="requestsPagination" class="custom-pagination"></div>
        </div>


    </div>
</div>

<!-- Profile modal (fields fixed to avoid duplicate IDs) -->
<div class="modal fade" id="alumniProfileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body" id="profileModalBody" style="padding:20px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="modal-title" id="profileModalLabel">Alumni Profile</h5>
                    <button type="button" class="btn-close btn-close-dark btn-sm" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="d-flex">
                    <img id="profileImage" src="" class="rounded-circle mb-3"
                        style="width:70px;height:70px;object-fit:cover;">
                    <div style="padding:10px 15px;">
                        <h5 id="profileFullName" style="font-weight:700;"></h5>
                        <p id="profileOccupationMain" style="color:#666;"></p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Full Name</strong> <br> <span id="profileFullName2"></span></p>
                        <p><strong>City & State</strong> <br><span id="profileLocation"></span></p>
                        <p><strong>Contact Number</strong> <br><span id="profileContact"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Year of Completion</strong><br> <span id="profileBatch"></span></p>
                        <p><strong>Email Address</strong> <br><span id="profileEmail2"></span></p>
                        <p><strong>Current Occupation/Field of Study</strong><br> <span id="profileOccupation"></span></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    // CSRF token for POST actions (accept/reject)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    let connectionsTable, requestsTable;

    $(function() {
        // Initialize Connections table
        connectionsTable = $('#connectionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("alumni.connections.list") }}',
            searching: true,
            paging: true,
            info: false, // disable default info (we'll render custom)
            lengthChange: false,
            pageLength: 10,
            ordering: true,
            dom: 't', // minimal DOM (table only)
            order: [[5, 'desc']],
            columns: [{
                    data: 'alumni',
                    name: 'alumni',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'batch',
                    name: 'batch',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'location',
                    name: 'location',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                { data: 'created_at', name: 'created_at', visible: false },
            ],
            drawCallback: function(settings) {
                // update counts badge (if you return counts via separate API, you can set here)
                // We'll update counts in the draw handler below using page.info()
            }
        });

        // Initialize Requests table
        requestsTable = $('#requestsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("alumni.connections.requests") }}',
            searching: true,
            paging: true,
            info: false,
            lengthChange: false,
            pageLength: 10,
            ordering: true,
            dom: 't',
            order: [[5, 'desc']],
            columns: [{
                    data: 'alumni',
                    name: 'alumni',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'batch',
                    name: 'batch',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'location',
                    name: 'location',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                { data: 'created_at', name: 'created_at', visible: false },
            ]
        });

        // Global search: apply to both tables
        $('#globalSearch').on('keyup', function() {
            let q = $(this).val();
            connectionsTable.search(q).draw();
            requestsTable.search(q).draw();
        });

        // Get ribbon state from database
        const ribbonState = $('#infoRibbon').data('ribbon-state');
        
        // Tab switching
        $('.tab-btn').on('click', function() {
            $('.tab-btn').removeClass('active').css({
                'background-color': '#e5e7eb',
                'color': '#6b7280'
            });
            $(this).addClass('active').css({
                'background-color': '#dc2626',
                'color': 'white'
            });

            $('.tab-content').hide();
            $('#' + $(this).data('tab')).show();

            // Show/hide info ribbon based on tab and database state
            if ($(this).data('tab') === 'requests') {

    // Check if requests table has records
    const hasRequests = requestsTable.data().count() > 0;

    if (ribbonState == 1 && hasRequests) {
        $('#infoRibbon').slideDown();
    } else {
        $('#infoRibbon').slideUp();
    }

} else {
    $('#infoRibbon').slideUp();
}


            // redraw table in case of column width issues
            if ($(this).data('tab') === 'connections') {
                connectionsTable.columns.adjust().draw(false);
            } else {
                requestsTable.columns.adjust().draw(false);
            }
        });

        // Close ribbon button - update database
        $('#closeRibbon').on('click', function() {
            $('#infoRibbon').slideUp();
            
            // Update database via API
            $.ajax({
                url: "{{ route('alumni.update.ribbon') }}",
                type: 'POST',
                data: {
                    is_request_ribbon: 0,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#infoRibbon').data('ribbon-state', 0);
                        showToast('Ribbon Closed Successfully!', 'success');
                    }
                },
                error: function(xhr) {
                    showToast('Failed to close ribbon: ' + xhr.responseText, 'error');
                }
            });
        });

        // Draw handlers for custom info + pagination
        connectionsTable.on('draw', function() {
            renderCustomPagination(connectionsTable, 'connectionsInfo', 'connectionsPagination');
            updateCountsBadge('connectionsCount', connectionsTable);
        });

        requestsTable.on('draw', function() {
            renderCustomPagination(requestsTable, 'requestsInfo', 'requestsPagination');
            updateCountsBadge('requestsCount', requestsTable);
        });

        // Filters (if you add filter inputs for these tables later)
        $('#filterBatch, #filterLocation').on('input', function() {
            connectionsTable.ajax.reload();
            requestsTable.ajax.reload();
        });
    });

    /**
     * Renders custom info text and pagination controls below a table.
     * @param dt DataTable instance
     * @param infoContainerId string id of info div
     * @param paginationContainerId string id of pagination div
     */
    function renderCustomPagination(dt, infoContainerId, paginationContainerId) {
        const info = dt.page.info();

        // Info text
        const start = info.recordsTotal === 0 ? 0 : info.start + 1;
        const end = info.recordsTotal === 0 ? 0 : info.end;
        const recordsText = `Showing ${start} to ${end} of ${info.recordsTotal} ${info.recordsTotal === 1 ? 'record' : 'records'}`;

        $('#' + infoContainerId).html(recordsText);

        // Pagination HTML
        const prevDisabled = info.page === 0 ? 'disabled' : '';
        const nextDisabled = (info.page + 1 === info.pages) ? 'disabled' : '';

        const paginationHtml = `
                <div class="d-flex justify-content-end align-items-center">
                    <button class="btn btn-light btn-sm me-2" id="${paginationContainerId}_prev" ${prevDisabled}>‹ Previous</button>
                    <span class="mx-2" style="font-weight:500;">Page ${info.page + 1} of ${Math.max(info.pages, 1)}</span>
                    <button class="btn btn-light btn-sm ms-2" id="${paginationContainerId}_next" ${nextDisabled}>Next ›</button>
                </div>
            `;

        $('#' + paginationContainerId).html(paginationHtml);

        // Attach handlers (unbind first to avoid multiple handlers)
        $('#' + paginationContainerId + '_prev').off('click').on('click', function() {
            dt.page('previous').draw('page');
        });

        $('#' + paginationContainerId + '_next').off('click').on('click', function() {
            dt.page('next').draw('page');
        });
    }

    /**
     * Update the small badge that shows number count on the tab
     */
    function updateCountsBadge(badgeId, dt) {
        const info = dt.page.info();
        // Here we use recordsTotal (total rows available server-side)
        $('#' + badgeId).text(info.recordsTotal);
    }

    // PROFILE MODAL AND ACTIONS
    function viewProfile(id) {
        $.ajax({
            url: "{{ route('alumni.connections.profile', '') }}/" + id,
            type: 'GET',
            success: function(data) {
                // Fill modal fields (safe fallback values)
                $('#profileImage').attr('src', data.image || '{{ asset("images/avatar/blank.png") }}');
                $('#profileFullName').text(data.name || '-');
                $('#profileFullName2').text(data.name || '-');
                $('#profileEmail2').text(data.email || '-');
                $('#profileBatch').text(data.batch || '-');
                $('#profileLocation').text(data.location || '-');
                $('#profileContact').text(data.contact || '-');
                $('#profileOccupation').text(data.occupation || '-');
                $('#profileOccupationMain').text(data.occupation || '-');

                // Show Bootstrap 5 modal
                var alumniModalEl = document.getElementById('alumniProfileModal');
                var alumniModal = new bootstrap.Modal(alumniModalEl);
                alumniModal.show();
            },
            error: function() {
                showToast('Unable to load profile details.', 'error');
            }
        });
    }

    function acceptRequest(id) {
        $.ajax({
            url: '/connections/accept/' + id,
            type: 'POST',
            success: function(data) {
                showToast(data.message);
                connectionsTable.ajax.reload(null, false);
                requestsTable.ajax.reload(null, false);
            },
            error: function() {
                showToast('Unable to accept connection request.', 'error');
            }
        });
    }

    function rejectRequest(id) {
        $.ajax({
            url: '/connections/reject/' + id,
            type: 'POST',
            success: function(data) {
                showToast(data.message);
                requestsTable.ajax.reload(null, false);
            },
            error: function() {
                showToast('Unable to reject connection request.', 'error');
            }
        });
    }
</script>
@endpush