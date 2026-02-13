-- Script para actualizar la base de datos con las nuevas tablas y cambios
-- Ejecutar este script si ya tienes la base de datos creada

USE calendario_app;

-- Crear tabla de tokens invalidados si no existe
CREATE TABLE IF NOT EXISTS invalidated_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(500) NOT NULL UNIQUE,
    invalidated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL
);

-- Modificar tabla event_attendees para agregar user_id si no existe
ALTER TABLE event_attendees
ADD COLUMN IF NOT EXISTS user_id INT NULL,
ADD COLUMN IF NOT EXISTS attendee_name VARCHAR(255) NULL;

-- Agregar foreign key si no existe
SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'calendario_app'
    AND TABLE_NAME = 'event_attendees'
    AND CONSTRAINT_NAME = 'fk_user_id'
);

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE event_attendees ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE',
    'SELECT "Foreign key already exists"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar índices si no existen
CREATE INDEX IF NOT EXISTS idx_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_token ON invalidated_tokens(token);
CREATE INDEX IF NOT EXISTS idx_expires_at ON invalidated_tokens(expires_at);

-- Insertar usuarios de ejemplo adicionales para probar la selección de asistentes
INSERT IGNORE INTO users (email, password, name, role) VALUES
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin'),
('juan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan Pérez', 'viewer'),
('maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María García', 'viewer'),
('carlos@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos López', 'viewer'),
('ana@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana Rodríguez', 'viewer');

-- La contraseña para todos es: password123 (hasheada)

SELECT 'Database update completed successfully' as status;