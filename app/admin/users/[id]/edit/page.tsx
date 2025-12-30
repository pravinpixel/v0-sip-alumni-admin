"use client"

import type React from "react"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { Card } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Button } from "@/components/ui/button"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { ArrowLeft, Save, Eye, EyeOff } from "lucide-react"

const availableRoles = ["Super Admin", "Content Moderator", "Viewer"]

const mockUsers = [
  {
    id: "USER001",
    name: "John Doe",
    email: "john.doe@sipadmin.com",
    role: "Super Admin",
  },
  {
    id: "USER002",
    name: "Jane Smith",
    email: "jane.smith@sipadmin.com",
    role: "Content Moderator",
  },
]

export default function EditUserPage({ params }: { params: { id: string } }) {
  const router = useRouter()
  const [userId, setUserId] = useState("")
  const [userName, setUserName] = useState("")
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [confirmPassword, setConfirmPassword] = useState("")
  const [selectedRole, setSelectedRole] = useState("")
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirmPassword, setShowConfirmPassword] = useState(false)

  useEffect(() => {
    const user = mockUsers.find((u) => u.id === params.id)
    if (user) {
      setUserId(user.id)
      setUserName(user.name)
      setEmail(user.email)
      setSelectedRole(user.role)
    }
  }, [params.id])

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()

    if (!userId || !userName || !email || !selectedRole) {
      alert("Please fill in all required fields")
      return
    }

    if (password && password !== confirmPassword) {
      alert("Passwords do not match")
      return
    }

    console.log("[v0] Updating user:", { userId, userName, email, selectedRole })
    alert("User updated successfully!")
    router.push("/admin/users")
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => router.back()}>
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <div>
          <h1 className="text-3xl font-bold text-foreground">Edit User</h1>
          <p className="text-muted-foreground mt-2">Update user details and permissions</p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        {/* User Details Card */}
        <Card className="p-6">
          <h2 className="text-xl font-bold mb-6">User Details</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-2">
              <Label htmlFor="userId" className="font-semibold">
                User ID <span className="text-destructive">*</span>
              </Label>
              <Input id="userId" value={userId} disabled className="h-11 bg-muted" />
            </div>
            <div className="space-y-2">
              <Label htmlFor="userName" className="font-semibold">
                User Name <span className="text-destructive">*</span>
              </Label>
              <Input
                id="userName"
                placeholder="e.g., John Doe"
                value={userName}
                onChange={(e) => setUserName(e.target.value)}
                required
                className="h-11"
              />
            </div>
            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="email" className="font-semibold">
                Email ID <span className="text-destructive">*</span>
              </Label>
              <Input
                id="email"
                type="email"
                placeholder="e.g., john.doe@sipadmin.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="h-11"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="password" className="font-semibold">
                New Password <span className="text-muted-foreground text-xs">(Leave blank to keep current)</span>
              </Label>
              <div className="relative">
                <Input
                  id="password"
                  type={showPassword ? "text" : "password"}
                  placeholder="Enter new password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="h-11 pr-10"
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  className="absolute right-0 top-0 h-11 w-11"
                  onClick={() => setShowPassword(!showPassword)}
                >
                  {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </Button>
              </div>
            </div>
            <div className="space-y-2">
              <Label htmlFor="confirmPassword" className="font-semibold">
                Confirm New Password
              </Label>
              <div className="relative">
                <Input
                  id="confirmPassword"
                  type={showConfirmPassword ? "text" : "password"}
                  placeholder="Re-enter new password"
                  value={confirmPassword}
                  onChange={(e) => setConfirmPassword(e.target.value)}
                  className="h-11 pr-10"
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  className="absolute right-0 top-0 h-11 w-11"
                  onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                >
                  {showConfirmPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </Button>
              </div>
            </div>
            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="role" className="font-semibold">
                Select Role <span className="text-destructive">*</span>
              </Label>
              <Select value={selectedRole} onValueChange={setSelectedRole} required>
                <SelectTrigger className="h-11">
                  <SelectValue placeholder="Select a role" />
                </SelectTrigger>
                <SelectContent>
                  {availableRoles.map((role) => (
                    <SelectItem key={role} value={role}>
                      {role}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </div>
        </Card>

        {/* Action Buttons */}
        <div className="flex justify-end gap-3">
          <Button type="button" variant="outline" onClick={() => router.back()} className="h-11 font-semibold">
            Cancel
          </Button>
          <Button type="submit" className="h-11 font-semibold">
            <Save className="mr-2 h-4 w-4" />
            Update User
          </Button>
        </div>
      </form>
    </div>
  )
}
