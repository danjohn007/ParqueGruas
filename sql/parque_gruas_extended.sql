-- Base de datos para Sistema Integral de Gestión de Parque de Grúas
-- VERSIÓN EXTENDIDA con más datos de ejemplo del estado de Querétaro

CREATE DATABASE IF NOT EXISTS parque_gruas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE parque_gruas;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'operator', 'viewer') DEFAULT 'operator',
    phone VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de grúas
CREATE TABLE IF NOT EXISTS cranes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crane_number VARCHAR(20) UNIQUE NOT NULL,
    plate VARCHAR(20) NOT NULL,
    brand VARCHAR(50),
    model VARCHAR(50),
    year INT,
    capacity_tons DECIMAL(5,2),
    status ENUM('available', 'in_service', 'maintenance', 'inactive') DEFAULT 'available',
    driver_name VARCHAR(100),
    driver_license VARCHAR(50),
    last_maintenance DATE,
    next_maintenance DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de vehículos infractores
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plate VARCHAR(20) NOT NULL,
    brand VARCHAR(50),
    model VARCHAR(50),
    year INT,
    color VARCHAR(30),
    vehicle_type ENUM('auto', 'moto', 'camioneta', 'camion', 'otro') DEFAULT 'auto',
    owner_name VARCHAR(100),
    owner_phone VARCHAR(20),
    owner_address TEXT,
    vin VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_plate (plate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de corralón (registros de grúas)
CREATE TABLE IF NOT EXISTS impounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(20) UNIQUE NOT NULL,
    vehicle_id INT NOT NULL,
    crane_id INT,
    infraction_type VARCHAR(100) NOT NULL,
    infraction_location TEXT NOT NULL,
    municipality VARCHAR(100) NOT NULL,
    impound_date DATETIME NOT NULL,
    release_date DATETIME,
    status ENUM('impounded', 'released', 'pending') DEFAULT 'impounded',
    officer_name VARCHAR(100),
    officer_badge VARCHAR(50),
    tow_cost DECIMAL(10,2) DEFAULT 0.00,
    storage_days INT DEFAULT 0,
    storage_cost_per_day DECIMAL(10,2) DEFAULT 100.00,
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    paid BOOLEAN DEFAULT FALSE,
    payment_id INT,
    observations TEXT,
    photos JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (crane_id) REFERENCES cranes(id) ON DELETE SET NULL,
    INDEX idx_folio (folio),
    INDEX idx_status (status),
    INDEX idx_impound_date (impound_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de pagos
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    impound_id INT NOT NULL,
    receipt_number VARCHAR(50) UNIQUE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'transfer', 'check') DEFAULT 'cash',
    payment_date DATETIME NOT NULL,
    cashier_name VARCHAR(100),
    user_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (impound_id) REFERENCES impounds(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_receipt (receipt_number),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de dispositivos HikVision
CREATE TABLE IF NOT EXISTS hikvision_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(100) NOT NULL,
    device_ip VARCHAR(50) NOT NULL,
    device_port INT DEFAULT 80,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    device_type ENUM('camera', 'dvr', 'nvr', 'access_control') DEFAULT 'camera',
    location VARCHAR(100),
    status ENUM('active', 'inactive', 'error') DEFAULT 'active',
    last_connection DATETIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de actividades/eventos para el calendario
CREATE TABLE IF NOT EXISTS calendar_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_type ENUM('maintenance', 'inspection', 'meeting', 'training', 'other') DEFAULT 'other',
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    all_day BOOLEAN DEFAULT FALSE,
    location VARCHAR(200),
    created_by INT,
    color VARCHAR(20) DEFAULT '#3788d8',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_start_date (start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de configuración del sistema
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS DE EJEMPLO EXTENDIDOS
-- ============================================

-- Insertar usuarios (Contraseña por defecto: admin123)
INSERT INTO users (username, password, full_name, email, role, phone, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador Sistema', 'admin@parquegruas.com', 'admin', '4421234567', 'active'),
('operador1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan Pérez García', 'operador1@parquegruas.com', 'operator', '4421234568', 'active'),
('operador2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María González López', 'operador2@parquegruas.com', 'operator', '4421234569', 'active'),
('operador3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Roberto Martínez Silva', 'operador3@parquegruas.com', 'operator', '4421234570', 'active'),
('visualizador1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carmen Ruiz Flores', 'viewer1@parquegruas.com', 'viewer', '4421234571', 'active');

-- Insertar grúas de ejemplo (Querétaro)
INSERT INTO cranes (crane_number, plate, brand, model, year, capacity_tons, status, driver_name, driver_license, last_maintenance, next_maintenance, notes) VALUES
('GR-001', 'QRO-001-A', 'International', 'DuraStar', 2020, 3.5, 'available', 'Pedro Ramírez Soto', 'Q1234567', '2024-11-15', '2025-02-15', 'Grúa en excelente estado'),
('GR-002', 'QRO-002-A', 'Freightliner', 'M2 106', 2021, 5.0, 'available', 'Carlos Sánchez Díaz', 'Q2345678', '2024-12-01', '2025-03-01', 'Mayor capacidad de carga'),
('GR-003', 'QRO-003-A', 'Kenworth', 'T270', 2019, 4.0, 'in_service', 'Roberto Torres Luna', 'Q3456789', '2024-10-20', '2025-01-20', 'Actualmente en servicio'),
('GR-004', 'QRO-004-A', 'International', 'DuraStar', 2022, 3.5, 'available', 'Luis Morales Castro', 'Q4567890', '2024-12-15', '2025-03-15', 'Grúa nueva'),
('GR-005', 'QRO-005-A', 'Hino', '268A', 2021, 3.0, 'maintenance', 'Jorge Hernández Villa', 'Q5678901', '2024-11-30', '2025-02-28', 'En mantenimiento preventivo'),
('GR-006', 'QRO-006-A', 'Peterbilt', '337', 2020, 4.5, 'available', 'Fernando García Ortiz', 'Q6789012', '2024-12-10', '2025-03-10', 'Grúa de alta capacidad'),
('GR-007', 'QRO-007-A', 'Freightliner', 'Business Class M2', 2023, 5.5, 'available', 'Andrés López Vega', 'Q7890123', '2025-01-05', '2025-04-05', 'Grúa más reciente'),
('GR-008', 'QRO-008-A', 'International', 'CV', 2018, 3.0, 'available', 'Miguel Ángel Ramos', 'Q8901234', '2024-11-20', '2025-02-20', 'Grúa ligera para maniobras');

-- Insertar vehículos de ejemplo (más ejemplos)
INSERT INTO vehicles (plate, brand, model, year, color, vehicle_type, owner_name, owner_phone, owner_address, vin, notes) VALUES
('ABC-123-D', 'Nissan', 'Versa', 2019, 'Blanco', 'auto', 'José García Martínez', '4421111111', 'Av. Constituyentes 100, Querétaro, Qro.', '3N1CN7AP1KL123456', ''),
('XYZ-456-E', 'Volkswagen', 'Jetta', 2020, 'Gris', 'auto', 'Ana López Rodríguez', '4421111112', 'Blvd. Bernardo Quintana 200, Querétaro, Qro.', '3VWC57BU5LM234567', ''),
('DEF-789-F', 'Toyota', 'Corolla', 2018, 'Negro', 'auto', 'Carlos Hernández Pérez', '4421111113', 'Av. 5 de Febrero 300, Querétaro, Qro.', '2T1BURHE9JC345678', ''),
('GHI-012-G', 'Honda', 'Civic', 2021, 'Rojo', 'auto', 'Laura Martínez González', '4421111114', 'Av. Universidad 400, Querétaro, Qro.', '19XFC2F59ME456789', ''),
('JKL-345-H', 'Chevrolet', 'Aveo', 2017, 'Azul', 'auto', 'Miguel Sánchez López', '4421111115', 'Av. Zaragoza 500, Querétaro, Qro.', 'KL1TD66E7HB567890', ''),
('MNO-678-I', 'Ford', 'Escape', 2019, 'Blanco', 'camioneta', 'Patricia Ramírez Torres', '4421111116', 'Prolongación Corregidora 600, Querétaro, Qro.', '1FMCU0GD6KUA678901', ''),
('PQR-901-J', 'Mazda', '3', 2020, 'Gris', 'auto', 'Roberto Flores Díaz', '4421111117', 'Av. Paseo de la República 700, Querétaro, Qro.', 'JM1BPBCM5L1789012', ''),
('STU-234-K', 'Kia', 'Rio', 2018, 'Plata', 'auto', 'Sandra Morales Castro', '4421111118', 'Circuito Universitario 800, Querétaro, Qro.', 'KNADH4A37J6890123', ''),
('VWX-567-L', 'Seat', 'Ibiza', 2021, 'Rojo', 'auto', 'Fernando Jiménez Cruz', '4421111119', 'Av. Antea 900, Querétaro, Qro.', 'VSSZZZ6L2MR901234', ''),
('YZA-890-M', 'Hyundai', 'Accent', 2019, 'Blanco', 'auto', 'Diana Vargas Núñez', '4421111120', 'Blvd. de los Arcos 1000, Querétaro, Qro.', 'KMHCT4AE5KU012345', ''),
('BCD-123-N', 'Chevrolet', 'Trax', 2020, 'Negro', 'camioneta', 'Eduardo Salazar Gómez', '4421111121', 'Av. Pie de la Cuesta 1100, Querétaro, Qro.', '3GNCJKSB8LL123456', ''),
('EFG-456-O', 'Nissan', 'Kicks', 2022, 'Azul', 'camioneta', 'Gabriela Torres Ávila', '4421111122', 'Circuito Tecnológico 1200, Querétaro, Qro.', '3N1CP5CV5NL234567', ''),
('HIJ-789-P', 'Honda', 'CR-V', 2021, 'Gris', 'camioneta', 'Alberto Méndez Parra', '4421111123', 'Av. del Parque 1300, Querétaro, Qro.', '2HKRW2H84MH345678', ''),
('KLM-012-Q', 'Toyota', 'RAV4', 2020, 'Blanco', 'camioneta', 'Claudia Reyes Moreno', '4421111124', 'Blvd. Hacienda del Carmen 1400, Qro.', '2T3F1RFV0LW456789', ''),
('NOP-345-R', 'Volkswagen', 'Tiguan', 2019, 'Negro', 'camioneta', 'Ricardo Navarro Gil', '4421111125', 'Calle Hidalgo 1500, Centro, Qro.', '3VV2B7AX9KM567890', ''),
('QRS-678-S', 'Suzuki', 'Swift', 2018, 'Rojo', 'auto', 'Verónica Campos Reyes', '4421111126', 'Av. Corregidora Norte 1600, Qro.', 'JS2YB413085678901', ''),
('TUV-901-T', 'Mazda', 'CX-5', 2021, 'Azul', 'camioneta', 'Javier Ortiz Beltrán', '4421111127', 'Paseo de la Reforma 1700, Qro.', 'JM3KFBDM9M0789012', 'SUV familiar'),
('WXY-234-U', 'Ford', 'Ranger', 2020, 'Gris', 'camioneta', 'Mariana Silva Duarte', '4421111128', 'Av. Constitución 1800, Qro.', '8AFBR3CH4L3890123', 'Camioneta de trabajo'),
('ZAB-567-V', 'Chevrolet', 'Spark', 2017, 'Amarillo', 'auto', 'Daniel Cruz Aguilar', '4421111129', 'Calle Allende 1900, Qro.', 'KL8CB6S97HC901234', ''),
('CDE-890-W', 'Nissan', 'Sentra', 2022, 'Plata', 'auto', 'Mónica Guerrero Lara', '4421111130', 'Av. Universidad 2000, Qro.', '3N1AB8BV8NY012345', 'Vehículo nuevo');

-- Insertar registros de corralón (más infracciones)
INSERT INTO impounds (folio, vehicle_id, crane_id, infraction_type, infraction_location, municipality, impound_date, status, officer_name, officer_badge, tow_cost, storage_days, storage_cost_per_day, fine_amount, total_amount, paid, observations) VALUES
('QRO-2024-001', 1, 1, 'Estacionamiento en lugar prohibido', 'Av. Constituyentes esquina con Zaragoza', 'Querétaro', '2024-12-15 10:30:00', 'released', 'Agente López', 'AG-001', 800.00, 2, 100.00, 1500.00, 2500.00, TRUE, 'Pagado en efectivo'),
('QRO-2024-002', 2, 2, 'Abandono de vehículo en vía pública', 'Blvd. Bernardo Quintana altura del Arco', 'Querétaro', '2024-12-18 14:20:00', 'released', 'Agente Martínez', 'AG-002', 800.00, 5, 100.00, 2000.00, 3300.00, TRUE, 'Pagado con tarjeta'),
('QRO-2024-003', 3, 3, 'Obstrucción de entrada vehicular', 'Calle 5 de Mayo Col. Centro', 'Querétaro', '2024-12-20 09:15:00', 'impounded', 'Agente Ramírez', 'AG-003', 800.00, 8, 100.00, 1200.00, 2800.00, FALSE, 'Pendiente de pago'),
('QRO-2024-004', 4, 1, 'Estacionamiento en zona de discapacitados', 'Plaza de Armas, Centro Histórico', 'Querétaro', '2024-12-22 16:45:00', 'impounded', 'Agente González', 'AG-004', 800.00, 6, 100.00, 2500.00, 3900.00, FALSE, ''),
('QRO-2024-005', 5, 2, 'Estacionamiento en doble fila', 'Av. Universidad frente a Tec de Monterrey', 'Querétaro', '2024-12-25 11:00:00', 'released', 'Agente Torres', 'AG-005', 800.00, 1, 100.00, 1000.00, 1900.00, TRUE, 'Transferencia bancaria'),
('QRO-2024-006', 6, 4, 'Vehículo sin placas', 'Av. 5 de Febrero Col. Niños Héroes', 'Querétaro', '2024-12-28 13:30:00', 'impounded', 'Agente Sánchez', 'AG-006', 800.00, 3, 100.00, 3000.00, 4100.00, FALSE, ''),
('QRO-2024-007', 7, 1, 'Estacionamiento en rampa para discapacitados', 'Av. Paseo de la República Centro Sur', 'Querétaro', '2024-12-30 08:20:00', 'impounded', 'Agente Morales', 'AG-007', 800.00, 2, 100.00, 2500.00, 3500.00, FALSE, ''),
('QRO-2025-001', 8, 2, 'Estacionamiento en ciclovía', 'Circuito Universitario altura Cimatario', 'Querétaro', '2025-01-02 15:10:00', 'impounded', 'Agente Díaz', 'AG-008', 800.00, 6, 100.00, 1500.00, 2900.00, FALSE, ''),
('QRO-2025-002', 9, 6, 'Estacionamiento en zona peatonal', 'Centro Comercial Antea', 'Querétaro', '2025-01-03 09:30:00', 'impounded', 'Agente Castillo', 'AG-009', 800.00, 1, 100.00, 1800.00, 2700.00, FALSE, ''),
('QRO-2025-003', 10, 7, 'Obstrucción de cajón de estacionamiento', 'Plaza de Toros Santa María', 'Querétaro', '2025-01-04 14:15:00', 'released', 'Agente Guzmán', 'AG-010', 800.00, 2, 100.00, 1200.00, 2200.00, TRUE, 'Pagado en efectivo'),
('QRO-2025-004', 11, 8, 'Estacionamiento en zona de carga y descarga', 'Mercado La Cruz', 'Querétaro', '2025-01-05 11:45:00', 'impounded', 'Agente Fernández', 'AG-011', 800.00, 4, 100.00, 1500.00, 2700.00, FALSE, ''),
('QRO-2025-005', 12, 1, 'Abandono de vehículo', 'Av. Pie de la Cuesta', 'Querétaro', '2025-01-06 16:20:00', 'impounded', 'Agente Rojas', 'AG-012', 800.00, 7, 100.00, 2500.00, 4000.00, FALSE, ''),
('QRO-2025-006', 13, 2, 'Estacionamiento en esquina', 'Calle Madero esquina Juárez', 'Querétaro', '2025-01-07 10:00:00', 'impounded', 'Agente Mendoza', 'AG-013', 800.00, 3, 100.00, 1000.00, 2100.00, FALSE, ''),
('QRO-2025-007', 14, 3, 'Vehículo estacionado más de 72 horas', 'Col. Jardines de la Hacienda', 'Querétaro', '2025-01-08 13:30:00', 'impounded', 'Agente Silva', 'AG-014', 800.00, 5, 100.00, 1500.00, 2800.00, FALSE, ''),
('QRO-2025-008', 15, 4, 'Obstrucción de vía pública', 'Blvd. de los Arcos', 'Querétaro', '2025-01-09 08:15:00', 'impounded', 'Agente Vázquez', 'AG-015', 800.00, 0, 100.00, 2000.00, 2800.00, FALSE, 'Recién ingresado');

-- Insertar pagos realizados
INSERT INTO payments (impound_id, receipt_number, amount, payment_method, payment_date, cashier_name, user_id, notes) VALUES
(1, 'REC-2024-001', 2500.00, 'cash', '2024-12-17 11:00:00', 'Cajero Principal', 2, 'Pago completo en efectivo'),
(2, 'REC-2024-002', 3300.00, 'card', '2024-12-23 10:30:00', 'Cajero Principal', 2, 'Pago con tarjeta Visa'),
(5, 'REC-2024-003', 1900.00, 'transfer', '2024-12-26 14:15:00', 'Cajero Secundario', 3, 'Transferencia SPEI'),
(10, 'REC-2025-001', 2200.00, 'cash', '2025-01-04 16:00:00', 'Cajero Principal', 2, 'Pago en efectivo');

-- Actualizar el campo paid en impounds
UPDATE impounds SET paid = TRUE, payment_id = 1, release_date = '2024-12-17 12:00:00', status = 'released' WHERE id = 1;
UPDATE impounds SET paid = TRUE, payment_id = 2, release_date = '2024-12-23 11:00:00', status = 'released' WHERE id = 2;
UPDATE impounds SET paid = TRUE, payment_id = 3, release_date = '2024-12-26 15:00:00', status = 'released' WHERE id = 5;
UPDATE impounds SET paid = TRUE, payment_id = 4, release_date = '2025-01-04 17:00:00', status = 'released' WHERE id = 10;

-- Insertar dispositivos HikVision de ejemplo
INSERT INTO hikvision_devices (device_name, device_ip, device_port, username, password, device_type, location, status) VALUES
('Cámara Entrada Principal', '192.168.1.64', 80, 'admin', 'hikvision123', 'camera', 'Puerta de entrada al corralón', 'active'),
('NVR Principal', '192.168.1.65', 8000, 'admin', 'hikvision123', 'nvr', 'Oficina de monitoreo', 'active'),
('Cámara Área de Almacenamiento', '192.168.1.66', 80, 'admin', 'hikvision123', 'camera', 'Área de vehículos almacenados', 'active'),
('Control de Acceso Principal', '192.168.1.67', 80, 'admin', 'hikvision123', 'access_control', 'Caseta de entrada', 'active'),
('Cámara Salida Vehículos', '192.168.1.68', 80, 'admin', 'hikvision123', 'camera', 'Salida del corralón', 'active'),
('Cámara Oficinas', '192.168.1.69', 80, 'admin', 'hikvision123', 'camera', 'Área de oficinas administrativas', 'active');

-- Insertar eventos de calendario
INSERT INTO calendar_events (title, description, event_type, start_date, end_date, all_day, location, created_by, color) VALUES
('Mantenimiento Grúa GR-005', 'Mantenimiento preventivo programado', 'maintenance', '2025-02-28 08:00:00', '2025-02-28 17:00:00', FALSE, 'Taller mecánico', 1, '#e74c3c'),
('Inspección Mensual Corralón', 'Inspección de seguridad mensual', 'inspection', '2025-02-15 09:00:00', '2025-02-15 12:00:00', FALSE, 'Instalaciones del corralón', 1, '#f39c12'),
('Capacitación Personal', 'Capacitación en nuevos procedimientos', 'training', '2025-02-10 10:00:00', '2025-02-10 14:00:00', FALSE, 'Sala de juntas', 1, '#3498db'),
('Reunión Mensual', 'Revisión de operaciones del mes', 'meeting', '2025-02-05 15:00:00', '2025-02-05 17:00:00', FALSE, 'Oficina administrativa', 1, '#9b59b6'),
('Mantenimiento Grúa GR-001', 'Revisión programada', 'maintenance', '2025-02-15 08:00:00', '2025-02-15 16:00:00', FALSE, 'Taller mecánico', 1, '#e74c3c'),
('Capacitación Operadores Nuevos', 'Inducción para nuevos operadores', 'training', '2025-02-20 09:00:00', '2025-02-20 18:00:00', FALSE, 'Sala de capacitación', 1, '#3498db'),
('Auditoría Interna', 'Revisión de procesos y documentación', 'inspection', '2025-03-01 08:00:00', '2025-03-01 17:00:00', FALSE, 'Oficinas administrativas', 1, '#f39c12');

-- Insertar configuraciones del sistema extendidas
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
-- Configuraciones básicas
('site_name', 'Parque de Grúas Querétaro', 'Nombre del sitio web'),
('site_logo', '', 'URL del logotipo del sitio'),
('storage_cost_per_day', '100.00', 'Costo de almacenaje por día en pesos'),
('base_tow_cost', '800.00', 'Costo base del servicio de grúa en pesos'),
('company_name', 'Parque de Grúas Querétaro', 'Nombre de la empresa'),
('company_address', 'Av. Constituyentes 1000, Querétaro, Qro.', 'Dirección de la empresa'),
('company_phone', '442-123-4567', 'Teléfono de contacto'),
('company_email', 'contacto@parquegruas.com', 'Email de contacto'),

-- Configuración de email
('email_from', 'admin@parquegruas.com', 'Email principal del sistema'),
('email_from_name', 'Parque de Grúas', 'Nombre del remitente de emails'),
('smtp_host', 'smtp.gmail.com', 'Host del servidor SMTP'),
('smtp_port', '587', 'Puerto del servidor SMTP'),
('smtp_username', '', 'Usuario SMTP'),
('smtp_password', '', 'Contraseña SMTP'),

-- Contacto
('contact_phone_1', '442-123-4567', 'Teléfono de contacto principal'),
('contact_phone_2', '442-765-4321', 'Teléfono de contacto secundario'),
('business_hours', 'Lunes a Viernes: 8:00 AM - 6:00 PM\nSábados: 9:00 AM - 2:00 PM', 'Horarios de atención'),
('contact_address', 'Av. Constituyentes 1000, Querétaro, Qro.', 'Dirección de contacto'),

-- Colores
('primary_color', '#3b82f6', 'Color primario del sistema'),
('secondary_color', '#1e40af', 'Color secundario del sistema'),
('accent_color', '#06b6d4', 'Color de acento del sistema'),

-- PayPal
('paypal_mode', 'sandbox', 'Modo de PayPal (sandbox/live)'),
('paypal_client_id', '', 'Client ID de PayPal'),
('paypal_secret', '', 'Secret de PayPal'),

-- API QR
('qr_api_provider', 'QR Code Generator API', 'Proveedor de API QR'),
('qr_api_key', '', 'API Key para generación de QR'),
('qr_api_endpoint', 'https://api.qrserver.com/v1/create-qr-code/', 'Endpoint de API QR'),

-- Configuraciones globales
('timezone', 'America/Mexico_City', 'Zona horaria del sistema'),
('date_format', 'd/m/Y', 'Formato de fecha'),
('currency', 'MXN', 'Moneda del sistema');
