"use client"

import type React from "react"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { Card } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Button } from "@/components/ui/button"
import { Checkbox } from "@/components/ui/checkbox"
import { ArrowLeft, Save } from "lucide-react"

const modules = [
  {
    name: "Dashboard",
    key: "dashboard",
    permissions: [{ label: "Access", key: "access" }],
  },
  {
    name: "Directory",
    key: "directory",
    permissions: [
      { label: "All", key: "all" },
      { label: "View", key: "view" },
      { label: "View Profile Pic", key: "viewProfilePic" },
      { label: "Block", key: "block" },
      { label: "Remove", key: "remove" },
    ],
  },
  {
    name: "Forums",
    key: "forums",
    permissions: [
      { label: "All", key: "all" },
      { label: "View", key: "view" },
      { label: "Approve", key: "approve" },
      { label: "Reject", key: "reject" },
      { label: "Remove", key: "remove" },
      { label: "Delete Comments", key: "deleteComments" },
    ],
  },
]

// Mock data - in real app, this would come from API
const mockRole = {
  id: "ROLE002",
  name: "Content Moderator",
  permissions: {
    dashboard: { access: true },
    directory: { all: false, view: true, viewProfilePic: true, block: false, remove: false },
    forums: { all: false, view: true, approve: true, reject: true, remove: false, deleteComments: true },
  },
}

export default function EditRolePage({ params }: { params: { id: string } }) {
  const router = useRouter()
  const [roleId, setRoleId] = useState("")
  const [roleName, setRoleName] = useState("")
  const [permissions, setPermissions] = useState<Record<string, Record<string, boolean>>>({
    dashboard: { access: false },
    directory: { all: false, view: false, viewProfilePic: false, block: false, remove: false },
    forums: { all: false, view: false, approve: false, reject: false, remove: false, deleteComments: false },
  })

  useEffect(() => {
    // Load role data - in real app, fetch from API
    setRoleId(mockRole.id)
    setRoleName(mockRole.name)
    setPermissions(mockRole.permissions)
  }, [params.id])

  const handlePermissionChange = (moduleKey: string, permissionKey: string, checked: boolean) => {
    setPermissions((prev) => {
      const updated = { ...prev }

      if (permissionKey === "all" && checked) {
        const allPermissions = modules.find((m) => m.key === moduleKey)?.permissions || []
        updated[moduleKey] = allPermissions.reduce(
          (acc, perm) => {
            acc[perm.key] = true
            return acc
          },
          {} as Record<string, boolean>,
        )
      } else if (permissionKey === "all" && !checked) {
        const allPermissions = modules.find((m) => m.key === moduleKey)?.permissions || []
        updated[moduleKey] = allPermissions.reduce(
          (acc, perm) => {
            acc[perm.key] = false
            return acc
          },
          {} as Record<string, boolean>,
        )
      } else {
        updated[moduleKey] = {
          ...updated[moduleKey],
          [permissionKey]: checked,
        }

        if (!checked && updated[moduleKey].all) {
          updated[moduleKey].all = false
        }
      }

      return updated
    })
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()

    if (!roleId || !roleName) {
      alert("Please fill in all required fields")
      return
    }

    console.log("[v0] Updating role:", { roleId, roleName, permissions })
    alert("Role updated successfully!")
    router.push("/admin/roles")
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => router.back()}>
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <div>
          <h1 className="text-3xl font-bold text-foreground">Edit Role</h1>
          <p className="text-muted-foreground mt-2">Update role details and permissions</p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Role Details Card */}
        <Card className="p-6">
          <h2 className="text-xl font-bold mb-6">Role Details</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-2">
              <Label htmlFor="roleId" className="font-semibold">
                Role ID <span className="text-destructive">*</span>
              </Label>
              <Input
                id="roleId"
                placeholder="e.g., ROLE001"
                value={roleId}
                onChange={(e) => setRoleId(e.target.value)}
                required
                disabled
                className="h-11 bg-muted"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="roleName" className="font-semibold">
                Role Name <span className="text-destructive">*</span>
              </Label>
              <Input
                id="roleName"
                placeholder="e.g., Content Moderator"
                value={roleName}
                onChange={(e) => setRoleName(e.target.value)}
                required
                className="h-11"
              />
            </div>
          </div>
        </Card>

        {/* Permissions Card */}
        <Card className="p-6">
          <h2 className="text-xl font-bold mb-6">Module Permissions</h2>
          <div className="space-y-6">
            {modules.map((module) => (
              <div key={module.key} className="border rounded-lg p-5 bg-muted/20">
                <h3 className="text-lg font-bold mb-4 text-primary">{module.name}</h3>
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                  {module.permissions.map((permission) => (
                    <div key={permission.key} className="flex items-center space-x-2">
                      <Checkbox
                        id={`${module.key}-${permission.key}`}
                        checked={permissions[module.key]?.[permission.key] || false}
                        onCheckedChange={(checked) =>
                          handlePermissionChange(module.key, permission.key, checked as boolean)
                        }
                      />
                      <Label htmlFor={`${module.key}-${permission.key}`} className="text-sm font-medium cursor-pointer">
                        {permission.label}
                      </Label>
                    </div>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </Card>

        {/* Action Buttons */}
        <div className="flex justify-end gap-3">
          <Button type="button" variant="outline" onClick={() => router.back()} className="h-11 font-semibold">
            Cancel
          </Button>
          <Button type="submit" className="h-11 font-semibold">
            <Save className="mr-2 h-4 w-4" />
            Update Role
          </Button>
        </div>
      </form>
    </div>
  )
}
