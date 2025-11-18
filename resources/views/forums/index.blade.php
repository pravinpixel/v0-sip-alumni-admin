@extends('layouts.index')
@section('title', 'Directory - Alumni Tracking')

@section('content')
<div style="margin-bottom: 30px;">
    <h1 style="font-size: 40px; font-weight: 700; color: #333; margin-bottom: 8px;">Forums</h1>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
        Manage community discussions and forum posts
    </p>
    <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <!-- Search and Filter -->
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 10px;">
            <div style="flex: 1; position: relative;">
                <input type="text" id="searchInput" placeholder="🔍 Search alumni..."
                    style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>
            <button id="filterToggleBtn"
                style="background-color: white; border: 1px solid #ccc; border-radius: 6px; padding: 10px 15px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                <span>🔽 Filters</span>
            </button>
            <button id="exportBtn"
                style="background-color: white; border: 1px solid #ccc; border-radius: 6px; padding: 10px 15px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                <span>Export</span>
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
        <table id="forumsTable" class="display" style="width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 6px;">
            <thead>
                <tr style="background: #ba0028; color: white; font-weight: 700; font-size: 14px;">
                    <th style="padding: 15px; text-align: left;">Created On</th>
                    <th style="padding: 15px; text-align: left;">Alumni</th>
                    <th style="padding: 15px; text-align: left;">Contact</th>
                    <th style="padding: 15px; text-align: left;">View Post</th>
                    <th style="padding: 15px; text-align: left;">Action Taken On</th>
                    <th style="padding: 15px; text-align: left;">Status</th>
                    <th style="padding: 15px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<style>
    /* Add padding to tbody cells */
    #forumsTable tbody td {
        padding: 12px 15px;
        /* Adjust as needed */
        vertical-align: middle;
    }
</style>

<!-- Profile Modal -->
<div class="modal fade" id="viewPostModal" tabindex="-1" aria-hidden="true">
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

        const table = $('#forumsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.forums.data') }}",
                type: 'GET',
                data: function(d) {
                    d.batch = $('#filterBatch').val();
                    d.location = $('#filterLocation').val();
                }
            },

            columns: [{
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'alumni',
                    name: 'alumni',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'contact',
                    name: 'contact'
                },
                {
                    data: 'view_post',
                    name: 'view_post',
                    orderable: false
                },
                {
                    data: 'action_taken_on',
                    name: 'action_taken_on'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],

            paging: true,
            searching: false,
            ordering: false,
            pageLength: 10,
            lengthChange: false,
            scrollX: true,
        });

        // Search
        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Filters
        $('#filterBatch, #filterLocation').on('input', function() {
            table.ajax.reload();
        });

        // Toggle filter section
        $('#filterToggleBtn').on('click', function() {
            const section = $('#filterSection');
            const isVisible = section.is(':visible');
            section.slideToggle();
            $(this).find('span').text(isVisible ? '🔽 Open Filters' : '🔼 Close Filters');
        });

    });

    function statusChange(id, status) {
        confirmBox("Are you sure you want to change the status?", function() {
            $.ajax({
                url: "{{ route('forums.change.status') }}",
                type: 'POST',
                data: {
                    id: id,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#forumsTable').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    alert('An error occurred while updating the status.');
                }
            });
        });
    }


    // Function to open profile modal
</script>
@endpush
@endsection