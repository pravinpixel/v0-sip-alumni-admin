@extends('alumni.layouts.index')
@section('title', 'Directory - Alumni Tracking')

@section('content')
<style>
    /* Override DataTables default styles */
    #alumniTable thead tr {
        background: linear-gradient(90deg, #dc2626 0%, #f59e0b 100%) !important;
    }

    #alumniTable thead th {
        background: transparent !important;
        color: white !important;
        border: none !important;
    }

    #alumniTable tbody tr:hover {
        background-color: #f9fafb !important;
    }

    /* Remove DataTables default elements */
    #alumniTable_length {
        display: none !important;
    }

    #alumniTable_filter {
        display: none !important;
    }

    /* Ensure table wrapper has proper border radius */
    .dataTables_wrapper {
        border-radius: 12px !important;
        overflow: hidden !important;
    }

    /* Style pagination */
    .dataTables_paginate {
        padding: 16px !important;
        text-align: center !important;
    }

    .dataTables_info {
        padding: 16px !important;
        color: #6b7280 !important;
        font-size: 14px !important;
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    {{-- Header --}}
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: 700; color: #111827; margin-bottom: 8px;">Alumni Directory</h1>
        <p style="color: #6b7280; font-size: 15px;">Connect with 15 alumni from SIP Academy</p>
    </div>

    {{-- Search and Filter --}}
    <div style="display: flex; gap: 12px; margin-bottom: 24px;">
        <div style="flex: 1; position: relative;">
            <i class="fas fa-search"
                style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
            <input type="text" id="searchInput" placeholder="Search alumni..."
                style="width: 100%; padding: 12px 16px 12px 45px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none;"
                onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#e5e7eb'">
        </div>
        <button id="filterToggleBtn"
            style="background: white; color: #374151; border: 2px solid #e5e7eb; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px;"
            onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
            <i class="fas fa-filter"></i>
            Filter
        </button>
    </div>

    {{-- Info Banner --}}
    <div
        style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <p style="color: #991b1b; font-size: 14px; margin: 0;">
            You can share your contact with alumni. Once they accept, you can view their profile and contact info in the
            Connections menu.
        </p>
        <button onclick="this.parentElement.style.display='none'"
            style="background: transparent; border: none; color: #991b1b; cursor: pointer; font-size: 18px; padding: 0 8px;">
            ×
        </button>
    </div>

    {{-- Filter Section --}}
    <div id="filterSection"
        style="display: none; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
            <div>
                <label
                    style="font-weight: 600; font-size: 14px; color: #374151; display: block; margin-bottom: 8px;">Batch
                    Year</label>
                <input type="text" id="filterBatch" placeholder="e.g. 2022"
                    style="width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none;"
                    onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#e5e7eb'">
            </div>
            <div>
                <label
                    style="font-weight: 600; font-size: 14px; color: #374151; display: block; margin-bottom: 8px;">Location</label>
                <input type="text" id="filterLocation" placeholder="e.g. Chennai"
                    style="width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none;"
                    onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#e5e7eb'">
            </div>
        </div>
    </div>

    {{-- Alumni Table --}}
    <div style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
        <table id="alumniTable" class="display" style="width: 100%; margin: 0; border-collapse: collapse;">
            <thead>
                <tr style="background: linear-gradient(90deg, #dc2626 0%, #f59e0b 100%); color: white;">
                    <th style="padding: 16px; font-weight: 600; text-align: left; border: none;">
                        Alumni <i class="fas fa-sort" style="margin-left: 8px; font-size: 12px;"></i>
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
    <div id="customPaginationContainer" class="mt-10"></div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#alumniTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('alumni.directory.data') }}",
                data: function(d) {
                    d.batch = $('#filterBatch').val();
                    d.location = $('#filterLocation').val();
                }
            },
            columns: [{
                    data: 'alumni',
                    name: 'alumni',
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
            ],
            paging: true,
            searching: false,
            ordering: false,
            lengthChange: false,
            pagelength: 10,
            dom: 't'
        });
        table.on('draw', function() {
            let info = table.page.info();

            let html = `
          <div class="d-flex justify-content-between align-items-center" style="padding: 10px 5px;">
            
            <div class="text-muted" style="font-size: 14px;">
                Showing ${info.start + 1}-${info.end} of ${info.recordsTotal} alumni
            </div>

            <div>
                <button class="btn btn-light btn-sm me-2" id="prevPage" ${info.page === 0 ? "disabled" : ""}>‹ Previous</button>

                <span class="px-3 py-1 bg-danger text-white rounded" style="font-weight:600;">
                    ${info.page + 1}
                </span>

                <button class="btn btn-light btn-sm ms-2" id="nextPage" ${(info.page + 1 === info.pages) ? "disabled" : ""}>Next ›</button>
            </div>

           </div>
           `;

            $("#customPaginationContainer").html(html);

            // Attach events again
            $("#prevPage").on("click", function() {
                table.page("previous").draw("page");
            });

            $("#nextPage").on("click", function() {
                table.page("next").draw("page");
            });
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

            const icon = $(this).find('i');
            if (isVisible) {
                icon.removeClass('fa-times').addClass('fa-filter');
            } else {
                icon.removeClass('fa-filter').addClass('fa-times');
            }
        });
    });
</script>
@endpush
@endsection