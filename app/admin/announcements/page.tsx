"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card } from "@/components/ui/card"
import { AnnouncementsTable } from "@/components/admin/announcements-table"
import { AnnouncementsFilters } from "@/components/admin/announcements-filters"
import { Badge } from "@/components/ui/badge"
import { Search, Filter, X, Plus } from "lucide-react"

export default function AnnouncementsPage() {
  const router = useRouter()
  const [searchQuery, setSearchQuery] = useState("")
  const [showFilters, setShowFilters] = useState(false)
  const [selectedFilters, setSelectedFilters] = useState<{
    statuses: string[]
  }>({
    statuses: [],
  })

  const hasActiveFilters = selectedFilters.statuses.length > 0

  const handleRemoveFilter = (type: "statuses", value: string) => {
    setSelectedFilters((prev) => ({
      ...prev,
      [type]: prev[type].filter((v) => v !== value),
    }))
  }

  const handleClearAllFilters = () => {
    setSelectedFilters({
      statuses: [],
    })
  }

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-3xl font-bold text-balance">Announcements</h1>
          <p className="text-muted-foreground mt-1">Manage and publish announcements to alumni</p>
        </div>
        <Button onClick={() => router.push("/admin/announcements/create")} className="font-semibold">
          <Plus className="mr-2 h-4 w-4" />
          Add Announcement
        </Button>
      </div>

      <Card className="p-6">
        <div className="space-y-4 mb-6">
          <div className="flex flex-col sm:flex-row gap-3">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Search by announcement title..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="pl-10 h-11"
              />
            </div>
            <Button
              variant={showFilters ? "default" : "outline"}
              onClick={() => setShowFilters(!showFilters)}
              className="h-11 font-semibold"
            >
              <Filter className="mr-2 h-4 w-4" />
              {showFilters ? "Close Filters" : "Filter"}
            </Button>
          </div>

          {showFilters && (
            <AnnouncementsFilters selectedFilters={selectedFilters} onFiltersChange={setSelectedFilters} />
          )}

          {hasActiveFilters && (
            <div className="flex flex-wrap items-center gap-2">
              <span className="text-sm font-medium text-muted-foreground">Active Filters:</span>
              {selectedFilters.statuses.map((status) => (
                <Badge key={status} variant="secondary" className="gap-1">
                  Status: {status}
                  <button
                    onClick={() => handleRemoveFilter("statuses", status)}
                    className="ml-1 hover:text-destructive"
                  >
                    <X className="h-3 w-3" />
                  </button>
                </Badge>
              ))}
              <Button
                variant="ghost"
                size="sm"
                onClick={handleClearAllFilters}
                className="h-7 text-xs font-semibold text-destructive hover:text-destructive"
              >
                Clear All Filters
              </Button>
            </div>
          )}
        </div>

        <AnnouncementsTable searchQuery={searchQuery} selectedFilters={selectedFilters} />
      </Card>
    </div>
  )
}
