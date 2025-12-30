"use client"

import { useState, useMemo } from "react"
import { useRouter } from "next/navigation"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Calendar } from "@/components/ui/calendar"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { format } from "date-fns"
import {
  CalendarIcon,
  Users,
  UserCheck,
  UserX,
  TrendingUp,
  MessageSquare,
  Clock,
  XCircle,
  Heart,
  Eye,
  Pin,
  Trophy,
} from "lucide-react"

const mockAlumni = Array.from({ length: 50 }, (_, i) => ({
  id: i + 1,
  name: `Alumni ${i + 1}`,
  profilePicture: `https://i.pravatar.cc/150?img=${(i % 70) + 1}`,
  yearOfJoining: 2015 + Math.floor(Math.random() * 10),
  connections: Math.floor(Math.random() * 150) + 10,
  status: ["Active", "Blocked", "Removed"][Math.floor(Math.random() * 3)] as "Active" | "Blocked" | "Removed",
  createdOn: new Date(2024, Math.floor(Math.random() * 12), Math.floor(Math.random() * 28) + 1).toISOString(),
}))

const mockForumPosts = Array.from({ length: 30 }, (_, i) => ({
  id: i + 1,
  createdOn: new Date(2024, Math.floor(Math.random() * 12), Math.floor(Math.random() * 28) + 1).toISOString(),
  alumniProfile: `https://i.pravatar.cc/150?img=${(i % 70) + 1}`,
  alumniName: `Alumni ${i + 1}`,
  postTitle: `Discussion Topic ${i + 1}`,
  postDescription: `This is a detailed description of the forum post ${i + 1}. It contains valuable insights and questions from the alumni community.`,
  status: ["Approved", "Rejected", "Pending"][Math.floor(Math.random() * 3)] as "Approved" | "Rejected" | "Pending",
  likes: Math.floor(Math.random() * 100),
  comments: Math.floor(Math.random() * 50),
  views: Math.floor(Math.random() * 500),
  pinned: Math.random() > 0.8,
}))

const COLORS = ["#E2001D", "#F7C744", "#B1040E", "#FF6B6B", "#4ECDC4", "#45B7D1", "#FFA07A", "#98D8C8"]

export default function DashboardPage() {
  const router = useRouter()
  const [dateFrom, setDateFrom] = useState<Date>()
  const [dateTo, setDateTo] = useState<Date>()
  const [selectedYear, setSelectedYear] = useState<number | null>(null)

  const filteredAlumni = useMemo(() => {
    return mockAlumni.filter((alumni) => {
      const alumniDate = new Date(alumni.createdOn)
      const matchesDateFrom = !dateFrom || alumniDate >= dateFrom
      const matchesDateTo = !dateTo || alumniDate <= dateTo

      return matchesDateFrom && matchesDateTo
    })
  }, [dateFrom, dateTo])

  const stats = useMemo(() => {
    return {
      total: filteredAlumni.length,
      active: filteredAlumni.filter((a) => a.status === "Active").length,
      blocked: filteredAlumni.filter((a) => a.status === "Blocked").length,
    }
  }, [filteredAlumni])

  const topAlumni = useMemo(() => {
    return [...filteredAlumni].sort((a, b) => b.connections - a.connections).slice(0, 8)
  }, [filteredAlumni])

  const forumStats = useMemo(() => {
    return {
      active: mockForumPosts.filter((p) => p.status === "Approved").length,
      pending: mockForumPosts.filter((p) => p.status === "Pending").length,
      rejected: mockForumPosts.filter((p) => p.status === "Rejected").length,
    }
  }, [])

  const topEngagementPosts = useMemo(() => {
    return [...mockForumPosts]
      .sort((a, b) => {
        const aEngagement = a.likes + a.comments + a.views + (a.pinned ? 100 : 0)
        const bEngagement = b.likes + b.comments + b.views + (b.pinned ? 100 : 0)
        return bEngagement - aEngagement
      })
      .slice(0, 3)
  }, [])

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold text-foreground">Admin Dashboard</h1>
          <p className="text-muted-foreground mt-1">Directory analytics and insights</p>
        </div>

        {/* Date Range Filters */}
        <div className="flex gap-3">
          {/* Date From */}
          <Popover>
            <PopoverTrigger asChild>
              <Button variant="outline" className="h-11 w-[200px] justify-start text-left font-normal bg-transparent">
                <CalendarIcon className="mr-2 h-4 w-4" />
                {dateFrom ? format(dateFrom, "MMM dd, yyyy") : "From Date"}
              </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0" align="end">
              <Calendar mode="single" selected={dateFrom} onSelect={setDateFrom} initialFocus />
            </PopoverContent>
          </Popover>

          {/* Date To */}
          <Popover>
            <PopoverTrigger asChild>
              <Button variant="outline" className="h-11 w-[200px] justify-start text-left font-normal bg-transparent">
                <CalendarIcon className="mr-2 h-4 w-4" />
                {dateTo ? format(dateTo, "MMM dd, yyyy") : "To Date"}
              </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0" align="end">
              <Calendar mode="single" selected={dateTo} onSelect={setDateTo} initialFocus />
            </PopoverContent>
          </Popover>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <Card className="border-l-4 border-l-primary">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Directory (Alumni)</CardTitle>
            <Users className="h-5 w-5 text-primary" />
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold text-primary">{stats.total}</div>
            <p className="text-xs text-muted-foreground mt-1">All registered alumni</p>
          </CardContent>
        </Card>

        <Card className="border-l-4 border-l-green-600">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Active Alumni</CardTitle>
            <UserCheck className="h-5 w-5 text-green-600" />
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold text-green-600">{stats.active}</div>
            <p className="text-xs text-muted-foreground mt-1">Currently active members</p>
          </CardContent>
        </Card>

        <Card className="border-l-4 border-l-red-600">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Blocked Alumni</CardTitle>
            <UserX className="h-5 w-5 text-red-600" />
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold text-red-600">{stats.blocked}</div>
            <p className="text-xs text-muted-foreground mt-1">Temporarily blocked</p>
          </CardContent>
        </Card>
      </div>

      {/* Top Alumni Overview */}
      <Card className="p-6">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h2 className="text-2xl font-bold text-foreground">Top Alumni by Connections</h2>
            <p className="text-sm text-muted-foreground mt-1">Alumni with the most network connections</p>
          </div>
          <TrendingUp className="h-6 w-6 text-primary" />
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          {topAlumni.map((alumni) => (
            <Card key={alumni.id} className="p-4 hover:shadow-lg transition-shadow cursor-pointer">
              <div className="flex flex-col items-center text-center space-y-3">
                <Avatar className="h-16 w-16 border-4 border-primary/20">
                  <AvatarImage src={alumni.profilePicture || "/placeholder.svg"} alt={alumni.name} />
                  <AvatarFallback className="text-lg font-bold">{alumni.name.charAt(0)}</AvatarFallback>
                </Avatar>
                <div>
                  <h3 className="font-bold text-foreground">{alumni.name}</h3>
                  <p className="text-sm text-muted-foreground">Year {alumni.yearOfJoining}</p>
                </div>
                <div className="flex items-center gap-2 text-primary">
                  <Users className="h-4 w-4" />
                  <span className="text-lg font-bold">{alumni.connections}</span>
                  <span className="text-xs text-muted-foreground">connections</span>
                </div>
              </div>
            </Card>
          ))}
        </div>
      </Card>

      {/* Forum Section */}
      <Card className="p-6">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h2 className="text-2xl font-bold text-foreground">Forum Statistics</h2>
            <p className="text-sm text-muted-foreground mt-1">Overview of forum posts and engagement</p>
          </div>
          <MessageSquare className="h-6 w-6 text-primary" />
        </div>

        {/* Forum Stats Cards */}
        <div className="grid gap-6 md:grid-cols-3 mb-6">
          <Card className="border-l-4 border-l-green-600">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Active Posts</CardTitle>
              <MessageSquare className="h-5 w-5 text-green-600" />
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold text-green-600">{forumStats.active}</div>
              <p className="text-xs text-muted-foreground mt-1">Approved and published</p>
            </CardContent>
          </Card>

          <Card className="border-l-4 border-l-yellow-600">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Waiting for Approval</CardTitle>
              <Clock className="h-5 w-5 text-yellow-600" />
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold text-yellow-600">{forumStats.pending}</div>
              <p className="text-xs text-muted-foreground mt-1">Pending review</p>
            </CardContent>
          </Card>

          <Card className="border-l-4 border-l-red-600">
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Rejected Posts</CardTitle>
              <XCircle className="h-5 w-5 text-red-600" />
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold text-red-600">{forumStats.rejected}</div>
              <p className="text-xs text-muted-foreground mt-1">Not approved</p>
            </CardContent>
          </Card>
        </div>

        {/* Highest Engagement Posts */}
        <div className="space-y-4">
          <div className="flex items-center gap-3 mb-4">
            <Trophy className="h-6 w-6 text-primary" />
            <h3 className="text-xl font-bold text-foreground">Highest Engagement Posts</h3>
          </div>

          <div className="grid gap-6 md:grid-cols-1 lg:grid-cols-3">
            {topEngagementPosts.map((post) => (
              <Card key={post.id} className="p-6 bg-gradient-to-br from-primary/5 to-primary/10 border-primary/20">
                <div className="flex flex-col gap-4">
                  {/* Alumni Info */}
                  <div className="flex items-center gap-3">
                    <Avatar className="h-12 w-12 border-2 border-primary/20">
                      <AvatarImage src={post.alumniProfile || "/placeholder.svg"} alt={post.alumniName} />
                      <AvatarFallback className="text-sm font-bold">{post.alumniName.charAt(0)}</AvatarFallback>
                    </Avatar>
                    <div className="flex-1 min-w-0">
                      <h4 className="font-bold text-foreground truncate">{post.alumniName}</h4>
                      <p className="text-xs text-muted-foreground">
                        {format(new Date(post.createdOn), "MMM dd, yyyy")}
                      </p>
                    </div>
                  </div>

                  {/* Post Details */}
                  <div>
                    <h4 className="font-bold text-foreground mb-2 line-clamp-1">{post.postTitle}</h4>
                    <p className="text-sm text-muted-foreground line-clamp-2">{post.postDescription}</p>
                  </div>

                  {/* Engagement Metrics */}
                  <div className="grid grid-cols-2 gap-3">
                    <div className="flex items-center gap-2">
                      <div className="p-2 rounded-lg bg-gradient-to-br from-red-500 to-pink-500">
                        <Heart className="h-3 w-3 text-white" fill="white" />
                      </div>
                      <div>
                        <p className="text-xs text-muted-foreground">Likes</p>
                        <p className="text-sm font-bold text-foreground">{post.likes}</p>
                      </div>
                    </div>

                    <div className="flex items-center gap-2">
                      <div className="p-2 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500">
                        <MessageSquare className="h-3 w-3 text-white" fill="white" />
                      </div>
                      <div>
                        <p className="text-xs text-muted-foreground">Comments</p>
                        <p className="text-sm font-bold text-foreground">{post.comments}</p>
                      </div>
                    </div>

                    <div className="flex items-center gap-2">
                      <div className="p-2 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-500">
                        <Eye className="h-3 w-3 text-white" fill="white" />
                      </div>
                      <div>
                        <p className="text-xs text-muted-foreground">Views</p>
                        <p className="text-sm font-bold text-foreground">{post.views}</p>
                      </div>
                    </div>

                    <div className="flex items-center gap-2">
                      <div className="p-2 rounded-lg bg-gradient-to-br from-yellow-500 to-orange-500">
                        <Pin className="h-3 w-3 text-white" fill="white" />
                      </div>
                      <div>
                        <p className="text-xs text-muted-foreground">Pinned</p>
                        <p className="text-sm font-bold text-foreground">{post.pinned ? "Yes" : "No"}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        </div>
      </Card>
    </div>
  )
}
