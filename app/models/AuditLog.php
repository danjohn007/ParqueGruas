<?php
/**
 * Modelo AuditLog - Auditoría del Sistema
 */

require_once __DIR__ . '/Model.php';

class AuditLog extends Model {
    protected $table = 'audit_log';
    
    // Registrar acción en el log
    public function log($userId, $action, $tableName, $recordId, $oldValues = null, $newValues = null) {
        // Obtener información adicional
        $username = null;
        if ($userId) {
            $stmt = $this->db->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            $username = $user ? $user['username'] : null;
        }
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $data = [
            'user_id' => $userId,
            'username' => $username,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ];
        
        return $this->create($data);
    }
    
    // Obtener registros de auditoría por usuario
    public function getByUser($userId, $limit = 100) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    // Obtener registros de auditoría por tabla
    public function getByTable($tableName, $limit = 100) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE table_name = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$tableName, $limit]);
        return $stmt->fetchAll();
    }
    
    // Obtener registros de auditoría por registro específico
    public function getByRecord($tableName, $recordId, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE table_name = ? AND record_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$tableName, $recordId, $limit]);
        return $stmt->fetchAll();
    }
    
    // Obtener registros de auditoría por acción
    public function getByAction($action, $limit = 100) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE action = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$action, $limit]);
        return $stmt->fetchAll();
    }
    
    // Buscar en logs con filtros
    public function search($filters = [], $limit = 100) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['username'])) {
            $sql .= " AND username LIKE ?";
            $params[] = '%' . $filters['username'] . '%';
        }
        
        if (!empty($filters['action'])) {
            $sql .= " AND action = ?";
            $params[] = $filters['action'];
        }
        
        if (!empty($filters['table_name'])) {
            $sql .= " AND table_name = ?";
            $params[] = $filters['table_name'];
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND created_at >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND created_at <= ?";
            $params[] = $filters['end_date'];
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Limpiar logs antiguos
    public function cleanup($days = 90) {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        return $stmt->execute([$days]);
    }
}
