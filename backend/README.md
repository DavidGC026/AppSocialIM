# PHP Backend API con MySQL

Este es un backend en PHP con MySQL para la autenticación de la aplicación de calendario.

## Configuración

### 1. Requisitos
- PHP 7.4 o superior con extensión PDO MySQL
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx) o servidor integrado de PHP

### 2. Configuración de Variables de Entorno

1. **Copiar el archivo .env:**
   \`\`\`bash
   cp .env.example .env
   \`\`\`

   > **Nota:** El archivo `.env` está incluido en `.gitignore` para evitar subir credenciales sensibles al repositorio.

2. **Configurar las variables:**
   Edita `backend/.env` con tus credenciales:
   \`\`\`env
   # Database Configuration
   DB_HOST=localhost
   DB_NAME=calendario_app
   DB_USER=tu_usuario_mysql
   DB_PASS=tu_password_mysql

   # JWT Secret Key (cambia esto en producción)
   JWT_SECRET=tu_clave_secreta_muy_segura

   # Application Environment
   APP_ENV=development
   \`\`\`

### 3. Configuración de Base de Datos

1. **Crear la base de datos:**
   \`\`\`sql
   mysql -u root -p < database/schema.sql
   \`\`\`

2. **Verificar conexión:**
   Las credenciales ahora se leen automáticamente del archivo `.env`

### 3. Ejecutar el Servidor
\`\`\`bash
cd backend
php -S localhost:8000
\`\`\`

## Endpoints

### POST /api/login.php
Inicia sesión de usuario.

**Request Body:**
\`\`\`json
{
  "email": "user@example.com",
  "password": "password123"
}
\`\`\`

**Response:**
\`\`\`json
{
  "token": "jwt_token_here",
  "user": {
    "id": "1",
    "email": "user@example.com",
    "name": "Usuario Ejemplo"
  }
}
\`\`\`

### POST /api/verify.php
Verifica un token JWT.

**Headers:**
\`\`\`
Authorization: Bearer jwt_token_here
\`\`\`

**Response:**
\`\`\`json
{
  "user": {
    "id": "1",
    "email": "user@example.com",
    "name": "Usuario Ejemplo"
  }
}
\`\`\`

### POST /api/logout.php
Cierra sesión e invalida el token.

**Headers:**
\`\`\`
Authorization: Bearer jwt_token_here
\`\`\`

**Response:**
\`\`\`json
{
  "message": "Logged out successfully"
}
\`\`\`

### GET /api/events.php
Obtiene todos los eventos.

**Headers:**
\`\`\`
Authorization: Bearer jwt_token_here
\`\`\`

**Response:**
\`\`\`json
[
  {
    "id": 1,
    "title": "Team Meeting",
    "description": "Weekly team sync-up",
    "startTime": "09:00",
    "endTime": "10:00",
    "date": "2025-03-05",
    "location": "Conference Room A",
    "color": "bg-blue-500",
    "organizer": "Alice Brown",
    "attendees": ["John Doe", "Jane Smith"],
    "creator_name": "Usuario Ejemplo"
  }
]
\`\`\`

### POST /api/events.php
Crea un nuevo evento (solo administradores).

**Headers:**
\`\`\`
Authorization: Bearer jwt_token_here
Content-Type: application/json
\`\`\`

**Request Body:**
\`\`\`json
{
  "title": "New Event",
  "description": "Event description",
  "startTime": "14:00",
  "endTime": "15:30",
  "date": "2025-03-10",
  "location": "Meeting Room",
  "color": "bg-green-500",
  "organizer": "John Doe",
  "attendees": ["Jane Smith", "Bob Johnson"]
}
\`\`\`

**Response:**
\`\`\`json
{
  "message": "Event created successfully",
  "event_id": 2
}
\`\`\`

### PUT /api/events.php?id=1
Actualiza un evento (solo administradores).

**Headers:**
\`\`\`
Authorization: Bearer jwt_token_here
Content-Type: application/json
\`\`\`

**Request Body:**
\`\`\`json
{
  "title": "Updated Event Title",
  "location": "Updated Location"
}
\`\`\`

**Response:**
\`\`\`json
{
  "message": "Event updated successfully"
}
\`\`\`

### DELETE /api/events.php?id=1
Elimina un evento (solo administradores).

**Headers:**
\`\`\`
Authorization: Bearer jwt_token_here
\`\`\`

**Response:**
\`\`\`json
{
  "message": "Event deleted successfully"
}
\`\`\`

## Usuarios de Prueba

### Usuario Administrador
- Email: `user@example.com`
- Password: `password123`
- Rol: `admin`

### Códigos de Registro Disponibles
- `ADMIN-2024-001` - Rol: Administrador
- `VIEWER-2024-001` - Rol: Visualizador
- `VIEWER-2024-002` - Rol: Visualizador

## Roles y Permisos

### Administrador (admin)
- ✅ Crear nuevos eventos
- ✅ Editar eventos existentes
- ✅ Eliminar eventos
- ✅ Ver todos los eventos
- ✅ Gestionar usuarios (en futuras versiones)

### Visualizador (viewer)
- ✅ Ver todos los eventos
- ❌ Crear nuevos eventos
- ❌ Editar eventos existentes
- ❌ Eliminar eventos

## Estructura de Base de Datos

### Tabla `users`
\`\`\`sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
\`\`\`

### Tabla `invalidated_tokens`
\`\`\`sql
CREATE TABLE invalidated_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(500) NOT NULL UNIQUE,
    invalidated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL
);
\`\`\`

### Tabla `events`
\`\`\`sql
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    date DATE NOT NULL,
    location VARCHAR(255),
    color VARCHAR(50) DEFAULT 'bg-blue-500',
    organizer VARCHAR(255),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);
\`\`\`

### Tabla `event_attendees`
\`\`\`sql
CREATE TABLE event_attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    attendee_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);
\`\`\`

## Notas de Seguridad

Este es un ejemplo básico. En producción:

1. ✅ Usa PDO con prepared statements (implementado)
2. ✅ Hashea contraseñas con `password_hash()` (implementado)
3. Implementa JWT propiamente con una librería como `firebase/php-jwt`
4. Agrega validación de entrada más robusta
5. Implementa rate limiting
6. ✅ Usa HTTPS
7. ✅ Almacena tokens en blacklist para logout real (implementado)
8. Configura CORS apropiadamente para tu dominio
9. Implementa logging de seguridad
10. Usa variables de entorno para credenciales

## Configuración de Producción

### Despliegue en Apache/Nginx

1. **Copiar archivos al servidor web:**
   \`\`\`bash
   # Copiar el backend a la raíz web
   cp -r backend/* /var/www/html/calendario/
   \`\`\`

2. **Configurar permisos:**
   \`\`\`bash
   chown -R www-data:www-data /var/www/html/calendario/backend/
   chmod -R 755 /var/www/html/calendario/backend/
   chmod 600 /var/www/html/calendario/backend/config/database.php
   \`\`\`

3. **Configurar Apache (.htaccess incluido):**
   El archivo `.htaccess` ya está configurado con:
   - Ejecución de PHP
   - Headers de seguridad
   - CORS para API
   - Protección de archivos de configuración

### Desarrollo Local

Para desarrollo local, puedes usar el servidor integrado de PHP:

\`\`\`bash
cd backend
php -S localhost:8000
\`\`\`

**Nota:** En desarrollo, las llamadas API usarán `http://localhost:8000/api/...`, pero en producción usarán rutas relativas `/backend/api/...`.

### Probar la API

Ejecuta el script de prueba para verificar que los endpoints funcionan:

\`\`\`bash
cd backend
php test-api.php
\`\`\`

Esto probará:
- Login con credenciales válidas
- Login con credenciales inválidas
- Verificación de token
- Logout

### Solución de Problemas

**Error de conexión a base de datos:**
- Verifica que MySQL esté ejecutándose
- Confirma las credenciales en `config/database.php`
- Asegúrate de que la base de datos `calendario_app` existe

**API no responde:**
- Verifica que el servidor web tenga permisos para ejecutar PHP
- Confirma que los archivos `.htaccess` sean procesados
- Revisa los logs de error de Apache/Nginx

**CORS errors:**
- En producción, como frontend y backend están en el mismo dominio, CORS no debería ser necesario
- Si hay problemas, verifica los headers CORS en los archivos PHP
