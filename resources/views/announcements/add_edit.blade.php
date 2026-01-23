@extends('layouts.index')

@section('title', 'Announcement Management')

@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div style="padding: 2rem;">
        <!-- Header Section -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                <a href="{{ route('admin.announcements.index') }}" style="color: #6b7280; text-decoration: none; font-size: 1.5rem;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">
                    @if(isset($announcement))
                        Edit Announcement
                    @else
                        Create Announcement
                    @endif
                </h1>
            </div>
            <p style="font-size: 1rem; color: #6b7280; margin-left: 2rem;">
                @if(isset($announcement))
                    Update announcement details
                @else
                    Add a new announcement for alumni
                @endif
            </p>
        </div>

        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem; width: 70%;">

            <form id="dynamic-form" method="post" action="{{ route('admin.announcements.save') }}">
                @csrf
                <input type="hidden" name="id" id="id" value="{{$announcement->id ?? ''}}">

                <!-- Announcement Title -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                        Announcement Title <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{$announcement->title ?? ''}}"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                        placeholder="Enter announcement title">
                    <span class="field-error" id="title-error"
                        style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                </div>

                <!-- Announcement Description -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                        Announcement Description <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical;"
                        placeholder="Enter announcement description">{{$announcement->description ?? ''}}</textarea>
                    <span class="field-error" id="description-error"
                        style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                </div>

                <!-- Announcement Expiry Date -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                        Expiry Date <span style="color: #dc2626;">*</span>
                    </label>
                    @php
                        $expiry = isset($announcement) && $announcement->expiry_date
                            ? \Carbon\Carbon::parse($announcement->expiry_date)->format('Y-m-d\TH:i')
                            : '';
                    @endphp
                    <input type="datetime-local" id="expiry_date" name="expiry_date"
                        value="{{ $expiry }}"
                        style="width:100%; padding:0.75rem; border:1px solid #e5e7eb; border-radius:0.5rem; font-size:0.875rem;">
                    <span class="field-error" id="expiry_date-error"
                        style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                </div>

                <!-- Status Toggle -->
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                        Status
                    </label>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.75rem;">
                        <label style="position: relative; display: inline-block; width: 48px; height: 24px;">
                            <input type="checkbox" id="status-toggle" 
                                {{ isset($announcement) && $announcement->status == 1 ? 'checked' : (!isset($announcement) ? 'checked' : '') }}
                                style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e5e7eb; transition: 0.3s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: 0.3s; border-radius: 50%;"></span>
                        </label>
                        <span id="status-label" style="font-size: 0.875rem; font-weight: 600; transition: color 0.3s;">
                            {{ isset($announcement) && $announcement->status == 1 ? 'Active' : (!isset($announcement) ? 'Active' : 'Inactive') }}
                        </span>
                    </div>
                    <input type="hidden" name="status" id="status" value="{{ isset($announcement) && $announcement->status == 1 ? '1' : (!isset($announcement) ? '1' : '0') }}">
                    <span class="field-error" id="status-error"
                        style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="button" onclick="window.location='{{ route('admin.announcements.index') }}'"
                        style="padding: 0.75rem 1.5rem; border: 1px solid #e5e7eb; background: white; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; color: #111827;">
                        Cancel
                    </button>
                    <button type="button" id="dynamic-submit"
                        style="padding: 0.75rem 1.5rem; background: #ba0028; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-save"></i>
                        @if(isset($announcement))
                            Update Announcement
                        @else
                            Create Announcement
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('script')
    @parent
    <style>
        /* Toggle Switch Styles */
        #status-toggle:checked + span {
            background-color: #ba0028 !important;
        }
        #status-toggle + span {
            background-color: #dedede !important;
        }
        #status-toggle:checked + span + span {
            transform: translateX(24px);
        }
    </style>
    <script>
        $(document).ready(function () {
            // Status Toggle Script
            $('#status-toggle').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#status').val('1');
                    $('#status-label').text('Active');
                } else {
                    $('#status').val('0');
                    $('#status-label').text('Inactive');
                }
            });

            $('#dynamic-submit').on('click', function (e) {
                saveUpdateAnnouncement(e);
            });

            document.getElementById('expiry_date').addEventListener('click', function () {
                this.showPicker();
            });

        });

        function saveUpdateAnnouncement(event) {
            event.preventDefault();

            // Clear previous errors
            $('.field-error').text('');

            // Serialize the form data
            let allValues = $('#dynamic-form').serialize();

            // Perform the AJAX request
            $.ajax({
                url: $('#dynamic-form').attr('action'),
                type: 'POST',
                data: allValues,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        showToast(response.message);
                        window.location.href = '{{ route("admin.announcements.index") }}';
                    } else {
                        showToast(response.error, 'error');
                    }
                },
                error: function (response) {
                    if (response.status === 422 && response.responseJSON.error) {
                        var errors = response.responseJSON.error;
                        $.each(errors, function (key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                    } else {
                        showToast(response.responseJSON?.error || 'An error occurred', 'error');
                    }
                }
            });
        }
    </script>
@endsection