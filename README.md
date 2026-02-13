# 📅 Calendario Interactivo Moderno

Un calendario moderno con diseño glassmorphism, sistema de autenticación completo, gestión de eventos y música de concentración integrada (con 2 canciones incluidas).

## ✨ Características

- 🎨 **Diseño Glassmorphism** con efectos visuales modernos
- 🔐 **Sistema de Autenticación** con roles (Admin/Viewer)
- 📅 **Calendario Interactivo** con vistas Día/Semana/Mes
- 🎵 **Música de Concentración** (2 canciones incluidas)
- 🔍 **Búsqueda Avanzada** con resaltado visual
- 🎨 **Eventos Personalizables** con colores
- 📱 **Responsive Design** para todos los dispositivos

## 🚀 Inicio Rápido

### Prerrequisitos

- Node.js 18+
- PHP 8.0+
- MySQL 8.0+
- Composer (para dependencias PHP)

### Instalación

1. **Clona el repositorio:**
   \`\`\`bash
   git clone <tu-repositorio>
   cd calendario
   \`\`\`

2. **Instala dependencias del frontend:**
   \`\`\`bash
   pnpm install
   # o
   npm install
   \`\`\`

3. **Configura la base de datos:**
   \`\`\`bash
   # Crea una base de datos MySQL
   mysql -u root -p
   CREATE DATABASE calendario_db;
   \`\`\`

4. **Configura las variables de entorno:**
   \`\`\`bash
   cp backend/.env.example backend/.env
   \`\`\`

   Edita `backend/.env`:
   \`\`\`env
   DB_HOST=localhost
   DB_NAME=calendario_db
   DB_USER=tu_usuario
   DB_PASS=tu_password
   JWT_SECRET=tu_jwt_secret_seguro
   \`\`\`

5. **Configura la base de datos:**
   \`\`\`bash
   # Ejecuta el esquema de la base de datos
   mysql -u tu_usuario -p calendario_db < backend/database/schema.sql
   \`\`\`

6. **Música incluida:**
   \`\`\`bash
   # Ya tienes 2 canciones disponibles en:
   public/music/
   # - hans-zimmer-concentration.mp3
   # - beret-morat-porfa-no-te-vayas-videoclip-oficial_audio_good_spanish.mp3

   # Para agregar más música, coloca archivos MP3 en public/music/
   \`\`\`

7. **Inicia el servidor de desarrollo:**
   \`\`\`bash
   pnpm dev
   # o
   npm run dev
   \`\`\`

8. **Abre [http://localhost:3000](http://localhost:3000)**

## 🎵 Música de Concentración

### 🎼 Canciones Disponibles

Actualmente tienes **2 canciones** en `public/music/`:

1. **`hans-zimmer-concentration.mp3`** - Música instrumental para concentración
2. **`beret-morat-porfa-no-te-vayas-videoclip-oficial_audio_good_spanish.mp3`** - Canción alternativa

### 🔄 Sistema de Reproducción

- **Primera opción**: Intenta reproducir `hans-zimmer-concentration.mp3`
- **Fallback automático**: Si falla, cambia automáticamente a la canción alternativa
- **Loop infinito**: La música se repite continuamente para mantener el estado de concentración
- **Controles intuitivos**: Play/Pause desde el popup de IA

### ➕ Cómo Agregar Más Música

1. **Coloca archivos MP3** en `public/music/`
2. **Formatos soportados:**
   - MP3 (recomendado)
   - OGG
   - WAV

3. **Nombres descriptivos**: Evita caracteres especiales en los nombres de archivo

4. **Fuentes recomendadas:**
   - **YouTube Audio Library** (gratis y legal)
   - **Bensound.com** (música gratuita)
   - **Free Music Archive** (licencias Creative Commons)
   - **Epidemic Sound** (para proyectos comerciales)

### 🎮 Funcionalidad del Popup IA

- **Activación automática**: Aparece después de 3 segundos de inactividad
- **Mensaje personalizado**: *"Parece que no tienes tantas reuniones hoy. ¿Te gustaría que reproduzca música de concentración?"*
- **Botón "Sí"**: Inicia la reproducción de música y muestra el mini reproductor
- **Botón "No"**: Cierra el popup sin reproducir
- **Botón "Pausar"**: Aparece cuando la música está reproduciendo
- **Estado visual**: Indicadores claros del estado de reproducción

### 🎵 Mini Reproductor de Música

**Ubicación**: Panel lateral izquierdo (sidebar), debajo de "Mis calendarios"

**Características:**
- **Controles intuitivos**: Botón de play/pause central
- **Nombre de canción**: Muestra el artista actual (Hans Zimmer o Beret & Morat)
- **Indicador visual**: Barra de progreso simulada
- **Cierre opcional**: Botón X para ocultar el reproductor
- **Scroll vertical**: El sidebar tiene scroll cuando el contenido excede el espacio
- **Activación automática**: Aparece cuando se inicia la música desde el popup IA

**Controles:**
- **▶️ Play**: Inicia la reproducción
- **⏸️ Pause**: Pausa la música actual
- **❌ Cerrar**: Oculta el mini reproductor (la música sigue reproduciendo)
- **🔊 Volumen**: Indicador visual (no funcional por ahora)

## 🔐 Sistema de Autenticación

### Códigos de Registro

- **Admin**: `admin123` - Acceso completo
- **Viewer**: `viewer123` - Solo lectura

### Endpoints de API

\`\`\`
POST /backend/api/register.php  # Registro
POST /backend/api/login.php     # Login
GET  /backend/api/verify.php    # Verificación de token
GET  /backend/api/events.php    # Obtener eventos
POST /backend/api/events.php    # Crear evento
PUT  /backend/api/events.php    # Actualizar evento
DELETE /backend/api/events.php  # Eliminar evento
\`\`\`

## 🗄️ Estructura de la Base de Datos

\`\`\`sql
-- Usuarios
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'viewer') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Eventos
CREATE TABLE events (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  date DATE NOT NULL,
  location VARCHAR(255),
  color VARCHAR(50) DEFAULT 'bg-blue-500',
  organizer VARCHAR(255),
  creator_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator_id) REFERENCES users(id)
);

-- Asistentes de eventos
CREATE TABLE event_attendees (
  id INT PRIMARY KEY AUTO_INCREMENT,
  event_id INT,
  attendee_name VARCHAR(255),
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);
\`\`\`

## 🎨 Tecnologías Utilizadas

### Frontend
- **Next.js 14** - Framework React
- **TypeScript** - Tipado estático
- **Tailwind CSS** - Estilos utilitarios
- **Lucide React** - Iconos
- **React Context** - Gestión de estado

### Backend
- **PHP 8.0+** - Lenguaje servidor
- **MySQL** - Base de datos
- **JWT** - Autenticación
- **PDO** - Conexión a BD

## 📱 Características del Calendario

### Vistas Disponibles
- **Vista Día**: Eventos detallados con horas
- **Vista Semana**: Vista semanal compacta
- **Vista Mes**: Vista mensual con scroll

### Funcionalidad de Búsqueda
- Búsqueda por título, organizador o creador
- Resaltado visual con anillos amarillos
- Auto-scroll al primer resultado
- Indicador de cantidad de resultados

### Gestión de Eventos
- **Solo Admins**: Crear, editar, eliminar eventos
- **Todos los usuarios**: Ver eventos
- Colores personalizables
- Información detallada (ubicación, asistentes, descripción)

## 🚀 Despliegue en Producción

### Variables de Entorno
\`\`\`env
# Base de datos
DB_HOST=tu_host_produccion
DB_NAME=tu_base_datos
DB_USER=tu_usuario
DB_PASS=tu_password

# JWT
JWT_SECRET=tu_secret_muy_seguro

# API URLs (si es necesario)
NEXT_PUBLIC_API_URL=https://tu-dominio.com/backend
\`\`\`

### Comandos de Build
\`\`\`bash
# Build del frontend
pnpm build

# Build de producción
pnpm start
\`\`\`

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

## 📞 Soporte

Si tienes problemas o preguntas:

1. Revisa la documentación
2. Abre un issue en GitHub
3. Contacta al equipo de desarrollo

---

**¡Disfruta organizando tus eventos con estilo! 🎉**
