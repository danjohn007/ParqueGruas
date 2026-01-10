<?php
/**
 * Modelo de Usuario
 */

require_once __DIR__ . '/Model.php';

class User extends Model {
    protected $table = 'users';
    
    // Autenticar usuario
    public function authenticate($usernameOrEmail, $password) {
        // Permitir login con username o email
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE (username = ? OR email = ?) AND status = 'active'");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    // Crear usuario con contraseña hasheada
    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->create($data);
    }
    
    // Actualizar usuario
    public function updateUser($id, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        return $this->update($id, $data);
    }
    
    // Verificar si el username ya existe
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    // Obtener usuarios por rol
    public function getByRole($role) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE role = ? ORDER BY full_name");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }
}
