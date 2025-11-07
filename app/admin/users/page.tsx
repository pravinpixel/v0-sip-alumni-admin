import { UsersTable } from "@/components/admin/users-table"

export default function UsersPage() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold text-foreground">User Management</h1>
        <p className="text-muted-foreground mt-2">Manage admin users and their access permissions</p>
      </div>

      <UsersTable />
    </div>
  )
}
