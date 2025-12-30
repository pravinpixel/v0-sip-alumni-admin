<!-- Filter Section -->
<div id="filterSection" style="display: none;">
    <div class="filter-wrapper">
        <div class="filter-row">

            <!-- Year Filter -->
            <div class="filter-dropdown">
                <button type="button" class="filter-dropdown-btn" data-filter="years">
                    <span>Year of Completion</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="filter-count" data-filter="years">0</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </button>
                <div class="filter-dropdown-menu" data-filter="years"></div>
            </div>

            <!-- City Filter -->
            <div class="filter-dropdown">
                <button type="button" class="filter-dropdown-btn" data-filter="cities">
                    <span>City</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="filter-count" data-filter="cities">0</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </button>
                <div class="filter-dropdown-menu" data-filter="cities"></div>
            </div>

            <!-- Occupation Filter -->
            <div class="filter-dropdown">
                <button type="button" class="filter-dropdown-btn" data-filter="occupations">
                    <span>Occupation</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="filter-count" data-filter="occupations">0</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </button>
                <div class="filter-dropdown-menu" data-filter="occupations"></div>
            </div>

        </div>
    </div>
</div>

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

