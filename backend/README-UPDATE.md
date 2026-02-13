# Actualización de Base de Datos - Calendario App

## Problemas Reportados y Soluciones

### 1. Error: "Table 'invalidated_tokens' doesn't exist"
**Problema**: La tabla `invalidated_tokens` no existe en la base de datos.

**Solución**: Ejecuta el script `backend/database/update_schema.sql` en tu base de datos MySQL.

### 2. No se cargan usuarios en el selector de asistentes
**Problema**: La tabla `users` puede no tener suficientes usuarios de prueba.

**Solución**: El script `update_schema.sql` agrega usuarios de ejemplo adicionales.

## Cómo Actualizar la Base de Datos

### Opción 1: Usando phpMyAdmin o MySQL Workbench
1. Abre phpMyAdmin o MySQL Workbench
2. Selecciona tu base de datos `calendario_app`
3. Ejecuta el contenido del archivo `backend/database/update_schema.sql`

### Opción 2: Usando línea de comandos
```bash
mysql -u [usuario] -p calendario_app < backend/database/update_schema.sql
```

### Opción 3: Ejecutar desde PHP (si tienes acceso)
Crea un archivo temporal `update_db.php` en la raíz del proyecto:

```php
<?php
// update_db.php - Script temporal para actualizar la base de datos
require_once 'backend/config/database.php';

try {
    $db = Database::getInstance();

    // Ejecutar las actualizaciones
    $sql = file_get_contents('backend/database/update_schema.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            $db->exec($statement);
        }
    }

    echo "Base de datos actualizada exitosamente!";
} catch (Exception $e) {
    echo "Error actualizando la base de datos: " . $e->getMessage();
}
```

Luego visita `http://tu-dominio/update_db.php` y elimina el archivo después.

## Usuarios de Prueba Agregados

Después de ejecutar la actualización, tendrás estos usuarios disponibles:

- admin@example.com (Administrador)
- juan@example.com (Juan Pérez)
- maria@example.com (María García)
- carlos@example.com (Carlos López)
- ana@example.com (Ana Rodríguez)
- user@example.com (Usuario Ejemplo - existente)

**Contraseña para todos**: `password123`

## Verificación

Después de actualizar:
1. Los errores de base de datos deberían desaparecer
2. El selector de asistentes debería mostrar los usuarios disponibles
3. Deberías poder crear eventos con asistentes seleccionados de la lista de usuarios

## Notas Importantes

- La tabla `event_attendees` ahora soporta tanto `user_id` (para usuarios registrados) como `attendee_name` (para nombres manuales)
- Los eventos existentes seguirán funcionando
- Las nuevas funcionalidades requieren que ejecutes la actualización de base de datos