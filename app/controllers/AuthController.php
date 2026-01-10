<?php
/**
 * Controlador de Autenticación
 */

require_once __DIR__ . '/Controller.php';

class AuthController extends Controller {
    
    // Mostrar página de login
    public function login() {
        // Si ya está autenticado, redirigir al dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        
        $data = ['title' => 'Iniciar Sesión'];
        $this->view('auth/login', $data);
    }
    
    // Procesar login
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Por favor ingrese usuario/correo y contraseña';
            $this->redirect('/login');
        }
        
        $userModel = $this->model('User');
        $user = $userModel->authenticate($username, $password);
        
        if ($user) {
            // Iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            $_SESSION['success'] = '¡Bienvenido ' . $user['full_name'] . '!';
            $this->redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Usuario/correo o contraseña incorrectos';
            $this->redirect('/login');
        }
    }
    
    // Cerrar sesión
    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
