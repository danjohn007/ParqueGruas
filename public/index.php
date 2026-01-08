<?php
/**
 * Punto de entrada principal del sistema
 * Parque de Grúas - Sistema Integral de Gestión
 */

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
