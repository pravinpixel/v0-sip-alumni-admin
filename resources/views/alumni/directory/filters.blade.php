{{-- Filter Section --}}
    <div id="filterSection">
        <div class="filter-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div>
                <label class="filter-label">
                    Batch Year
                </label>
                <div class="multi-select-container" data-filter="batch">
                    <div class="multi-select-display">
                        <span class="placeholder">Select batch years</span>
                        <i class="fas fa-chevron-down "></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
            <div>
                <label class="filter-label">
                    Location
                </label>
                <div class="multi-select-container" data-filter="location">
                    <div class="multi-select-display">
                        <span class="placeholder">Select locations</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
            <div>
                <label class="filter-label">
                    Status
                </label>
                <div class="multi-select-container" data-filter="status">
                    <div class="multi-select-display">
                        <span class="placeholder">Select status</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Selected Filters Display (Always visible when filters are selected) --}}
    <div id="selectedFiltersDisplay">
        <div class="selected-tags"></div>
    </div>
        