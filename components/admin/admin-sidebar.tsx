"use client"

import Link from "next/link"
import { usePathname } from "next/navigation"
import { cn } from "@/lib/utils"
import { LayoutDashboard, Users, MessageSquare, Shield, UserCog } from "lucide-react"
import Image from "next/image"

const menuItems = [
  {
    title: "Dashboard",
    href: "/admin/dashboard",
    icon: LayoutDashboard,
  },
  {
    title: "Directory",
    href: "/admin/directory",
    icon: Users,
  },
  {
    title: "Forums",
    href: "/admin/forums",
    icon: MessageSquare,
  },
  {
    title: "Announcements",
    href: "/admin/announcements",
    icon: MessageSquare,
  },
  {
    title: "User",
    href: "/admin/users",
    icon: UserCog,
  },
  {
    title: "Roles",
    href: "/admin/roles",
    icon: Shield,
  },
]

interface AdminSidebarProps {
  isOpen: boolean
  onToggle: () => void
}

export function AdminSidebar({ isOpen, onToggle }: AdminSidebarProps) {
  const pathname = usePathname()

  return (
    <>
      <aside
        className={cn(
          "fixed inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-slate-50 to-slate-100 border-r border-slate-200 transition-transform duration-300 ease-in-out shadow-lg",
          isOpen ? "translate-x-0" : "-translate-x-full",
        )}
      >
        <div className="flex flex-col h-full">
          <div className="flex items-center justify-center px-6 py-6 border-b border-slate-200 bg-white">
            <Image
              src="/images/image-206.png"
              alt="SIP Academy Logo"
              width={180}
              height={60}
              className="object-contain"
              priority
            />
          </div>

          <nav className="flex-1 px-4 py-6 space-y-2">
            {menuItems.map((item) => {
              const isActive = pathname === item.href || pathname.startsWith(item.href)
              const Icon = item.icon

              return (
                <Link
                  key={item.href}
                  href={item.href}
                  onClick={() => {
                    if (window.innerWidth < 768) {
                      onToggle()
                    }
                  }}
                  className={cn(
                    "flex items-center gap-3 px-4 py-3 rounded-lg font-semibold transition-all",
                    isActive
                      ? "bg-[#E2001D] text-white shadow-md"
                      : "text-slate-700 hover:bg-slate-200 hover:text-slate-900",
                  )}
                >
                  <Icon className="h-5 w-5" />
                  <span>{item.title}</span>
                </Link>
              )
            })}
          </nav>

          <div className="px-6 py-4 border-t border-slate-200 bg-white">
            <p className="text-xs text-slate-500 text-center font-medium">© 2025 SIP Academy</p>
          </div>
        </div>
      </aside>

      {isOpen && <div className="fixed inset-0 bg-black/50 z-30 md:hidden" onClick={onToggle} />}
    </>
  )
}
