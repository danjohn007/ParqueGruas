<?php
/**
 * Modelo Commission - Comisiones
 */

require_once __DIR__ . '/Model.php';

class Commission extends Model {
    protected $table = 'commissions';
    
    // Calcular comisión para un servicio
    public function calculateForService($serviceId) {
        // Obtener datos del servicio
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);
        $service = $stmt->fetch();
        
        if (!$service || $service['total_amount'] <= 0) {
            return false;
        }
        
        // Buscar reglas de comisión aplicables
        $rules = $this->getApplicableRules($service);
        
        if (empty($rules)) {
            return false;
        }
        
        // Aplicar la regla con mayor prioridad
        $rule = $rules[0];
        
        $baseAmount = floatval($service['total_amount']);
        $commissionAmount = 0;
        $commissionPercentage = 0;
        
        if ($rule['calculation_type'] == 'percentage') {
            $commissionPercentage = floatval($rule['commission_value']);
            $commissionAmount = $baseAmount * ($commissionPercentage / 100);
        } else {
            $commissionAmount = floatval($rule['commission_value']);
            $commissionPercentage = $baseAmount > 0 ? ($commissionAmount / $baseAmount) * 100 : 0;
        }
        
        // Aplicar límites
        if ($rule['min_amount'] > 0 && $commissionAmount < $rule['min_amount']) {
            $commissionAmount = $rule['min_amount'];
        }
        if ($rule['max_amount'] > 0 && $commissionAmount > $rule['max_amount']) {
            $commissionAmount = $rule['max_amount'];
        }
        
        // Crear registro de comisión
        $data = [
            'service_id' => $serviceId,
            'driver_id' => $service['driver_id'],
            'crane_id' => $service['crane_id'],
            'commission_rule_id' => $rule['id'],
            'base_amount' => $baseAmount,
            'commission_percentage' => $commissionPercentage,
            'commission_amount' => $commissionAmount,
            'status' => 'calculada'
        ];
        
        return $this->create($data);
    }
    
    // Obtener reglas aplicables a un servicio
    private function getApplicableRules($service) {
        $sql = "
            SELECT * FROM commission_rules
            WHERE is_active = TRUE
            AND (start_date IS NULL OR start_date <= CURDATE())
            AND (end_date IS NULL OR end_date >= CURDATE())
            AND (
                (rule_type = 'driver' AND driver_id = ?)
                OR (rule_type = 'crane' AND crane_id = ?)
                OR (rule_type = 'company' AND company_id = ?)
                OR (rule_type = 'service_type' AND service_type = ?)
                OR (rule_type = 'fixed')
            )
            ORDER BY priority DESC, id DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $service['driver_id'],
            $service['crane_id'],
            $service['company_id'],
            $service['service_type']
        ]);
        
        return $stmt->fetchAll();
    }
    
    // Obtener comisiones por estado
    public function getByStatus($status, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   s.folio as service_folio,
                   s.service_type,
                   d.full_name as driver_name,
                   cr.crane_number
            FROM {$this->table} c
            INNER JOIN services s ON c.service_id = s.id
            LEFT JOIN drivers d ON c.driver_id = d.id
            LEFT JOIN cranes cr ON c.crane_id = cr.id
            WHERE c.status = ?
            ORDER BY c.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$status, $limit]);
        return $stmt->fetchAll();
    }
    
    // Aprobar comisión
    public function approve($commissionId) {
        return $this->update($commissionId, ['status' => 'aprobada']);
    }
    
    // Marcar como pagada
    public function markAsPaid($commissionId, $paymentReference) {
        return $this->update($commissionId, [
            'status' => 'pagada',
            'payment_date' => date('Y-m-d'),
            'payment_reference' => $paymentReference
        ]);
    }
    
    // Reporte de comisiones por chofer
    public function getDriverReport($driverId, $startDate = null, $endDate = null) {
        $sql = "
            SELECT 
                DATE_FORMAT(c.created_at, '%Y-%m') as period,
                COUNT(*) as total_commissions,
                SUM(c.commission_amount) as total_amount,
                SUM(CASE WHEN c.status = 'pagada' THEN c.commission_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN c.status IN ('calculada', 'aprobada') THEN c.commission_amount ELSE 0 END) as pending_amount
            FROM {$this->table} c
            WHERE c.driver_id = ?
        ";
        
        $params = [$driverId];
        
        if ($startDate) {
            $sql .= " AND c.created_at >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND c.created_at <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY DATE_FORMAT(c.created_at, '%Y-%m') ORDER BY period DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Corte de comisiones
    public function getCutReport($startDate, $endDate, $status = 'aprobada') {
        $sql = "
            SELECT 
                d.id as driver_id,
                d.full_name as driver_name,
                d.employee_number,
                COUNT(c.id) as total_services,
                SUM(c.base_amount) as total_sales,
                SUM(c.commission_amount) as total_commission
            FROM {$this->table} c
            INNER JOIN drivers d ON c.driver_id = d.id
            WHERE c.created_at BETWEEN ? AND ?
        ";
        
        $params = [$startDate, $endDate];
        
        if ($status) {
            $sql .= " AND c.status = ?";
            $params[] = $status;
        }
        
        $sql .= " GROUP BY d.id ORDER BY total_commission DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
