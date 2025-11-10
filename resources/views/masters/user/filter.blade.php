<style>
    .padBt_10 {
        padding-bottom: 10px;
    }


</style>
<form id="filter_form">
<div class="card-header border-0 pt-6" id="filter_sub" style="display: none">
<div class="card-title">
    <div class="row row-gap-10px">

                                <div class="w-150px me-3">
                                    <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-order-filter="status">
                                        <option value="all">All</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <!--end::Select2-->
                                </div>

                                <div class="w-150px me-3">
                                    <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Roles" data-kt-ecommerce-order-filter="role">
                                        <option value="all">All</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
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