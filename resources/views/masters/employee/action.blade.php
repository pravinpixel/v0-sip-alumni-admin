@extends('layouts.index')

@section('title', 'Task Master | Usha Fire')
@section('style')
@parent
@endsection

<style>
    .action_td {
        display: flex;
    }

    .input-group {
        position: relative;
        width: 100%;
    }

    .input-group .form-control {
        width: 100%;
        padding-right: 40px;
        /* Adjust to leave space for the icon */
    }

    .input-group .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        /* Adjust as needed */
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 1.2em;
        /* Adjust for icon size */
        z-index: 999;
    }

    #profile-img {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        overflow-clip-margin: 0 !important;
    }

    .select2-container--bootstrap5 .select2-selection--multiple .select2-search.select2-search--inline {
        display: contents !important;
    }

    @media (max-width: 768px) {
        .input-group .toggle-password {
            font-size: 1em;
            /* Adjust size for smaller screens */
            right: 5px;
            /* Adjust for smaller screens */
        }
    }

    @media (max-width: 480px) {
        .input-group .toggle-password {
            font-size: 0.9em;
            /* Further adjust for very small screens */
            right: 3px;
            /* Adjust as needed */
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

@section('content')
@php
$decryptedPassword = '';
if (isset($user->hash_password)) {
try {
$decryptedPassword = Crypt::decryptString($user->hash_password);
} catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
// Handle decryption failure
// Log the error or take appropriate action
$decryptedPassword = ''; // Default to an empty string or handle as needed
}
}
@endphp
<div id="pageLoader" class="page-loader">
    <div class="loader"></div>
</div>

<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <!--begin::Title-->
            @if(isset($user))
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Edit Employee</h1>
            @else
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Add Employee</h1>
            @endif
            <!--end::Title-->
            <!--begin::Breadcrumb-->
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <!--begin::Item-->
                <li class="breadcrumb-item text-muted">
                    <a href="{{url('dashboard')}}" class="text-muted text-hover-primary">Dashbord</a>
                </li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{url('employee')}}" class="text-muted text-hover-primary">Employees</a>
                </li>
                <!--end::Item-->
            </ul>
            <!--end::Breadcrumb-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <!--begin::Filter menu-->
            <div class="m-0">
                <!--begin::Menu toggle-->
                <!--end::Menu 1-->
            </div>
            <!--end::Filter menu-->
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Toolbar container-->
</div>
<!--end::Toolbar-->
<div class="card mb-5 mb-xl-8">
    <!--begin::Header-->
    <div class="card-header border-0 pt-5 card_arrow">
        <a href="{{url('employee')}}" style="cursor: pointer;" class="text-muted text-hover-primary">
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
        </a>
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Employee </span>
        </h3>
        <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" title="Click to add a employee">
        </div>
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body py-3">
        <!--begin::Table container-->
        <form method="post" id="dynamic-form" method="post" action="{{ url('/employee/save') }}" class="form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="id" value="{{$user->id??''}}">
            <div class="col-md-12 row mb-6 fv-row">
                <div class="col-md-3 mt-4">
                    <label class=" form-label">Profile Image </label>
                </div>
                <div class="col-md-7">
                    <div class="card-body text-center pt-0">

                        <div class="image-input  image-input-outline image-input-placeholder mb-3" data-kt-image-input="true">
                            <div class="image-input-wrapper w-150px h-150px">
                                <img class="w-150px h-150px" id="profile-img" src="{{ isset($user->profile_image) && !empty($user->profile_image) ? url($user->profile_image) : asset('images/avatar/blank.png') }}">
                            </div>
                            <span class="field-error" id="profile_image-error" style="color:red"></span>

                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Upload Image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" id="file-input" name="profile_image" accept=".png, .jpg, .jpeg" value="{{ $user->profile_image ?? old('profile_image') }}" />
                                <input type="hidden" name="avatar_remove" value="0" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove profile image">
                                <i class="bi bi-x fs-2"></i>
                            </span>

                        </div>

                    </div>
                </div>
                <span class="field-error" style="color:red" id="cover_image-error"></span>
            </div>
            <!--end::Input group-->


            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="employee_id">Employee ID</label>
                        <input type="text" name="employee_id" placeholder="Employee ID" class=" form-control" value="{{ $user->employee_id ?? old('employee_id') }}" />
                        <span class="field-error" id="employee_id-error" style="color:red"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="first_name">First Name</label>
                                <input type="text" name="first_name" placeholder="First Name" class=" form-control" value="{{ $user->first_name ?? old('first_name') }}" />
                                <span class="field-error" id="first_name-error" style="color:red"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="last_name">Last Name</label>
                                <input type="text" name="last_name" placeholder="Last Name" class=" form-control" value="{{ $user->last_name ?? old('last_name') }}" />
                                <span class="field-error" id="last_name-error" style="color:red"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-6">
                    <label class="required" for="email">Email</label>
                    <input type="email" name="email" placeholder=" Email" class="required form-control" value="{{ $user->email ?? old('email') }}" />
                    <span class="field-error" id="email-error" style="color:red"></span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="mobile_number">Mobile Number</label>
                    <input type="tel" id="mobile" name="mobile" placeholder="Mobile Number" class="required form-control" value="{{ $user->phone_number ?? old('phone_number') }}" maxlength="10" />
                    <span class="field-error" id="mobile-error" style="color:red"></span>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-6">
                    <label for="status" class="required">Status</label>
                    <select name="status" class="form-control form-control-lg" data-minimum-results-for-search="Infinity" data-control="select2" data-hide-search="true" value="{{ $user->status ?? old('status') }}" data-placeholder="Choose Status">
                        <option></option>
                        <option value="1" @if(isset($user)){{$user->status == 1  ? 'selected' : '' }} @else selected @endif>Active</option>
                        <option value="0" @if(isset($user)){{$user->status== 0  ? 'selected' : '' }}@endif>Inactive</option>
                    </select>
                    <span class="field-error" id="status-error" style="color:red"></span>
                </div>
                <div class="col-md-6">
                    <label for="status" class="required">Department</label>
                    <select data-control="select2" data-hide-search="false" name="department_id" class="form-control" value="{{ $user->department ?? old('department') }}" data-placeholder="Choose Department">
                        <option></option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ isset($user) && $department->id == old('department_id', $user->department_id) ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    <span class="field-error" id="department_id-error" style="color:red"></span>
                </div>
            </div>

            <div class="row mt-5">
                <!-- <div class="col-md-6">
                    <label for="status" class="required">Branch</label>
                    <select data-control="select2" data-hide-search="false" name="branch_id" class="form-control" value="{{ $user->branch ?? old('branch') }}" data-placeholder="Choose Branch">
                        <option></option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ isset($user) && $branch->id == old('branch_id', $user->branch_id) ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <span class="field-error" id="branch_id-error" style="color:red"></span>
                </div> -->

                <div class="col-md-6">
                    <label for="status" class="required">Branch</label>
                    <select data-control="select2" id ="branchSelect" data-hide-search="false" name="branch_id[]" class="form-control" multiple="multiple" data-placeholder="Choose or Search Branches">
                        @php
                            // Get selected branch IDs from old input or user data
                            $branchIds = $user->branch_id ?? [];

                            if (!is_array($branchIds)) {
                                $branchIds = is_string($branchIds) ? json_decode($branchIds, true) : [$branchIds];
                            }

                            $selectedBranches = old('branch_id', (array) $branchIds);

                            // Get all branch IDs from the branches list
                            $allBranchIds = $branches->pluck('id')->map(fn($id) => (string) $id)->toArray();
                            $selectedBranchIds = array_map('strval', $selectedBranches);

                            // Check if all branch IDs are selected
                            $isAllSelected = count($allBranchIds) > 0 && empty(array_diff($allBranchIds, $selectedBranchIds));
                        @endphp
                        <option value="all" {{ $isAllSelected ? 'selected' : '' }} >-- Select All Branches --</option>
                        @php
                        $branchIds = $user->branch_id ?? [];
                        if (!is_array($branchIds)) {
                        $branchIds = is_string($branchIds) ? json_decode($branchIds, true) : [$branchIds];
                        }
                        $selectedBranches = old('branch_id', (array) $branchIds);
                        @endphp

                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ in_array($branch->id, $selectedBranches) ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                        @endforeach
                    </select>
                    <span class="field-error" id="branch_id-error" style="color:red"></span>
                </div>

                <div class="col-md-6">
                    <label for="status" class="required">Office Location</label>
                    <select data-control="select2" data-hide-search="false" name="location_id" class="form-control" value="{{ $user->location ?? old('location') }}" data-placeholder="Choose Location">
                        <option></option>
                        @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ isset($user) && $location->id == old('location_id', $user->location_id) ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                    <span class="field-error" id="location_id-error" style="color:red"></span>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-6">
                    <label for="status" class="required">Designation</label>
                    <select data-control="select2" data-hide-search="false" name="designation_id" class="form-control" value="{{ $user->designation ?? old('designation') }}" data-placeholder="Choose Designation">
                        <option></option>
                        @foreach($designations as $designation)
                        <option value="{{ $designation->id }}" {{ isset($user) && $designation->id == old('designation_id', $user->designation_id) ? 'selected' : '' }}>{{ $designation->name }}</option>
                        @endforeach
                    </select>
                    <span class="field-error" id="designation_id-error" style="color:red"></span>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="required" for="password">Password</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" value="{{ $decryptedPassword }}" class="form-control" placeholder="Enter Password">
                            <i class="toggle-password fa fa-eye-slash" data-toggle="password"></i>
                        </div>
                        <span class="field-error" id="password-error" style="color:red"></span>
                    </div>
                </div>

                <div class="col-md-2 d-flex justify-content-center">
                    <div class="form-group">
                        <button type="button" class="btn btn-success btn-submit" style="font-size: smaller;padding:13px;" id="generatePasswordButton">Generate Password</button>
                    </div>
                </div>

            </div>

            <div class="row mt-5">
                <div class="col-md-6">
                    <label for="status">Role</label>
                    <select data-control="select2" data-allow-clear="true" data-hide-search="false" name="role_id" class="form-control" data-placeholder="Select Choose Role">
                        <option></option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ isset($user) && $role->id == old('role_id', $user->role_id ?? '') ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <span class="field-error" id="role_id-error" style="color:red"></span>
                </div>

                <div class="col-md-6">
                    <label for="status" class="required">Reporting Manager</label>
                    <select data-control="select2" id="kt_docs_select2_reporting_manager" name="reporting_manager[]" class="form-control" multiple="multiple" data-maximum-selection-length="5" data-placeholder="Choose Reporting Managers">
                        @php
                        $reportingManager = isset($user) ? $user->reporting_manager ?? [] : [];
                        if (!is_array($reportingManager)) {
                        $reportingManager = is_string($reportingManager) ? json_decode($reportingManager, true) : [$reportingManager];
                        }
                        $selectedManagers = old('reporting_manager', (array) $reportingManager);
                        $employees = isset($user) ? $employees->whereNotIn('id', $user->id) : $employees;
                        @endphp
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ in_array($employee->id, $selectedManagers) ? 'selected' : '' }} data-kt-select2-user="{{ isset($employee->profile_image) && !empty($employee->profile_image) ? url($employee->profile_image) : asset('images/avatar/blank.png') }}">
                            <span style="display: flex; align-items: center;">
                                <img src="{{ isset($employee->profile_image) && !empty($employee->profile_image) ? url($employee->profile_image) : asset('images/avatar/blank.png') }}"  alt="image" style="vertical-align: middle !important; width: 38px !important; height: 38px !important; border-radius: 50% !important;"/>
                                {{ $employee->first_name .' '. $employee->last_name . (isset($employee->designation) && !empty($employee->designation) ? '-'. $employee->designation : '') }}
                            </span>
                        </option>
                        @endforeach
                    </select>
                    <span class="field-error" id="reporting_manager-error" style="color:red"></span>
                </div>

                <div class="col-md-6" style="margin-top: 20px;">
                    <label for="status">WCR Date Extended</label> &nbsp;
                    <input type="checkbox" name="wcr_date_extended" value="1" {{ isset($user) && $user->wcr_date_extended == 1 ? 'checked' : '' }}/>
                </div>



            </div>


            <div class="row mt-10">
                <!-- <div class="col-md-4"></div> -->
                <div class="col-md-12">
                    <center>
                        <button type="button" class="btn btn-primary" id="dynamic-submit">Submit</button>
                        <button type="button" class="btn btn-primary" id="clear">Clear</button>
                    </center>
                </div>
                <!-- <div class="col-md-4"></div> -->
            </div>
            <br>
        </form>
        <!--end::Table container-->
        <div class="modal fade" id="cropAvatarmodal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Crop the image</h5>
                        <button type="button" class="btn-close" id="modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="img-container">
                            <img id="uploadedAvatar" style="max-height: 50%;max-width:50%;width:300px;height:300px" src="https://avatars0.githubusercontent.com/u/3456749">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="crop">Crop</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
@parent
<script>
    $(document).ready(function() {
        var optionFormat = function(item) {
            if (!item.id) {
                return item.text;
            }

            var span = document.createElement('span');
            var imgUrl = item.element.getAttribute('data-kt-select2-user');
            var template = '';

            template += '<img src="' + imgUrl + '" style="vertical-align: middle !important; width: 38px !important; height: 38px !important; border-radius: 50% !important;" alt="image"/>';
            template += item.text;

            span.innerHTML = template;

            return $(span);
        }


        $('#kt_docs_select2_reporting_manager').select2({
            minimumResultsForSearch: 0,
            allowClear: true,
            templateSelection: optionFormat,
            templateResult: optionFormat,
            dropdownAutoWidth: true,
            escapeMarkup: function(markup) {
                return markup;
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const branchSelect = $('#branchSelect');
        let isAutoSelecting = false;
        let previousSelection = branchSelect.val() || [];

        branchSelect.on('select2:select', function () {
            previousSelection = branchSelect.val() || [];
        });

        branchSelect.on('select2:select', function (e) {
            const lastSelectedValue = e.params.data.id;
             // Prevent auto-selection loop
            if (isAutoSelecting) return; 

             const selected = $(this).val();
             const getChildBranchesUrl = "{{ url('masters/branch/get-child-branches') }}";

             if (selected.includes('all') || previousSelection.includes('all')) return;

              const removed = previousSelection.filter(val => !selected.includes(val));
              previousSelection = selected;
              
            //   if (removed.length > 0 && selected[0] != 'all') {
            //         removed.forEach(removedId => {
            //         fetch(`${getChildBranchesUrl}/${removedId}`)
            //             .then(res => res.json())
            //             .then(children => {
            //             const current = new Set(branchSelect.val() || []);
            //                 children.forEach(child => {
            //                     const childIdStr = child.id.toString();
            //                     current.delete(childIdStr);
            //                     setTimeout(() => {
            //                         branchSelect.select2('close');
            //                         branchSelect.select2('open');
            //                     }, 0);
            //                 });
            //                 isAutoSelecting = true;
            //                 branchSelect.val(Array.from(current)).trigger('change');
            //                 isAutoSelecting = false;
            //             }).catch(err => {
            //                 isAutoSelecting = false;
            //                 console.error('Error (on unselect):', err);
            //             });
            //     });
            //   }

              if (selected.length > 0 && selected[0] != 'all' ) {
                fetch(`${getChildBranchesUrl}/${lastSelectedValue}`)
                    .then(res => res.json())
                    .then(children => {
                        const existing = new Set(branchSelect.val() || []);

                        children.forEach(branch => {
                            const branchIdStr = branch.id.toString();

                            if (branchSelect.find(`option[value="${branchIdStr}"]`).length === 0) {
                                const newOption = new Option(branch.name, branch.id, true, true); 
                                branchSelect.append(newOption);
                            } else {
                                setTimeout(() => {
                                    branchSelect.select2('close');
                                    branchSelect.select2('open');
                                }, 0);
                            }

                            existing.add(branchIdStr);
                        });
                        isAutoSelecting = true;
                        branchSelect.val(Array.from(existing)).trigger("change");
                        isAutoSelecting = false;
                    }).catch(err => {
                        isAutoSelecting = false;
                        console.error('Error:', err);
                    });
              }
        });

        branchSelect.on('select2:unselect', function (e) {
             // Prevent auto-selection loop
            if (isAutoSelecting) return; 

             const selected = $(this).val();
             const getChildBranchesUrl = "{{ url('masters/branch/get-child-branches') }}";

             if (selected.includes('all') || previousSelection.includes('all')) return;

              const removed = previousSelection.filter(val => !selected.includes(val));
              previousSelection = selected;
              
            //   if (removed.length > 0 && selected[0] != 'all') {
            //         removed.forEach(removedId => {
            //         fetch(`${getChildBranchesUrl}/${removedId}`)
            //             .then(res => res.json())
            //             .then(children => {
            //             const current = new Set(branchSelect.val() || []);
            //                 children.forEach(child => {
            //                     const childIdStr = child.id.toString();
            //                     current.delete(childIdStr);
            //                     setTimeout(() => {
            //                         branchSelect.select2('close');
            //                         branchSelect.select2('open');
            //                     }, 0);
            //                 });
            //                 isAutoSelecting = true;
            //                 branchSelect.val(Array.from(current)).trigger('change');
            //                 isAutoSelecting = false;
            //             }).catch(err => {
            //                 isAutoSelecting = false;
            //                 console.error('Error (on unselect):', err);
            //             });
            //     });
            //   }
        });

        document.getElementById('clear').addEventListener('click', function() {
            const form = document.getElementById('dynamic-form');
            form.reset();
            const selects = form.querySelectorAll('select');
            selects.forEach(select => {
                select.value = '';
                $(select).trigger('change');
            });
        });

        document.getElementById('mobile').addEventListener('input', function(e) {
            var input = e.target.value;
            e.target.value = input.replace(/[^0-9]/g, '');
        });

        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                let input = document.getElementById(this.getAttribute('data-toggle'));
                let icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        });

        // Generate Password Event Trigger
        const myElement = document.querySelector('#generatePasswordButton');
        if (myElement) {
            document.getElementById('generatePasswordButton').addEventListener('click', function() {
                generatePasswordButton.textContent = "Please wait...";
                setTimeout(function() {
                    const passwordField = document.getElementById('password');
                    // const confirmPasswordField = document.getElementById('confirm_password');

                    const generatedPassword = generateRandomPassword();
                    passwordField.value = generatedPassword;
                    // confirmPasswordField.value = generatedPassword;
                    generatePasswordButton.textContent = "Generate Password";
                }, 1000);
            });
        }


        $('#dynamic-submit').on('click', function(e) {
            saveUpdateEmployee(e);
        });

        // Password Generator 
        function generateRandomPassword(length = 6) {
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+<>?";
            let password = "";
            for (let i = 0, n = charset.length; i < length; ++i) {
                password += charset.charAt(Math.floor(Math.random() * n));
            }
            return password;
        }

        function saveUpdateEmployee(event) {
            event.preventDefault();
            $('#pageLoader').fadeIn();
            let formData = new FormData(document.getElementById('dynamic-form'));
            // If a cropped image is available, append it to the FormData
            if (croppedImageBlob) {
                formData.append('profile_image', croppedImageBlob, 'avatar.jpg');
                formData.append('avatar_remove', 0);
            }
            // Perform the AJAX request
            $.ajax({
                url: $('#dynamic-form').attr('action'), // Get the form action URL
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token if needed
                },
                success: function(response) {
                    $('#pageLoader').fadeOut();
                    // Handle success response
                    if (response) {
                        toastr.success(response.message)
                        window.location.href = '{{ route("employee.index") }}';
                    }

                },
                error: function(response) {
                    $('#pageLoader').fadeOut();
                    if (response.status === 422 && response.responseJSON.error) {
                        var errors = response.responseJSON.error;
                        $('#dynamic-form').find(".field-error").text('');
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]); // Display only the first error message for each field
                        });
                    } else {
                        toastr.error(response.responseJSON.error)
                    }
                }
            });
        }

        // Image Crop Script Start Here

        var avatar = document.getElementById('profile-img');
        var image = document.getElementById('uploadedAvatar');
        var input = document.getElementById('file-input');
        var cropBtn = document.getElementById('crop');

        var $modal = $('#cropAvatarmodal');
        var cropper;
        var croppedImageBlob;

        input.addEventListener('change', function(e) {
            var files = e.target.files;
            var done = function(url) {
                image.src = url;
                $modal.modal('show');
            };

            if (files && files.length > 0) {
                let file = files[0];
                var reader = new FileReader();
                reader.onload = function(e) {
                    done(reader.result);
                };
                reader.readAsDataURL(file);
            }
        });

        $modal.on('shown.bs.modal', function() {
            cropper = new Cropper(image, {
                viewMode: 1,
                autoCropArea: 1,
                background: false,
                movable: true,
                cropBoxMovable: true,
                cropBoxResizable: true
            });
        }).on('hidden.bs.modal', function() {
            cropper.destroy();
            cropper = null;
        });

        cropBtn.addEventListener('click', function() {
            $modal.modal('hide');

            if (cropper) {
                var canvas = cropper.getCroppedCanvas({
                    width: 160,
                    height: 160,
                });

                avatar.src = canvas.toDataURL();

                // Optionally, remove the background image from the wrapper
                document.querySelector('.image-input-wrapper').style.backgroundImage = 'none';

                canvas.toBlob(function(blob) {
                    croppedImageBlob = blob; // Store the cropped image blob
                });
            }
        });


        document.querySelector('[data-kt-image-input-action="cancel"]').addEventListener('click', function() {
            // avatar.src = '{{ isset($user->profile_image) && !empty($user->profile_image) ? url($user->profile_image) : asset('images/avatar/blank.png')}}';
            avatar.src = '{{ isset($user->profile_image) && !empty($user->profile_image) ? url($user->profile_image) : asset('images / avatar / blank.png ') }}';
            input.value = ''; // Clear file input
            document.querySelector('input[name="avatar_remove"]').value = 0; // Reset avatar_remove to 0
        });

        document.querySelector('[data-kt-image-input-action="remove"]').addEventListener('click', function() {
            avatar.src = '{{ asset('images / avatar / blank.png ')}}';
            input.value = ''; // Clear file input
            document.querySelector('input[name="avatar_remove"]').value = 1; // Set avatar_remove to 1
        });

        // Image Crop Script End Here
    });
   $(document).ready(function () {
    let $select = $('#branchSelect');

    $select.select2({
        closeOnSelect: false,
        placeholder: "Choose or Search Branches"
    });

    // When 'Select All' is selected
    $select.on('select2:select', function (e) {
        var selected = $select.val() || [];
        var allRealOptions = $select.find('option').filter(function () {
                return $(this).val() !== 'all';
            });
        if (e.params.data.id === 'all') {
            let allValues = [];

            // Include 'all' + all other branch IDs
            $select.find('option').each(function () {
                allValues.push($(this).val());
            });

            $select.val(allValues).trigger('change.select2');

            setTimeout(function () {
                $select.select2('close');
                $select.select2('open');
            }, 0);
        } else {
            // Auto-select 'all' when user selects all branches manually
            const selected = $select.val() || [];
            const selectedRealValues = selected.filter(id => id !== 'all');

            if (selectedRealValues.length === allRealOptions.length) {
                selectedRealValues.push('all');
                $select.val(selectedRealValues).trigger('change.select2');

                setTimeout(() => {
                    $select.select2('close');
                    $select.select2('open');
                }, 0);
            }

            
        }
    });
    $select.on('select2:unselect', function (e) {
        if (e.params.data.id != 'all') {
            const selected = $select.val() || [];
            const selectedRealValues = selected.filter(id => id !== 'all');
            const allRealOptions = $select.find('option').filter(function () {
                            return $(this).val() !== 'all';
                        });
            
            if (selectedRealValues.length !== allRealOptions.length) {
                $select.val(selectedRealValues).trigger('change.select2');
                setTimeout(() => {
                    $select.select2('close');
                    $select.select2('open');
                }, 0);
            }
        }
    });

    // When 'Select All' is unselected
    $select.on('select2:unselect', function (e) {
        if (e.params.data.id === 'all') {
            $select.val(null).trigger('change.select2');
            $select.select2('close');
        } else {
            // Deselect 'all' if any individual branch is removed
            const selected = $select.val() || [];
            const updated = selected.filter(id => id !== 'all');
            setTimeout(() => {
                $select.val(updated).trigger('change.select2');
            }, 0);
            // $select.val(updated).trigger('change.select2');
        }
    });
   });

</script>

@endsection