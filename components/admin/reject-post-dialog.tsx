"use client"

import { useState } from "react"
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Textarea } from "@/components/ui/textarea"
import { Label } from "@/components/ui/label"

interface RejectPostDialogProps {
  post: {
    id: number
    postTitle: string
  }
  onReject: (remarks: string) => void
  onClose: () => void
}

export function RejectPostDialog({ post, onReject, onClose }: RejectPostDialogProps) {
  const [remarks, setRemarks] = useState("")

  const handleSubmit = () => {
    if (remarks.trim()) {
      onReject(remarks)
    }
  }

  return (
    <Dialog open={true} onOpenChange={onClose}>
      <DialogContent className="max-w-md">
        <DialogHeader>
          <DialogTitle className="text-xl font-bold">Reject Post</DialogTitle>
        </DialogHeader>
        <div className="space-y-4">
          <div>
            <p className="text-sm text-muted-foreground mb-2">Post: {post.postTitle}</p>
          </div>
          <div className="space-y-2">
            <Label htmlFor="remarks" className="font-semibold">
              Rejection Remarks *
            </Label>
            <Textarea
              id="remarks"
              placeholder="Please provide a reason for rejecting this post..."
              value={remarks}
              onChange={(e) => setRemarks(e.target.value)}
              rows={4}
              className="resize-none"
            />
          </div>
        </div>
        <DialogFooter>
          <Button variant="outline" onClick={onClose}>
            Cancel
          </Button>
          <Button onClick={handleSubmit} disabled={!remarks.trim()} className="bg-destructive hover:bg-destructive/90">
            Reject Post
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}
