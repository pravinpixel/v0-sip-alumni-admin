<!-- Filter Section (Hidden by default) -->
<div id="filter_section" style="display: none; margin-bottom: 1.5rem;">
    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <!-- Status Filter Dropdown -->
            <div class="filter-dropdown" style="position: relative;">
                <button type="button" class="filter-dropdown-btn" data-filter="status"
                    style="background: white; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; min-width: 180px; justify-content: space-between;"
                    onmouseover="this.style.background='#ba0028'; this.style.color='#fff';" onmouseout="this.style.background='white'; this.style.color='#000000ff';">
                    <span>Status</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span class="filter-count" data-filter="status" style="background: #ba0028; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; font-weight: 600; align-items: center; justify-content: center; display: none;">0</span>
                        <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                    </div>
                </button>
                <div class="filter-dropdown-menu" data-filter="status" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 4px; background: white; border: 1px solid #d1d5db; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 1000; min-width: 200px; max-height: 300px; overflow-y: auto;">
                    <div class="filter-option" data-value="all" style="padding: 12px 16px; cursor: pointer; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        <input type="checkbox" style="margin: 0; accent-color: #ba0028;">
                        <span>All Status</span>
                    </div>
                    <div class="filter-option" data-value="1" style="padding: 12px 16px; cursor: pointer; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        <input type="checkbox" style="margin: 0; accent-color: #ba0028;">
                        <span>Active</span>
                    </div>
                    <div class="filter-option" data-value="0" style="padding: 12px 16px; cursor: pointer; font-size: 14px; color: #374151; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        <input type="checkbox" style="margin: 0; accent-color: #ba0028;">
                        <span>Inactive</span>
                    </div>
                </div>
            </div>

            <!-- Role Filter Dropdown -->
            <div class="filter-dropdown" style="position: relative;">
                <button type="button" class="filter-dropdown-btn" data-filter="role"
                    style="background: white; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 16px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; min-width: 180px; justify-content: space-between;"
                    onmouseover="this.style.background='#ba0028'; this.style.color='#fff';" onmouseout="this.style.background='white'; this.style.color='#000000ff';">
                    <span>Role</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span class="filter-count" data-filter="role" style="background: #ba0028; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; font-weight: 600; align-items: center; justify-content: center; display: none;">0</span>
                        <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
                    </div>
                </button>
                <div class="filter-dropdown-menu" data-filter="role" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 4px; background: white; border: 1px solid #d1d5db; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 1000; min-width: 200px; max-height: 300px; overflow-y: auto;">
                    <div class="filter-option" data-value="all" style="padding: 12px 16px; cursor: pointer; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        <input type="checkbox" style="margin: 0; accent-color: #ba0028;">
                        <span>All Roles</span>
                    </div>
                    @foreach ($roles as $role)
                    <div class="filter-option" data-value="{{ $role->id }}" style="padding: 12px 16px; cursor: pointer; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                        <input type="checkbox" style="margin: 0; accent-color: #ba0028;">
                        <span>{{ $role->name }}</span>
                    </div>
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