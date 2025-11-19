@extends('layouts.index')
@section('title', 'Directory - Alumni Tracking')

@section('content')
<div style="margin-bottom: 30px;">
    <h1 style="font-size: 40px; font-weight: 700; color: #333; margin-bottom: 8px;">Alumni Directory</h1>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
        Manage and view all alumni profiles
    </p>
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
        <table id="directoryTable" class="display" style="width: 100%;border-collapse: collapse;background-color: white;box-shadow: 0 2px 8px rgba(0,0,0,0.08);border-radius: 6px;border: 1px solid #e0e0e0;border-radius: 8px;">
            <thead>
                <tr style="background: #ba0028; color: white; font-weight: 700; font-size: 12px;">
                    <th style="padding: 15px; text-align: left;">Created On</th>
                    <th style="padding: 15px; text-align: left;">Profile Picture</th>
                    <th style="padding: 15px; text-align: left;">Name</th>
                    <th style="padding: 15px; text-align: left;">Year</th>
                    <th style="padding: 15px; text-align: left;">City & State</th>
                    <th style="padding: 15px; text-align: left;">Email</th>
                    <th style="padding: 15px; text-align: left;">Contact</th>
                    <th style="padding: 15px; text-align: left;">Occupation</th>
                    <th style="padding: 15px; text-align: left;">Status</th>
                    <th style="padding: 15px; text-align: left;">Connections</th>
                    <th style="padding: 15px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<style>
    /* Add padding to tbody cells */
    #directoryTable tbody td {
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

    #directoryTable tbody td {
        border-bottom: 1px solid #f0f0f0;
        /* soft line between rows */
    }

    #directoryTable thead th {
        border-bottom: 2px solid #e0e0e0;
        /* slightly thicker under header */
    }
</style>

<!-- Profile Picture Modal -->
<div class="modal fade" id="profilePicModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alumni Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="alumniProfileImage" src="" class="img-fluid rounded" alt="Profile Picture">
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>


<script>
    $(document).ready(function() {
        const table = $('#directoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.directory.data') }}",
                type: 'GET',
                data: function(d) {
                    d.batch = $('#filterBatch').val();
                    d.location = $('#filterLocation').val();
                }
            },
            columns: [{
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    data: 'alumni',
                    name: 'alumni',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'full_name',
                    name: 'full_name'
                },
                {
                    data: 'year_of_completion',
                    name: 'year_of_completion'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'mobile_number',
                    name: 'mobile_number'
                },
                {
                    data: 'occupation',
                    name: 'occupation'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'connections',
                    name: 'connections',
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
    function viewConnections(id) {
        window.location.href = "{{ route('admin.directory.view.connections.page', '') }}/" + id;
    }

    function updateStatus(id, status) {
        if (!confirm(`Are you sure you want to ${status === 'blocked' ? 'block' : 'unblock'} this alumni?`)) {
            return;
        }

        $.ajax({
            url: "{{ route('directory.update.status') }}",
            type: 'POST',
            data: {
                id: id,
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert(response.message);
                $('#directoryTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                const res = xhr.responseJSON;
                alert(res && res.message ? res.message : 'An error occurred while updating status.');
            }
        });
    }

    function viewProfilePic(imageUrl) {
        window.open(imageUrl, '_blank');
    }
</script>
@endpush
@endsection