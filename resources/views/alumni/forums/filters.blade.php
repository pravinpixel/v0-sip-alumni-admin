    {{-- Filter Section --}}
    <div id="filterSection">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div>
                <label class="filter-label">
                    Date Range
                </label>
                <div class="multi-select-container" data-filter="dateRange">
                    <div class="multi-select-display">
                        <span class="placeholder">Select date range</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
            <div>
                <label class="filter-label">
                    Sort By
                </label>
                <div class="multi-select-container" data-filter="sortBy">
                    <div class="multi-select-display">
                        <span class="placeholder">Select sorting</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
            <div>
                <label class="filter-label">
                    Batch Year
                </label>
                <div class="multi-select-container" data-filter="batch">
                    <div class="multi-select-display">
                        <span class="placeholder">Select batch years</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
            <div>
                <label class="filter-label">
                    Post Type
                </label>
                <div class="multi-select-container" data-filter="postType">
                    <div class="multi-select-display">
                        <span class="placeholder">Select post type</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
            <div>
                <label class="filter-label">
                    Label
                </label>
                <div class="multi-select-container" data-filter="label">
                    <div class="multi-select-display">
                        <span class="placeholder">Select labels</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="multi-select-dropdown"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Selected Filters Display --}}
    <div id="selectedFiltersDisplay">
        <div class="selected-tags"></div>
    </div>