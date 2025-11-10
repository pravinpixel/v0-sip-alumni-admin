<style>
    .padBt_10 {
        padding-bottom: 10px;
    }

    .date-input {
        z-index: 999;
    }

    .clearable-input {
        position: relative;
    }

    .clearable-input .clear-button {
        position: absolute;
        right: 33px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        font-size: 1.5rem;
        line-height: 1;
        cursor: pointer;
        color: #aaa;
        padding: 0;
        margin: 0;
        display: none;
    }

    .clearable-input .clear-button.show {
            display: block;
    }

    .clearable-input .clear-button:hover {
        color: #000;
    }


</style>
<form id="filter_form">
<div class="card-header border-0 pt-6" id="filter_sub" style="display: none">
<div class="card-title">
    <div class="row row-gap-10px">
            <div class="w-200px padBt_10">
                <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Select Employee" name="employee">
                    <option value="">Select Employee</option>
                    @php
                        $uniqueEmployees = $employees->map(function ($employee) {
                            return strtolower($employee->first_name . ' ' . $employee->last_name);
                        })->unique();
                    @endphp
                    @foreach($uniqueEmployees as $employeeName)
                            <option value="{{ $employeeName }}">{{ ucfirst($employeeName) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-200px">
                <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Select Designation" name="designation">
                    <option value="">Select Designation</option>
                    @php
                        $uniqueDesignations = $designations->pluck('name')->unique(function ($designation) {
                            return strtolower($designation); 
                        });
                    @endphp
                    @foreach($uniqueDesignations as $designation)
                        <option value="{{ $designation }}">{{ $designation }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-200px">
                <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Select Department" name="department">
                    <option value="">Select Department</option>
                    @php
                        $uniqueDepartments = $departments->pluck('name')->unique(function ($departments) {
                            return strtolower($departments); 
                        });
                    @endphp
                    @foreach($uniqueDepartments as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-200px">
                    <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Select Branch" name="branch">
                        <option value="">Select Branch</option>
                        @php
                            $uniqueBranches = $branches->pluck('name')->unique(function ($branches) {
                                return strtolower($branches); 
                            });
                        @endphp
                        @foreach($uniqueBranches as $branch)
                            <option value="{{ $branch }}">{{ $branch }}</option>
                        @endforeach
                    </select>
            </div>
            <div class="w-200px padBt_10">
                    <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Select Location" name="location">
                        <option value="">Select Location</option>
                        @php
                            $uniqueLocations = $locations->pluck('name')->unique(function ($locations) {
                                return strtolower($locations); 
                            });
                        @endphp
                        @foreach($uniqueLocations as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                        @endforeach
                    </select>
            </div>
            <div class="w-200px">
                    <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Assigned By" name="assigned_by">
                        <option value="">Select Assigned By</option>
                        $uniqueEmployees = $employees->map(function ($employee) {
                                                            return strtolower($employee->first_name . ' ' . $employee->last_name);
                                                        })->unique();
                        @foreach($uniqueEmployees as $employee)
                            <option value="{{ $employee }}">{{ ucwords($employee) }}</option>
                        @endforeach
                    </select>
            </div>
            <div class="w-200px">
                    <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Assigned To" name="assigned_to">
                        <option value="">Select Assigned To</option>
                        @php
                            $uniqueEmployees = $employees->map(function ($employee) {
                                return strtolower($employee->first_name . ' ' . $employee->last_name);
                            })->unique();
                        @endphp
                        @foreach($uniqueEmployees as $employee)
                            <option value="{{ $employee }}">{{ ucwords($employee) }}</option>
                        @endforeach
                    </select>
            </div>
            <div class="w-200px">
                    <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Priority" name="priority">
                        <option value="">Select Priority</option>
                        <option value="5">High Priority</option>
                        <option value="6">Low Priority</option>
                        <option value="7">Medium Priority</option>
                    </select>
            </div>
            <div class="w-200px padBt_10">
                <div class="clearable-input">
                    <input type="date" class="form-control date-input" id="start_date" data-placeholder="Select Start Date" name="start_date">
                    <div class="input-group-append">
                        <button class="clear-button" type="button" id="clear-start-date">
                            &times;
                        </button>
                    </div>
                </div>
            </div>        
            <div class="w-200px">
                <div class="clearable-input">
                    <input type="date" class="form-control date-input" id="end_date" data-placeholder="Select End Date" name="end_date">
                    <div class="input-group-append">
                        <button class="clear-button" type="button" id="clear-end-date">
                            &times;
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-200px">
                <!--begin::Select2-->
                <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-order-filter="status">
                                        <option value="all">All</option>
                                        <option value="1">Completed</option>
                                        <option value="2">Inprogress</option>
                                        <option value="3">Cancelled</option>
                </select>
                <!--end::Select2-->
            </div>
            <!-- Clear Filters Button -->
            <div class="w-200px">
                <button type="button" id="clear-filters" name="filter_clear_button" class="btn btn-primary">Clear</button>
            </div>

        </div>
</div>
</div>
</form>
