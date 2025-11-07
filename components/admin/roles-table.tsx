"use client"

import { useState, useMemo } from "react"
import { useRouter } from "next/navigation"
import { Card } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { Switch } from "@/components/ui/switch"
import { Search, Filter, X, ChevronLeft, ChevronRight, MoreVertical, Edit, Trash2, Plus } from "lucide-react"
import { RolesFilters } from "./roles-filters"
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog"

const mockRoles = [
  {
    id: "ROLE001",
    name: "Super Admin",
    status: "Active",
    createdOn: "2024-01-15",
    permissions: {
      dashboard: { access: true },
      directory: { all: true, view: true, viewProfilePic: true, block: true, remove: true },
      forums: { all: true, view: true, approve: true, reject: true, remove: true, deleteComments: true },
    },
  },
  {
    id: "ROLE002",
    name: "Content Moderator",
    status: "Active",
    createdOn: "2024-02-10",
    permissions: {
      dashboard: { access: true },
      directory: { all: false, view: true, viewProfilePic: true, block: false, remove: false },
      forums: { all: false, view: true, approve: true, reject: true, remove: false, deleteComments: true },
    },
  },
  {
    id: "ROLE003",
    name: "Viewer",
    status: "Inactive",
    createdOn: "2024-03-05",
    permissions: {
      dashboard: { access: true },
      directory: { all: false, view: true, viewProfilePic: true, block: false, remove: false },
      forums: { all: false, view: true, approve: false, reject: false, remove: false, deleteComments: false },
    },
  },
]

const ITEMS_PER_PAGE = 10

export function RolesTable() {
  const router = useRouter()
  const [searchQuery, setSearchQuery] = useState("")
  const [showFilters, setShowFilters] = useState(false)
  const [selectedFilters, setSelectedFilters] = useState<{
    statuses: string[]
  }>({
    statuses: [],
  })
  const [currentPage, setCurrentPage] = useState(1)
  const [roles, setRoles] = useState(mockRoles)
  const [deletingRole, setDeletingRole] = useState<(typeof mockRoles)[0] | null>(null)

  const filteredRoles = useMemo(() => {
    return roles.filter((role) => {
      const matchesSearch =
        role.id.toLowerCase().includes(searchQuery.toLowerCase()) ||
        role.name.toLowerCase().includes(searchQuery.toLowerCase())

      const matchesStatus = selectedFilters.statuses.length === 0 || selectedFilters.statuses.includes(role.status)

      return matchesSearch && matchesStatus
    })
  }, [searchQuery, selectedFilters, roles])

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

  const hasActiveFilters = selectedFilters.statuses.length > 0

  const handleToggleStatus = (roleId: string) => {
    setRoles((prev) =>
      prev.map((role) =>
        role.id === roleId ? { ...role, status: role.status === "Active" ? "Inactive" : "Active" } : role,
      ),
    )
  }

  const handleDelete = (roleId: string) => {
    console.log(`[v0] Deleting role ${roleId}`)
    setRoles((prev) => prev.filter((role) => role.id !== roleId))
    setDeletingRole(null)
  }

  return (
    <Card className="p-6">
      {/* Search, Filter, and Create Button */}
      <div className="space-y-4 mb-6">
        <div className="flex flex-col sm:flex-row gap-3">
          <div className="relative flex-1">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search by role ID or name..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="pl-10 h-11"
            />
          </div>
          <div className="flex gap-3">
            <Button
              variant={showFilters ? "default" : "outline"}
              onClick={() => setShowFilters(!showFilters)}
              className="h-11 font-semibold"
            >
              <Filter className="mr-2 h-4 w-4" />
              {showFilters ? "Close Filters" : "Filter"}
            </Button>
            <Button onClick={() => router.push("/admin/roles/create")} className="h-11 font-semibold">
              <Plus className="mr-2 h-4 w-4" />
              Create Role
            </Button>
          </div>
        </div>

        {/* Filter Panel */}
        {showFilters && <RolesFilters selectedFilters={selectedFilters} onFiltersChange={setSelectedFilters} />}

        {/* Active Filter Chips */}
        {hasActiveFilters && (
          <div className="flex flex-wrap items-center gap-2">
            <span className="text-sm font-medium text-muted-foreground">Active Filters:</span>
            {selectedFilters.statuses.map((status) => (
              <Badge key={status} variant="secondary" className="gap-1">
                Status: {status}
                <button onClick={() => handleRemoveFilter("statuses", status)} className="ml-1 hover:text-destructive">
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
                <TableHead className="font-bold text-primary-foreground">Role ID</TableHead>
                <TableHead className="font-bold text-primary-foreground">Role Name</TableHead>
                <TableHead className="font-bold text-primary-foreground">Status</TableHead>
                <TableHead className="font-bold text-primary-foreground text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredRoles
                .slice((currentPage - 1) * ITEMS_PER_PAGE, currentPage * ITEMS_PER_PAGE)
                .map((role, index) => (
                  <TableRow
                    key={role.id}
                    className={index % 2 === 0 ? "bg-background hover:bg-muted/50" : "bg-muted/20 hover:bg-muted/50"}
                  >
                    <TableCell className="font-medium">{role.id}</TableCell>
                    <TableCell className="font-medium">{role.name}</TableCell>
                    <TableCell>
                      <div className="flex items-center gap-3">
                        <Switch
                          checked={role.status === "Active"}
                          onCheckedChange={() => handleToggleStatus(role.id)}
                          className="data-[state=checked]:bg-green-600"
                        />
                        <Badge
                          variant={role.status === "Active" ? "default" : "secondary"}
                          className={
                            role.status === "Active"
                              ? "bg-green-600 hover:bg-green-700 font-semibold"
                              : "bg-gray-400 hover:bg-gray-500 font-semibold"
                          }
                        >
                          {role.status}
                        </Badge>
                      </div>
                    </TableCell>
                    <TableCell className="text-right">
                      <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                          <Button variant="ghost" size="icon" className="h-8 w-8">
                            <MoreVertical className="h-4 w-4" />
                          </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" className="w-40">
                          <DropdownMenuItem
                            onClick={() => router.push(`/admin/roles/${role.id}/edit`)}
                            className="hover:bg-primary hover:text-white cursor-pointer"
                          >
                            <Edit className="mr-2 h-4 w-4" />
                            Edit
                          </DropdownMenuItem>
                          <DropdownMenuItem
                            onClick={() => setDeletingRole(role)}
                            className="text-destructive hover:bg-destructive hover:text-white cursor-pointer"
                          >
                            <Trash2 className="mr-2 h-4 w-4" />
                            Delete
                          </DropdownMenuItem>
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
          Showing {filteredRoles.slice((currentPage - 1) * ITEMS_PER_PAGE, currentPage * ITEMS_PER_PAGE).length} of{" "}
          {filteredRoles.length} roles
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
            Page {currentPage} of {Math.ceil(filteredRoles.length / ITEMS_PER_PAGE)}
          </span>
          <Button
            variant="outline"
            size="sm"
            onClick={() =>
              setCurrentPage((prev) => Math.min(Math.ceil(filteredRoles.length / ITEMS_PER_PAGE), prev + 1))
            }
            disabled={currentPage === Math.ceil(filteredRoles.length / ITEMS_PER_PAGE)}
          >
            Next
            <ChevronRight className="h-4 w-4 ml-1" />
          </Button>
        </div>
      </div>

      {/* Delete Role Alert Dialog */}
      <AlertDialog open={!!deletingRole} onOpenChange={() => setDeletingRole(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete Role</AlertDialogTitle>
            <AlertDialogDescription>
              Are you sure you want to delete the role "{deletingRole?.name}"? This action cannot be undone and will
              affect all users assigned to this role.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={() => deletingRole && handleDelete(deletingRole.id)}
              className="bg-destructive hover:bg-destructive/90"
            >
              Delete Role
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </Card>
  )
}
