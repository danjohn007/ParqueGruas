<?php
/**
 * Modelo Service - Servicios (Core Module)
 */

require_once __DIR__ . '/Model.php';

class Service extends Model {
    protected $table = 'services';
    
    // Generar folio único
    public function generateFolio() {
        $stmt = $this->db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'service_folio_prefix'");
        $prefix = $stmt->fetch()['setting_value'] ?? 'SRV';
        
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE YEAR(created_at) = ?");
        $stmt->execute([$year]);
        $count = $stmt->fetch()['total'] + 1;
        
        return $prefix . '-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }
    
    // Obtener servicios por estado
    public function getByStatus($status, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT s.*, 
                   c.business_name as company_name,
                   d.full_name as driver_name,
                   cr.crane_number,
                   y.yard_name
            FROM {$this->table} s
            LEFT JOIN companies c ON s.company_id = c.id
            LEFT JOIN drivers d ON s.driver_id = d.id
            LEFT JOIN cranes cr ON s.crane_id = cr.id
            LEFT JOIN yards y ON s.yard_id = y.id
            WHERE s.status = ?
            ORDER BY s.request_date DESC
            LIMIT ?
        ");
        $stmt->execute([$status, $limit]);
        return $stmt->fetchAll();
    }
    
    // Buscar servicios con filtros
    public function search($filters = [], $limit = 50, $offset = 0) {
        $sql = "
            SELECT s.*, 
                   c.business_name as company_name,
                   d.full_name as driver_name,
                   cr.crane_number,
                   y.yard_name
            FROM {$this->table} s
            LEFT JOIN companies c ON s.company_id = c.id
            LEFT JOIN drivers d ON s.driver_id = d.id
            LEFT JOIN cranes cr ON s.crane_id = cr.id
            LEFT JOIN yards y ON s.yard_id = y.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['folio'])) {
            $sql .= " AND s.folio LIKE ?";
            $params[] = '%' . $filters['folio'] . '%';
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['service_type'])) {
            $sql .= " AND s.service_type = ?";
            $params[] = $filters['service_type'];
        }
        
        if (!empty($filters['company_id'])) {
            $sql .= " AND s.company_id = ?";
            $params[] = $filters['company_id'];
        }
        
        if (!empty($filters['driver_id'])) {
            $sql .= " AND s.driver_id = ?";
            $params[] = $filters['driver_id'];
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND s.request_date >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND s.request_date <= ?";
            $params[] = $filters['end_date'];
        }
        
        $sql .= " ORDER BY s.request_date DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Cambiar estado del servicio
    public function updateStatus($serviceId, $newStatus, $reason = null) {
        $data = ['status' => $newStatus];
        
        if ($reason) {
            $data['cancellation_reason'] = $reason;
        }
        
        // Actualizar fechas según estado
        switch ($newStatus) {
            case 'en_proceso':
                $data['start_date'] = date('Y-m-d H:i:s');
                break;
            case 'culminado':
                $data['end_date'] = date('Y-m-d H:i:s');
                break;
        }
        
        return $this->update($serviceId, $data);
    }
    
    // Calcular totales
    public function calculateTotals($serviceId) {
        $service = $this->getById($serviceId);
        
        if (!$service) return false;
        
        // Obtener tasa de IVA
        $stmt = $this->db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'default_tax_rate'");
        $taxRate = floatval($stmt->fetch()['setting_value'] ?? 16.00);
        
        $baseCost = floatval($service['base_cost']);
        $additionalCharges = floatval($service['additional_charges']);
        $discounts = floatval($service['discounts']);
        
        $subtotal = $baseCost + $additionalCharges - $discounts;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;
        
        return $this->update($serviceId, [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total
        ]);
    }
    
    // Estadísticas generales
    public function getGeneralStats($startDate = null, $endDate = null) {
        $sql = "
            SELECT 
                COUNT(*) as total_services,
                SUM(CASE WHEN status = 'culminado' OR status = 'facturado' OR status = 'cobrado' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status IN ('cotizado', 'aceptado', 'asignado', 'en_proceso') THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = 'culminado' OR status = 'facturado' OR status = 'cobrado' THEN total_amount ELSE 0 END) as total_revenue,
                AVG(CASE WHEN status = 'culminado' AND start_date IS NOT NULL AND end_date IS NOT NULL 
                    THEN TIMESTAMPDIFF(HOUR, start_date, end_date) ELSE NULL END) as avg_service_hours
            FROM {$this->table}
            WHERE 1=1
        ";
        
        $params = [];
        
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
        return $stmt->fetch();
    }
}
