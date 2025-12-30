"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { Badge } from "@/components/ui/badge"
import { Switch } from "@/components/ui/switch"
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip"
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
import { MoreVertical, Edit, Trash2, ChevronLeft, ChevronRight } from "lucide-react"

const ITEMS_PER_PAGE = 10

// Mock data
const mockAnnouncements = Array.from({ length: 25 }, (_, i) => ({
  id: i + 1,
  title: `Announcement ${i + 1}: ${["Annual Meetup 2024", "Career Opportunities", "New Partnership Program", "Alumni Success Story", "Webinar Invitation"][i % 5]}`,
  description: `This is the detailed description for announcement ${i + 1}. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.`,
  createdOn: new Date(2024, 0, i + 1).toISOString(),
  expiryDate: new Date(2024, 11, i + 15).toISOString(),
  status: ["Active", "Inactive"][i % 2] as "Active" | "Inactive",
}))

interface AnnouncementsTableProps {
  searchQuery: string
  selectedFilters: {
    statuses: string[]
  }
}

export function AnnouncementsTable({ searchQuery, selectedFilters }: AnnouncementsTableProps) {
  const router = useRouter()
  const [currentPage, setCurrentPage] = useState(1)
  const [announcements, setAnnouncements] = useState(mockAnnouncements)
  const [deletingAnnouncement, setDeletingAnnouncement] = useState<(typeof mockAnnouncements)[0] | null>(null)

  // Filter logic
  const filteredAnnouncements = announcements.filter((announcement) => {
    const matchesSearch = announcement.title.toLowerCase().includes(searchQuery.toLowerCase())
    const matchesStatus =
      selectedFilters.statuses.length === 0 || selectedFilters.statuses.includes(announcement.status)

    return matchesSearch && matchesStatus
  })

  const totalPages = Math.ceil(filteredAnnouncements.length / ITEMS_PER_PAGE)

  const handleStatusToggle = (id: number, currentStatus: "Active" | "Inactive") => {
    setAnnouncements((prev) =>
      prev.map((announcement) =>
        announcement.id === id
          ? { ...announcement, status: currentStatus === "Active" ? "Inactive" : ("Active" as const) }
          : announcement,
      ),
    )
  }

  const handleEdit = (id: number) => {
    router.push(`/admin/announcements/${id}/edit`)
  }

  const handleDelete = (id: number) => {
    setAnnouncements((prev) => prev.filter((announcement) => announcement.id !== id))
    setDeletingAnnouncement(null)
  }

  const truncateText = (text: string, maxLength: number) => {
    if (text.length <= maxLength) return text
    return text.substring(0, maxLength) + "..."
  }

  return (
    <>
      <div className="border rounded-lg overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow className="bg-primary hover:bg-primary">
                <TableHead className="font-bold text-primary-foreground">Created On</TableHead>
                <TableHead className="font-bold text-primary-foreground">Announcement Title</TableHead>
                <TableHead className="font-bold text-primary-foreground">Announcement Description</TableHead>
                <TableHead className="font-bold text-primary-foreground">Announcement Expiry</TableHead>
                <TableHead className="font-bold text-primary-foreground">Status</TableHead>
                <TableHead className="font-bold text-primary-foreground text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredAnnouncements
                .slice((currentPage - 1) * ITEMS_PER_PAGE, currentPage * ITEMS_PER_PAGE)
                .map((announcement, index) => (
                  <TableRow
                    key={announcement.id}
                    className={index % 2 === 0 ? "bg-background hover:bg-muted/50" : "bg-muted/20 hover:bg-muted/50"}
                  >
                    <TableCell className="whitespace-nowrap">
                      {new Date(announcement.createdOn).toLocaleDateString("en-US", {
                        year: "numeric",
                        month: "short",
                        day: "numeric",
                      })}
                    </TableCell>
                    <TableCell className="font-medium">{announcement.title}</TableCell>
                    <TableCell className="max-w-md">
                      <TooltipProvider>
                        <Tooltip>
                          <TooltipTrigger asChild>
                            <div className="cursor-help">{truncateText(announcement.description, 80)}</div>
                          </TooltipTrigger>
                          <TooltipContent className="max-w-lg">
                            <p>{announcement.description}</p>
                          </TooltipContent>
                        </Tooltip>
                      </TooltipProvider>
                    </TableCell>
                    <TableCell className="whitespace-nowrap">
                      {new Date(announcement.expiryDate).toLocaleDateString("en-US", {
                        year: "numeric",
                        month: "short",
                        day: "numeric",
                      })}
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        <Switch
                          checked={announcement.status === "Active"}
                          onCheckedChange={() => handleStatusToggle(announcement.id, announcement.status)}
                        />
                        <Badge variant={announcement.status === "Active" ? "default" : "secondary"}>
                          {announcement.status}
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
                        <DropdownMenuContent align="end" className="w-48">
                          <DropdownMenuItem
                            onClick={() => handleEdit(announcement.id)}
                            className="hover:bg-primary hover:text-white cursor-pointer"
                          >
                            <Edit className="mr-2 h-4 w-4" />
                            Edit
                          </DropdownMenuItem>
                          <DropdownMenuItem
                            onClick={() => setDeletingAnnouncement(announcement)}
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

      <div className="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4">
        <p className="text-sm text-muted-foreground">
          Showing {(currentPage - 1) * ITEMS_PER_PAGE + 1} to{" "}
          {Math.min(currentPage * ITEMS_PER_PAGE, filteredAnnouncements.length)} of {filteredAnnouncements.length}{" "}
          announcements
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
          <span className="text-sm font-medium">
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

      <AlertDialog open={!!deletingAnnouncement} onOpenChange={() => setDeletingAnnouncement(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete Announcement</AlertDialogTitle>
            <AlertDialogDescription>
              Are you sure you want to delete this announcement? This action cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={() => deletingAnnouncement && handleDelete(deletingAnnouncement.id)}
              className="bg-destructive hover:bg-destructive/90"
            >
              Delete
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  )
}
