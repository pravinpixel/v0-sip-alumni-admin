import { Skeleton } from "@/components/ui/skeleton"
import { Card } from "@/components/ui/card"

export default function Loading() {
  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div className="space-y-2">
          <Skeleton className="h-8 w-64" />
          <Skeleton className="h-4 w-96" />
        </div>
        <Skeleton className="h-10 w-48" />
      </div>

      <Card className="p-6">
        <div className="space-y-4">
          <Skeleton className="h-11 w-full" />
          <Skeleton className="h-96 w-full" />
        </div>
      </Card>
    </div>
  )
}
