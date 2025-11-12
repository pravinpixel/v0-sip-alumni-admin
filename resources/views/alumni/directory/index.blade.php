@extends('alumni.layouts.index')
@section('title', 'Directory - Alumni Tracking')

@section('content')
<div style="margin-bottom: 30px;">
    <h2 style="font-size: 24px; font-weight: 700; color: #333; margin-bottom: 8px;">Alumni Directory</h2>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
        Connect with 15 alumni from SIP Academy
    </p>

    <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 10px;">
        <div style="flex: 1; position: relative;">
            <input type="text" id="searchInput" placeholder="🔍 Search alumni..."
                style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
        </div>
        <button id="filterToggleBtn"
            style="background-color: white; border: 1px solid #ccc; border-radius: 6px; padding: 10px 15px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px;">
            <span>🔽 Open Filters</span>
        </button>
    </div>

    <div id="filterSection"
        style="display: none; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: 600; font-size: 14px; color: #333;">Batch Year</label>
                <input type="text" id="filterBatch" placeholder="e.g. 2022"
                    style="width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="font-weight: 600; font-size: 14px; color: #333;">Location</label>
                <input type="text" id="filterLocation" placeholder="e.g. Chennai"
                    style="width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>
        </div>
    </div>

    <table id="alumniTable" class="display" style="width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 6px;">
        <thead>
            <tr style="background: linear-gradient(90deg, #c41e3a 0%, #c41e3a 30%, #ff8c42 70%, #ff8c42 100%); color: white; font-weight: 700; font-size: 14px;">
                <th style="padding: 15px; text-align: left;">Alumni</th>
                <th style="padding: 15px; text-align: left;">Batch</th>
                <th style="padding: 15px; text-align: left;">Location</th>
                <th style="padding: 15px; text-align: left;">Action</th>
            </tr>
        </thead>
    </table>
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
            data: function (d) {
                d.batch = $('#filterBatch').val();
                d.location = $('#filterLocation').val();
            }
        },
        columns: [
            { data: 'alumni', name: 'alumni', orderable: false, searchable: false },
            { data: 'batch', name: 'batch', orderable: false, searchable: false },
            { data: 'location', name: 'location', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        paging: true,
        searching: false,
        ordering: false,
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
        $(this).find('span').text(isVisible ? '🔽 Open Filters' : '🔼 Close Filters');
    });
});
</script>
@endpush
@endsection
