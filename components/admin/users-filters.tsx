"use client"

import { Card } from "@/components/ui/card"
import { Label } from "@/components/ui/label"
import { Checkbox } from "@/components/ui/checkbox"

const filterOptions = {
  roles: ["Super Admin", "Content Moderator", "Viewer"],
  statuses: ["Active", "Inactive"],
}

interface UsersFiltersProps {
  selectedFilters: {
    roles: string[]
    statuses: string[]
  }
  onFiltersChange: (filters: { roles: string[]; statuses: string[] }) => void
}

export function UsersFilters({ selectedFilters, onFiltersChange }: UsersFiltersProps) {
  const handleFilterChange = (type: "roles" | "statuses", value: string, checked: boolean) => {
    onFiltersChange({
      ...selectedFilters,
      [type]: checked ? [...selectedFilters[type], value] : selectedFilters[type].filter((item) => item !== value),
    })
  }

  return (
    <Card className="p-6 bg-muted/30">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Role Filter */}
        <div className="space-y-3">
          <Label className="text-base font-bold">Role</Label>
          <div className="space-y-2">
            {filterOptions.roles.map((role) => (
              <div key={role} className="flex items-center space-x-2">
                <Checkbox
                  id={`role-${role}`}
                  checked={selectedFilters.roles.includes(role)}
                  onCheckedChange={(checked) => handleFilterChange("roles", role, checked as boolean)}
                />
                <Label htmlFor={`role-${role}`} className="text-sm font-medium cursor-pointer">
                  {role}
                </Label>
              </div>
            ))}
          </div>
        </div>

        {/* Status Filter */}
        <div className="space-y-3">
          <Label className="text-base font-bold">Status</Label>
          <div className="space-y-2">
            {filterOptions.statuses.map((status) => (
              <div key={status} className="flex items-center space-x-2">
                <Checkbox
                  id={`status-${status}`}
                  checked={selectedFilters.statuses.includes(status)}
                  onCheckedChange={(checked) => handleFilterChange("statuses", status, checked as boolean)}
                />
                <Label htmlFor={`status-${status}`} className="text-sm font-medium cursor-pointer">
                  {status}
                </Label>
              </div>
            ))}
          </div>
        </div>
      </div>
    </Card>
  )
}
