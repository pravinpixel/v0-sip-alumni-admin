"use client"

import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { MessageSquare } from "lucide-react"
import { Separator } from "@/components/ui/separator"

interface ViewPostDialogProps {
  post: {
    id: number
    postTitle: string
    postDescription: string
    labels: string
    commentsCount: number
  }
  onClose: () => void
  onViewComments: (postId: number) => void
}

export function ViewPostDialog({ post, onClose, onViewComments }: ViewPostDialogProps) {
  return (
    <Dialog open={true} onOpenChange={onClose}>
      <DialogContent className="max-w-2xl max-h-[85vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="text-2xl font-bold text-primary">Post Details</DialogTitle>
        </DialogHeader>
        <div className="space-y-6">
          {/* Post Title */}
          <div className="space-y-3">
            <h3 className="text-sm font-bold text-foreground uppercase tracking-wide">Post Title</h3>
            <div className="bg-muted/50 p-4 rounded-lg border border-border">
              <p className="text-xl font-semibold text-foreground leading-relaxed">{post.postTitle}</p>
            </div>
          </div>

          <Separator />

          {/* Post Description */}
          <div className="space-y-3">
            <h3 className="text-sm font-bold text-foreground uppercase tracking-wide">Post Description</h3>
            <div className="bg-muted/50 p-4 rounded-lg border border-border">
              <p className="text-base leading-relaxed text-foreground">{post.postDescription}</p>
            </div>
          </div>

          <Separator />

          {/* Labels */}
          <div className="space-y-3">
            <h3 className="text-sm font-bold text-foreground uppercase tracking-wide">Labels</h3>
            <div className="flex flex-wrap gap-2">
              <Badge variant="secondary" className="text-sm px-4 py-2 font-semibold">
                {post.labels}
              </Badge>
            </div>
          </div>

          <Separator />

          {/* View Comments Button */}
          <div className="pt-2">
            <Button
              onClick={() => {
                onClose()
                onViewComments(post.id)
              }}
              className="w-full h-12 font-bold text-base"
              size="lg"
            >
              <MessageSquare className="mr-2 h-5 w-5" />
              View Comments ({post.commentsCount})
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  )
}
