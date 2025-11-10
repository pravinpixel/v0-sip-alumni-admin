@extends('layouts.index')

@section('title', 'Task Master | Usha Fire')

@section('style')
@parent
<style>
    .loading-cursor {
        cursor: wait !important;
    }

    .del {
        margin-top: 10% !important;
    }

    .let {
        font-size: 120% !important;

    }
</style>
<style>
    /* Add this to your CSS stylesheet */
    .mtb {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .active_btn {
        display: flex;
        align-items: center;
    }

    .fixed-label {
        width: 150px;
        display: inline-block;
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
@endsection
@section('content')
<div id="pageLoader" class="page-loader">
    <div class="loader"></div>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    @if(isset($role))
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Edit Role</h1>
                    @else
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Add Role</h1>
                    @endif

                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{url('dashboard')}}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>

                        <li class="breadcrumb-item text-muted">
                            <a href="{{url('/role')}}" class="text-muted text-hover-primary">Roles</a>
                        </li>
                    </ul>
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
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <form id="dynamic-form" method="post" action="{{ url('/role/save') }}">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{$role->id??''}}">
                            <input type="hidden" name="title" id="title" value="">
                            <div class="page-title d-flex justify-content-center flex-wrap me-3 card_arrow">
                                <a href="{{url('role')}}" style="cursor: pointer;" class="text-muted text-hover-primary">
                                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                </a>
                                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Basic Information</h1>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-auto">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">Role Name:</label>
                                        <input type="text" id="role_name" name="role_name" value="{{$role->name??''}}" class="form-control" placeholder="Enter Role Name">
                                        <span class="field-error" id="role_name-error" style="color:red"></span>
                                    </div>
                                </div>
                                <div class="col-auto active_btn">
                                    <!-- Checkbox for creating a new user -->
                                    @if(!isset($role) || is_null($role->id))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status_create" name="status" value="1" checked>
                                        <label class="form-check-label" for="status_create">Active</label>
                                    </div>
                                    @endif

                                    <!-- Checkbox for updating an existing user -->
                                    @if(isset($role) && !is_null($role->id))
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status_update" name="status" value="1" {{ $role->status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_update">Active</label>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="dashboard-permissions">
                                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Admin Dashboard Permissions</h1>
                                    </div>
                                    <hr>

                                    <div class="row all_task mtb backend-section">
                                        <div class="col-auto d-flex align-items-center">
                                            <label class="form-label fixed-label">
                                                <span>Task:</span>
                                            </label>
                                        </div>

                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend-all"
                                                    type="checkbox"
                                                    id="viewTasks"
                                                    name="permissions[]"
                                                    value="task.view"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('task.view') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="viewTasks">View</label>
                                            </div>
                                        </div>

                                        <span class="field-error" style="color:red" id="status-error"></span>
                                    </div>

                                    <div class="row all_employee mtb backend-section">
                                        <div class="col-auto d-flex align-items-center">
                                            <label class="form-label fixed-label">
                                                <span>Employee:</span>
                                            </label>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input module_employee backend-all" type="checkbox"
                                                    value="module_employee"
                                                    id="module_employee"
                                                    onclick="groupcheck('module_employee','all_employee','module_employee')"
                                                    name="selectAll"
                                                    @if(isset($role) && access()->checkRole($role->id,'employee') == 4) checked @endif
                                                >
                                                <label class="form-check-label">All</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="viewemployees"
                                                    name="permissions[]"
                                                    value="employee.view"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('employee.view') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="viewemployees">View</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="createemployee"
                                                    name="permissions[]"
                                                    value="employee.create"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('employee.create') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="createemployee">Create</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="editemployees"
                                                    name="permissions[]"
                                                    value="employee.edit"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('employee.edit') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="editemployees">Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="deleteemployees"
                                                    name="permissions[]"
                                                    value="employee.delete"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('employee.delete') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="deleteemployees">Delete</label>
                                            </div>
                                        </div>
                                        <span class="field-error" style="color:red" id="status-error"></span>
                                    </div>

                                    <div class="row all_organization mtb backend-section">
                                        <div class="col-auto d-flex align-items-center">
                                            <label class="form-label fixed-label">
                                                <span>organization:</span>
                                            </label>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input module_organization backend-all" type="checkbox"
                                                    value="module_organization"
                                                    id="module_organization"
                                                    onclick="groupcheck('module_organization','all_organization','module_organization')"
                                                    name="selectAll"
                                                    @if(isset($role) && access()->checkRole($role->id,'organization') == 4) checked @endif
                                                >
                                                <label class="form-check-label">All</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="vieworganization"
                                                    name="permissions[]"
                                                    value="organization.view"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('organization.view') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="vieworganization">View</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="createorganization"
                                                    name="permissions[]"
                                                    value="organization.create"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('organization.create') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="createorganization">Create</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="editorganization"
                                                    name="permissions[]"
                                                    value="organization.edit"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('organization.edit') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="editorganization">Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="deleteorganization"
                                                    name="permissions[]"
                                                    value="organization.delete"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('organization.delete') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="deleteorganization">Delete</label>
                                            </div>
                                        </div>
                                        <span class="field-error" style="color:red" id="status-error"></span>
                                    </div>

                                    <div class="row all_master mtb backend-section">
                                        <div class="col-auto d-flex align-items-center">
                                            <label class="form-label fixed-label">
                                                <span>Masters:</span>
                                            </label>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input module_master backend-all" type="checkbox"
                                                    value="module_master"
                                                    id="module_master"
                                                    onclick="groupcheck('module_master','all_master','module_master')"
                                                    name="selectAll"
                                                    @if(isset($role) && access()->checkRole($role->id,'master') == 4) checked @endif
                                                >
                                                <label class="form-check-label">All</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="viewMasters"
                                                    name="permissions[]"
                                                    value="master.view"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('master.view') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="viewMasters">View</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="createComments"
                                                    name="permissions[]"
                                                    value="master.create"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('master.create') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="createMasters">Create</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="editMasters"
                                                    name="permissions[]"
                                                    value="master.edit"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('master.edit') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="editMasters">Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="deleteMasters"
                                                    name="permissions[]"
                                                    value="master.delete"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('master.delete') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="deleteMasters">Delete</label>
                                            </div>
                                        </div>
                                        <span class="field-error" style="color:red" id="status-error"></span>
                                    </div>

                                    <div class="row all_user mtb backend-section">
                                        <div class="col-auto d-flex align-items-center">
                                            <label class="form-label fixed-label">
                                                <span>Users:</span>
                                            </label>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input module_user backend-all" type="checkbox"
                                                    value="module_user"
                                                    id="module_user"
                                                    onclick="groupcheck('module_user','all_user','module_user')"
                                                    name="selectAll"
                                                    @if(isset($role) && access()->checkRole($role->id,'user') == 4) checked @endif
                                                >
                                                <label class="form-check-label">All</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="viewUsers"
                                                    name="permissions[]"
                                                    value="user.view"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('user.view') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="viewUsers">View</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="createUsers"
                                                    name="permissions[]"
                                                    value="user.create"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('user.create') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="createUsers">Create</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="editUsers"
                                                    name="permissions[]"
                                                    value="user.edit"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('user.edit') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="editUsers">Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="deleteUsers"
                                                    name="permissions[]"
                                                    value="user.delete"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('user.delete') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="deleteUsers">Delete</label>
                                            </div>
                                        </div>
                                        <span class="field-error" style="color:red" id="status-error"></span>
                                    </div>

                                    <div class="row all_role mtb backend-section">
                                        <div class="col-auto d-flex align-items-center">
                                            <label class="form-label fixed-label">
                                                <span>Roles:</span>
                                            </label>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input module_role backend-all" type="checkbox"
                                                    value="module_role"
                                                    id="module_role"
                                                    onclick="groupcheck('module_role','all_role','module_role')"
                                                    name="selectAll"
                                                    @if(isset($role) && access()->checkRole($role->id,'role') == 4) checked @endif
                                                >
                                                <label class="form-check-label">All</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="viewRoles"
                                                    name="permissions[]"
                                                    value="role.view"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('role.view') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="viewRoles">View</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="createRoles"
                                                    name="permissions[]"
                                                    value="role.create"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('role.create') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="createRoles">Create</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="editRoles"
                                                    name="permissions[]"
                                                    value="role.edit"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('role.edit') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="editRoles">Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend"
                                                    type="checkbox"
                                                    id="deleteRoles"
                                                    name="permissions[]"
                                                    value="role.delete"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('role.delete') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="deleteRoles">Delete</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mtb backend-section">
                                        <div class="col-auto d-flex align-items-center">
                                            <label class="form-label fixed-label">
                                                <span>Setting:</span>
                                            </label>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend-all"
                                                    type="checkbox"
                                                    id="viewSetting"
                                                    name="permissions[]"
                                                    value="setting.view"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('setting.view') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="viewRoles">View</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mtb backend-section">
                                        <div class="col-auto d-flex align-items-center">
                                            <label class="form-label fixed-label">
                                                <span>Bulk Upload:</span>
                                            </label>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input backend-all"
                                                    type="checkbox"
                                                    id="viewBulkUpload"
                                                    name="permissions[]"
                                                    value="bulk_upload.view"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('bulk_upload.view') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="viewRoles">View</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="frontend-permissions">
                                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Frontend Permissions</h1>
                                    </div>
                                    <hr>
                                    <div class="row all_i_alert_employee mtb frontend-section">
                                        <div class="col-auto d-flex align-items-center">
                                            <label class="form-label fixed-label">
                                                <span>I-Alert Employee:</span>
                                            </label>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input module_i_alert_employee frontend-all" type="checkbox"
                                                    value="module_i_alert_employee"
                                                    id="module_i_alert_employee"
                                                    onclick="groupcheck('module_i_alert_employee','all_i_alert_employee','module_i_alert_employee')"
                                                    name="selectAll"
                                                    @if(isset($role) && access()->checkRole($role->id,'i_alert_employee') == 5) checked @endif
                                                >
                                                <label class="form-check-label">All</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input frontend"
                                                    type="checkbox"
                                                    id="viewi_alert_employees"
                                                    name="permissions[]"
                                                    value="i_alert_employee.view"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('i_alert_employee.view') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="viewi_alert_employees">View</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input frontend"
                                                    type="checkbox"
                                                    id="createi_alert_employee"
                                                    name="permissions[]"
                                                    value="i_alert_employee.create"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('i_alert_employee.create') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="createi_alert_employee">Create Task</label>
                                            </div>
                                        </div>
                                        <!-- <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input frontend"
                                                    type="checkbox"
                                                    id="commenti_alert_employee"
                                                    name="permissions[]"
                                                    value="i_alert_employee.comment"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('i_alert_employee.comment') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="commenti_alert_employee">Comment Task</label>
                                            </div>
                                        </div> -->
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input frontend"
                                                    type="checkbox"
                                                    id="editi_alert_employees"
                                                    name="permissions[]"
                                                    value="i_alert_employee.edit"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('i_alert_employee.edit') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="editi_alert_employees">Edit</label>
                                            </div>
                                        </div>
                                        <div class="col-auto d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input frontend"
                                                    type="checkbox"
                                                    id="attachmenti_alert_employees"
                                                    name="permissions[]"
                                                    value="i_alert_employee.attachment"
                                                    @if(isset($role)) {{ $role->hasPermissionTo('i_alert_employee.attachment') ? 'checked' : '' }} @endif>
                                                <label class="form-check-label" for="attachmenti_alert_employees">Upload Document</label>
                                            </div>
                                        </div>
                                        <span class="field-error" style="color:red" id="status-error"></span>
                                    </div>

                                </div>
                            </div>
                            <div class="mb-3 text-center">
                                <button type="button" class="btn btn-success btn-submit" id="dynamic-submit">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!--end::Card body-->
                </div>
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
</div>



@endsection

@section('script')
@parent
<script>
    // Group Check Functionality
    function groupcheck(module_id, module_class, module_value) {
        $("#" + module_id).on("change", function(e) {
            document.querySelectorAll("." + module_class + " input[type='checkbox']").forEach(function(checkbox) {
                if ($('input[type="checkbox"][value="' + module_value + '"]').prop('checked')) {
                    if (checkbox.value != "stock-overview.create") {
                        $('input[type="checkbox"][value="' + checkbox.value + '"]').prop('checked', true);
                    }
                } else {
                    if (checkbox.value != "stock-overview.create") {
                        $('input[type="checkbox"][value="' + checkbox.value + '"]').prop('checked', false);
                    }
                }
            });
        });
    }


    $(".all_master input[type='checkbox']").on("click", function() {
        var parent = $('input[type="checkbox"][value="module_master"]');
        if ($(this).val() != 'module_master') {
            var checkboxes = document.querySelectorAll(".all_master input[type='checkbox']:not(.module_master)");
            var tolatcheckCount = checkboxes.length;
            var checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            if (tolatcheckCount != checkedCount) {
                parent.prop('checked', false);
            }
            if (tolatcheckCount == checkedCount) {
                parent.prop('checked', true);
            }
        }
    });
    $(".all_user input[type='checkbox']").on("click", function() {
        var parent = $('input[type="checkbox"][value="module_user"]');
        if ($(this).val() != 'module_user') {
            var checkboxes = document.querySelectorAll(".all_user input[type='checkbox']:not(.module_user)");
            var tolatcheckCount = checkboxes.length;
            var checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            if (tolatcheckCount != checkedCount) {
                parent.prop('checked', false);
            }
            if (tolatcheckCount == checkedCount) {
                parent.prop('checked', true);
            }
        }
    });
    $(".all_role input[type='checkbox']").on("click", function() {

        var parent = $('input[type="checkbox"][value="module_role"]');
        if ($(this).val() != 'module_role') {
            var checkboxes = document.querySelectorAll(".all_role input[type='checkbox']:not(.module_role)");
            var tolatcheckCount = checkboxes.length;
            var checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            if (tolatcheckCount != checkedCount) {
                parent.prop('checked', false);
            }
            if (tolatcheckCount == checkedCount) {
                parent.prop('checked', true);
            }
        }
    });
    $(".all_task input[type='checkbox']").on("click", function() {

        var parent = $('input[type="checkbox"][value="module_task"]');
        if ($(this).val() != 'module_task') {
            var checkboxes = document.querySelectorAll(".all_task input[type='checkbox']:not(.module_task");
            var tolatcheckCount = checkboxes.length;
            var checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            if (tolatcheckCount != checkedCount) {
                parent.prop('checked', false);
            }
            if (tolatcheckCount == checkedCount) {
                parent.prop('checked', true);
            }
        }
    });
    $(".all_employee input[type='checkbox']").on("click", function() {
        var parent = $('input[type="checkbox"][value="module_employee"]');
        if ($(this).val() != 'module_employee') {
            var checkboxes = document.querySelectorAll(".all_employee input[type='checkbox']:not(.module_employee)");
            var tolatcheckCount = checkboxes.length;
            var checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            if (tolatcheckCount != checkedCount) {
                parent.prop('checked', false);
            }
            if (tolatcheckCount == checkedCount) {
                parent.prop('checked', true);
            }
        }
    });
    $(".all_i_alert_employee input[type='checkbox']").on("click", function() {
        var parent = $('input[type="checkbox"][value="module_i_alert_employee"]');
        if ($(this).val() != 'module_i_alert_employee') {
            var checkboxes = document.querySelectorAll(".all_i_alert_employee input[type='checkbox']:not(.module_i_alert_employee)");
            var tolatcheckCount = checkboxes.length;
            var checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            if (tolatcheckCount != checkedCount) {

                parent.prop('checked', false);
            }
            if (tolatcheckCount == checkedCount) {

                parent.prop('checked', true);
            }
        }
    });


    $(".all_ input[type='checkbox']").on("click", function() {
        var parent = $('input[type="checkbox"][value="module_location"]');
        if ($(this).val() != 'module_location') {
            var checkboxes = document.querySelectorAll(".all_location input[type='checkbox']:not(.module_location)");
            var tolatcheckCount = checkboxes.length;
            var checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            if (tolatcheckCount != checkedCount) {
                parent.prop('checked', false);
            }
            if (tolatcheckCount == checkedCount) {
                parent.prop('checked', true);
            }
        }
    });

    $(document).ready(function() {
        toggleSections();
        $('#dynamic-submit').on('click', function(e) {
            if ($("input[type='checkbox']:checked").length <= 1) {
                e.preventDefault();
                toastr.error('Please check any one permission before submitting.');
                return false;
            }
            saveUpdateRole(e); // Call the function to handle the AJAX request
        });
    });

    function toggleSections() {
        let backendChecked = $('.backend:checked').length > 0 || $('.backend-all:checked').length > 0
        let frontendChecked = $('.frontend:checked').length > 0 || $('.frontend-all:checked').length > 0

        $('.frontend-section input').prop('disabled', backendChecked);
        $('.backend-section input').prop('disabled', frontendChecked);

    }

    $('.backend-all').change(function() {
        $(this).closest('.backend-section').find('.backend').prop('checked', this.checked);
        toggleSections();
    })

    $('.backend').change(function() {
        let section = $(this).closest('.backend-section');
        let allChecked = section.find('.backend:checked').length === section.find('.backend').length;
        section.find('.backend-all').prop('checked', allChecked);
        toggleSections();
    })

    $('.frontend-all').change(function() {
        $(this).closest('.frontend-section').find('.frontend').prop('checked', this.checked);
        toggleSections();
    })

    $(document).ready(function() {
        let section = $('.frontend').closest('.frontend-section');
        let allChecked = section.find('.frontend').length === section.find('.frontend:checked').length;
        section.find('.frontend-all').prop('checked', allChecked);
        toggleSections();
    });

    function saveUpdateRole(event) {
        event.preventDefault(); // Prevent the default form submission
        $('#pageLoader').fadeIn();
        // Serialize the form data
        let allValues = $('#dynamic-form').serialize();
        // Perform the AJAX request
        $.ajax({
            url: $('#dynamic-form').attr('action'), // Get the form action URL
            type: 'POST',
            data: allValues,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token if needed
            },
            success: function(response) {
                $('#pageLoader').fadeOut();
                // Handle success response
                if (response.success) {
                    toastr.success(response.message);
                    window.location.href = '{{ route("role.index") }}';
                } else {
                    toastr.success(response.error);
                }
            },
            error: function(response) {
                $('#pageLoader').fadeOut();
                if (response.status === 422 && response.responseJSON.error) {
                    console.log("-eer", response);
                    var errors = response.responseJSON.error;
                    $('#ajax-form').find(".field-error").text('');
                    $.each(errors, function(key, value) {
                        $('#' + key + '-error').text(value[0]); // Display only the first error message for each field
                    });
                } else {
                    toastr.error(response.responseJSON.error)
                }
            }
        });
    }
</script>

<script>
    $(document).ready(function() {
        let viewCheckbox = document.getElementById("viewi_alert_employees");
        let allCheckbox = document.getElementById("module_i_alert_employee");
        let createCheckbox = document.getElementById("createi_alert_employee");
        let editCheckbox = document.getElementById("editi_alert_employees");
        let attachmentCheckbox = document.getElementById("attachmenti_alert_employees");
        createCheckbox.disabled = true;
        editCheckbox.disabled = true;
        attachmentCheckbox.disabled = true;

        function toggleCreateCheckbox() {
            if (!viewCheckbox.checked) {
                createCheckbox.disabled = true;
                editCheckbox.disabled = true;
                attachmentCheckbox.disabled = true;
                createCheckbox.checked = false;
                editCheckbox.checked = false;
                attachmentCheckbox.checked = false;
            } else {
                createCheckbox.disabled = false;
                editCheckbox.disabled = false;
                attachmentCheckbox.disabled = false;
            }
        }

        toggleCreateCheckbox();

        viewCheckbox.addEventListener("change", toggleCreateCheckbox);
        allCheckbox.addEventListener("change", toggleCreateCheckbox);
    });
</script>


@endsection