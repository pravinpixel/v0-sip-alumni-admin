import { RolesTable } from "@/components/admin/roles-table"

export default function RolesPage() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold text-foreground">Roles Management</h1>
        <p className="text-muted-foreground mt-2">Manage admin roles and permissions</p>
      </div>

      <RolesTable />
    </div>
  )
}
