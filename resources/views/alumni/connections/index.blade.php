@extends('alumni.layouts.index')

@section('content')
<style>
    /* Override DataTables default styles */
    #connectionsTable thead tr,
    #requestsTable thead tr {
        background: linear-gradient(90deg, #e2001d 0%, #f7c744 48%, #b1040e 100%) !important;
    }
    #connectionsTable_wrapper #connectionsTable thead tr th:last-child {
        width: 120px !important;
    }

    #connectionsTable tbody tr:hover,
    #requestsTable tbody tr:hover {
        background-color: #f9fafb !important;
    }

    /* Center align table data */
    #connectionsTable tbody td,
    #requestsTable tbody td {
        /* text-align: center !important; */
        vertical-align: middle !important;
    }

    /* Table wrapper with scroll */
    .table-wrapper {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding: 0;
    }

    .table-wrapper::-webkit-scrollbar {
        height: 8px;
    }

    .table-wrapper::-webkit-scrollbar-track {
        background: #f3f4f6;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
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

    /* Hide default DataTables sorting arrows */
    table.dataTable thead th.sorting:before,
    table.dataTable thead th.sorting_asc:before,
    table.dataTable thead th.sorting_desc:before,
    table.dataTable thead th.sorting:after,
    table.dataTable thead th.sorting_asc:after,
    table.dataTable thead th.sorting_desc:after {
        display: none !important;
    }

    /* Sortable columns styling */
    #connectionsTable thead th.sorting,
    #connectionsTable thead th.sorting_asc,
    #connectionsTable thead th.sorting_desc,
    #requestsTable thead th.sorting,
    #requestsTable thead th.sorting_asc,
    #requestsTable thead th.sorting_desc {
        cursor: pointer !important;
        position: relative !important;
        padding-right: 30px !important;
    }

    /* Remove sorted column background highlighting */
    table.dataTable.order-column>tbody tr>.sorting_1,
    table.dataTable.order-column>tbody tr>.sorting_2,
    table.dataTable.order-column>tbody tr>.sorting_3,
    table.dataTable.display>tbody tr>.sorting_1,
    table.dataTable.display>tbody tr>.sorting_2,
    table.dataTable.display>tbody tr>.sorting_3,
    table.dataTable tbody tr > .sorting_1,
    table.dataTable tbody tr > .sorting_2,
    table.dataTable tbody tr > .sorting_3,
    #connectionsTable tbody td,
    #requestsTable tbody td {
        background-color: inherit !important;
        box-shadow: none !important;
    }

    /* Tab button active state */
    .tab-btn.active {
        background-color: #dc2626 !important;
        color: white !important;
    }

    /* Responsive Styles */
    @media (max-width: 991px) {
        .page-header h1 {
            font-size: 28px !important;
        }

        .page-header p {
            font-size: 14px !important;
        }

        .search-container {
            width: 100% !important;
            max-width: 100% !important;
        }

        .tab-btn {
            padding: 8px 30px !important;
            font-size: 13px !important;
        }

        #connectionsTable thead th,
        #requestsTable thead th {
            padding: 12px !important;
            font-size: 13px !important;
        }

        #connectionsTable tbody td,
        #requestsTable tbody td {
            padding: 12px !important;
            font-size: 13px !important;
        }
        
        #connectionsTable_wrapper #connectionsTable thead tr th:last-child {
            width: 100% !important;
        }
    }

    @media (max-width: 767px) {
        .page-header h1 {
            font-size: 24px !important;
        }

        .page-header p {
            font-size: 13px !important;
        }

        .search-container {
            margin-bottom: 16px !important;
        }

        .tab-container {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .tab-btn {
            padding: 8px 24px !important;
            font-size: 12px !important;
            white-space: nowrap;
        }

        .table-wrapper {
            border-radius: 8px !important;
        }

        #connectionsTable,
        #requestsTable {
            min-width: 700px;
        }

        /* #connectionsTable thead th,
        #requestsTable thead th {
            padding: 10px !important;
            font-size: 12px !important;
        } */

        #connectionsTable tbody td,
        #requestsTable tbody td {
            padding: 10px !important;
            font-size: 12px !important;
        }

        .custom-info {
            padding: 10px 12px !important;
            font-size: 12px !important;
        }

        .custom-pagination .btn {
            font-size: 12px !important;
            padding: 6px 12px !important;
        }

        .pagination-wrapper {
            flex-direction: column !important;
            gap: 10px;
        }

        .custom-info,
        .custom-pagination {
            text-align: center !important;
            width: 100%;
        }
    }

    @media (max-width: 575px) {
        .page-header {
            padding: 15px !important;
        }

        .page-header h1 {
            font-size: 20px !important;
        }

        .page-header p {
            font-size: 12px !important;
        }

        .search-container input {
            font-size: 13px !important;
            padding: 6px 12px 6px 40px !important;
        }

        .tab-btn {
            padding: 6px 20px !important;
            font-size: 11px !important;
        }

        #connectionsTable,
        #requestsTable {
            min-width: 650px;
        }

        /* #connectionsTable thead th,
        #requestsTable thead th {
            padding: 8px !important;
            font-size: 11px !important;
        } */

        #connectionsTable tbody td,
        #requestsTable tbody td {
            padding: 8px !important;
            font-size: 12px !important;
        }

        .custom-info {
            font-size: 11px !important;
        }

        .custom-pagination .btn {
            font-size: 11px !important;
            padding: 5px 10px !important;
        }

        .custom-pagination span {
            font-size: 11px !important;
        }

        .custom-pagination {
            padding: 0 !important;
        }

        #infoRibbon {
            padding: 6px 15px !important;
            font-size: 12px !important;
        }

        #infoRibbon i {
            font-size: 16px !important;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 20px; background: white;">
    {{-- Header --}}
    <div class="" style="margin-bottom: 30px;">
        <h1 style="font-weight: 700; color: #111827; margin-bottom: 8px;" class="main-title">My Connections</h1>
        <p style="color: #6b7280;" class="sub-title">Manage your alumni network and connection requests</p>
    </div>

    {{-- Search Bar (Right Aligned) --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 4px;">
        <div class="search-container" style="position: relative; width: 250px;">
            <i class="fas fa-search"
                style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 15px;"></i>
            <input type="text" id="globalSearch" placeholder="Search connections..."
                style="width: 100%; padding: 8px 16px 8px 45px; border: 1px solid #d1d5db; border-radius: 30px; font-size: 14px; outline: none;"
                onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#d1d5db'">
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="tab-container" style="margin-bottom: 20px;">
        <button class="tab-btn active" data-tab="connections"
            style="background-color: #dc2626; color: white; border: none; padding: 8px 40px; border-radius: 3px; font-weight: 600; cursor: pointer; font-size: 14px; transition: all 0.3s;">
            Connections (<span id="connectionsCount">0</span>)
        </button>
        <button class="tab-btn" data-tab="requests"
            style="background-color: #e5e7eb; color: #6b7280; border: none; padding: 8px 40px; border-radius: 3px; font-weight: 600; cursor: pointer; font-size: 14px; transition: all 0.3s;">
            Requests (<span id="requestsCount">0</span>)
        </button>
    </div>

    {{-- Info Ribbon (Only for Requests Tab) --}}
    <div id="infoRibbon" data-ribbon-state="{{ $isRequestRibbon ?? 0 }}" style="display: none; background: #dbeafe; border: 1px solid #93c5fd; border-radius: 8px; padding: 8px 20px; margin-bottom: 20px; position: relative;">
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
                        <th class="table-header">Alumni</th>
                        <th class="table-header">Email</th>
                        <th class="table-header">Batch</th>
                        <th class="table-header">Location</th>
                        <th class="table-header">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- custom info & pagination containers (below this table) -->
        <div class="pagination-wrapper d-flex justify-content-between align-items-center mt-2 w-100">
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
                        <th class="table-header">Alumni</th>
                        <th class="table-header">Email</th>
                        <th class="table-header">Batch</th>
                        <th class="table-header">Location</th>
                        <th class="table-header">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- custom info & pagination containers (below this table) -->
        <div class="pagination-wrapper d-flex justify-content-between align-items-center mt-2 w-100">
            <div id="requestsInfo" class="custom-info"></div>
            <div id="requestsPagination" class="custom-pagination"></div>
        </div>


    </div>
</div>

<!-- Profile modal (fields fixed to avoid duplicate IDs) -->
<div class="modal fade" id="alumniProfileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body" id="profileModalBody">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="modal-title text-danger" id="profileModalLabel">Alumni Profile</h3>
                    <button type="button" class="btn-close btn-close-dark btn-sm" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="d-flex">
                    <img id="profileImage" src="" class="rounded-circle mb-3"
                        style="width:70px;height:70px;object-fit:cover;">
                    <div style="padding:10px 15px;">
                        <h5 id="profileFullName" class="fw-bolder"></h5>
                        <p id="profileOccupationMain" style="color:#666;"></p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p>Full Name <br> <span id="profileFullName2" class="fw-bolder"></span></p>
                        <p>City & State <br><span id="profileLocation" class="fw-bolder"></span></p>
                        <p>Contact Number <br><span id="profileContact" class="fw-bolder"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p>Year of Completion<br> <span id="profileBatch" class="fw-bolder"></span></p>
                        <p>Email Address <br><span id="profileEmail2" class="fw-bolder"></span></p>
                        <p>Current Occupation/Field of Study<br> <span id="profileOccupation" class="fw-bolder"></span></p>
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
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'batch',
                    name: 'batch',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'location',
                    name: 'location',
                    orderable: true,
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
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'batch',
                    name: 'batch',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'location',
                    name: 'location',
                    orderable: true,
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
            $('#globalSearch').val('');
            connectionsTable.search('').page(0).draw(false);
            requestsTable.search('').page(0).draw(false);
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

            const ribbonState = $('#infoRibbon').data('ribbon-state');
            if ($(this).data('tab') === 'requests') {
                const hasRequests = requestsTable.page.info().recordsTotal > 0;
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
                        // showToast('Ribbon Closed Successfully!', 'success');
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
            updateSortIcons('connectionsTable');
        });

        requestsTable.on('draw', function() {
            renderCustomPagination(requestsTable, 'requestsInfo', 'requestsPagination');
            updateCountsBadge('requestsCount', requestsTable);
            updateSortIcons('requestsTable');
        });

        // Update sorting icons after table order changes
        connectionsTable.on('order.dt', function() {
            updateSortIcons('connectionsTable');
        });

        requestsTable.on('order.dt', function() {
            updateSortIcons('requestsTable');
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

        // Fix info text for empty results
        const start = info.recordsDisplay > 0 ? info.start + 1 : 0;
        const end = info.recordsDisplay > 0 ? info.end : 0;
        const recordsText = `Showing ${start} to ${end} of ${info.recordsTotal} ${info.recordsTotal === 1 ? 'record' : 'records'}`;

        $('#' + infoContainerId).html(recordsText);

        // Fix pagination button logic - disable if no records or on first/last page
        const prevDisabled = info.page === 0 || info.recordsDisplay === 0 ? 'disabled' : '';
        const nextDisabled = info.recordsDisplay === 0 || info.page + 1 >= info.pages ? 'disabled' : '';

        const paginationHtml = `
                <div class="d-flex justify-content-between align-items-center">
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
                $('#profileContact').text(data.contact ? '+91 ' + data.contact : '-');
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
            url: "{{ route('alumni.connections.accept', '') }}/" + id,
            type: 'POST',
            success: function(data) {
                showToast("Alumni added to your connections.");
                connectionsTable.ajax.reload(null, false);
                requestsTable.ajax.reload(null, false);
                requestsTable.one('draw', function () {
                    const totalRequests = requestsTable.page.info().recordsTotal;
                    const ribbonState = $('#infoRibbon').data('ribbon-state');

                    if (ribbonState == 1 && totalRequests > 0) {
                        $('#infoRibbon').slideDown();
                    } else {
                        $('#infoRibbon').slideUp();
                    }
                });
            },
            error: function() {
                showToast('Unable to accept connection request.', 'error');
            }
        });
    }

    function rejectRequest(id) {
        $.ajax({
            url: "{{ route('alumni.connections.reject', '') }}/" + id,
            type: 'POST',
            success: function(data) {
                showToast("alumni request has been rejected.");
                requestsTable.ajax.reload(null, false);
                requestsTable.one('draw', function () {
                    const totalRequests = requestsTable.page.info().recordsTotal;
                    const ribbonState = $('#infoRibbon').data('ribbon-state');

                    if (ribbonState == 1 && totalRequests > 0) {
                        $('#infoRibbon').slideDown();
                    } else {
                        $('#infoRibbon').slideUp();
                    }
                });
            },
            error: function() {
                showToast('Unable to reject connection request.', 'error');
            }
        });
    }

    // Function to update sorting icons
    function updateSortIcons(tableId) {
        $(`#${tableId} thead th`).each(function() {
            const $th = $(this);
            
            // Remove existing sort icons
            $th.find('.sort-icon').remove();
            
            // Add appropriate sort icon based on current state
            if ($th.hasClass('sorting_asc')) {
                $th.append('<i class="bi bi-arrow-up sort-icon"></i>');
            } else if ($th.hasClass('sorting_desc')) {
                $th.append('<i class="bi bi-arrow-down sort-icon"></i>');
            } else if ($th.hasClass('sorting')) {
                $th.append('<i class="bi bi-arrow-down-up sort-icon"></i>');
                //  style="display:inline-block;vertical-align:middle;"
            }
        });
    }
</script>
@endpush