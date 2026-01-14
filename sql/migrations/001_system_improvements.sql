-- ============================================
-- MIGRACION: Mejoras Sustanciales del Sistema
-- Version: 2.0.0
-- Fecha: 2025-01-14
-- ============================================
-- Esta migración agrega las nuevas entidades y funcionalidades
-- preservando la estructura y datos existentes
-- ============================================

USE parque_gruas;

-- ============================================
-- 1. TABLA DE EMPRESAS (Clientes/Convenios)
-- ============================================
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(255) NOT NULL,
    rfc VARCHAR(13) NOT NULL,
    tax_regime VARCHAR(100),
    contact_name VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(10),
    -- Datos fiscales para facturación
    payment_method VARCHAR(50) DEFAULT 'PUE',
    payment_form VARCHAR(50) DEFAULT '01',
    cfdi_use VARCHAR(50) DEFAULT 'G03',
    -- Control
    status ENUM('active', 'inactive') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_rfc (rfc),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. TABLA DE CHOFERES/OPERADORES
-- ============================================
CREATE TABLE IF NOT EXISTS drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_number VARCHAR(20) UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    license_number VARCHAR(50) NOT NULL,
    license_type VARCHAR(20),
    license_expiration DATE,
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    emergency_contact VARCHAR(100),
    emergency_phone VARCHAR(20),
    -- Documentación
    curp VARCHAR(18),
    rfc VARCHAR(13),
    nss VARCHAR(20),
    blood_type VARCHAR(5),
    -- Estado
    status ENUM('active', 'inactive', 'suspended', 'on_leave') DEFAULT 'active',
    hire_date DATE,
    termination_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_employee_number (employee_number),
    INDEX idx_license_number (license_number),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. TABLA DE CORRALONES (Parques vehiculares)
-- ============================================
CREATE TABLE IF NOT EXISTS yards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    yard_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(10),
    capacity INT DEFAULT 0,
    phone VARCHAR(20),
    manager_name VARCHAR(100),
    -- Horarios
    business_hours TEXT,
    -- Estado
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. TABLA DE SERVICIOS (Core Module)
-- ============================================
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(30) UNIQUE NOT NULL,
    client_folio VARCHAR(50),
    -- Relaciones
    company_id INT,
    driver_id INT,
    crane_id INT,
    yard_id INT,
    -- Tipo de servicio
    service_type ENUM('arrastre', 'traslado', 'rescate', 'auxilio', 'otro') DEFAULT 'arrastre',
    -- Fechas
    request_date DATETIME NOT NULL,
    scheduled_date DATETIME,
    start_date DATETIME,
    end_date DATETIME,
    -- Ubicaciones
    origin_address TEXT NOT NULL,
    origin_city VARCHAR(100),
    origin_municipality VARCHAR(100),
    destination_address TEXT NOT NULL,
    destination_city VARCHAR(100),
    destination_municipality VARCHAR(100),
    -- Datos del vehículo del servicio
    vehicle_plate VARCHAR(20),
    vehicle_brand VARCHAR(50),
    vehicle_model VARCHAR(50),
    vehicle_year INT,
    vehicle_color VARCHAR(30),
    -- Costos
    base_cost DECIMAL(10,2) DEFAULT 0.00,
    additional_charges DECIMAL(10,2) DEFAULT 0.00,
    discounts DECIMAL(10,2) DEFAULT 0.00,
    subtotal DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    -- Estado del servicio
    status ENUM('cotizado', 'aceptado', 'asignado', 'en_proceso', 'culminado', 'facturado', 'cobrado', 'cancelado', 'rechazado') DEFAULT 'cotizado',
    cancellation_reason TEXT,
    -- Evidencia
    photos JSON,
    documents JSON,
    -- Observaciones
    description TEXT,
    observations TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL,
    FOREIGN KEY (crane_id) REFERENCES cranes(id) ON DELETE SET NULL,
    FOREIGN KEY (yard_id) REFERENCES yards(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_folio (folio),
    INDEX idx_status (status),
    INDEX idx_request_date (request_date),
    INDEX idx_company (company_id),
    INDEX idx_driver (driver_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. TABLA DE COTIZACIONES
-- ============================================
CREATE TABLE IF NOT EXISTS quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote_number VARCHAR(30) UNIQUE NOT NULL,
    service_id INT,
    company_id INT,
    -- Datos de cotización
    quote_date DATE NOT NULL,
    valid_until DATE,
    -- Conceptos
    description TEXT,
    items JSON,
    -- Montos
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    -- Estado
    status ENUM('pendiente', 'aceptada', 'rechazada', 'vencida') DEFAULT 'pendiente',
    accepted_date DATE,
    rejection_reason TEXT,
    -- Control
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_quote_number (quote_number),
    INDEX idx_status (status),
    INDEX idx_quote_date (quote_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. TABLA DE FACTURAS
-- ============================================
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    -- Relaciones
    service_id INT,
    company_id INT NOT NULL,
    -- UUID de Facturama
    facturama_id VARCHAR(100),
    uuid VARCHAR(100) UNIQUE,
    -- Datos fiscales
    series VARCHAR(10),
    folio VARCHAR(20),
    invoice_date DATE NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'PUE',
    payment_form VARCHAR(50) DEFAULT '01',
    cfdi_use VARCHAR(50) DEFAULT 'G03',
    -- Montos
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    -- Carta Porte (si aplica)
    requires_carta_porte BOOLEAN DEFAULT FALSE,
    carta_porte_data JSON,
    -- Estado
    status ENUM('borrador', 'emitida', 'timbrada', 'cancelada', 'pagada') DEFAULT 'borrador',
    cancellation_date DATE,
    cancellation_reason TEXT,
    -- Archivos
    pdf_url VARCHAR(255),
    xml_url VARCHAR(255),
    -- Control
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_uuid (uuid),
    INDEX idx_status (status),
    INDEX idx_invoice_date (invoice_date),
    INDEX idx_company (company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. TABLA DE CONCEPTOS DE FACTURA
-- ============================================
CREATE TABLE IF NOT EXISTS invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    item_order INT DEFAULT 0,
    -- SAT
    product_code VARCHAR(20),
    unit_code VARCHAR(20),
    -- Descripción
    description TEXT NOT NULL,
    quantity DECIMAL(10,2) DEFAULT 1.00,
    unit_price DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0.00,
    -- Montos
    subtotal DECIMAL(10,2) NOT NULL,
    tax_rate DECIMAL(5,2) DEFAULT 16.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    INDEX idx_invoice (invoice_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. TABLA DE REGLAS DE COMISIONES
-- ============================================
CREATE TABLE IF NOT EXISTS commission_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rule_name VARCHAR(100) NOT NULL,
    rule_type ENUM('driver', 'crane', 'company', 'service_type', 'fixed') DEFAULT 'driver',
    -- Aplica a
    driver_id INT,
    crane_id INT,
    company_id INT,
    service_type VARCHAR(50),
    -- Cálculo
    calculation_type ENUM('percentage', 'fixed') DEFAULT 'percentage',
    commission_value DECIMAL(10,2) NOT NULL,
    min_amount DECIMAL(10,2) DEFAULT 0.00,
    max_amount DECIMAL(10,2) DEFAULT 0.00,
    -- Estado
    is_active BOOLEAN DEFAULT TRUE,
    priority INT DEFAULT 0,
    start_date DATE,
    end_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE CASCADE,
    FOREIGN KEY (crane_id) REFERENCES cranes(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_rule_type (rule_type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. TABLA DE COMISIONES CALCULADAS
-- ============================================
CREATE TABLE IF NOT EXISTS commissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    driver_id INT,
    crane_id INT,
    commission_rule_id INT,
    -- Cálculo
    base_amount DECIMAL(10,2) NOT NULL,
    commission_percentage DECIMAL(5,2) DEFAULT 0.00,
    commission_amount DECIMAL(10,2) NOT NULL,
    -- Estado
    status ENUM('calculada', 'aprobada', 'pagada', 'cancelada') DEFAULT 'calculada',
    payment_date DATE,
    payment_reference VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL,
    FOREIGN KEY (crane_id) REFERENCES cranes(id) ON DELETE SET NULL,
    FOREIGN KEY (commission_rule_id) REFERENCES commission_rules(id) ON DELETE SET NULL,
    INDEX idx_service (service_id),
    INDEX idx_driver (driver_id),
    INDEX idx_status (status),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. TABLA DE ÓRDENES DE TALLER
-- ============================================
CREATE TABLE IF NOT EXISTS workshop_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(30) UNIQUE NOT NULL,
    -- Unidad (grúa o vehículo utilitario)
    crane_id INT,
    vehicle_id INT,
    unit_type ENUM('crane', 'vehicle', 'utility') DEFAULT 'crane',
    -- Orden
    entry_date DATETIME NOT NULL,
    scheduled_exit_date DATE,
    actual_exit_date DATETIME,
    -- Tipo de trabajo
    work_type ENUM('preventivo', 'correctivo', 'emergencia', 'inspeccion') DEFAULT 'correctivo',
    description TEXT NOT NULL,
    diagnosis TEXT,
    work_performed TEXT,
    -- Proveedor/Mecánico
    provider_name VARCHAR(100),
    provider_contact VARCHAR(100),
    mechanic_name VARCHAR(100),
    -- Costos
    labor_cost DECIMAL(10,2) DEFAULT 0.00,
    parts_cost DECIMAL(10,2) DEFAULT 0.00,
    other_costs DECIMAL(10,2) DEFAULT 0.00,
    total_cost DECIMAL(10,2) DEFAULT 0.00,
    -- Estado
    status ENUM('abierta', 'en_proceso', 'esperando_refacciones', 'completada', 'cancelada') DEFAULT 'abierta',
    priority ENUM('baja', 'media', 'alta', 'urgente') DEFAULT 'media',
    -- Odómetro/Horómetro
    odometer_in INT,
    odometer_out INT,
    -- Control
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (crane_id) REFERENCES cranes(id) ON DELETE SET NULL,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_status (status),
    INDEX idx_entry_date (entry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. TABLA DE CONCEPTOS DE TALLER
-- ============================================
CREATE TABLE IF NOT EXISTS workshop_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workshop_order_id INT NOT NULL,
    item_type ENUM('refaccion', 'mano_obra', 'servicio', 'otro') DEFAULT 'refaccion',
    description TEXT NOT NULL,
    part_number VARCHAR(50),
    quantity DECIMAL(10,2) DEFAULT 1.00,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    provider VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (workshop_order_id) REFERENCES workshop_orders(id) ON DELETE CASCADE,
    INDEX idx_workshop_order (workshop_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. TABLA DE AUDITORÍA
-- ============================================
CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(50),
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(50),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_table (table_name),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 13. ACTUALIZACIÓN DE TABLA DE PAGOS
-- ============================================
-- Agregar campos para soportar servicios y facturas
ALTER TABLE payments 
    ADD COLUMN IF NOT EXISTS service_id INT AFTER impound_id,
    ADD COLUMN IF NOT EXISTS invoice_id INT AFTER service_id,
    ADD COLUMN IF NOT EXISTS payment_type ENUM('impound', 'service', 'invoice') DEFAULT 'impound' AFTER payment_method,
    ADD COLUMN IF NOT EXISTS reference_number VARCHAR(100) AFTER receipt_number,
    ADD COLUMN IF NOT EXISTS remaining_balance DECIMAL(10,2) DEFAULT 0.00 AFTER amount;

-- Agregar foreign keys si no existen
SET @stmt = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE CONSTRAINT_NAME = 'fk_payments_service' AND TABLE_NAME = 'payments' AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE payments ADD CONSTRAINT fk_payments_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL',
    'SELECT 1'
));
PREPARE stmt FROM @stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @stmt = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE CONSTRAINT_NAME = 'fk_payments_invoice' AND TABLE_NAME = 'payments' AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE payments ADD CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE SET NULL',
    'SELECT 1'
));
PREPARE stmt FROM @stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Agregar índices
ALTER TABLE payments
    ADD INDEX IF NOT EXISTS idx_service_id (service_id),
    ADD INDEX IF NOT EXISTS idx_invoice_id (invoice_id),
    ADD INDEX IF NOT EXISTS idx_payment_type (payment_type);

-- ============================================
-- 14. ACTUALIZACIÓN DE TABLA DE IMPOUNDS
-- ============================================
-- Agregar relación con yards
ALTER TABLE impounds
    ADD COLUMN IF NOT EXISTS yard_id INT AFTER crane_id;

SET @stmt = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE CONSTRAINT_NAME = 'fk_impounds_yard' AND TABLE_NAME = 'impounds' AND TABLE_SCHEMA = DATABASE()) = 0,
    'ALTER TABLE impounds ADD CONSTRAINT fk_impounds_yard FOREIGN KEY (yard_id) REFERENCES yards(id) ON DELETE SET NULL',
    'SELECT 1'
));
PREPARE stmt FROM @stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE impounds
    ADD INDEX IF NOT EXISTS idx_yard_id (yard_id);

-- ============================================
-- 15. DATOS INICIALES
-- ============================================

-- Insertar corralón principal (basado en datos actuales)
INSERT INTO yards (yard_name, address, city, state, capacity, phone, manager_name, status) 
SELECT 'Corralón Principal Querétaro', 'Av. Constituyentes 1000, Querétaro, Qro.', 'Querétaro', 'Querétaro', 100, '442-123-4567', 'Administrador Sistema', 'active'
WHERE NOT EXISTS (SELECT 1 FROM yards LIMIT 1);

-- Actualizar impounds existentes para asignarlos al corralón principal
UPDATE impounds SET yard_id = (SELECT id FROM yards LIMIT 1) WHERE yard_id IS NULL;

-- Insertar configuraciones adicionales del sistema
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('facturama_api_enabled', 'false', 'Habilitar integración con Facturama'),
('facturama_api_key', '', 'API Key de Facturama'),
('facturama_api_secret', '', 'API Secret de Facturama'),
('facturama_sandbox_mode', 'true', 'Modo sandbox de Facturama (desarrollo)'),
('default_tax_rate', '16.00', 'Tasa de IVA por defecto (%)'),
('commission_approval_required', 'true', 'Las comisiones requieren aprobación antes de pagarse'),
('service_folio_prefix', 'SRV', 'Prefijo para folios de servicios'),
('quote_folio_prefix', 'COT', 'Prefijo para folios de cotizaciones'),
('invoice_series', 'A', 'Serie para facturas'),
('workshop_order_prefix', 'TAL', 'Prefijo para órdenes de taller')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ============================================
-- MIGRACIÓN COMPLETA
-- ============================================
-- La migración preserva todos los datos existentes
-- Las tablas nuevas están vacías y listas para usar
-- Los impounds existentes se vinculan al corralón principal
-- ============================================
