"use client"
import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import { Label } from "@/components/ui/label"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { ChevronDown } from "lucide-react"

const filterOptions = {
  years: ["2020", "2021", "2022", "2023", "2024"],
  cities: ["Mumbai", "Delhi", "Bangalore", "Chennai", "Kolkata"],
  occupations: ["Software Engineer", "Data Scientist", "Product Manager", "Designer", "Entrepreneur"],
}

interface DirectoryFiltersProps {
  selectedFilters: {
    years: string[]
    cities: string[]
    occupations: string[]
  }
  onFiltersChange: (filters: {
    years: string[]
    cities: string[]
    occupations: string[]
  }) => void
}

export function DirectoryFilters({ selectedFilters, onFiltersChange }: DirectoryFiltersProps) {
  const handleToggle = (type: "years" | "cities" | "occupations", value: string) => {
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
        {/* Year Filter */}
        <Popover>
          <PopoverTrigger asChild>
            <Button variant="outline" className="h-10 font-medium bg-transparent">
              Year of Completion
              {selectedFilters.years.length > 0 && (
                <span className="ml-2 px-2 py-0.5 bg-primary text-primary-foreground text-xs rounded-full">
                  {selectedFilters.years.length}
                </span>
              )}
              <ChevronDown className="ml-2 h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-56" align="start">
            <div className="space-y-3">
              <h4 className="font-semibold text-sm">Select Years</h4>
              {filterOptions.years.map((year) => (
                <div key={year} className="flex items-center space-x-2">
                  <Checkbox
                    id={`year-${year}`}
                    checked={selectedFilters.years.includes(year)}
                    onCheckedChange={() => handleToggle("years", year)}
                  />
                  <Label htmlFor={`year-${year}`} className="text-sm font-normal cursor-pointer">
                    {year}
                  </Label>
                </div>
              ))}
            </div>
          </PopoverContent>
        </Popover>

        {/* City Filter */}
        <Popover>
          <PopoverTrigger asChild>
            <Button variant="outline" className="h-10 font-medium bg-transparent">
              City
              {selectedFilters.cities.length > 0 && (
                <span className="ml-2 px-2 py-0.5 bg-primary text-primary-foreground text-xs rounded-full">
                  {selectedFilters.cities.length}
                </span>
              )}
              <ChevronDown className="ml-2 h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-56" align="start">
            <div className="space-y-3">
              <h4 className="font-semibold text-sm">Select Cities</h4>
              {filterOptions.cities.map((city) => (
                <div key={city} className="flex items-center space-x-2">
                  <Checkbox
                    id={`city-${city}`}
                    checked={selectedFilters.cities.includes(city)}
                    onCheckedChange={() => handleToggle("cities", city)}
                  />
                  <Label htmlFor={`city-${city}`} className="text-sm font-normal cursor-pointer">
                    {city}
                  </Label>
                </div>
              ))}
            </div>
          </PopoverContent>
        </Popover>

        {/* Occupation Filter */}
        <Popover>
          <PopoverTrigger asChild>
            <Button variant="outline" className="h-10 font-medium bg-transparent">
              Occupation
              {selectedFilters.occupations.length > 0 && (
                <span className="ml-2 px-2 py-0.5 bg-primary text-primary-foreground text-xs rounded-full">
                  {selectedFilters.occupations.length}
                </span>
              )}
              <ChevronDown className="ml-2 h-4 w-4" />
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-56" align="start">
            <div className="space-y-3">
              <h4 className="font-semibold text-sm">Select Occupations</h4>
              {filterOptions.occupations.map((occupation) => (
                <div key={occupation} className="flex items-center space-x-2">
                  <Checkbox
                    id={`occupation-${occupation}`}
                    checked={selectedFilters.occupations.includes(occupation)}
                    onCheckedChange={() => handleToggle("occupations", occupation)}
                  />
                  <Label htmlFor={`occupation-${occupation}`} className="text-sm font-normal cursor-pointer">
                    {occupation}
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
