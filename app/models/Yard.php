<?php
/**
 * Modelo Yard - Corralones/Parques Vehiculares
 */

require_once __DIR__ . '/Model.php';

class Yard extends Model {
    protected $table = 'yards';
    
    // Obtener todos los corralones activos
    public function getAllActive() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY yard_name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Obtener ocupación actual
    public function getCurrentOccupancy($yardId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as occupied
            FROM impounds
            WHERE yard_id = ? AND status = 'impounded'
        ");
        $stmt->execute([$yardId]);
        $result = $stmt->fetch();
        
        $yard = $this->getById($yardId);
        
        return [
            'capacity' => $yard['capacity'],
            'occupied' => $result['occupied'],
            'available' => $yard['capacity'] - $result['occupied'],
            'percentage' => $yard['capacity'] > 0 ? round(($result['occupied'] / $yard['capacity']) * 100, 2) : 0
        ];
    }
    
    // Obtener vehículos en el corralón
    public function getImpoundedVehicles($yardId, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT i.*, v.plate, v.brand, v.model, v.year, v.color
            FROM impounds i
            INNER JOIN vehicles v ON i.vehicle_id = v.id
            WHERE i.yard_id = ? AND i.status = 'impounded'
            ORDER BY i.impound_date DESC
            LIMIT ?
        ");
        $stmt->execute([$yardId, $limit]);
        return $stmt->fetchAll();
    }
    
    // Estadísticas del corralón
    public function getStats($yardId, $startDate = null, $endDate = null) {
        $sql = "
            SELECT 
                COUNT(*) as total_impounds,
                SUM(CASE WHEN status = 'released' THEN 1 ELSE 0 END) as released,
                SUM(CASE WHEN status = 'impounded' THEN 1 ELSE 0 END) as current_impounded,
                AVG(CASE WHEN status = 'released' AND release_date IS NOT NULL 
                    THEN DATEDIFF(release_date, impound_date) ELSE NULL END) as avg_days_stored,
                SUM(total_amount) as total_revenue
            FROM impounds
            WHERE yard_id = ?
        ";
        
        $params = [$yardId];
        
        if ($startDate) {
            $sql .= " AND impound_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND impound_date <= ?";
            $params[] = $endDate;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    // Reporte de ingresos/egresos por periodo
    public function getMovementReport($yardId, $startDate, $endDate) {
        $stmt = $this->db->prepare("
            SELECT 
                DATE(impound_date) as date,
                COUNT(*) as entries,
                SUM(CASE WHEN release_date IS NOT NULL AND DATE(release_date) = DATE(impound_date) THEN 1 ELSE 0 END) as exits
            FROM impounds
            WHERE yard_id = ? AND impound_date BETWEEN ? AND ?
            GROUP BY DATE(impound_date)
            ORDER BY date DESC
        ");
        $stmt->execute([$yardId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }
}
