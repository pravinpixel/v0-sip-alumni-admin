"use client"

import { useRouter } from "next/navigation"
import { Card } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Badge } from "@/components/ui/badge"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Dialog, DialogContent } from "@/components/ui/dialog"
import { Checkbox } from "@/components/ui/checkbox"
import { Label } from "@/components/ui/label"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { ArrowLeft, Search, Filter, X, ChevronLeft, ChevronRight, Eye, ChevronDown } from "lucide-react"
import { useState, useMemo } from "react"

const mockConnections = Array.from({ length: 30 }, (_, i) => ({
  id: i + 1,
  name: `Alumni Connection ${i + 1}`,
  avatar: `https://i.pravatar.cc/150?img=${(i % 70) + 1}`,
  batch: 2018 + Math.floor(Math.random() * 7),
  location: ["Mumbai, Maharashtra", "Delhi, NCR", "Bangalore, Karnataka", "Chennai, Tamil Nadu", "Pune, Maharashtra"][
    Math.floor(Math.random() * 5)
  ],
  status: "Contact Accepted",
  yearOfCompletion: 2018 + Math.floor(Math.random() * 7),
  city: ["Mumbai", "Delhi", "Bangalore", "Chennai", "Pune"][Math.floor(Math.random() * 5)],
  state: ["Maharashtra", "NCR", "Karnataka", "Tamil Nadu", "Maharashtra"][Math.floor(Math.random() * 5)],
  email: `connection${i + 1}@example.com`,
  contactNumber: `+91 ${Math.floor(Math.random() * 9000000000) + 1000000000}`,
  occupation: ["Software Engineer", "Data Scientist", "Product Manager", "Designer", "Entrepreneur"][
    Math.floor(Math.random() * 5)
  ],
}))

const ITEMS_PER_PAGE = 10

const filterOptions = {
  batches: Array.from(new Set(mockConnections.map((c) => c.batch.toString()))).sort(),
  locations: Array.from(new Set(mockConnections.map((c) => c.location))).sort(),
}

export default function ConnectionsPage({ params }: { params: { id: string } }) {
  const router = useRouter()
  const [searchQuery, setSearchQuery] = useState("")
  const [showFilters, setShowFilters] = useState(false)
  const [selectedBatches, setSelectedBatches] = useState<string[]>([])
  const [selectedLocations, setSelectedLocations] = useState<string[]>([])
  const [currentPage, setCurrentPage] = useState(1)
  const [selectedProfile, setSelectedProfile] = useState<(typeof mockConnections)[0] | null>(null)

  const alumniName = `Alumni ${params.id}`

  const filteredConnections = useMemo(() => {
    return mockConnections.filter((connection) => {
      const matchesSearch =
        connection.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        connection.location.toLowerCase().includes(searchQuery.toLowerCase())

      const matchesBatch = selectedBatches.length === 0 || selectedBatches.includes(connection.batch.toString())

      const matchesLocation = selectedLocations.length === 0 || selectedLocations.includes(connection.location)

      return matchesSearch && matchesBatch && matchesLocation
    })
  }, [searchQuery, selectedBatches, selectedLocations])

  const totalPages = Math.ceil(filteredConnections.length / ITEMS_PER_PAGE)
  const paginatedConnections = filteredConnections.slice(
    (currentPage - 1) * ITEMS_PER_PAGE,
    currentPage * ITEMS_PER_PAGE,
  )

  const handleBatchToggle = (batch: string) => {
    setSelectedBatches((prev) => (prev.includes(batch) ? prev.filter((b) => b !== batch) : [...prev, batch]))
  }

  const handleLocationToggle = (location: string) => {
    setSelectedLocations((prev) => (prev.includes(location) ? prev.filter((l) => l !== location) : [...prev, location]))
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="outline" size="icon" onClick={() => router.back()}>
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div>
          <h1 className="text-3xl font-bold text-foreground">{alumniName} connections with the Alumni Peoples</h1>
          <p className="text-muted-foreground mt-1">View and manage alumni connections</p>
        </div>
      </div>

      <Card className="p-6">
        <div className="space-y-4 mb-6">
          <div className="flex flex-col sm:flex-row gap-3">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder="Search by name or location..."
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
            <Card className="p-4 bg-muted/30">
              <div className="flex flex-wrap gap-3">
                <Popover>
                  <PopoverTrigger asChild>
                    <Button variant="outline" className="h-10 font-medium bg-transparent">
                      Batch
                      {selectedBatches.length > 0 && (
                        <span className="ml-2 px-2 py-0.5 bg-primary text-primary-foreground text-xs rounded-full">
                          {selectedBatches.length}
                        </span>
                      )}
                      <ChevronDown className="ml-2 h-4 w-4" />
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent className="w-56" align="start">
                    <div className="space-y-3">
                      <h4 className="font-semibold text-sm">Select Batches</h4>
                      {filterOptions.batches.map((batch) => (
                        <div key={batch} className="flex items-center space-x-2">
                          <Checkbox
                            id={`batch-${batch}`}
                            checked={selectedBatches.includes(batch)}
                            onCheckedChange={() => handleBatchToggle(batch)}
                          />
                          <Label htmlFor={`batch-${batch}`} className="text-sm font-normal cursor-pointer">
                            {batch}
                          </Label>
                        </div>
                      ))}
                    </div>
                  </PopoverContent>
                </Popover>

                <Popover>
                  <PopoverTrigger asChild>
                    <Button variant="outline" className="h-10 font-medium bg-transparent">
                      Location
                      {selectedLocations.length > 0 && (
                        <span className="ml-2 px-2 py-0.5 bg-primary text-primary-foreground text-xs rounded-full">
                          {selectedLocations.length}
                        </span>
                      )}
                      <ChevronDown className="ml-2 h-4 w-4" />
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent className="w-56" align="start">
                    <div className="space-y-3">
                      <h4 className="font-semibold text-sm">Select Locations</h4>
                      {filterOptions.locations.map((location) => (
                        <div key={location} className="flex items-center space-x-2">
                          <Checkbox
                            id={`location-${location}`}
                            checked={selectedLocations.includes(location)}
                            onCheckedChange={() => handleLocationToggle(location)}
                          />
                          <Label htmlFor={`location-${location}`} className="text-sm font-normal cursor-pointer">
                            {location}
                          </Label>
                        </div>
                      ))}
                    </div>
                  </PopoverContent>
                </Popover>
              </div>
            </Card>
          )}

          {(selectedBatches.length > 0 || selectedLocations.length > 0) && (
            <div className="flex flex-wrap items-center gap-2">
              <span className="text-sm font-medium text-muted-foreground">Active Filters:</span>
              {selectedBatches.map((batch) => (
                <Badge key={batch} variant="secondary" className="gap-1">
                  Batch: {batch}
                  <button
                    onClick={() => setSelectedBatches((prev) => prev.filter((b) => b !== batch))}
                    className="ml-1 hover:text-destructive"
                  >
                    <X className="h-3 w-3" />
                  </button>
                </Badge>
              ))}
              {selectedLocations.map((location) => (
                <Badge key={location} variant="secondary" className="gap-1">
                  {location}
                  <button
                    onClick={() => setSelectedLocations((prev) => prev.filter((l) => l !== location))}
                    className="ml-1 hover:text-destructive"
                  >
                    <X className="h-3 w-3" />
                  </button>
                </Badge>
              ))}
              <Button
                variant="ghost"
                size="sm"
                onClick={() => {
                  setSelectedBatches([])
                  setSelectedLocations([])
                }}
                className="h-7 text-xs font-semibold text-destructive hover:text-destructive"
              >
                Clear All Filters
              </Button>
            </div>
          )}
        </div>

        <div className="border rounded-lg overflow-hidden">
          <div className="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow className="bg-primary hover:bg-primary">
                  <TableHead className="font-bold text-primary-foreground">Alumni Name</TableHead>
                  <TableHead className="font-bold text-primary-foreground">Batch</TableHead>
                  <TableHead className="font-bold text-primary-foreground">Location</TableHead>
                  <TableHead className="font-bold text-primary-foreground">View Profile</TableHead>
                  <TableHead className="font-bold text-primary-foreground">Status</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {paginatedConnections.map((connection, index) => (
                  <TableRow
                    key={connection.id}
                    className={index % 2 === 0 ? "bg-background hover:bg-muted/50" : "bg-muted/20 hover:bg-muted/50"}
                  >
                    <TableCell>
                      <div className="flex items-center gap-3">
                        <Avatar className="h-10 w-10 border-2 border-border">
                          <AvatarImage src={connection.avatar || "/placeholder.svg"} alt={connection.name} />
                          <AvatarFallback>{connection.name.charAt(0)}</AvatarFallback>
                        </Avatar>
                        <span className="font-medium">{connection.name}</span>
                      </div>
                    </TableCell>
                    <TableCell className="font-medium">{connection.batch}</TableCell>
                    <TableCell>{connection.location}</TableCell>
                    <TableCell>
                      <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => setSelectedProfile(connection)}
                        className="h-8 w-8 hover:bg-primary/10"
                      >
                        <Eye className="h-4 w-4" />
                      </Button>
                    </TableCell>
                    <TableCell>
                      <Badge variant="default" className="font-semibold bg-green-600 hover:bg-green-700">
                        Contact Accepted
                      </Badge>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        </div>

        <div className="flex items-center justify-between mt-6">
          <p className="text-sm text-muted-foreground">
            Showing {paginatedConnections.length} of {filteredConnections.length} connections
          </p>
          <div className="flex items-center gap-2">
            <Button
              variant="outline"
              size="sm"
              onClick={() => setCurrentPage((prev) => Math.max(1, prev - 1))}
              disabled={currentPage === 1}
            >
              <ChevronLeft className="h-4 w-4 mr-1" />
              Previous
            </Button>
            <span className="text-sm text-muted-foreground px-2">
              Page {currentPage} of {totalPages}
            </span>
            <Button
              variant="outline"
              size="sm"
              onClick={() => setCurrentPage((prev) => Math.min(totalPages, prev + 1))}
              disabled={currentPage === totalPages}
            >
              Next
              <ChevronRight className="h-4 w-4 ml-1" />
            </Button>
          </div>
        </div>
      </Card>

      <Dialog open={!!selectedProfile} onOpenChange={() => setSelectedProfile(null)}>
        <DialogContent className="sm:max-w-md">
          {selectedProfile && (
            <div className="space-y-4 py-4">
              <div className="flex items-center gap-4">
                <Avatar className="h-20 w-20 border-4 border-primary">
                  <AvatarImage src={selectedProfile.avatar || "/placeholder.svg"} alt={selectedProfile.name} />
                  <AvatarFallback className="text-2xl">{selectedProfile.name.charAt(0)}</AvatarFallback>
                </Avatar>
                <div>
                  <h3 className="text-xl font-bold">{selectedProfile.name}</h3>
                  <p className="text-sm text-muted-foreground">{selectedProfile.occupation}</p>
                </div>
              </div>
              <div className="space-y-3 border-t pt-4">
                <div className="grid grid-cols-3 gap-2">
                  <span className="text-sm font-semibold text-muted-foreground">Full Name:</span>
                  <span className="col-span-2 text-sm font-medium">{selectedProfile.name}</span>
                </div>
                <div className="grid grid-cols-3 gap-2">
                  <span className="text-sm font-semibold text-muted-foreground">Year:</span>
                  <span className="col-span-2 text-sm font-medium">{selectedProfile.yearOfCompletion}</span>
                </div>
                <div className="grid grid-cols-3 gap-2">
                  <span className="text-sm font-semibold text-muted-foreground">City & State:</span>
                  <span className="col-span-2 text-sm font-medium">
                    {selectedProfile.city}, {selectedProfile.state}
                  </span>
                </div>
                <div className="grid grid-cols-3 gap-2">
                  <span className="text-sm font-semibold text-muted-foreground">Email:</span>
                  <span className="col-span-2 text-sm font-medium">{selectedProfile.email}</span>
                </div>
                <div className="grid grid-cols-3 gap-2">
                  <span className="text-sm font-semibold text-muted-foreground">Contact:</span>
                  <span className="col-span-2 text-sm font-medium">{selectedProfile.contactNumber}</span>
                </div>
                <div className="grid grid-cols-3 gap-2">
                  <span className="text-sm font-semibold text-muted-foreground">Occupation:</span>
                  <span className="col-span-2 text-sm font-medium">{selectedProfile.occupation}</span>
                </div>
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  )
}
