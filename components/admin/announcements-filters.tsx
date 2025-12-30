"use client"

import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import { Label } from "@/components/ui/label"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { ChevronDown } from "lucide-react"

const filterOptions = {
  statuses: ["Active", "Inactive"],
}

interface AnnouncementsFiltersProps {
  selectedFilters: {
    statuses: string[]
  }
  onFiltersChange: (filters: { statuses: string[] }) => void
}

export function AnnouncementsFilters({ selectedFilters, onFiltersChange }: AnnouncementsFiltersProps) {
  const handleToggle = (type: "statuses", value: string) => {
    const current = selectedFilters[type]
    const updated = current.includes(value) ? current.filter((v) => v !== value) : [...current, value]

    onFiltersChange({
      ...selectedFilters,
      [type]: updated,
    })
  }

  return (
    <div className="p-4 bg-muted/30 rounded-lg border border-border space-y-4">
      <div className="flex flex-wrap gap-3">
        <Popover>
          <PopoverTrigger asChild>
            <Button variant="outline" className="h-10 font-medium bg-transparent">
              Status
              {selectedFilters.statuses.length > 0 && (
                <span className="ml-2 px-2 py-0.5 bg-primary text-primary-foreground text-xs rounded-full">
                  {selectedFilters.statuses.length}
                </span>
              )}
              <ChevronDown className="ml-2 h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-56" align="start">
            <div className="space-y-3">
              <h4 className="font-semibold text-sm">Select Status</h4>
              {filterOptions.statuses.map((status) => (
                <div key={status} className="flex items-center space-x-2">
                  <Checkbox
                    id={`status-${status}`}
                    checked={selectedFilters.statuses.includes(status)}
                    onCheckedChange={() => handleToggle("statuses", status)}
                  />
                  <Label htmlFor={`status-${status}`} className="text-sm font-normal cursor-pointer">
                    {status}
                  </Label>
                </div>
              ))}
            </div>
          </PopoverContent>
        </Popover>
      </div>
    </div>
  )
}
