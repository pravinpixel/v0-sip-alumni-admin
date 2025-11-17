@extends('layouts.index')

@section('title', 'Role Management')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div style="padding: 2rem;">
    <!-- Header Section -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
            <a href="{{url('role')}}" style="color: #6b7280; text-decoration: none; font-size: 1.5rem;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">
                @if(isset($role))
                    Edit Role
                @else
                    Create New Role
                @endif
            </h1>
        </div>
        <p style="font-size: 0.875rem; color: #6b7280; margin-left: 3.5rem;">
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
            
            <div style="display: grid; grid-template-columns: 1fr auto; gap: 1.5rem; margin-bottom: 2rem; align-items: start;">
                <!-- Role Name -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                        Role Name <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" id="role_name" name="role_name" value="{{$role->name??''}}" 
                        style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;" 
                        placeholder="e.g., Content Moderator">
                    <span class="field-error" id="role_name-error" style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                </div>

                <!-- Status Toggle -->
                <div style="padding-top: 2rem;">
                    @if(!isset($role) || is_null($role->id))
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status_create" name="status" value="1" checked>
                        <label class="form-check-label" for="status_create">Active</label>
                    </div>
                    @endif

                    @if(isset($role) && !is_null($role->id))
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status_update" name="status" value="1" {{ $role->status ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_update">Active</label>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Permissions Section -->
            <div style="border-top: 1px solid #e5e7eb; padding-top: 2rem;">
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">Permissions</h2>
                
                <!-- User Management -->
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                        <div style="min-width: 150px;">
                            <span style="font-weight: 600; color: #111827;">User Management</span>
                        </div>
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="user.view" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('user.view') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">View</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="user.create" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('user.create') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Create</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="user.edit" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('user.edit') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Edit</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="user.delete" 
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
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="role.view" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('role.view') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">View</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="role.create" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('role.create') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Create</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="role.edit" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('role.edit') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Edit</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="role.delete" 
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
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="directory.view" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('directory.view') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">View</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="directory.create" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('directory.create') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Create</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="directory.edit" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('directory.edit') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Edit</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="directory.delete" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('directory.delete') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Delete</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Forum Management -->
                <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                        <div style="min-width: 150px;">
                            <span style="font-weight: 600; color: #111827;">Forum</span>
                        </div>
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="forum.view" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('forum.view') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">View</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="forum.create" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('forum.create') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Create</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="forum.edit" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('forum.edit') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Edit</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="forum.delete" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('forum.delete') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Delete</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="forum.approve" 
                                    @if(isset($role)) {{ $role->hasPermissionTo('forum.approve') ? 'checked' : '' }} @endif
                                    style="width: 18px; height: 18px; cursor: pointer;">
                                <span style="font-size: 0.875rem;">Approve</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; margin-top: 2rem;">
                <button type="button" onclick="window.location='{{url('role')}}'" 
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
        $("#status_create, #status_update").on('change', function() {
            if ($(this).is(':checked')) {
                $(this).val(1);
                $('label[for="' + $(this).attr('id') + '"]').text('Active');
            } else {
                $(this).val(0);
                $('label[for="' + $(this).attr('id') + '"]').text('Inactive');
            }
        });

        $('#dynamic-submit').on('click', function(e) {
            saveUpdateRole(e);
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
                    toastr.success(response.message);
                    window.location.href = '{{ route("role.index") }}';
                } else {
                    toastr.error(response.error);
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
                    toastr.error(response.responseJSON.error || 'An error occurred');
                }
            }
        });
    }
</script>
@endsection
