"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Card } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Badge } from "@/components/ui/badge"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { ArrowLeft, Search, Trash2, Heart, MessageSquare, Eye, Pin, ChevronDown, ChevronRight } from "lucide-react"
import { Separator } from "@/components/ui/separator"
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

// Mock post data
const mockPost = {
  id: 1,
  title: "Discussion Topic 1",
  description:
    "This is a detailed description of the forum post. It contains valuable insights and questions from the alumni community.",
  labels: ["Career", "Networking"],
  likes: 142,
  comments: 15,
  views: 1247,
  isPinned: true,
}

const mockComments = Array.from({ length: 15 }, (_, i) => ({
  id: i + 1,
  alumniProfile: `https://i.pravatar.cc/150?img=${(i % 70) + 1}`,
  alumniName: `Alumni ${i + 1}`,
  commentText: `This is a thoughtful comment from alumni member ${i + 1}. It provides valuable insights and contributes to the discussion.`,
  timeCommented: new Date(
    2024,
    Math.floor(Math.random() * 12),
    Math.floor(Math.random() * 28) + 1,
    Math.floor(Math.random() * 24),
    Math.floor(Math.random() * 60),
  ).toISOString(),
  threadsCount: Math.floor(Math.random() * 8),
  threads: Array.from({ length: Math.floor(Math.random() * 8) }, (_, j) => ({
    id: `${i + 1}-${j + 1}`,
    alumniProfile: `https://i.pravatar.cc/150?img=${((i + j) % 70) + 1}`,
    alumniName: `Alumni ${((i + j) % 30) + 1}`,
    commentText: `This is a reply to comment ${i + 1}. Great point!`,
    timeCommented: new Date(
      2024,
      Math.floor(Math.random() * 12),
      Math.floor(Math.random() * 28) + 1,
      Math.floor(Math.random() * 24),
      Math.floor(Math.random() * 60),
    ).toISOString(),
  })),
}))

export default function CommentsPage({ params }: { params: { id: string } }) {
  const router = useRouter()
  const [searchQuery, setSearchQuery] = useState("")
  const [deletingComment, setDeletingComment] = useState<(typeof mockComments)[0] | null>(null)
  const [expandedThreads, setExpandedThreads] = useState<Set<number>>(new Set())

  const filteredComments = mockComments.filter(
    (comment) =>
      comment.alumniName.toLowerCase().includes(searchQuery.toLowerCase()) ||
      comment.commentText.toLowerCase().includes(searchQuery.toLowerCase()),
  )

  const handleDeleteComment = (commentId: number) => {
    console.log(`[v0] Deleting comment ${commentId}`)
    alert(`Comment ${commentId} has been deleted`)
    setDeletingComment(null)
  }

  const toggleThread = (commentId: number) => {
    setExpandedThreads((prev) => {
      const newSet = new Set(prev)
      if (newSet.has(commentId)) {
        newSet.delete(commentId)
      } else {
        newSet.add(commentId)
      }
      return newSet
    })
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-start gap-4">
        <Button variant="outline" size="icon" onClick={() => router.back()} className="shrink-0">
          <ArrowLeft className="h-4 w-4" />
        </Button>
        <div className="flex-1">
          <h1 className="text-3xl font-bold text-foreground">
            {mockPost.title.split(" ")[0]} {mockPost.title.split(" ")[1]} connections with the Alumni Peoples
          </h1>
          <p className="text-muted-foreground mt-1">View and manage all comments on this post</p>
        </div>
      </div>

      <Card className="p-6 bg-gradient-to-br from-background to-muted/20 border-2">
        <div className="space-y-5">
          <div>
            <h3 className="text-sm font-bold text-foreground uppercase tracking-wide mb-3">Post Title</h3>
            <p className="text-xl font-semibold text-foreground">{mockPost.title}</p>
          </div>

          <Separator />

          <div>
            <h3 className="text-sm font-bold text-foreground uppercase tracking-wide mb-3">Post Description</h3>
            <p className="text-base leading-relaxed text-foreground">{mockPost.description}</p>
          </div>

          <Separator />

          <div>
            <h3 className="text-sm font-bold text-foreground uppercase tracking-wide mb-3">Labels</h3>
            <div className="flex flex-wrap gap-2">
              {mockPost.labels.map((label) => (
                <Badge key={label} variant="secondary" className="text-sm px-3 py-1 font-semibold">
                  {label}
                </Badge>
              ))}
            </div>
          </div>

          <Separator />

          <div className="flex flex-wrap items-center gap-6 pt-2">
            {/* Likes */}
            <div className="flex items-center gap-3">
              <div className="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-red-100 to-pink-100 dark:from-red-900/30 dark:to-pink-900/30 shadow-sm">
                <Heart className="h-6 w-6 text-red-600 dark:text-red-400 fill-red-600 dark:fill-red-400" />
              </div>
              <div>
                <p className="text-2xl font-bold text-foreground">{mockPost.likes}</p>
                <p className="text-xs text-muted-foreground font-medium">Likes</p>
              </div>
            </div>

            {/* Comments */}
            <div className="flex items-center gap-3">
              <div className="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 shadow-sm">
                <MessageSquare className="h-6 w-6 text-blue-600 dark:text-blue-400 fill-blue-600 dark:fill-blue-400" />
              </div>
              <div>
                <p className="text-2xl font-bold text-foreground">{mockPost.comments}</p>
                <p className="text-xs text-muted-foreground font-medium">Comments</p>
              </div>
            </div>

            {/* Views */}
            <div className="flex items-center gap-3">
              <div className="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 shadow-sm">
                <Eye className="h-6 w-6 text-green-600 dark:text-green-400" />
              </div>
              <div>
                <p className="text-2xl font-bold text-foreground">{mockPost.views}</p>
                <p className="text-xs text-muted-foreground font-medium">Views</p>
              </div>
            </div>

            {/* Pinned - Always displayed with count */}
            <div className="flex items-center gap-3">
              <div className="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-yellow-100 to-amber-100 dark:from-yellow-900/30 dark:to-amber-900/30 shadow-sm">
                <Pin className="h-6 w-6 text-yellow-600 dark:text-yellow-500 fill-yellow-600 dark:fill-yellow-500" />
              </div>
              <div>
                <p className="text-2xl font-bold text-foreground">{mockPost.isPinned ? "1" : "0"}</p>
                <p className="text-xs text-muted-foreground font-medium">Pinned</p>
              </div>
            </div>
          </div>
        </div>
      </Card>

      {/* Comments Section */}
      <Card className="p-6">
        <div className="space-y-6">
          <div className="flex items-center justify-between">
            <h2 className="text-xl font-bold">Comments ({filteredComments.length})</h2>
          </div>

          {/* Search Bar */}
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search comments..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="pl-10 h-11"
            />
          </div>

          <div className="border rounded-lg overflow-hidden">
            <div className="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow className="bg-primary hover:bg-primary">
                    <TableHead className="font-bold text-primary-foreground">Alumni Profile</TableHead>
                    <TableHead className="font-bold text-primary-foreground">Alumni Name</TableHead>
                    <TableHead className="font-bold text-primary-foreground">Comment</TableHead>
                    <TableHead className="font-bold text-primary-foreground">Time Commented</TableHead>
                    <TableHead className="font-bold text-primary-foreground">Threads</TableHead>
                    <TableHead className="font-bold text-primary-foreground text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {filteredComments.map((comment, index) => (
                    <>
                      <TableRow
                        key={comment.id}
                        className={
                          index % 2 === 0 ? "bg-background hover:bg-muted/50" : "bg-muted/20 hover:bg-muted/50"
                        }
                      >
                        <TableCell>
                          <Avatar className="h-10 w-10 border-2 border-border">
                            <AvatarImage src={comment.alumniProfile || "/placeholder.svg"} alt={comment.alumniName} />
                            <AvatarFallback>{comment.alumniName.charAt(0)}</AvatarFallback>
                          </Avatar>
                        </TableCell>
                        <TableCell className="font-medium">{comment.alumniName}</TableCell>
                        <TableCell className="max-w-md">
                          <p className="line-clamp-2">{comment.commentText}</p>
                        </TableCell>
                        <TableCell className="whitespace-nowrap">
                          {new Date(comment.timeCommented).toLocaleString("en-US", {
                            year: "numeric",
                            month: "short",
                            day: "numeric",
                            hour: "2-digit",
                            minute: "2-digit",
                          })}
                        </TableCell>
                        <TableCell>
                          {comment.threadsCount > 0 ? (
                            <Button
                              variant="outline"
                              size="sm"
                              onClick={() => toggleThread(comment.id)}
                              className="font-semibold"
                            >
                              {expandedThreads.has(comment.id) ? (
                                <ChevronDown className="mr-2 h-4 w-4" />
                              ) : (
                                <ChevronRight className="mr-2 h-4 w-4" />
                              )}
                              {comment.threadsCount} {comment.threadsCount === 1 ? "Reply" : "Replies"}
                            </Button>
                          ) : (
                            <span className="text-sm text-muted-foreground">No replies</span>
                          )}
                        </TableCell>
                        <TableCell className="text-right">
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => setDeletingComment(comment)}
                            className="font-semibold text-destructive hover:text-destructive"
                          >
                            <Trash2 className="mr-2 h-4 w-4" />
                            Delete
                          </Button>
                        </TableCell>
                      </TableRow>

                      {expandedThreads.has(comment.id) && comment.threads.length > 0 && (
                        <TableRow className="bg-muted/40">
                          <TableCell colSpan={6} className="p-0">
                            <div className="pl-16 pr-6 py-4 space-y-3">
                              <p className="text-sm font-semibold text-muted-foreground mb-3">
                                Replies to this comment:
                              </p>
                              {comment.threads.map((thread) => (
                                <div
                                  key={thread.id}
                                  className="flex items-start gap-3 p-3 bg-background rounded-lg border border-border"
                                >
                                  <Avatar className="h-8 w-8 border border-border shrink-0">
                                    <AvatarImage
                                      src={thread.alumniProfile || "/placeholder.svg"}
                                      alt={thread.alumniName}
                                    />
                                    <AvatarFallback>{thread.alumniName.charAt(0)}</AvatarFallback>
                                  </Avatar>
                                  <div className="flex-1 min-w-0">
                                    <div className="flex items-center gap-2 mb-1">
                                      <p className="text-sm font-semibold">{thread.alumniName}</p>
                                      <span className="text-xs text-muted-foreground">
                                        {new Date(thread.timeCommented).toLocaleString("en-US", {
                                          month: "short",
                                          day: "numeric",
                                          hour: "2-digit",
                                          minute: "2-digit",
                                        })}
                                      </span>
                                    </div>
                                    <p className="text-sm text-foreground">{thread.commentText}</p>
                                  </div>
                                </div>
                              ))}
                            </div>
                          </TableCell>
                        </TableRow>
                      )}
                    </>
                  ))}
                </TableBody>
              </Table>
            </div>
          </div>
        </div>
      </Card>

      {/* Delete Comment Alert Dialog */}
      <AlertDialog open={!!deletingComment} onOpenChange={() => setDeletingComment(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete Comment</AlertDialogTitle>
            <AlertDialogDescription>
              Are you sure you want to delete this comment? This action cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={() => deletingComment && handleDeleteComment(deletingComment.id)}
              className="bg-destructive hover:bg-destructive/90"
            >
              Delete Comment
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  )
}
