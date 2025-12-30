"use client"

import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Card } from "@/components/ui/card"
import { ScrollArea } from "@/components/ui/scroll-area"
import { AlertTriangle, Calendar, MapPin } from "lucide-react"

interface Report {
  id: number
  reporterName: string
  reporterProfile: string
  center: string
  reason: string
  remarks: string
  reportedOn: string
}

interface ViewReportsDialogProps {
  post: {
    id: number
    postTitle: string
    alumniName: string
    reportCount: number
    reports: Report[]
  }
  onClose: () => void
}

export function ViewReportsDialog({ post, onClose }: ViewReportsDialogProps) {
  return (
    <Dialog open={true} onOpenChange={onClose}>
      <DialogContent className="max-w-3xl max-h-[85vh] flex flex-col">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-3 text-xl">
            <AlertTriangle className="h-6 w-6 text-orange-600" />
            <span>Post Reports ({post.reportCount})</span>
          </DialogTitle>
        </DialogHeader>

        <div className="space-y-4">
          <div className="bg-muted/50 p-4 rounded-lg border">
            <p className="font-bold text-lg">{post.postTitle}</p>
            <p className="text-sm text-muted-foreground mt-2">Posted by {post.alumniName}</p>
          </div>

          <div>
            <p className="font-bold text-base mb-3">Reports Submitted by Alumni</p>
            <ScrollArea className="h-[400px] pr-4">
              <div className="space-y-3">
                {post.reports.map((report) => (
                  <Card key={report.id} className="p-4 border-l-4 border-l-orange-500 bg-orange-50/50">
                    <div className="flex items-start gap-3 mb-3">
                      <Avatar className="h-10 w-10 border-2 border-border">
                        <AvatarImage src={report.reporterProfile || "/placeholder.svg"} alt={report.reporterName} />
                        <AvatarFallback>{report.reporterName.charAt(0)}</AvatarFallback>
                      </Avatar>
                      <div className="flex-1">
                        <span className="font-semibold text-base">{report.reporterName}</span>
                        <div className="flex items-center gap-4 text-xs text-muted-foreground mt-1">
                          <div className="flex items-center gap-1">
                            <MapPin className="h-3 w-3" />
                            <span>{report.center}</span>
                          </div>
                          <div className="flex items-center gap-1">
                            <Calendar className="h-3 w-3" />
                            <span>
                              {new Date(report.reportedOn).toLocaleDateString("en-US", {
                                year: "numeric",
                                month: "short",
                                day: "numeric",
                              })}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div className="ml-13">
                      <p className="text-sm text-muted-foreground leading-relaxed">{report.remarks}</p>
                    </div>
                  </Card>
                ))}
              </div>
            </ScrollArea>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  )
}
