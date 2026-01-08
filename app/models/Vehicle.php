<?php
/**
 * Modelo de Vehículo
 */

require_once __DIR__ . '/Model.php';

class Vehicle extends Model {
    protected $table = 'vehicles';
    
    // Buscar vehículo por placa
    public function getByPlate($plate) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE plate = ?");
        $stmt->execute([$plate]);
        return $stmt->fetch();
    }
    
    // Buscar vehículos con filtros
    public function search($filters = []) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if (!empty($filters['plate'])) {
            $sql .= " AND plate LIKE ?";
            $params[] = '%' . $filters['plate'] . '%';
        }
        
        if (!empty($filters['owner_name'])) {
            $sql .= " AND owner_name LIKE ?";
            $params[] = '%' . $filters['owner_name'] . '%';
        }
        
        if (!empty($filters['brand'])) {
            $sql .= " AND brand LIKE ?";
            $params[] = '%' . $filters['brand'] . '%';
        }
        
        if (!empty($filters['vehicle_type'])) {
            $sql .= " AND vehicle_type = ?";
            $params[] = $filters['vehicle_type'];
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Contar vehículos por tipo
    public function countByType() {
        $stmt = $this->db->query("SELECT vehicle_type, COUNT(*) as count FROM {$this->table} GROUP BY vehicle_type");
        return $stmt->fetchAll();
    }
}
