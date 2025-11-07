"use client"

import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import { Label } from "@/components/ui/label"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Calendar } from "@/components/ui/calendar"
import { ChevronDown, CalendarIcon } from "lucide-react"
import { format } from "date-fns"
import { cn } from "@/lib/utils"

const filterOptions = {
  statuses: ["Pending", "Approved", "Rejected", "Post Deleted", "Removed by Admin"],
}

interface ForumsFiltersProps {
  selectedFilters: {
    statuses: string[]
    dateFrom: Date | undefined
    dateTo: Date | undefined
  }
  onFiltersChange: (filters: {
    statuses: string[]
    dateFrom: Date | undefined
    dateTo: Date | undefined
  }) => void
}

export function ForumsFilters({ selectedFilters, onFiltersChange }: ForumsFiltersProps) {
  const handleToggle = (type: "statuses", value: string) => {
    const current = selectedFilters[type]
    const updated = current.includes(value) ? current.filter((v) => v !== value) : [...current, value]

    onFiltersChange({
      ...selectedFilters,
      [type]: updated,
    })
  }

  const handleDateFromChange = (date: Date | undefined) => {
    onFiltersChange({
      ...selectedFilters,
      dateFrom: date,
    })
  }

  const handleDateToChange = (date: Date | undefined) => {
    onFiltersChange({
      ...selectedFilters,
      dateTo: date,
    })
  }

  return (
    <div className="p-4 bg-muted/30 rounded-lg border border-border space-y-4">
      <div className="flex flex-wrap gap-3">
        {/* Status Filter */}
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

        <Popover>
          <PopoverTrigger asChild>
            <Button
              variant="outline"
              className={cn(
                "h-10 font-medium bg-transparent justify-start text-left",
                !selectedFilters.dateFrom && "text-muted-foreground",
              )}
            >
              <CalendarIcon className="mr-2 h-4 w-4" />
              {selectedFilters.dateFrom ? format(selectedFilters.dateFrom, "MMM dd, yyyy") : "From Date"}
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-auto p-0" align="start">
            <Calendar mode="single" selected={selectedFilters.dateFrom} onSelect={handleDateFromChange} initialFocus />
          </PopoverContent>
        </Popover>

        <Popover>
          <PopoverTrigger asChild>
            <Button
              variant="outline"
              className={cn(
                "h-10 font-medium bg-transparent justify-start text-left",
                !selectedFilters.dateTo && "text-muted-foreground",
              )}
            >
              <CalendarIcon className="mr-2 h-4 w-4" />
              {selectedFilters.dateTo ? format(selectedFilters.dateTo, "MMM dd, yyyy") : "To Date"}
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-auto p-0" align="start">
            <Calendar mode="single" selected={selectedFilters.dateTo} onSelect={handleDateToChange} initialFocus />
          </PopoverContent>
        </Popover>
      </div>
    </div>
  )
}
