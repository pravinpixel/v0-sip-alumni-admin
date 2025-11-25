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
<div class="modal fade" id="alumniProfileDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="text-end p-4">
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="profileModalBody" style="padding:10px 30px;">
                <div style="padding: 10px;">
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <div id="profileImageContainer" style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 4px solid #dc2626; flex-shrink: 0; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);">
                            <img id="profileImage" src="" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <h2 id="profileName" style="margin: 0 0 8px 0; font-size: 24px; font-weight: 700; color: #111827; line-height: 1.2;">Alumni Connection 1</h2>
                            <p id="profileOccupation" style="margin: 0; font-size: 15px; color: #6b7280; font-weight: 500;">Software Engineer</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div style="padding: 24px 32px 32px 32px;">
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">Full Name:</label>
                            <p id="profileFullName" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1;">John Doe</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">Year:</label>
                            <p id="profileBatch" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1;">2000</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">City & State:</label>
                            <p id="profileLocation" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1;">Mumbai, Tamil Nadu</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">Email:</label>
                            <p id="profileEmail" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1; word-break: break-all;">connection1@example.com</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">Contact:</label>
                            <p id="profileNumber" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1;">+91 4616661464</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 0;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <label style="font-size: 14px; font-weight: 600; color: #6b7280; min-width: 120px; padding-top: 2px;">Occupation:</label>
                            <p id="profileOccupation" style="margin: 0; font-size: 15px; color: #111827; font-weight: 500; flex: 1;">Software Engineer</p>
                        </div>
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

        let alumniId = "{{ $id }}";

        const table = $('#ConnectionListTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.directory.view.connections.list', ':id') }}".replace(':id', alumniId),
                type: 'GET',
                data: function(d) {
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
                }
            ],

            pageLength: 10,
            lengthChange: false,
            searching: false,
            ordering: false,
            scrollX: true,
            dom: 't<"row mt-10"<"col-6 dt-info-custom"><"col-6 dt-pagination-custom text-end">>',
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ connections"
            }
        });
        // Custom Pagination Rendering
        table.on('draw', function() {
            let info = table.page.info();

            $(".dt-info-custom").html(
                `Showing ${info.start + 1} to ${info.end} connections`
            );

            let paginationHtml = `
            <button class="btn btn-light btn-sm me-2" id="prevPage" ${info.page === 0 ? "disabled" : ""}>
                ‹ Previous
            </button>

            <span class="mx-2" style="font-weight:500;">
                Page ${info.page + 1} of ${info.pages}
            </span>

            <button class="btn btn-light btn-sm ms-2" id="nextPage" ${(info.page + 1 === info.pages) ? "disabled" : ""}>
                Next ›
            </button>
            `;

            $(".dt-pagination-custom").html(paginationHtml);

            $("#prevPage").on("click", function() {
                table.page("previous").draw("page");
            });
            $("#nextPage").on("click", function() {
                table.page("next").draw("page");
            });
        });

    });


    function viewProfileDetails(id) {
        $.ajax({
            url: "{{ route('admin.directory.view.profile', ':id') }}".replace(':id', id),
            type: 'GET',
            success: function(response) {
                data = response.data;

                $('#profileImage').attr('src', data.image_url);
                $('#profileName').text(data.name);
                $('#profileFullName').text(data.name);
                $('#profileEmail').text(data.email);
                $('#profileBatch').text(data.batch);
                $('#profileLocation').text(data.location);
                $('#profileOccupation').text(data.occupation);
                $('#profileOccupationDetails').text(data.occupation);
                $('#profileCompany').text(data.company);
                $('#profileNumber').text(data.mobile_number);

                $('#alumniProfileDetailsModal').modal('show');
            },
            error: function() {
                alert('Failed to fetch profile details.');
            }
        });
    }
</script>

@endpush
@endsection