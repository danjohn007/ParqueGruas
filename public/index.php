<?php
/**
 * Punto de entrada principal del sistema
 * Parque de Grúas - Sistema Integral de Gestión
 */

// Configuración de sesiones (antes de session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 si se usa HTTPS

// Iniciar sesión
session_start();

// Cargar configuración
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Router.php';

// Cargar clases base
require_once __DIR__ . '/../app/controllers/Controller.php';
require_once __DIR__ . '/../app/models/Model.php';

// Inicializar el router
$router = new Router();
