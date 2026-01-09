<?php
/**
 * Clase Base Controller
 * Todos los controladores heredan de esta clase
 */

class Controller {
    
    // Cargar modelo
    protected function model($model) {
        $modelPath = __DIR__ . '/../models/' . $model . '.php';
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        } else {
            die('El modelo ' . $model . ' no existe');
        }
    }
    
    // Cargar vista
    protected function view($view, $data = []) {
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            extract($data);
            require_once $viewPath;
        } else {
            die('La vista ' . $view . ' no existe');
        }
    }
    
    // Redireccionar
    protected function redirect($url) {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }
    
    // Verificar si el usuario está autenticado
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Requerir autenticación
    protected function requireAuth() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }
    
    // Obtener usuario actual
    protected function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? '',
                'role' => $_SESSION['role'] ?? 'user'
            ];
        }
        return null;
    }
    
    // Enviar respuesta JSON
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
