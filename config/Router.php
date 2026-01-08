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
        
        // Si no hay URL, usar el controlador por defecto
        if (empty($url)) {
            $this->loadController();
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
