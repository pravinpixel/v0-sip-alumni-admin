<style>
    .padBt_10 {
        padding-bottom: 10px;
    }
</style>
<form id="filter_form">
    <div class="card-header border-0 pt-6" id="filter_sub" style="display: none">
        <div class="card-title">
            <div class="row row-gap-10px">

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
                <div class="w-200px">
                    <select class="form-select" data-allow-clear="true" data-control="select2" data-placeholder="Select Reporting Manager" name="reporting_manager[]" multiple>
                        @foreach($reportingManagers as $manager)
                        <option value="{{ $manager->id }}">{{ $manager->first_name }} {{ $manager->last_name }}</option>
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
                    <!--begin::Select2-->
                    <select class="form-select form-select-solid" name="status" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-order-filter="status">
                        <option value="all">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
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