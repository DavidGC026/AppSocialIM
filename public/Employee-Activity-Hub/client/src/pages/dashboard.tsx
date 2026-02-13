import { Sidebar, Header } from "@/components/layout/AppLayout";
import { CreateActivity } from "@/components/dashboard/CreateActivity";
import { ActivityItem } from "@/components/dashboard/ActivityItem";
import { TeamSidebar } from "@/components/dashboard/TeamSidebar";
import { MOCK_ACTIVITIES } from "@/lib/mockData";
import { Card, CardContent } from "@/components/ui/card";
import { CheckCircle2, TrendingUp } from "lucide-react";

export default function Dashboard() {
  return (
    <div className="min-h-screen bg-background font-sans">
      <Sidebar />
      <Header />
      
      <main className="ml-64 p-6 pt-8">
        <div className="max-w-6xl mx-auto flex gap-8 items-start">
          
          <div className="flex-1 min-w-0">
            {/* Banner */}
            <div className="h-32 w-full rounded-xl bg-gradient-to-r from-primary/90 to-indigo-600 mb-8 flex items-center px-8 relative overflow-hidden shadow-lg">
              <div className="absolute inset-0 bg-[url('/images/cover-bg.png')] opacity-20 bg-cover bg-center mix-blend-overlay" />
              <div className="relative z-10 text-white">
                <h1 className="text-2xl font-bold font-display">¡Buen día, Alex! ☀️</h1>
                <p className="text-white/80 mt-1">Tienes 4 tareas pendientes y 2 reuniones hoy.</p>
              </div>
            </div>

            {/* Stats Row */}
            <div className="grid grid-cols-2 gap-4 mb-8">
               <Card className="border-none shadow-sm bg-primary/5 ring-1 ring-primary/10">
                 <CardContent className="p-4 flex items-center gap-4">
                   <div className="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                     <CheckCircle2 className="h-5 w-5" />
                   </div>
                   <div>
                     <div className="text-2xl font-bold">12</div>
                     <div className="text-xs text-muted-foreground font-medium">Tareas Completadas</div>
                   </div>
                 </CardContent>
               </Card>
               <Card className="border-none shadow-sm bg-emerald-500/5 ring-1 ring-emerald-500/10">
                 <CardContent className="p-4 flex items-center gap-4">
                   <div className="h-10 w-10 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-600">
                     <TrendingUp className="h-5 w-5" />
                   </div>
                   <div>
                     <div className="text-2xl font-bold">94%</div>
                     <div className="text-xs text-muted-foreground font-medium">Productividad</div>
                   </div>
                 </CardContent>
               </Card>
            </div>

            <h2 className="text-lg font-semibold mb-4 px-1">Actividad del Equipo</h2>
            <CreateActivity />
            
            <div className="space-y-4">
              {MOCK_ACTIVITIES.map(activity => (
                <ActivityItem key={activity.id} activity={activity} />
              ))}
            </div>
          </div>

          <TeamSidebar />
          
        </div>
      </main>
    </div>
  );
}
