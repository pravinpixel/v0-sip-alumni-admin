"use client"

import { useState, useMemo } from "react"
import { useRouter } from "next/navigation"
import { Card } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu"
import { format } from "date-fns"
import {
  Search,
  Filter,
  X,
  ChevronLeft,
  ChevronRight,
  Eye,
  CheckCircle,
  XCircle,
  Trash2,
  MoreVertical,
} from "lucide-react"
import { ForumsFilters } from "./forums-filters"
import { ViewPostDialog } from "./view-post-dialog"
import { RejectPostDialog } from "./reject-post-dialog"
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

const mockPosts = Array.from({ length: 30 }, (_, i) => ({
  id: i + 1,
  createdOn: new Date(2024, Math.floor(Math.random() * 12), Math.floor(Math.random() * 28) + 1).toISOString(),
  alumniProfile: `https://i.pravatar.cc/150?img=${(i % 70) + 1}`,
  alumniName: `Alumni ${i + 1}`,
  contact: `+1 ${Math.floor(Math.random() * 900) + 100}-${Math.floor(Math.random() * 900) + 100}-${Math.floor(Math.random() * 9000) + 1000}`,
  postTitle: `Discussion Topic ${i + 1}`,
  postDescription: `This is a detailed description of the forum post ${i + 1}. It contains valuable insights and questions from the alumni community.`,
  labels: ["Career", "Networking", "Events"][Math.floor(Math.random() * 3)],
  actionTakenOn: new Date(2024, Math.floor(Math.random() * 12), Math.floor(Math.random() * 28) + 1).toISOString(),
  status: ["Pending", "Approved", "Rejected", "Post Deleted", "Removed by Admin"][Math.floor(Math.random() * 5)] as
    | "Pending"
    | "Approved"
    | "Rejected"
    | "Post Deleted"
    | "Removed by Admin",
  commentsCount: Math.floor(Math.random() * 20),
}))

const ITEMS_PER_PAGE = 10

export function ForumsTable() {
  const router = useRouter()
  const [searchQuery, setSearchQuery] = useState("")
  const [showFilters, setShowFilters] = useState(false)
  const [selectedFilters, setSelectedFilters] = useState<{
    statuses: string[]
    dateFrom: Date | undefined
    dateTo: Date | undefined
  }>({
    statuses: [],
    dateFrom: undefined,
    dateTo: undefined,
  })
  const [currentPage, setCurrentPage] = useState(1)
  const [selectedPost, setSelectedPost] = useState<(typeof mockPosts)[0] | null>(null)
  const [rejectingPost, setRejectingPost] = useState<(typeof mockPosts)[0] | null>(null)
  const [removingPost, setRemovingPost] = useState<(typeof mockPosts)[0] | null>(null)
  const [removeRemarks, setRemoveRemarks] = useState("")

  const filteredPosts = useMemo(() => {
    return mockPosts.filter((post) => {
      const matchesSearch =
        post.alumniName.toLowerCase().includes(searchQuery.toLowerCase()) ||
        post.postTitle.toLowerCase().includes(searchQuery.toLowerCase())

      const matchesStatus = selectedFilters.statuses.length === 0 || selectedFilters.statuses.includes(post.status)

      const actionDate = new Date(post.actionTakenOn)
      const matchesDateFrom = !selectedFilters.dateFrom || actionDate >= selectedFilters.dateFrom
      const matchesDateTo = !selectedFilters.dateTo || actionDate <= selectedFilters.dateTo

      return matchesSearch && matchesStatus && matchesDateFrom && matchesDateTo
    })
  }, [searchQuery, selectedFilters])

  const handleRemoveFilter = (type: "statuses", value: string) => {
    setSelectedFilters((prev) => ({
      ...prev,
      [type]: prev[type].filter((v) => v !== value),
    }))
  }

  const handleRemoveDateFilter = (type: "dateFrom" | "dateTo") => {
    setSelectedFilters((prev) => ({
      ...prev,
      [type]: undefined,
    }))
  }

  const handleClearAllFilters = () => {
    setSelectedFilters({
      statuses: [],
      dateFrom: undefined,
      dateTo: undefined,
    })
  }

  const hasActiveFilters =
    selectedFilters.statuses.length > 0 ||
    selectedFilters.dateFrom !== undefined ||
    selectedFilters.dateTo !== undefined

  const handleApprove = (postId: number) => {
    console.log(`[v0] Approving post ${postId}`)
    alert(`Post ${postId} has been approved`)
  }

  const handleReject = (postId: number, remarks: string) => {
    console.log(`[v0] Rejecting post ${postId} with remarks:`, remarks)
    alert(`Post ${postId} has been rejected`)
    setRejectingPost(null)
  }

  const handleRemove = (postId: number, remarks: string) => {
    console.log(`[v0] Removing post ${postId} with remarks:`, remarks)
    alert(`Post ${postId} has been removed`)
    setRemovingPost(null)
    setRemoveRemarks("")
  }

  const getStatusBadge = (status: "Pending" | "Approved" | "Rejected" | "Post Deleted" | "Removed by Admin") => {
    switch (status) {
      case "Pending":
        return (
          <Badge variant="secondary" className="font-semibold bg-yellow-500 text-white hover:bg-yellow-600">
            Pending
          </Badge>
        )
      case "Approved":
        return (
          <Badge variant="default" className="font-semibold bg-green-600 hover:bg-green-700">
            Approved
          </Badge>
        )
      case "Rejected":
        return (
          <Badge variant="destructive" className="font-semibold">
            Rejected
          </Badge>
        )
      case "Post Deleted":
        return (
          <Badge variant="secondary" className="font-semibold bg-gray-500 text-white hover:bg-gray-600">
            Post Deleted
          </Badge>
        )
      case "Removed by Admin":
        return (
          <Badge variant="secondary" className="font-semibold bg-orange-600 text-white hover:bg-orange-700">
            Removed by Admin
          </Badge>
        )
    }
  }

  return (
    <Card className="p-6">
      <div className="space-y-4 mb-6">
        <div className="flex flex-col sm:flex-row gap-3">
          <div className="relative flex-1">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search by alumni name or post title..."
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

        {showFilters && <ForumsFilters selectedFilters={selectedFilters} onFiltersChange={setSelectedFilters} />}

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
            {selectedFilters.dateFrom && (
              <Badge variant="secondary" className="gap-1">
                From: {format(selectedFilters.dateFrom, "MMM dd, yyyy")}
                <button onClick={() => handleRemoveDateFilter("dateFrom")} className="ml-1 hover:text-destructive">
                  <X className="h-3 w-3" />
                </button>
              </Badge>
            )}
            {selectedFilters.dateTo && (
              <Badge variant="secondary" className="gap-1">
                To: {format(selectedFilters.dateTo, "MMM dd, yyyy")}
                <button onClick={() => handleRemoveDateFilter("dateTo")} className="ml-1 hover:text-destructive">
                  <X className="h-3 w-3" />
                </button>
              </Badge>
            )}
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

      <div className="border rounded-lg overflow-hidden">
        <div className="overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow className="bg-primary hover:bg-primary">
                <TableHead className="font-bold text-primary-foreground">Created On</TableHead>
                <TableHead className="font-bold text-primary-foreground">Alumni</TableHead>
                <TableHead className="font-bold text-primary-foreground">Contact</TableHead>
                <TableHead className="font-bold text-primary-foreground">View Post</TableHead>
                <TableHead className="font-bold text-primary-foreground">Action Taken On</TableHead>
                <TableHead className="font-bold text-primary-foreground">Status</TableHead>
                <TableHead className="font-bold text-primary-foreground text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filteredPosts
                .slice((currentPage - 1) * ITEMS_PER_PAGE, currentPage * ITEMS_PER_PAGE)
                .map((post, index) => (
                  <TableRow
                    key={post.id}
                    className={index % 2 === 0 ? "bg-background hover:bg-muted/50" : "bg-muted/20 hover:bg-muted/50"}
                  >
                    <TableCell className="whitespace-nowrap">
                      {new Date(post.createdOn).toLocaleDateString("en-US", {
                        year: "numeric",
                        month: "short",
                        day: "numeric",
                      })}
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-3">
                        <Avatar className="h-10 w-10 border-2 border-border">
                          <AvatarImage src={post.alumniProfile || "/placeholder.svg"} alt={post.alumniName} />
                          <AvatarFallback>{post.alumniName.charAt(0)}</AvatarFallback>
                        </Avatar>
                        <span className="font-medium">{post.alumniName}</span>
                      </div>
                    </TableCell>
                    <TableCell className="whitespace-nowrap">{post.contact}</TableCell>
                    <TableCell>
                      <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setSelectedPost(post)}
                        className="font-semibold"
                      >
                        <Eye className="mr-2 h-4 w-4" />
                        View
                      </Button>
                    </TableCell>
                    <TableCell className="whitespace-nowrap">
                      {new Date(post.actionTakenOn).toLocaleDateString("en-US", {
                        year: "numeric",
                        month: "short",
                        day: "numeric",
                      })}
                    </TableCell>
                    <TableCell>{getStatusBadge(post.status)}</TableCell>
                    <TableCell className="text-right">
                      {post.status !== "Post Deleted" && post.status !== "Removed by Admin" && (
                        <DropdownMenu>
                          <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="icon" className="h-8 w-8">
                              <MoreVertical className="h-4 w-4" />
                            </Button>
                          </DropdownMenuTrigger>
                          <DropdownMenuContent align="end" className="w-48">
                            {post.status === "Pending" && (
                              <>
                                <DropdownMenuItem
                                  onClick={() => handleApprove(post.id)}
                                  className="hover:bg-primary hover:text-white cursor-pointer"
                                >
                                  <CheckCircle className="mr-2 h-4 w-4" />
                                  Approve
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                  onClick={() => setRejectingPost(post)}
                                  className="text-destructive hover:bg-destructive hover:text-white cursor-pointer"
                                >
                                  <XCircle className="mr-2 h-4 w-4" />
                                  Reject
                                </DropdownMenuItem>
                              </>
                            )}

                            {post.status === "Approved" && (
                              <DropdownMenuItem
                                onClick={() => setRemovingPost(post)}
                                className="text-destructive hover:bg-destructive hover:text-white cursor-pointer"
                              >
                                <Trash2 className="mr-2 h-4 w-4" />
                                Remove
                              </DropdownMenuItem>
                            )}

                            {post.status === "Rejected" && (
                              <>
                                <DropdownMenuItem disabled className="opacity-50 cursor-not-allowed">
                                  <CheckCircle className="mr-2 h-4 w-4" />
                                  Approve
                                </DropdownMenuItem>
                                <DropdownMenuItem disabled className="opacity-50 cursor-not-allowed">
                                  <XCircle className="mr-2 h-4 w-4" />
                                  Reject
                                </DropdownMenuItem>
                              </>
                            )}
                          </DropdownMenuContent>
                        </DropdownMenu>
                      )}
                    </TableCell>
                  </TableRow>
                ))}
            </TableBody>
          </Table>
        </div>
      </div>

      <div className="flex items-center justify-between mt-6">
        <p className="text-sm text-muted-foreground">
          Showing {filteredPosts.slice((currentPage - 1) * ITEMS_PER_PAGE, currentPage * ITEMS_PER_PAGE).length} of{" "}
          {filteredPosts.length} posts
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
            Page {currentPage} of {Math.ceil(filteredPosts.length / ITEMS_PER_PAGE)}
          </span>
          <Button
            variant="outline"
            size="sm"
            onClick={() =>
              setCurrentPage((prev) => Math.min(Math.ceil(filteredPosts.length / ITEMS_PER_PAGE), prev + 1))
            }
            disabled={currentPage === Math.ceil(filteredPosts.length / ITEMS_PER_PAGE)}
          >
            Next
            <ChevronRight className="h-4 w-4 ml-1" />
          </Button>
        </div>
      </div>

      {selectedPost && (
        <ViewPostDialog
          post={selectedPost}
          onClose={() => setSelectedPost(null)}
          onViewComments={(postId) => router.push(`/admin/forums/${postId}/comments`)}
        />
      )}

      {rejectingPost && (
        <RejectPostDialog
          post={rejectingPost}
          onReject={(remarks) => handleReject(rejectingPost.id, remarks)}
          onClose={() => setRejectingPost(null)}
        />
      )}

      <AlertDialog open={!!removingPost} onOpenChange={() => setRemovingPost(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Remove Post</AlertDialogTitle>
            <AlertDialogDescription>
              Removing this post will make it no longer available to any of the alumni. Please provide a reason for
              removal.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <div className="py-4">
            <textarea
              className="w-full min-h-[100px] p-3 border rounded-md resize-none focus:outline-none focus:ring-2 focus:ring-primary"
              placeholder="Enter remarks for removing this post..."
              value={removeRemarks}
              onChange={(e) => setRemoveRemarks(e.target.value)}
            />
          </div>
          <AlertDialogFooter>
            <AlertDialogCancel onClick={() => setRemoveRemarks("")}>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={() => removingPost && handleRemove(removingPost.id, removeRemarks)}
              className="bg-destructive hover:bg-destructive/90"
              disabled={!removeRemarks.trim()}
            >
              Remove Post
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </Card>
  )
}
