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
import { UsersFilters } from "./users-filters"
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

const mockUsers = [
  {
    id: "USER001",
    name: "John Doe",
    email: "john.doe@sipadmin.com",
    role: "Super Admin",
    status: "Active",
    createdOn: "2024-01-15",
  },
  {
    id: "USER002",
    name: "Jane Smith",
    email: "jane.smith@sipadmin.com",
    role: "Content Moderator",
    status: "Active",
    createdOn: "2024-02-10",
  },
  {
    id: "USER003",
    name: "Mike Johnson",
    email: "mike.johnson@sipadmin.com",
    role: "Viewer",
    status: "Inactive",
    createdOn: "2024-03-05",
  },
  {
    id: "USER004",
    name: "Sarah Williams",
    email: "sarah.williams@sipadmin.com",
    role: "Content Moderator",
    status: "Active",
    createdOn: "2024-03-20",
  },
]

const ITEMS_PER_PAGE = 10

export function UsersTable() {
  const router = useRouter()
  const [searchQuery, setSearchQuery] = useState("")
  const [showFilters, setShowFilters] = useState(false)
  const [selectedFilters, setSelectedFilters] = useState<{
    roles: string[]
    statuses: string[]
  }>({
    roles: [],
    statuses: [],
  })
  const [currentPage, setCurrentPage] = useState(1)
  const [users, setUsers] = useState(mockUsers)
  const [deletingUser, setDeletingUser] = useState<(typeof mockUsers)[0] | null>(null)

  const filteredUsers = useMemo(() => {
    return users.filter((user) => {
      const matchesSearch =
        user.id.toLowerCase().includes(searchQuery.toLowerCase()) ||
        user.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        user.email.toLowerCase().includes(searchQuery.toLowerCase())

      const matchesRole = selectedFilters.roles.length === 0 || selectedFilters.roles.includes(user.role)
      const matchesStatus = selectedFilters.statuses.length === 0 || selectedFilters.statuses.includes(user.status)

      return matchesSearch && matchesRole && matchesStatus
    })
  }, [searchQuery, selectedFilters, users])

  const handleRemoveFilter = (type: "roles" | "statuses", value: string) => {
    setSelectedFilters((prev) => ({
      ...prev,
      [type]: prev[type].filter((v) => v !== value),
    }))
  }

  const handleClearAllFilters = () => {
    setSelectedFilters({
      roles: [],
      statuses: [],
    })
  }

  const hasActiveFilters = selectedFilters.roles.length > 0 || selectedFilters.statuses.length > 0

  const handleToggleStatus = (userId: string) => {
    setUsers((prev) =>
      prev.map((user) =>
        user.id === userId ? { ...user, status: user.status === "Active" ? "Inactive" : "Active" } : user,
      ),
    )
  }

  const handleDelete = (userId: string) => {
    console.log(`[v0] Deleting user ${userId}`)
    setUsers((prev) => prev.filter((user) => user.id !== userId))
    setDeletingUser(null)
  }

  return (
    <Card className="p-6">
      {/* Search, Filter, and Create Button */}
      <div className="space-y-4 mb-6">
        <div className="flex flex-col sm:flex-row gap-3">
          <div className="relative flex-1">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search by user ID, name, or email..."
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
            <Button onClick={() => router.push("/admin/users/create")} className="h-11 font-semibold">
              <Plus className="mr-2 h-4 w-4" />
              Create User
            </Button>
          </div>
        </div>

        {/* Filter Panel */}
        {showFilters && <UsersFilters selectedFilters={selectedFilters} onFiltersChange={setSelectedFilters} />}

        {/* Active Filter Chips */}
        {hasActiveFilters && (
          <div className="flex flex-wrap items-center gap-2">
            <span className="text-sm font-medium text-muted-foreground">Active Filters:</span>
            {selectedFilters.roles.map((role) => (
              <Badge key={role} variant="secondary" className="gap-1">
                Role: {role}
                <button onClick={() => handleRemoveFilter("roles", role)} className="ml-1 hover:text-destructive">
                  <X className="h-3 w-3" />
                </button>
              </Badge>
            ))}
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
                <TableHead className="font-bold text-primary-foreground">User ID</TableHead>
                <TableHead className="font-bold text-primary-foreground">User Name</TableHead>
                <TableHead className="font-bold text-primary-foreground">Email ID</TableHead>
                <TableHead className="font-bold text-primary-foreground">Role</TableHead>
                <TableHead className="font-bold text-primary-foreground">Status</TableHead>
                <TableHead className="font-bold text-primary-foreground text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredUsers
                .slice((currentPage - 1) * ITEMS_PER_PAGE, currentPage * ITEMS_PER_PAGE)
                .map((user, index) => (
                  <TableRow
                    key={user.id}
                    className={index % 2 === 0 ? "bg-background hover:bg-muted/50" : "bg-muted/20 hover:bg-muted/50"}
                  >
                    <TableCell className="font-medium">{user.id}</TableCell>
                    <TableCell className="font-medium">{user.name}</TableCell>
                    <TableCell>{user.email}</TableCell>
                    <TableCell>
                      <Badge variant="outline" className="font-semibold">
                        {user.role}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-3">
                        <Switch
                          checked={user.status === "Active"}
                          onCheckedChange={() => handleToggleStatus(user.id)}
                          className="data-[state=checked]:bg-green-600"
                        />
                        <Badge
                          variant={user.status === "Active" ? "default" : "secondary"}
                          className={
                            user.status === "Active"
                              ? "bg-green-600 hover:bg-green-700 font-semibold"
                              : "bg-gray-400 hover:bg-gray-500 font-semibold"
                          }
                        >
                          {user.status}
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
                            onClick={() => router.push(`/admin/users/${user.id}/edit`)}
                            className="hover:bg-primary hover:text-white cursor-pointer"
                          >
                            <Edit className="mr-2 h-4 w-4" />
                            Edit
                          </DropdownMenuItem>
                          <DropdownMenuItem
                            onClick={() => setDeletingUser(user)}
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
          Showing {filteredUsers.slice((currentPage - 1) * ITEMS_PER_PAGE, currentPage * ITEMS_PER_PAGE).length} of{" "}
          {filteredUsers.length} users
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
            Page {currentPage} of {Math.ceil(filteredUsers.length / ITEMS_PER_PAGE)}
          </span>
          <Button
            variant="outline"
            size="sm"
            onClick={() =>
              setCurrentPage((prev) => Math.min(Math.ceil(filteredUsers.length / ITEMS_PER_PAGE), prev + 1))
            }
            disabled={currentPage === Math.ceil(filteredUsers.length / ITEMS_PER_PAGE)}
          >
            Next
            <ChevronRight className="h-4 w-4 ml-1" />
          </Button>
        </div>
      </div>

      {/* Delete User Alert Dialog */}
      <AlertDialog open={!!deletingUser} onOpenChange={() => setDeletingUser(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete User</AlertDialogTitle>
            <AlertDialogDescription>
              Are you sure you want to delete the user "{deletingUser?.name}"? This action cannot be undone and will
              remove all access permissions for this user.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={() => deletingUser && handleDelete(deletingUser.id)}
              className="bg-destructive hover:bg-destructive/90"
            >
              Delete User
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </Card>
  )
}
