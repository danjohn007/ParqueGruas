<?php
/**
 * Modelo Driver - Choferes/Operadores
 */

require_once __DIR__ . '/Model.php';

class Driver extends Model {
    protected $table = 'drivers';
    
    // Obtener todos los conductores activos
    public function getAllActive() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY full_name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Buscar por número de licencia
    public function getByLicense($licenseNumber) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE license_number = ?");
        $stmt->execute([$licenseNumber]);
        return $stmt->fetch();
    }
    
    // Obtener licencias próximas a vencer
    public function getExpiringSoon($days = 30) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE status = 'active'
            AND license_expiration IS NOT NULL
            AND license_expiration <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
            AND license_expiration >= CURDATE()
            ORDER BY license_expiration
        ");
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
    
    // Obtener servicios de un chofer
    public function getServices($driverId, $startDate = null, $endDate = null, $limit = 20) {
        $sql = "
            SELECT s.*, c.crane_number, co.business_name as company_name
            FROM services s
            LEFT JOIN cranes c ON s.crane_id = c.id
            LEFT JOIN companies co ON s.company_id = co.id
            WHERE s.driver_id = ?
        ";
        
        $params = [$driverId];
        
        if ($startDate) {
            $sql .= " AND s.request_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND s.request_date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY s.request_date DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Obtener comisiones de un chofer
    public function getCommissions($driverId, $status = null) {
        $sql = "
            SELECT c.*, s.folio as service_folio, s.service_type, s.total_amount as service_amount
            FROM commissions c
            INNER JOIN services s ON c.service_id = s.id
            WHERE c.driver_id = ?
        ";
        
        $params = [$driverId];
        
        if ($status) {
            $sql .= " AND c.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Estadísticas de un chofer
    public function getStats($driverId, $startDate = null, $endDate = null) {
        $sql = "
            SELECT 
                COUNT(*) as total_services,
                SUM(CASE WHEN status = 'culminado' OR status = 'facturado' OR status = 'cobrado' THEN 1 ELSE 0 END) as completed_services,
                SUM(CASE WHEN status = 'culminado' OR status = 'facturado' OR status = 'cobrado' THEN total_amount ELSE 0 END) as total_revenue
            FROM services
            WHERE driver_id = ?
        ";
        
        $params = [$driverId];
        
        if ($startDate) {
            $sql .= " AND request_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND request_date <= ?";
            $params[] = $endDate;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $serviceStats = $stmt->fetch();
        
        // Obtener comisiones
        $sql = "
            SELECT 
                COUNT(*) as total_commissions,
                SUM(commission_amount) as total_commission_amount,
                SUM(CASE WHEN status = 'pagada' THEN commission_amount ELSE 0 END) as paid_commission_amount
            FROM commissions
            WHERE driver_id = ?
        ";
        
        $params = [$driverId];
        
        if ($startDate) {
            $sql .= " AND created_at >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND created_at <= ?";
            $params[] = $endDate;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $commissionStats = $stmt->fetch();
        
        return array_merge($serviceStats, $commissionStats);
    }
}
