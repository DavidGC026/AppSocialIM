import { SOCIAL_USERS as USERS, PROJECTS } from "@/lib/social-data";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { cn } from "@/lib/utils";

export function TeamSidebar() {
  const onlineUsers = USERS.filter(u => u.status === 'online');
  const otherUsers = USERS.filter(u => u.status !== 'online');

  return (
    <div className="w-80 p-6 hidden xl:block space-y-8 sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto">
      <div>
        <h3 className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-4">Proyectos Activos</h3>
        <div className="space-y-3">
          {PROJECTS.map((project) => (
            <div key={project.name} className="flex items-center gap-3 p-2 rounded-lg hover:bg-muted/50 transition-colors cursor-pointer group">
              <div className={cn("h-2.5 w-2.5 rounded-full ring-2 ring-background group-hover:scale-110 transition-transform", project.color)} />
              <span className="text-sm font-medium">{project.name}</span>
            </div>
          ))}
        </div>
      </div>

      <div>
        <h3 className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-4">En línea - {onlineUsers.length}</h3>
        <div className="space-y-2">
          {onlineUsers.map(user => (
            <div key={user.id} className="flex items-center gap-3 p-2 rounded-lg hover:bg-muted/50 transition-colors cursor-pointer">
              <div className="relative">
                <Avatar className="h-8 w-8 border border-border">
                  <AvatarImage src={user.avatar} />
                  <AvatarFallback>{user.name.charAt(0)}</AvatarFallback>
                </Avatar>
                <span className="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-background" />
              </div>
              <span className="text-sm font-medium">{user.name}</span>
            </div>
          ))}
        </div>
      </div>

      <div>
        <h3 className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-4">Ausente</h3>
        <div className="space-y-2">
          {otherUsers.map(user => (
            <div key={user.id} className="flex items-center gap-3 p-2 rounded-lg hover:bg-muted/50 transition-colors cursor-pointer opacity-70 hover:opacity-100">
              <div className="relative">
                <Avatar className="h-8 w-8 border border-border grayscale">
                  <AvatarImage src={user.avatar} />
                  <AvatarFallback>{user.name.charAt(0)}</AvatarFallback>
                </Avatar>
                <span className={cn(
                  "absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full ring-2 ring-background",
                  user.status === 'busy' ? "bg-red-500" : "bg-yellow-500"
                )} />
              </div>
              <span className="text-sm font-medium">{user.name}</span>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
