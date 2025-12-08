@extends('layouts.index')

@section('title', 'Role Management')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div style="padding: 2rem;">
    <!-- Header Section -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
            <a href="{{ route('role.index') }}" style="color: #6b7280; text-decoration: none; font-size: 1.5rem;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 style="font-size: 3rem; font-weight: 700; color: #111827; margin: 0;">
                @if(isset($role))
                    Edit Role
                @else
                    Create New Role
                @endif
            </h1>
        </div>
        <p style="font-size: 1.2rem; color: #6b7280; margin-left: 1.8rem;">
            @if(isset($role))
                Update role details and permissions
            @else
                Add a new role with specific permissions
            @endif
        </p>
    </div>

    <!-- Form Card -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem;">
        <form id="dynamic-form" method="post" action="{{ route('role.save') }}">
            @csrf
            <input type="hidden" name="id" id="id" value="{{$role->id??''}}">
            
            <!-- Basic Information -->
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">Basic Information</h2>
            
            <!-- Role Name -->
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                    Role Name <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" id="role_name" name="role_name" value="{{$role->name??''}}" 
                    style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;" 
                    placeholder="e.g., Content Moderator">
                <span class="field-error" id="role_name-error" style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
            </div>

            <!-- Hidden status field - default to active -->
            <input type="hidden" name="status" value="{{$role->status??1}}">

            <!-- Permissions Section -->
            <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">Permissions</h2>
                
                <!-- User Management -->
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
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
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
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
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
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
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; margin-top: 2rem;">
                <button type="button" onclick="window.location='{{ route('role.index') }}'" 
                    style="padding: 0.75rem 1.5rem; border: 1px solid #e5e7eb; background: white; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; color: #111827;">
                    Cancel
                </button>
                <button type="button" id="dynamic-submit" 
                    style="padding: 0.75rem 1.5rem; background: #dc2626; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
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
                        $('#' + key + '-error').text(value[0]);
                    });
                } else {
                    showToast(response.responseJSON.error || 'An error occurred', 'error');
                }
            }
        });
    }
</script>
@endsection
