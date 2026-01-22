<div id="filterSection" style="display: none;">
    <div class="filter-wrapper">
        <div class="filter-row">
            <!-- Status Filter Dropdown -->
            <div class="filter-dropdown">
                <button type="button" class="filter-dropdown-btn" data-filter="statuses">
                    <span>Status</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="filter-count" data-filter="statuses">0</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </button>
                <div class="filter-dropdown-menu" data-filter="statuses">
                    <!-- Options will be loaded here -->
                </div>
            </div>

            <!-- Center Location Filter Dropdown -->
            <div class="filter-dropdown">
                <button type="button" class="filter-dropdown-btn" data-filter="centerLocations">
                    <span>Center Location</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="filter-count" data-filter="centerLocations">0</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </button>
                <div class="filter-dropdown-menu" data-filter="centerLocations">
                    <!-- Options will be loaded here -->
                </div>
            </div>

            <!-- From Date -->
            <div class="date-filter">
                <input type="date" id="filterFromDate" placeholder="From Date">
            </div>

            <!-- To Date -->
            <div class="date-filter">
                <input type="date" id="filterToDate" placeholder="To Date">
            </div>
        </div>
    </div>
</div>

<!-- Active Filters Display -->
<div id="activeFiltersContainer" style="display: none;">
    <div class="active-filters-wrapper">
        <span class="active-filters-label">Active Filters:</span>
        <div id="activeFiltersChips">
            <!-- Filter chips will be added here -->
        </div>
        <button id="clearAllFiltersBtn">
            Clear All Filters
        </button>
    </div>
</div>