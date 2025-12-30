"use client"

import { useState, useMemo } from "react"
import { useRouter } from "next/navigation"
import { Card } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import {
  Search,
  Filter,
  X,
  Download,
  MoreVertical,
  Eye,
  ImageIcon,
  ChevronLeft,
  ChevronRight,
  Ban,
  UserCheck,
  Users,
} from "lucide-react"
import { DirectoryFilters } from "./directory-filters"
import { ImageLightbox } from "./image-lightbox"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"

const mockAlumni = Array.from({ length: 50 }, (_, i) => ({
  id: i + 1,
  createdOn: new Date(2024, Math.floor(Math.random() * 12), Math.floor(Math.random() * 28) + 1).toISOString(),
  profilePicture: `https://i.pravatar.cc/150?img=${(i % 70) + 1}`,
  name: `Alumni ${i + 1}`,
  yearOfCompletion: 2020 + Math.floor(Math.random() * 5),
  city: ["Mumbai", "Delhi", "Bangalore", "Chennai", "Kolkata"][Math.floor(Math.random() * 5)],
  state: ["Maharashtra", "Delhi", "Karnataka", "Tamil Nadu", "West Bengal"][Math.floor(Math.random() * 5)],
  email: `alumni${i + 1}@example.com`,
  contactNumber: `+91 ${Math.floor(Math.random() * 9000000000) + 1000000000}`,
  occupation: ["Software Engineer", "Data Scientist", "Product Manager", "Designer", "Entrepreneur"][
    Math.floor(Math.random() * 5)
  ],
  status: ["Active", "Blocked"][Math.floor(Math.random() * 2)] as "Active" | "Blocked",
}))

const ITEMS_PER_PAGE = 10

export function DirectoryTable() {
  const router = useRouter()
  const [searchQuery, setSearchQuery] = useState("")
  const [showFilters, setShowFilters] = useState(false)
  const [selectedFilters, setSelectedFilters] = useState<{
    years: string[]
    cities: string[]
    occupations: string[]
  }>({
    years: [],
    cities: [],
    occupations: [],
  })
  const [lightboxImage, setLightboxImage] = useState<string | null>(null)
  const [currentPage, setCurrentPage] = useState(1)

  // Filter and search logic
  const filteredAlumni = useMemo(() => {
    return mockAlumni.filter((alumni) => {
      // Search filter
      const matchesSearch =
        alumni.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        alumni.email.toLowerCase().includes(searchQuery.toLowerCase())

      // Year filter
      const matchesYear =
        selectedFilters.years.length === 0 || selectedFilters.years.includes(alumni.yearOfCompletion.toString())

      // City filter
      const matchesCity = selectedFilters.cities.length === 0 || selectedFilters.cities.includes(alumni.city)

      // Occupation filter
      const matchesOccupation =
        selectedFilters.occupations.length === 0 || selectedFilters.occupations.includes(alumni.occupation)

      return matchesSearch && matchesYear && matchesCity && matchesOccupation
    })
  }, [searchQuery, selectedFilters])

  // Pagination
  const totalPages = Math.ceil(filteredAlumni.length / ITEMS_PER_PAGE)
  const paginatedAlumni = filteredAlumni.slice((currentPage - 1) * ITEMS_PER_PAGE, currentPage * ITEMS_PER_PAGE)

  const handleRemoveFilter = (type: "years" | "cities" | "occupations", value: string) => {
    setSelectedFilters((prev) => ({
      ...prev,
      [type]: prev[type].filter((v) => v !== value),
    }))
  }

  const handleClearAllFilters = () => {
    setSelectedFilters({
      years: [],
      cities: [],
      occupations: [],
    })
  }

  const hasActiveFilters =
    selectedFilters.years.length > 0 || selectedFilters.cities.length > 0 || selectedFilters.occupations.length > 0

  const handleExport = (format: "csv" | "excel") => {
    const data = filteredAlumni.map((alumni) => ({
      "Created On": new Date(alumni.createdOn).toLocaleDateString(),
      Name: alumni.name,
      "Year of Completion": alumni.yearOfCompletion,
      City: alumni.city,
      State: alumni.state,
      Email: alumni.email,
      "Contact Number": alumni.contactNumber,
      Occupation: alumni.occupation,
      Status: alumni.status,
    }))

    console.log(`Exporting ${data.length} records as ${format}`, data)
    alert(`Exporting ${data.length} records as ${format.toUpperCase()}`)
  }

  // Status badge helper function
  const getStatusBadge = (status: "Active" | "Blocked") => {
    const variants = {
      Active: "default",
      Blocked: "destructive",
    } as const

    return (
      <Badge variant={variants[status]} className="font-semibold">
        {status}
      </Badge>
    )
  }

  return (
    <Card className="p-6">
      {/* Search and Filter Bar */}
      <div className="space-y-4 mb-6">
        <div className="flex flex-col sm:flex-row gap-3">
          <div className="relative flex-1">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search by name or email..."
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
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="outline" className="h-11 font-semibold bg-transparent">
                <Download className="mr-2 h-4 w-4" />
                Export
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuItem onClick={() => handleExport("csv")}>Export as CSV</DropdownMenuItem>
              <DropdownMenuItem onClick={() => handleExport("excel")}>Export as Excel</DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>

        {/* Filter Panel */}
        {showFilters && <DirectoryFilters selectedFilters={selectedFilters} onFiltersChange={setSelectedFilters} />}

        {/* Active Filter Chips */}
        {hasActiveFilters && (
          <div className="flex flex-wrap items-center gap-2">
            <span className="text-sm font-medium text-muted-foreground">Active Filters:</span>
            {selectedFilters.years.map((year) => (
              <Badge key={year} variant="secondary" className="gap-1">
                Year: {year}
                <button onClick={() => handleRemoveFilter("years", year)} className="ml-1 hover:text-destructive">
                  <X className="h-3 w-3" />
                </button>
              </Badge>
            ))}
            {selectedFilters.cities.map((city) => (
              <Badge key={city} variant="secondary" className="gap-1">
                City: {city}
                <button onClick={() => handleRemoveFilter("cities", city)} className="ml-1 hover:text-destructive">
                  <X className="h-3 w-3" />
                </button>
              </Badge>
            ))}
            {selectedFilters.occupations.map((occupation) => (
              <Badge key={occupation} variant="secondary" className="gap-1">
                {occupation}
                <button
                  onClick={() => handleRemoveFilter("occupations", occupation)}
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

      {/* Table */}
      <div className="border rounded-lg overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow className="bg-primary hover:bg-primary">
                <TableHead className="font-bold text-primary-foreground">Created On</TableHead>
                <TableHead className="font-bold text-primary-foreground">Profile Picture</TableHead>
                <TableHead className="font-bold text-primary-foreground">Name</TableHead>
                <TableHead className="font-bold text-primary-foreground">Year</TableHead>
                <TableHead className="font-bold text-primary-foreground">City & State</TableHead>
                <TableHead className="font-bold text-primary-foreground">Email</TableHead>
                <TableHead className="font-bold text-primary-foreground">Contact</TableHead>
                <TableHead className="font-bold text-primary-foreground">Occupation</TableHead>
                <TableHead className="font-bold text-primary-foreground">Status</TableHead>
                <TableHead className="font-bold text-primary-foreground">Connections</TableHead>
                <TableHead className="font-bold text-primary-foreground text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {paginatedAlumni.map((alumni, index) => (
                <TableRow
                  key={alumni.id}
                  className={index % 2 === 0 ? "bg-background hover:bg-muted/50" : "bg-muted/20 hover:bg-muted/50"}
                >
                  <TableCell className="whitespace-nowrap">
                    {new Date(alumni.createdOn).toLocaleDateString("en-US", {
                      year: "numeric",
                      month: "short",
                      day: "numeric",
                    })}
                  </TableCell>
                  <TableCell>
                    <button onClick={() => setLightboxImage(alumni.profilePicture)} className="group relative">
                      <Avatar className="h-10 w-10 border-2 border-border group-hover:border-primary transition-colors">
                        <AvatarImage src={alumni.profilePicture || "/placeholder.svg"} alt={alumni.name} />
                        <AvatarFallback>{alumni.name.charAt(0)}</AvatarFallback>
                      </Avatar>
                      <div className="absolute inset-0 flex items-center justify-center bg-black/50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                        <ImageIcon className="h-4 w-4 text-white" />
                      </div>
                    </button>
                  </TableCell>
                  <TableCell className="font-medium">{alumni.name}</TableCell>
                  <TableCell>{alumni.yearOfCompletion}</TableCell>
                  <TableCell className="whitespace-nowrap">
                    {alumni.city}, {alumni.state}
                  </TableCell>
                  <TableCell className="text-sm">{alumni.email}</TableCell>
                  <TableCell className="whitespace-nowrap text-sm">{alumni.contactNumber}</TableCell>
                  <TableCell>
                    <Badge variant="outline">{alumni.occupation}</Badge>
                  </TableCell>
                  <TableCell>{getStatusBadge(alumni.status)}</TableCell>
                  <TableCell>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => router.push(`/admin/directory/${alumni.id}/connections`)}
                      className="font-semibold"
                    >
                      <Users className="mr-2 h-4 w-4" />
                      View
                    </Button>
                  </TableCell>
                  <TableCell className="text-right">
                    <DropdownMenu>
                      <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="icon" className="h-8 w-8">
                          <MoreVertical className="h-4 w-4" />
                        </Button>
                      </DropdownMenuTrigger>
                      <DropdownMenuContent align="end">
                        <DropdownMenuItem onClick={() => setLightboxImage(alumni.profilePicture)}>
                          <Eye className="mr-2 h-4 w-4" />
                          View Profile Pic
                        </DropdownMenuItem>
                        {alumni.status === "Active" && (
                          <DropdownMenuItem className="text-destructive hover:text-white hover:bg-destructive">
                            <Ban className="mr-2 h-4 w-4" />
                            Block
                          </DropdownMenuItem>
                        )}
                        {alumni.status === "Blocked" && (
                          <DropdownMenuItem className="hover:text-white hover:bg-primary">
                            <UserCheck className="mr-2 h-4 w-4" />
                            Unblock
                          </DropdownMenuItem>
                        )}
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </div>
      </div>

      {/* Pagination */}
      <div className="flex items-center justify-between mt-6">
        <p className="text-sm text-muted-foreground">
          Showing {paginatedAlumni.length} of {filteredAlumni.length} alumni
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

      {/* Image Lightbox */}
      {lightboxImage && <ImageLightbox imageUrl={lightboxImage} onClose={() => setLightboxImage(null)} />}
    </Card>
  )
}
