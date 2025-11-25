<style>
    .padBt_10 {
        padding-bottom: 10px;
    }


</style>
<form id="filter_form">
<div class="card-header border-0 pt-6" id="filter_sub" style="display: none">
<div class="card-title">
    <div class="row row-gap-10px">

            <div class="w-200px padBt_10">
                    <select class="form-select" data-allow-clear="false" data-control="select2" data-placeholder="Select Location" id="location" name="location">
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
            <!-- Clear Filters Button -->
            <div class="w-200px">
                <button type="button" id="clear-filters" name="filter_clear_button" class="btn btn-primary">Clear</button>
            </div>

        </div>
</div>
</div>
</form>