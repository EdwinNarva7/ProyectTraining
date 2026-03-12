-- Schema: sieap (MySQL 8+)
-- Propósito: Control de ingreso/salida de aprendices, administración de usuarios, horarios y certificados.
-- Notas:
--  - Sin tabla 'programs'.
--  - Sin tablas de trazabilidad 'user_stories' ni 'acceptance_criteria'.
--  - Incluye triggers para mantener updated_at.
--  - Usa ENUMs nativos de MySQL y claves foráneas.

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ======== CORE CATALOGS ========
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,                -- 'Administrador','Aprendiz'
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    full_name VARCHAR(160) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,                     -- almacenar hash (bcrypt/argon2)
    status ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Información adicional para aprendices
CREATE TABLE IF NOT EXISTS apprentice_profiles (
    user_id BIGINT PRIMARY KEY,
    document_number VARCHAR(40) UNIQUE,              -- cédula / documento
    phone VARCHAR(30),
    cohort VARCHAR(60),                              -- ficha o grupo
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_apprentice_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======== AUTH ========
CREATE TABLE IF NOT EXISTS login_audit (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    ip_address VARCHAR(45),                          -- soporta IPv4/IPv6 como texto
    user_agent TEXT,
    success BOOLEAN NOT NULL,
    message TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_login_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======== HORARIOS ========
CREATE TABLE IF NOT EXISTS schedules (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    apprentice_id BIGINT NOT NULL,
    weekday TINYINT NOT NULL CHECK (weekday BETWEEN 1 AND 7), -- 1=Lunes ... 7=Domingo
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_by BIGINT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_time_range CHECK (end_time > start_time),
    CONSTRAINT fk_sched_apprentice FOREIGN KEY (apprentice_id) REFERENCES users(id),
    CONSTRAINT fk_sched_creator FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_schedules_apprentice_weekday ON schedules(apprentice_id, weekday);

-- ======== REGISTRO DE ENTRADAS/SALIDAS ========
CREATE TABLE IF NOT EXISTS attendance_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    apprentice_id BIGINT NOT NULL,
    event_type ENUM('entrada','salida') NOT NULL,
    occurred_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    source VARCHAR(40) DEFAULT 'lector',             -- p.ej. lector, web, admin
    note TEXT,
    created_by BIGINT,
    CONSTRAINT fk_attlog_apprentice FOREIGN KEY (apprentice_id) REFERENCES users(id),
    CONSTRAINT fk_attlog_creator FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_attendance_apprentice_time ON attendance_logs(apprentice_id, occurred_at);

-- Sesiones emparejadas (entrada + salida)
CREATE TABLE IF NOT EXISTS attendance_sessions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    apprentice_id BIGINT NOT NULL,
    start_at TIMESTAMP NOT NULL,
    end_at TIMESTAMP,
    duration_minutes INT,                            -- opcional: calcular en consultas
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_session_time CHECK (end_at IS NULL OR end_at > start_at),
    CONSTRAINT fk_attsess_apprentice FOREIGN KEY (apprentice_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_sessions_apprentice_start ON attendance_sessions(apprentice_id, start_at);

-- ======== CERTIFICADOS ========
CREATE TABLE IF NOT EXISTS certificates (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    apprentice_id BIGINT NOT NULL,
    hours_completed INT NOT NULL CHECK (hours_completed >= 0),
    issued_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('generado','enviado','descargado','anulado') NOT NULL DEFAULT 'generado',
    pdf_path TEXT,                                    -- ruta/URI al PDF generado
    email_sent_at TIMESTAMP,
    email_to VARCHAR(190),
    created_by BIGINT,
    CONSTRAINT fk_cert_apprentice FOREIGN KEY (apprentice_id) REFERENCES users(id),
    CONSTRAINT fk_cert_creator FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS certificate_events (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    certificate_id BIGINT NOT NULL,
    event VARCHAR(40) NOT NULL,                       -- 'generado','enviado','descargado','anulado'
    detail TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_certevt_cert FOREIGN KEY (certificate_id) REFERENCES certificates(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======== DESCARGAS ========
CREATE TABLE IF NOT EXISTS download_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    resource VARCHAR(60) NOT NULL,                    -- 'certificado', etc.
    resource_id BIGINT,
    from_ip VARCHAR(45),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_dw_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ======== VISTAS DE APOYO ========
DROP VIEW IF EXISTS apprentice_hours_summary;
CREATE VIEW apprentice_hours_summary AS
SELECT
    u.id AS apprentice_id,
    u.full_name,
    COALESCE(SUM(s.duration_minutes), 0) AS minutes_total,
    COALESCE(ROUND(SUM(s.duration_minutes)/60.0, 2), 0) AS hours_total
FROM users u
LEFT JOIN attendance_sessions s
  ON s.apprentice_id = u.id
WHERE u.status = 'activo'
GROUP BY u.id, u.full_name;

-- ======== TRIGGERS MySQL PARA updated_at ========
DROP TRIGGER IF EXISTS trg_users_updated_at;
DELIMITER $$
CREATE TRIGGER trg_users_updated_at
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
  SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_schedules_updated_at;
DELIMITER $$
CREATE TRIGGER trg_schedules_updated_at
BEFORE UPDATE ON schedules
FOR EACH ROW
BEGIN
  SET NEW.updated_at = CURRENT_TIMESTAMP;
END$$
DELIMITER ;

-- ======== SEMILLAS ========
INSERT INTO roles(name, description) VALUES
    ('Administrador', 'Gestiona usuarios, horarios y certificados'),
    ('Aprendiz', 'Registra asistencia y descarga certificados')
ON DUPLICATE KEY UPDATE id=id;

-- Usuario admin ejemplo (password_hash debe reemplazarse por un hash real)
-- INSERT INTO users (role_id, full_name, email, password_hash) 
-- SELECT id, 'Admin Principal', 'admin@ejemplo.com', '$argon2id$...' FROM roles WHERE name='Administrador';
