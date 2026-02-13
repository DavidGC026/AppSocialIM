"use client"
import Link from "next/link";
import { usePathname } from "next/navigation";
import { cn } from "@/lib/utils";
import {
  LayoutDashboard,
  Users,
  Calendar,
  FileText,
  Settings,
  Bell,
  Search,
  MessageSquare
} from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { CURRENT_USER } from "@/lib/social-data";
import { useAuth } from "@/lib/auth-context";

export function Sidebar() {
  const pathname = usePathname();
  const { user } = useAuth();
  const displayUser = user ? { ...CURRENT_USER, name: user.name } : CURRENT_USER;

  const navItems = [
    { icon: LayoutDashboard, label: "Feed", href: "/" },
    { icon: Users, label: "Equipo", href: "/team" },
    { icon: Calendar, label: "Calendario", href: "/calendar" }, // Placeholder for future feature
    { icon: FileText, label: "Documentos", href: "/documents" },
    { icon: MessageSquare, label: "Mensajes", href: "/messages" },
  ];

  return (
    <div className="h-screen w-64 border-r bg-sidebar flex flex-col fixed left-0 top-0 z-30">
      <div className="h-16 flex items-center px-6 border-b border-sidebar-border">
        <div className="flex items-center gap-2">
          <div className="h-8 w-8 rounded-lg bg-primary flex items-center justify-center">
            <div className="h-4 w-4 bg-white rounded-sm" />
          </div>
          <span className="font-display font-bold text-xl text-sidebar-foreground">WorkSync</span>
        </div>
      </div>

      <div className="flex-1 py-6 px-4 space-y-1">
        <div className="text-xs font-medium text-muted-foreground uppercase tracking-wider px-2 mb-2">Menú</div>
        {navItems.map((item) => {
          const isActive = item.href === '/' ? false : pathname === item.href;
          return (
            <Link key={item.href} href={item.href}>
              <div
                className={cn(
                  "flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-pointer",
                  isActive
                    ? "bg-primary/10 text-primary"
                    : "text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
                )}
              >
                <item.icon className="h-4 w-4" />
                {item.label}
              </div>
            </Link>
          );
        })}

        <div className="mt-8 text-xs font-medium text-muted-foreground uppercase tracking-wider px-2 mb-2">Configuración</div>
        <Link href="/settings">
          <div className="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground cursor-pointer">
            <Settings className="h-4 w-4" />
            Ajustes
          </div>
        </Link>
      </div>

      <div className="p-4 border-t border-sidebar-border">
        <div className="flex items-center gap-3 p-2 rounded-md hover:bg-sidebar-accent transition-colors cursor-pointer">
          <Avatar className="h-9 w-9 border border-border">
            <AvatarImage src={displayUser.avatar} />
            <AvatarFallback>{displayUser.name.charAt(0)}</AvatarFallback>
          </Avatar>
          <div className="flex flex-col overflow-hidden">
            <span className="text-sm font-medium truncate">{displayUser.name}</span>
            <span className="text-xs text-muted-foreground truncate">{displayUser.role}</span>
          </div>
        </div>
      </div>
    </div>
  );
}

export function Header() {
  return (
    <header className="h-16 border-b bg-background/80 backdrop-blur-md sticky top-0 z-20 flex items-center justify-between px-6 ml-64">
      <div className="flex items-center flex-1 max-w-md">
        <div className="relative w-full">
          <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
          <input
            type="text"
            placeholder="Buscar actividades, proyectos o personas..."
            className="w-full h-9 pl-9 pr-4 rounded-md border bg-muted/50 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all"
          />
        </div>
      </div>

      <div className="flex items-center gap-4">
        <button className="relative h-9 w-9 flex items-center justify-center rounded-full hover:bg-muted transition-colors">
          <Bell className="h-5 w-5 text-muted-foreground" />
          <span className="absolute top-2 right-2 h-2 w-2 rounded-full bg-primary ring-2 ring-background" />
        </button>
      </div>
    </header>
  );
}

export function AppLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="flex min-h-screen w-full">
      <Sidebar />
      <div className="flex-1 flex flex-col ml-64">
        <Header />
        <main className="flex-1 bg-background">
          {children}
        </main>
      </div>
    </div>
  );
}
