<?php
/**
 * Controlador de Usuarios
 */

require_once __DIR__ . '/Controller.php';

class UsersController extends Controller {
    
    // Listar usuarios
    public function index() {
        $this->requireAuth();
        
        // Solo admin puede gestionar usuarios
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tiene permisos para acceder a esta sección';
            $this->redirect('/dashboard');
        }
        
        $userModel = $this->model('User');
        $users = $userModel->getAll();
        
        $data = [
            'title' => 'Gestión de Usuarios',
            'subtitle' => 'Administración de usuarios del sistema',
            'users' => $users
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/users/index.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Ver detalle de usuario
    public function details($id) {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $id) {
            $_SESSION['error'] = 'No tiene permisos para ver este usuario';
            $this->redirect('/dashboard');
        }
        
        $userModel = $this->model('User');
        $user = $userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Usuario no encontrado';
            $this->redirect('/users');
        }
        
        $data = [
            'title' => 'Perfil de Usuario',
            'subtitle' => $user['full_name'],
            'user' => $user
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/users/view.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Formulario crear usuario
    public function create() {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tiene permisos para crear usuarios';
            $this->redirect('/dashboard');
        }
        
        $data = [
            'title' => 'Nuevo Usuario',
            'subtitle' => 'Registrar nuevo usuario del sistema'
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/users/create.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Guardar usuario
    public function store() {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tiene permisos';
            $this->redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/users/create');
        }
        
        $userModel = $this->model('User');
        
        $data = [
            'username' => $_POST['username'] ?? '',
            'password' => $_POST['password'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role' => $_POST['role'] ?? 'operator',
            'phone' => $_POST['phone'] ?? '',
            'status' => $_POST['status'] ?? 'active'
        ];
        
        // Validaciones
        if (empty($data['username']) || empty($data['password']) || empty($data['full_name']) || empty($data['email'])) {
            $_SESSION['error'] = 'Todos los campos obligatorios deben ser llenados';
            $this->redirect('/users/create');
        }
        
        if ($userModel->usernameExists($data['username'])) {
            $_SESSION['error'] = 'El nombre de usuario ya existe';
            $this->redirect('/users/create');
        }
        
        if ($userModel->createUser($data)) {
            $_SESSION['success'] = 'Usuario creado exitosamente';
            $this->redirect('/users');
        } else {
            $_SESSION['error'] = 'Error al crear el usuario';
            $this->redirect('/users/create');
        }
    }
    
    // Formulario editar usuario
    public function edit($id) {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $id) {
            $_SESSION['error'] = 'No tiene permisos para editar este usuario';
            $this->redirect('/dashboard');
        }
        
        $userModel = $this->model('User');
        $user = $userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Usuario no encontrado';
            $this->redirect('/users');
        }
        
        $data = [
            'title' => 'Editar Usuario',
            'subtitle' => $user['full_name'],
            'user' => $user
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/users/edit.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Actualizar usuario
    public function update($id) {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $id) {
            $_SESSION['error'] = 'No tiene permisos';
            $this->redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/users/edit/' . $id);
        }
        
        $userModel = $this->model('User');
        
        $data = [
            'username' => $_POST['username'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
        ];
        
        // Solo admin puede cambiar rol y estado
        if ($_SESSION['role'] === 'admin') {
            $data['role'] = $_POST['role'] ?? 'operator';
            $data['status'] = $_POST['status'] ?? 'active';
        }
        
        // Si se proporciona contraseña, incluirla
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }
        
        if ($userModel->updateUser($id, $data)) {
            $_SESSION['success'] = 'Usuario actualizado exitosamente';
            
            // Si el usuario editado es el actual, actualizar sesión
            if ($_SESSION['user_id'] == $id) {
                $_SESSION['full_name'] = $data['full_name'];
                $_SESSION['email'] = $data['email'];
            }
            
            $this->redirect('/users/details/' . $id);
        } else {
            $_SESSION['error'] = 'Error al actualizar el usuario';
            $this->redirect('/users/edit/' . $id);
        }
    }
    
    // Eliminar usuario
    public function delete($id) {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tiene permisos para eliminar usuarios';
            $this->redirect('/users');
        }
        
        // No permitir eliminar el propio usuario
        if ($_SESSION['user_id'] == $id) {
            $_SESSION['error'] = 'No puede eliminar su propio usuario';
            $this->redirect('/users');
        }
        
        $userModel = $this->model('User');
        
        if ($userModel->delete($id)) {
            $_SESSION['success'] = 'Usuario eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el usuario';
        }
        
        $this->redirect('/users');
    }
}
