<?php
/**
 * Configuración General del Sistema
 * Parque de Grúas - Sistema Integral de Gestión
 */

// Configuración de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'recurso_gruas');
define('DB_USER', 'recurso_gruas');
define('DB_PASS', 'Danjohn007!');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la Aplicación
define('APP_NAME', 'Parque de Grúas - Sistema Integral');
define('APP_VERSION', '1.0.0');

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Mostrar errores en desarrollo (cambiar a 0 en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Auto-detección de URL Base
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = str_replace('/index.php', '', $script);
    $path = rtrim($path, '/');
    return $protocol . '://' . $host . $path;
}

define('BASE_URL', getBaseUrl());
define('BASE_PATH', rtrim(str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']), '/'));

// Configuración de API HikVision
define('HIKVISION_API_ENABLED', true);
define('HIKVISION_DEVICES', []); // Se configuran en la base de datos

// Configuración de archivos
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
