import { ForumsTable } from "@/components/admin/forums-table"

export default function ForumsPage() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold text-foreground">Forums</h1>
        <p className="text-muted-foreground mt-1">Manage community discussions and forum posts</p>
      </div>

      <ForumsTable />
    </div>
  )
}
