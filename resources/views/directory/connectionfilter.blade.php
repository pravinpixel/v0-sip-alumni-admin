<div id="filterSection" class="border border-gray-300 rounded mb-6" style="display: none; padding: 20px; background-color: #f9fafb;">
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <div class="multi-select-container" data-filter="batch" style="min-width: 180px;">
            <div class="multi-select-display" style="padding: 8px 12px; min-height: 38px;">
                <span class="placeholder" style="font-size: 13px;">Batch</span>
                <i class="fas fa-chevron-down" style="color: #9ca3af; font-size: 10px;"></i>
            </div>
            <div class="multi-select-dropdown"></div>
        </div>
        <div class="multi-select-container" data-filter="location" style="min-width: 180px;">
            <div class="multi-select-display" style="padding: 8px 12px; min-height: 38px;">
                <span class="placeholder" style="font-size: 13px;">Center Location</span>
                <i class="fas fa-chevron-down" style="color: #9ca3af; font-size: 10px;"></i>
            </div>
            <div class="multi-select-dropdown"></div>
        </div>
    </div>
</div>

<!-- Selected Filters Display -->
<div id="selectedFiltersDisplay" style="display: none; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <span style="font-weight: 600; font-size: 14px; color: #6b7280;">Active Filters:</span>
        <div class="selected-tags" style="display: flex; flex-wrap: wrap; gap: 8px; flex: 1;"></div>
        <button id="clearAllFiltersBtn" onclick="clearAllFilters()"
            style="background: transparent; border: none; color: #ba0028; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: underline; white-space: nowrap;"
            onmouseover="this.style.color='#9a0020'" onmouseout="this.style.color='#ba0028'">
            Clear All Filters
        </button>
    </div>
</div>