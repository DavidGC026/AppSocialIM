export interface SocialUser {
  id: string;
  name: string;
  role: string;
  avatar: string;
  status: 'online' | 'offline' | 'busy' | 'away';
}

export interface Activity {
  id: string;
  userId: string;
  user: SocialUser;
  type: 'status' | 'task_complete' | 'meeting' | 'document' | 'milestone';
  content: string;
  timestamp: string;
  likes: number;
  comments: number;
  project?: string;
  originalEvent?: any; // To store the original calendar event if applicable
}

export const CURRENT_USER: SocialUser = {
  id: 'u1',
  name: 'Tú', // Will be overwritten by auth context
  role: 'Empleado',
  avatar: '/placeholder-user.jpg', // Ensure this asset exists or use a default
  status: 'online'
};

export const SOCIAL_USERS: SocialUser[] = [
  CURRENT_USER,
  {
    id: 'u2',
    name: 'Sarah Chen',
    role: 'Líder de Ingeniería',
    avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop',
    status: 'busy'
  },
  {
    id: 'u3',
    name: 'Marcus Johnson',
    role: 'CTO',
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop',
    status: 'online'
  },
  {
    id: 'u4',
    name: 'Emily Davis',
    role: 'Desarrolladora Frontend',
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&h=150&fit=crop',
    status: 'offline'
  },
  {
    id: 'u5',
    name: 'David Wilson',
    role: 'Desarrollador Backend',
    avatar: 'https://images.unsplash.com/photo-1599566150163-29194dcaad36?w=150&h=150&fit=crop',
    status: 'away'
  }
];

export const MOCK_ACTIVITIES: Activity[] = [
  {
    id: 'a1',
    userId: 'u2',
    user: SOCIAL_USERS[1],
    type: 'task_complete',
    content: 'Desplegado el nuevo servicio de autenticación a producción. 🚀',
    timestamp: 'Hace 10 min',
    likes: 12,
    comments: 3,
    project: 'Infraestructura Core'
  },
  {
    id: 'a2',
    userId: 'u3',
    user: SOCIAL_USERS[2],
    type: 'milestone',
    content: 'Las diapositivas para la revisión de metas del Q3 están listas.',
    timestamp: 'Hace 1 hora',
    likes: 24,
    comments: 5,
    project: 'Gestión'
  },
  {
    id: 'a3',
    userId: 'u1',
    user: SOCIAL_USERS[0],
    type: 'document',
    content: 'Actualizada la documentación del sistema de diseño.',
    timestamp: 'Hace 2 horas',
    likes: 8,
    comments: 1,
    project: 'Sistema de Diseño'
  },
  {
    id: 'a4',
    userId: 'u5',
    user: SOCIAL_USERS[4],
    type: 'status',
    content: 'Saliendo a almorzar, vuelvo en 45.',
    timestamp: 'Hace 3 horas',
    likes: 2,
    comments: 0
  }
];

export const PROJECTS = [
  { name: 'Infraestructura Core', color: 'bg-blue-500' },
  { name: 'App Móvil', color: 'bg-purple-500' },
  { name: 'Sistema de Diseño', color: 'bg-pink-500' },
  { name: 'Sitio Web de Marketing', color: 'bg-orange-500' },
  { name: 'Herramientas Internas', color: 'bg-green-500' }
];
