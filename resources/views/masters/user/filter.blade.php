<!-- Filter Section (Hidden by default) -->
<div id="filterSection" style="display: none;">
    <div class="filter-wrapper">
        <div class="filter-row">
            
            <div class="filter-dropdown">
                <button type="button" class="filter-dropdown-btn" data-filter="status">
                    <span>Status</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="filter-count" data-filter="status">0</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </button>
                <div class="filter-dropdown-menu" data-filter="status">
                    <label class="filter-option" data-value="1">
                        <input type="checkbox">
                        <span>Active</span>
                    </label>
                    <label class="filter-option" data-value="0">
                        <input type="checkbox">
                        <span>Inactive</span>
                    </label>
                </div>
            </div>

            <!-- Role Filter Dropdown -->
            <div class="filter-dropdown">
                <button type="button" class="filter-dropdown-btn" data-filter="role">
                    <span>Role</span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="filter-count" data-filter="role">0</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </button>
                <div class="filter-dropdown-menu" data-filter="role">
                    @foreach ($roles as $role)
                    <label class="filter-option" data-value="{{ $role->id }}">
                        <input type="checkbox">
                        <span>{{ $role->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Clear Filters Button -->
            <button type="button" id="clearFiltersBtn" style="display: none; background: #ba0028; color: white; border: none; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-size: 14px; font-weight: 500;">
                Clear All Filters
            </button>
        </div>
    </div>
</div>