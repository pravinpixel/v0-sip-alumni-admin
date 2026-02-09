@extends('layouts.index')

@section('title', 'Role Management')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="create-page-container">
    <!-- Header Section -->
    <div class="create-page-header">
        <div class="main-head">
            <a href="{{ route('role.index') }}">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>
                @if(isset($role))
                    Edit Role
                @else
                    Create New Role
                @endif
            </h1>
        </div>
        <p class="main-subhead">
            @if(isset($role))
                Update role details and permissions
            @else
                Add a new role with specific permissions
            @endif
        </p>
    </div>

    <!-- Form Card -->
    <div class="create-page-form w-100">
        <form id="dynamic-form" method="post" action="{{ route('role.save') }}">
            @csrf
            <input type="hidden" name="id" id="id" value="{{$role->id??''}}">
            
            <!-- Basic Information -->
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">Basic Information</h2>
            
            <!-- Role Name -->
            <div class="form-group">
                <label class="form-label">
                    Role Name <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" id="role_name" name="role_name" value="{{$role->name??''}}" 
                    class="form-input" 
                    placeholder="e.g., Content Moderator">
                <span class="field-error" id="role_name-error"></span>
            </div>

            <!-- Hidden status field - default to active -->
            <input type="hidden" name="status" value="{{$role->status??1}}">
            @php
            $isFranchisee = isset($role) && $role->name === 'Franchisee';
            $block = 'user';
            @endphp

            <!-- Permissions Section -->
            <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">Permissions</h2>
                <span class="field-error" id="role_permissions-error"></span>
                <!-- User Management -->
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;" class="perm-block {{ $isFranchisee && $block !== 'directory' ? 'disabled' : '' }}">
                    <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                        <div style="min-width: 150px;">
                            <span style="font-weight: 600; color: #111827;">User Management</span>
                        </div>
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" class="select-all-row" data-group="user" 
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem; font-weight: 600;">All</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="user.view" class="permission-user"
                                    @if(isset($role)) {{ $role->hasPermissionTo('user.view') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">View</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="user.create" class="permission-user"
                                    @if(isset($role)) {{ $role->hasPermissionTo('user.create') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Create</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="user.edit" class="permission-user"
                                    @if(isset($role)) {{ $role->hasPermissionTo('user.edit') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Edit</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="user.delete" class="permission-user"
                                    @if(isset($role)) {{ $role->hasPermissionTo('user.delete') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Delete</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Role Management -->
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;" class="perm-block {{ $isFranchisee && $block !== 'directory' ? 'disabled' : '' }}">
                    <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                        <div style="min-width: 150px;">
                            <span style="font-weight: 600; color: #111827;">Role Management</span>
                        </div>
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" class="select-all-row" data-group="role" 
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem; font-weight: 600;">All</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="role.view" class="permission-role"
                                    @if(isset($role)) {{ $role->hasPermissionTo('role.view') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">View</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="role.create" class="permission-role"
                                    @if(isset($role)) {{ $role->hasPermissionTo('role.create') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Create</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="role.edit" class="permission-role"
                                    @if(isset($role)) {{ $role->hasPermissionTo('role.edit') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Edit</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="role.delete" class="permission-role"
                                    @if(isset($role)) {{ $role->hasPermissionTo('role.delete') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Delete</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Directory Management -->
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                        <div style="min-width: 150px;">
                            <span style="font-weight: 600; color: #111827;">Directory</span>
                        </div>
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" class="select-all-row" data-group="directory" 
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem; font-weight: 600;">All</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="directory.view" class="permission-directory"
                                    @if(isset($role)) {{ $role->hasPermissionTo('directory.view') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">View</span>
                            </label>
                            <!-- <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="directory.create" class="permission-directory"
                                    @if(isset($role)) {{ $role->hasPermissionTo('directory.create') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Create</span>
                            </label> -->
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="directory.edit" class="permission-directory"
                                    @if(isset($role)) {{ $role->hasPermissionTo('directory.edit') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Block/UnBlock</span>
                            </label>
                            <!-- <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="directory.delete" class="permission-directory"
                                    @if(isset($role)) {{ $role->hasPermissionTo('directory.delete') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Delete</span>
                            </label> -->
                        </div>
                    </div>
                </div>

                <!-- Forum Management -->
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;" class="perm-block {{ $isFranchisee && $block !== 'directory' ? 'disabled' : '' }}">
                    <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                        <div style="min-width: 150px;">
                            <span style="font-weight: 600; color: #111827;">Forum</span>
                        </div>
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" class="select-all-row" data-group="forum" 
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem; font-weight: 600;">All</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="forum.view" class="permission-forum"
                                    @if(isset($role)) {{ $role->hasPermissionTo('forum.view') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">View</span>
                            </label>
                            <!-- <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="forum.create" class="permission-forum"
                                    @if(isset($role)) {{ $role->hasPermissionTo('forum.create') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Create</span>
                            </label> -->
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="forum.edit" class="permission-forum"
                                    @if(isset($role)) {{ $role->hasPermissionTo('forum.edit') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Approve/Reject</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="forum.delete" class="permission-forum"
                                    @if(isset($role)) {{ $role->hasPermissionTo('forum.delete') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Remove</span>
                            </label>
                            <!-- <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="forum.approve" class="permission-forum"
                                    @if(isset($role)) {{ $role->hasPermissionTo('forum.approve') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Approve</span>
                            </label> -->
                        </div>
                    </div>
                </div>

                <!-- Announcement Management -->
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;" class="perm-block {{ $isFranchisee && $block !== 'directory' ? 'disabled' : '' }}">
                    <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                        <div style="min-width: 150px;">
                            <span style="font-weight: 600; color: #111827;">Announcement</span>
                        </div>
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" class="select-all-row" data-group="announcement"
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem; font-weight: 600;">All</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="announcement.view" class="permission-announcement"
                                    @if(isset($role)) {{ $role->hasPermissionTo('announcement.view') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">View</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="announcement.create" class="permission-announcement"
                                    @if(isset($role)) {{ $role->hasPermissionTo('announcement.create') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Create</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="announcement.edit" class="permission-announcement"
                                    @if(isset($role)) {{ $role->hasPermissionTo('announcement.edit') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Edit</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="announcement.delete" class="permission-announcement"
                                    @if(isset($role)) {{ $role->hasPermissionTo('announcement.delete') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Delete</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

                    <div class="form-group">
                        <label
                            class="form-label">
                            Status
                        </label>
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.75rem;">
                            <label style="position: relative; display: inline-block; width: 48px; height: 24px;">
                                <input type="checkbox" id="status-toggle" 
                                    {{ isset($role) && $role->status == 1 ? 'checked' : (!isset($role) ? 'checked' : '') }}
                                    style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e5e7eb; transition: 0.3s; border-radius: 24px;"></span>
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: 0.3s; border-radius: 50%;"></span>
                            </label>
                            <span id="status-label" style="font-size: 12px; font-weight: 600; color: {{ isset($role) && $role->status == 1 ? '#16a34a' : (!isset($role) ? '#16a34a' : '#dc2626') }}; transition: color 0.3s;">
                                {{ isset($role) && $role->status == 1 ? 'Active' : (!isset($role) ? 'Active' : 'Inactive') }}
                            </span>
                        </div>
                        <input type="hidden" name="status" id="status" value="{{ isset($role) && $role->status == 1 ? '1' : (!isset($role) ? '1' : '0') }}">
                        <span class="field-error" id="status-error"
                            style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                    </div>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; margin-top: 2rem;">
                <button type="button" onclick="window.location='{{ route('role.index') }}'" 
                    style="padding: 0.75rem 1.5rem; border: 1px solid #e5e7eb; background: white; border-radius: 0.5rem; font-size: 12px; font-weight: 600; cursor: pointer; color: #111827;">
                    Cancel
                </button>
                <button type="button" id="dynamic-submit" 
                    style="padding: 0.75rem 1.5rem; background: #ba0028; color: white; border: none; border-radius: 0.5rem; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-save"></i>
                    @if(isset($role))
                        Update Role
                    @else
                        Create Role
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
            background-color: #16a34a !important;
        }
        #status-toggle + span {
            background-color: #dc2626 !important;
        }
        #status-toggle:checked + span + span {
            transform: translateX(24px);
        }
        .perm-block.disabled {
            opacity: .5;
            pointer-events: none;
        }
    </style>
<script>
    $(document).ready(function() {
        $('#dynamic-submit').on('click', function(e) {
            saveUpdateRole(e);
        });

        // Select All for each row
        $('.select-all-row').on('change', function() {
            const group = $(this).data('group');
            const isChecked = $(this).is(':checked');
            $(`.permission-${group}`).prop('checked', isChecked);
        });

        // Update row select-all when individual checkboxes change
        $('input[name="permissions[]"]').on('change', function() {
            const classList = $(this).attr('class');
            const groupMatch = classList.match(/permission-(\w+)/);
            
            if (groupMatch) {
                const group = groupMatch[1];
                const totalInGroup = $(`.permission-${group}`).length;
                const checkedInGroup = $(`.permission-${group}:checked`).length;
                
                $(`.select-all-row[data-group="${group}"]`).prop('checked', totalInGroup === checkedInGroup);
            }
        });

        // Initialize row select-all checkboxes on page load
        $('.select-all-row').each(function() {
            const group = $(this).data('group');
            const totalInGroup = $(`.permission-${group}`).length;
            const checkedInGroup = $(`.permission-${group}:checked`).length;
            $(this).prop('checked', totalInGroup === checkedInGroup && totalInGroup > 0);
        });
        $('#status-toggle').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#status').val('1');
                    $('#status-label').text('Active').css('color', '#16a34a');
                } else {
                    $('#status').val('0');
                    $('#status-label').text('Inactive').css('color', '#dc2626');
                }
            });

            $('#dynamic-submit').on('click', function (e) {
                saveUpdateUser(e);
            });

            // Auto-select VIEW when CREATE / EDIT / DELETE is checked
            $('input[name="permissions[]"]').on('change', function () {
                const value = $(this).val();
                const isChecked = $(this).is(':checked');
                const parts = value.split('.');
                if (parts.length !== 2) return;

                const module = parts[0];
                const action = parts[1];
                const viewCheckbox = $(`input[name="permissions[]"][value="${module}.view"]`);
                if (['create', 'edit', 'delete'].includes(action) && isChecked) {
                    viewCheckbox.prop('checked', true);
                }
                // If view unchecked → uncheck create/edit/delete
                if (action === 'view' && !isChecked) {
                    $(`input[name="permissions[]"][value="${module}.create"],
                    input[name="permissions[]"][value="${module}.edit"],
                    input[name="permissions[]"][value="${module}.delete"]`)
                    .prop('checked', false);
                }
            });

    });

    function saveUpdateRole(event) {
        event.preventDefault();

        let allValues = $('#dynamic-form').serialize();
        
        $.ajax({
            url: $('#dynamic-form').attr('action'),
            type: 'POST',
            data: allValues,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message);
                    window.location.href = '{{ route("role.index") }}';
                } else {
                    showToast(response.error, 'error');
                }
            },
            error: function(response) {
                if (response.status === 422 && response.responseJSON.error) {
                    var errors = response.responseJSON.error;
                    $('#dynamic-form').find(".field-error").text('');
                    $.each(errors, function(key, value) {
                    if (key === 'permissions') {
                        $('#role_permissions-error').text(value[0]);
                    } else {
                        $('#' + key + '-error').text(value[0]);
                    }
                });
                } else {
                    showToast(response.responseJSON.error || 'An error occurred', 'error');
                }
            }
        });
    }
</script>
@endsection
