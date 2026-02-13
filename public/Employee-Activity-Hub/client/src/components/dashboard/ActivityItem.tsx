import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Card, CardContent, CardFooter, CardHeader } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Activity } from "@/lib/mockData";
import { cn } from "@/lib/utils";
import { Heart, MessageSquare, MoreHorizontal, Share2 } from "lucide-react";

interface ActivityItemProps {
  activity: Activity;
}

export function ActivityItem({ activity }: ActivityItemProps) {
  const getTypeIcon = (type: Activity['type']) => {
    switch(type) {
      case 'task_complete': return '✅';
      case 'milestone': return '🏆';
      case 'meeting': return '📅';
      case 'document': return '📄';
      default: return '💬';
    }
  };

  return (
    <Card className="mb-4 border-none shadow-sm ring-1 ring-black/5 hover:ring-primary/20 transition-all duration-300">
      <CardHeader className="p-4 pb-2 flex flex-row items-start gap-3 space-y-0">
        <Avatar className="h-10 w-10 border border-border cursor-pointer">
          <AvatarImage src={activity.user.avatar} />
          <AvatarFallback>{activity.user.name.charAt(0)}</AvatarFallback>
        </Avatar>
        <div className="flex-1 min-w-0">
          <div className="flex items-center justify-between">
            <div className="flex flex-col">
              <span className="font-semibold text-sm hover:underline cursor-pointer">{activity.user.name}</span>
              <span className="text-xs text-muted-foreground flex items-center gap-1">
                {activity.user.role} • {activity.timestamp}
              </span>
            </div>
            <Button variant="ghost" size="icon" className="h-8 w-8 text-muted-foreground">
              <MoreHorizontal className="h-4 w-4" />
            </Button>
          </div>
        </div>
      </CardHeader>
      <CardContent className="p-4 pt-1 pb-3">
        {activity.project && (
          <Badge variant="secondary" className="mb-2 font-medium bg-secondary/50 text-secondary-foreground hover:bg-secondary">
            {activity.project}
          </Badge>
        )}
        <p className="text-sm leading-relaxed text-foreground/90 whitespace-pre-wrap">
          {getTypeIcon(activity.type)} {activity.content}
        </p>
      </CardContent>
      <CardFooter className="p-3 bg-muted/30 border-t flex items-center justify-between">
        <div className="flex items-center gap-4">
          <Button variant="ghost" size="sm" className="h-8 gap-1.5 text-muted-foreground hover:text-red-500 hover:bg-red-500/10 transition-colors">
            <Heart className="h-4 w-4" />
            <span className="text-xs font-medium">{activity.likes}</span>
          </Button>
          <Button variant="ghost" size="sm" className="h-8 gap-1.5 text-muted-foreground hover:text-primary hover:bg-primary/10 transition-colors">
            <MessageSquare className="h-4 w-4" />
            <span className="text-xs font-medium">{activity.comments}</span>
          </Button>
        </div>
        <Button variant="ghost" size="sm" className="h-8 gap-1.5 text-muted-foreground">
          <Share2 className="h-4 w-4" />
        </Button>
      </CardFooter>
    </Card>
  );
}
