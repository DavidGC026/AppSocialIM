-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS calendario_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE calendario_app;

-- Tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'viewer') NOT NULL DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de códigos de registro
CREATE TABLE registration_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    role ENUM('admin', 'viewer') NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL
);

-- Tabla de eventos
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

-- Tabla de asistentes a eventos
CREATE TABLE event_attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NULL,
    attendee_name VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Migrar datos existentes (si los hay) de attendee_name a user_id donde sea posible
-- Nota: Esta migración es opcional y debe ejecutarse manualmente si hay datos existentes
-- UPDATE event_attendees ea
-- JOIN users u ON ea.attendee_name = u.name
-- SET ea.user_id = u.id
-- WHERE ea.user_id IS NULL;

-- Insertar códigos de registro de ejemplo
INSERT INTO registration_codes (code, role, expires_at) VALUES
('ADMIN-2025-001', 'admin', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('VIEWER-2025-001', 'viewer', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('VIEWER-2025-002', 'viewer', DATE_ADD(NOW(), INTERVAL 1 YEAR));

-- Insertar eventos de ejemplo
INSERT INTO events (title, description, start_time, end_time, date, location, color, organizer, created_by) VALUES
('Team Meeting', 'Weekly team sync-up', '09:00', '10:00', '2025-03-05', 'Conference Room A', 'bg-blue-500', 'Alice Brown', 1),
('Lunch with Sarah', 'Discuss project timeline', '12:30', '13:30', '2025-03-05', 'Cafe Nero', 'bg-green-500', 'You', 1),
('Project Review', 'Q2 project progress review', '14:00', '15:30', '2025-03-07', 'Meeting Room 3', 'bg-purple-500', 'Project Manager', 1);

-- Insertar asistentes de ejemplo
INSERT INTO event_attendees (event_id, attendee_name) VALUES
(1, 'John Doe'),
(1, 'Jane Smith'),
(1, 'Bob Johnson'),
(2, 'Sarah Lee');

-- Insertar usuario de prueba
INSERT INTO users (email, password, name, role) VALUES

-- Tabla de tokens invalidados (para logout real)
CREATE TABLE invalidated_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(500) NOT NULL UNIQUE,
    invalidated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL
);

-- Índices para mejor rendimiento
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_token ON invalidated_tokens(token);
CREATE INDEX idx_expires_at ON invalidated_tokens(expires_at);
