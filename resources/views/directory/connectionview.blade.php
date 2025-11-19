@extends('layouts.index')
@section('title', 'Directory - Alumni Tracking')

@section('content')
<div style="margin-bottom: 30px;">
    <div style="display: flex; gap:10px">
        <div style="margin :auto 0">
           <button onclick="history.back()" style="background-color: #e2dedfff; color: white; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 14px; margin-bottom: 15px;">
                ←
            </button>
        </div>
        <div>
            <h1 style="font-size: 40px; font-weight: 700; color: #333; margin-bottom: 8px;">Alumni Directory</h1>
            <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
                Manage and view all alumni profiles
            </p>
        </div>
    </div>

    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <!-- Search and Filter -->
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 10px;">
            <div style="flex: 1; position: relative;">
                <input type="text" id="searchInput" placeholder="🔍 Search by name or email..."
                    style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>
            <button id="filterToggleBtn"
                style="background-color: white; border: 1px solid #ccc; border-radius: 6px; padding: 10px 15px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                <span>🔽 Filters</span>
            </button>
        </div>

        <!-- Filter Section -->
        <div id="filterSection"
            style="display: none; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight: 600; font-size: 14px; color: #333;">Year</label>
                    <input type="text" id="filterBatch" placeholder="e.g. 2022"
                        style="width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="font-weight: 600; font-size: 14px; color: #333;">City</label>
                    <input type="text" id="filterLocation" placeholder="e.g. Chennai"
                        style="width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
                </div>
            </div>
        </div>

        <!-- Alumni Table -->
        <table id="ConnectionListTable" class="display" style="width: 100%;border-collapse: collapse;background-color: white;box-shadow: 0 2px 8px rgba(0,0,0,0.08);border-radius: 6px;border: 1px solid #e0e0e0;border-radius: 8px;">
            <thead>
                <tr style="background: #ba0028; color: white; font-weight: 700; font-size: 12px;">
                    <th style="padding: 15px; text-align: left;">Alumni Name</th>
                    <th style="padding: 15px; text-align: left;">Batch</th>
                    <th style="padding: 15px; text-align: left;">Location</th>
                    <th style="padding: 15px; text-align: left;">View profile</th>
                    <th style="padding: 15px; text-align: left;">Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<style>
    /* Add padding to tbody cells */
    #ConnectionListTable tbody td {
        padding: 12px 15px;
        /* Adjust as needed */
        vertical-align: middle;
        box-sizing: border-box;
    }

    table.dataTable th,
    table.dataTable td {
        white-space: nowrap;
        /* Prevent wrapping */
    }

    .dataTables_wrapper {
        margin-top: 25px !important;
        /* pushes the whole table area down */
    }

    table.dataTable thead th {
        box-sizing: border-box;
        /* Ensure proper width calculation */
    }

    #ConnectionListTable tbody td {
        border-bottom: 1px solid #f0f0f0;
        /* soft line between rows */
    }

    #ConnectionListTable thead th {
        border-bottom: 2px solid #e0e0e0;
        /* slightly thicker under header */
    }
</style>

<!-- Profile Modal -->
<div class="modal fade" id="alumniProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#c41e3a;color:white;">
                <h5 class="modal-title">Alumni Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                        <p><strong>Batch:</strong> <span id="profileBatch"></span></p>
                        <p><strong>Location:</strong> <span id="profileLocation"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Occupation:</strong> <span id="profileOccupation"></span></p>
                        <p><strong>Company:</strong> <span id="profileCompany"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


<script>
    $(document).ready(function() {

        let alumniId = "{{ $id }}"; // correct variable

        const table = $('#ConnectionListTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.directory.view.connections.list', ':id') }}".replace(':id', alumniId),
                type: 'GET',
                data: function(d) {
                    console.log(d);

                    d.batch = $('#filterBatch').val();
                    d.location = $('#filterLocation').val();
                }
            },
            columns: [{
                    data: 'alumni',
                    name: 'alumni'
                },
                {
                    data: 'batch',
                    name: 'batch'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'viewProfile',
                    name: 'viewProfile'
                },
                {
                    data: 'status',
                    name: 'status'
                },
            ],
            paging: true,
            searching: false,
            ordering: false,
            pageLength: 10,
            lengthChange: false,
            scrollX: true,
        });

        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        $('#filterBatch, #filterLocation').on('input', function() {
            table.ajax.reload();
        });

        $('#filterToggleBtn').on('click', function() {
            const section = $('#filterSection');
            const isVisible = section.is(':visible');
            section.slideToggle();
            $(this).find('span').text(isVisible ? '🔽 Filters' : '🔼 Close Filters');
        });

    });
</script>

@endpush
@endsection