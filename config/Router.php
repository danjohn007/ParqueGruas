<?php
/**
 * Router - Maneja el enrutamiento de URLs
 */

class Router {
    private $controller = 'DashboardController';
    private $method = 'index';
    private $params = [];
    
    public function __construct() {
        $url = $this->parseUrl();
        
        // Manejar rutas especiales de autenticación
        if (!empty($url) && in_array($url[0], ['login', 'logout'])) {
            $this->controller = 'AuthController';
            $this->method = $url[0];
            unset($url[0]);
            $this->params = $url ? array_values($url) : [];
            $this->loadController();
            call_user_func_array([$this->controller, $this->method], $this->params);
            return;
        }
        
        // Si no hay URL, verificar si el usuario está autenticado
        // Nota: session_start() debe haberse llamado antes (ver public/index.php)
        if (empty($url)) {
            // Si no está autenticado, mostrar login
            if (!isset($_SESSION['user_id'])) {
                $this->controller = 'AuthController';
                $this->method = 'login';
            }
            // Si está autenticado, usar controlador por defecto (DashboardController)
            $this->loadController();
            call_user_func_array([$this->controller, $this->method], $this->params);
            return;
        }
        
        // Buscar controlador
        $controllerName = ucfirst($url[0]) . 'Controller';
        $controllerPath = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
        
        if (file_exists($controllerPath)) {
            $this->controller = $controllerName;
            unset($url[0]);
        }
        
        $this->loadController();
        
        // Buscar método
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        // Obtener parámetros
        $this->params = $url ? array_values($url) : [];
        
        // Llamar al método del controlador con parámetros
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
    
    private function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
    
    private function loadController() {
        require_once __DIR__ . '/../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;
    }
}
