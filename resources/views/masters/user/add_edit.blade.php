@extends('layouts.index')

@section('title', 'User Management')

@section('content')

    @php
        $decryptedPassword = '';
        if (isset($user->hash_password)) {
            try {
                $decryptedPassword = Crypt::decryptString($user->hash_password);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                $decryptedPassword = '';
            }
        }
    @endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div style="padding: 2rem;">
        <!-- Header Section -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                <a href="{{ route('user.index') }}" style="color: #6b7280; text-decoration: none; font-size: 1.5rem;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 style="font-size: 2rem; font-weight: 700; color: #111827; margin: 0;">
                    @if(isset($user))
                        Edit User
                    @else
                        Create New User
                    @endif
                </h1>
            </div>
            <p style="font-size: 0.875rem; color: #6b7280; margin-left: 3.5rem;">
                @if(isset($user))
                    Update user details and permissions
                @else
                    Add a new admin user to the system
                @endif
            </p>
        </div>

        <!-- Form Card -->
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem;">
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.5rem 0;">User Details</h2>

            <form id="dynamic-form" method="post" action="{{ route('user.save') }}">
                @csrf
                <input type="hidden" name="id" id="id" value="{{$user->id ?? ''}}">

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <!-- User ID -->
                    <div>
                        <label
                            style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                            User ID <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text"
                            value="{{isset($user) ? 'USER' . str_pad($user->id, 3, '0', STR_PAD_LEFT) : 'Auto-generated'}}"
                            style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: #f9fafb;"
                            disabled>
                    </div>

                    <!-- User Name -->
                    <div>
                        <label
                            style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                            User Name <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" id="user_name" name="user_name" value="{{$user->name ?? ''}}"
                            style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                            placeholder="e.g., John Doe">
                        <span class="field-error" id="user_name-error"
                            style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                    </div>
                </div>

                <!-- Email ID (Full Width) -->
                <div style="margin-bottom: 1.5rem;">
                    <label
                        style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                        Email ID <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{$user->email ?? ''}}"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                        placeholder="e.g., john.doe@sipadmin.com">
                    <span class="field-error" id="email-error"
                        style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <!-- Password -->
                    <div>
                        <label
                            style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                            @if(isset($user))
                                New Password <span style="color: #6b7280; font-weight: 400;">(Leave blank to keep current)</span>
                            @else
                                Password <span style="color: #dc2626;">*</span>
                            @endif
                        </label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" 
                                value="{{ isset($user) && $decryptedPassword ? $decryptedPassword : '' }}"
                                style="width: 100%; padding: 0.75rem; padding-right: 3rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                placeholder="Enter password">
                            <i class="fas fa-eye-slash toggle-password" data-toggle="password"
                                style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280;"></i>
                        </div>
                        <span class="field-error" id="password-error"
                            style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label
                            style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                            @if(isset($user))
                                Confirm New Password
                            @else
                                Confirm Password <span style="color: #dc2626;">*</span>
                            @endif
                        </label>
                        <div style="position: relative;">
                            <input type="password" id="retype_password" name="retype_password"
                                value="{{ isset($user) && $decryptedPassword ? $decryptedPassword : '' }}"
                                style="width: 100%; padding: 0.75rem; padding-right: 3rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem;"
                                placeholder="Re-enter password">
                            <i class="fas fa-eye-slash toggle-password" data-toggle="retype_password"
                                style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6b7280;"></i>
                        </div>
                        <span class="field-error" id="retype_password-error"
                            style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                    <!-- Select Role -->
                    <div>
                        <label
                            style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                            Select Role <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="role_id" id="role_id"
                            style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ isset($user) && $role->id == old('role_id', $user->role_id) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="field-error" id="role_id-error"
                            style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                    </div>

                    <!-- Status -->
                    <div>
                        <label
                            style="display: block; font-size: 0.875rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">
                            Status
                        </label>
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.75rem;">
                            <label style="position: relative; display: inline-block; width: 48px; height: 24px;">
                                <input type="checkbox" id="status-toggle" 
                                    {{ isset($user) && $user->status == 1 ? 'checked' : (!isset($user) ? 'checked' : '') }}
                                    style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e5e7eb; transition: 0.3s; border-radius: 24px;"></span>
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: 0.3s; border-radius: 50%;"></span>
                            </label>
                            <span id="status-label" style="font-size: 0.875rem; font-weight: 600; color: {{ isset($user) && $user->status == 1 ? '#16a34a' : (!isset($user) ? '#16a34a' : '#dc2626') }}; transition: color 0.3s;">
                                {{ isset($user) && $user->status == 1 ? 'Active' : (!isset($user) ? 'Active' : 'Inactive') }}
                            </span>
                        </div>
                        <input type="hidden" name="status" id="status" value="{{ isset($user) && $user->status == 1 ? '1' : (!isset($user) ? '1' : '0') }}">
                        <span class="field-error" id="status-error"
                            style="color:#dc2626; font-size: 0.75rem; margin-top: 0.25rem; display: block;"></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                    <button type="button" onclick="window.location='{{ route('user.index') }}'"
                        style="padding: 0.75rem 1.5rem; border: 1px solid #e5e7eb; background: white; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; color: #111827;">
                        Cancel
                    </button>
                    <button type="button" id="dynamic-submit"
                        style="padding: 0.75rem 1.5rem; background: #ba0028; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-save"></i>
                        @if(isset($user))
                            Update User
                        @else
                            Create User
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
    </style>
    <script>
        $(document).ready(function () {
            // Password Eye Toggle Script
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function () {
                    let input = document.getElementById(this.getAttribute('data-toggle'));

                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    } else {
                        input.type = 'password';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    }
                });
            });

            // Status Toggle Script
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
        });

        function saveUpdateUser(event) {
            event.preventDefault();

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
                        window.location.href = '{{ route("user.index") }}';
                    } else {
                        showToast(response.error, 'error');
                    }
                },
                error: function (response) {
                    if (response.status === 422 && response.responseJSON.error) {
                        var errors = response.responseJSON.error;
                        $('#dynamic-form').find(".field-error").text('');
                        $.each(errors, function (key, value) {
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