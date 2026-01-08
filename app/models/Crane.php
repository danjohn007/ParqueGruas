<?php
/**
 * Modelo de Grúa
 */

require_once __DIR__ . '/Model.php';

class Crane extends Model {
    protected $table = 'cranes';
    
    // Obtener grúas por estado
    public function getByStatus($status) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = ? ORDER BY crane_number");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
    
    // Obtener grúas disponibles
    public function getAvailable() {
        return $this->getByStatus('available');
    }
    
    // Contar por estado
    public function countByStatus() {
        $stmt = $this->db->query("SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status");
        return $stmt->fetchAll();
    }
    
    // Verificar si el número de grúa existe
    public function craneNumberExists($craneNumber, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE crane_number = ?";
        $params = [$craneNumber];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    // Obtener grúas que requieren mantenimiento pronto
    public function getMaintenanceDue($days = 30) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE next_maintenance IS NOT NULL 
            AND next_maintenance <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
            ORDER BY next_maintenance ASC
        ");
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}
