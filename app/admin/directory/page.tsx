import { DirectoryTable } from "@/components/admin/directory-table"

export default function DirectoryPage() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold text-foreground">Alumni Directory</h1>
        <p className="text-muted-foreground mt-1">Manage and view all alumni profiles</p>
      </div>

      <DirectoryTable />
    </div>
  )
}
