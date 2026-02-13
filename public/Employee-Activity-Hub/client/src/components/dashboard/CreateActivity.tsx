import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Textarea } from "@/components/ui/textarea";
import { CURRENT_USER } from "@/lib/mockData";
import { Image, Paperclip, Send } from "lucide-react";

export function CreateActivity() {
  return (
    <Card className="mb-6 overflow-hidden border-none shadow-sm ring-1 ring-black/5">
      <CardContent className="p-4">
        <div className="flex gap-4">
          <Avatar className="h-10 w-10 border border-border">
            <AvatarImage src={CURRENT_USER.avatar} />
            <AvatarFallback>AM</AvatarFallback>
          </Avatar>
          <div className="flex-1 space-y-3">
            <Textarea 
              placeholder="¿En qué estás trabajando?" 
              className="min-h-[80px] border-none focus-visible:ring-0 px-0 text-base resize-none shadow-none bg-transparent"
            />
            <div className="flex items-center justify-between pt-2 border-t">
              <div className="flex items-center gap-2">
                <Button variant="ghost" size="icon" className="h-8 w-8 text-muted-foreground hover:text-primary hover:bg-primary/10">
                  <Image className="h-4 w-4" />
                </Button>
                <Button variant="ghost" size="icon" className="h-8 w-8 text-muted-foreground hover:text-primary hover:bg-primary/10">
                  <Paperclip className="h-4 w-4" />
                </Button>
              </div>
              <Button size="sm" className="bg-primary text-white hover:bg-primary/90 gap-2 font-medium">
                Compartir Actualización <Send className="h-3.5 w-3.5" />
              </Button>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
