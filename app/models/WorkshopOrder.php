<?php
/**
 * Modelo WorkshopOrder - Órdenes de Taller
 */

require_once __DIR__ . '/Model.php';

class WorkshopOrder extends Model {
    protected $table = 'workshop_orders';
    
    // Generar número de orden
    public function generateOrderNumber() {
        $stmt = $this->db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'workshop_order_prefix'");
        $prefix = $stmt->fetch()['setting_value'] ?? 'TAL';
        
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE YEAR(created_at) = ?");
        $stmt->execute([$year]);
        $count = $stmt->fetch()['total'] + 1;
        
        return $prefix . '-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }
    
    // Obtener órdenes con detalles
    public function getAllWithDetails($limit = 50) {
        $stmt = $this->db->prepare("
            SELECT w.*, 
                   c.crane_number,
                   c.plate as crane_plate,
                   v.plate as vehicle_plate,
                   u.full_name as created_by_name
            FROM {$this->table} w
            LEFT JOIN cranes c ON w.crane_id = c.id
            LEFT JOIN vehicles v ON w.vehicle_id = v.id
            LEFT JOIN users u ON w.created_by = u.id
            ORDER BY w.entry_date DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    // Obtener por estado
    public function getByStatus($status, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT w.*, 
                   c.crane_number,
                   v.plate as vehicle_plate
            FROM {$this->table} w
            LEFT JOIN cranes c ON w.crane_id = c.id
            LEFT JOIN vehicles v ON w.vehicle_id = v.id
            WHERE w.status = ?
            ORDER BY w.entry_date DESC
            LIMIT ?
        ");
        $stmt->execute([$status, $limit]);
        return $stmt->fetchAll();
    }
    
    // Obtener órdenes abiertas (en taller)
    public function getOpen() {
        return $this->getByStatus('abierta', 100);
    }
    
    // Obtener órdenes en proceso
    public function getInProgress() {
        $stmt = $this->db->prepare("
            SELECT w.*, 
                   c.crane_number,
                   v.plate as vehicle_plate
            FROM {$this->table} w
            LEFT JOIN cranes c ON w.crane_id = c.id
            LEFT JOIN vehicles v ON w.vehicle_id = v.id
            WHERE w.status IN ('abierta', 'en_proceso', 'esperando_refacciones')
            ORDER BY w.priority DESC, w.entry_date ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Obtener conceptos de una orden
    public function getItems($orderId) {
        $stmt = $this->db->prepare("
            SELECT * FROM workshop_items
            WHERE workshop_order_id = ?
            ORDER BY id
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    // Agregar concepto a orden
    public function addItem($orderId, $itemData) {
        $itemData['workshop_order_id'] = $orderId;
        
        $quantity = floatval($itemData['quantity']);
        $unitPrice = floatval($itemData['unit_price']);
        $totalPrice = $quantity * $unitPrice;
        
        $itemData['total_price'] = $totalPrice;
        
        $stmt = $this->db->prepare("
            INSERT INTO workshop_items 
            (workshop_order_id, item_type, description, part_number, quantity, unit_price, total_price, provider, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $itemData['workshop_order_id'],
            $itemData['item_type'],
            $itemData['description'],
            $itemData['part_number'] ?? null,
            $quantity,
            $unitPrice,
            $totalPrice,
            $itemData['provider'] ?? null,
            $itemData['notes'] ?? null
        ]);
        
        if ($result) {
            $this->recalculateTotals($orderId);
        }
        
        return $result;
    }
    
    // Recalcular totales de orden
    public function recalculateTotals($orderId) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN item_type = 'mano_obra' THEN total_price ELSE 0 END) as labor_cost,
                SUM(CASE WHEN item_type = 'refaccion' THEN total_price ELSE 0 END) as parts_cost,
                SUM(CASE WHEN item_type IN ('servicio', 'otro') THEN total_price ELSE 0 END) as other_costs,
                SUM(total_price) as total_cost
            FROM workshop_items
            WHERE workshop_order_id = ?
        ");
        $stmt->execute([$orderId]);
        $totals = $stmt->fetch();
        
        return $this->update($orderId, [
            'labor_cost' => $totals['labor_cost'] ?? 0,
            'parts_cost' => $totals['parts_cost'] ?? 0,
            'other_costs' => $totals['other_costs'] ?? 0,
            'total_cost' => $totals['total_cost'] ?? 0
        ]);
    }
    
    // Completar orden
    public function complete($orderId, $workPerformed, $odometer_out = null) {
        $data = [
            'status' => 'completada',
            'actual_exit_date' => date('Y-m-d H:i:s'),
            'work_performed' => $workPerformed
        ];
        
        if ($odometer_out) {
            $data['odometer_out'] = $odometer_out;
        }
        
        $result = $this->update($orderId, $data);
        
        // Actualizar estado de la grúa a 'available'
        if ($result) {
            $order = $this->getById($orderId);
            if ($order && $order['crane_id']) {
                $stmt = $this->db->prepare("UPDATE cranes SET status = 'available' WHERE id = ?");
                $stmt->execute([$order['crane_id']]);
            }
        }
        
        return $result;
    }
    
    // Estadísticas de taller
    public function getStats($startDate = null, $endDate = null) {
        $sql = "
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'completada' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status IN ('abierta', 'en_proceso', 'esperando_refacciones') THEN 1 ELSE 0 END) as in_progress,
                SUM(total_cost) as total_cost,
                AVG(CASE WHEN status = 'completada' AND actual_exit_date IS NOT NULL 
                    THEN DATEDIFF(actual_exit_date, entry_date) ELSE NULL END) as avg_days_in_workshop
            FROM {$this->table}
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND entry_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND entry_date <= ?";
            $params[] = $endDate;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    // Reporte de costos por unidad
    public function getCostReport($unitType = null, $startDate = null, $endDate = null) {
        $sql = "
            SELECT 
                w.crane_id,
                w.vehicle_id,
                w.unit_type,
                c.crane_number,
                c.plate as crane_plate,
                v.plate as vehicle_plate,
                COUNT(w.id) as total_orders,
                SUM(w.total_cost) as total_cost,
                AVG(w.total_cost) as avg_cost_per_order
            FROM {$this->table} w
            LEFT JOIN cranes c ON w.crane_id = c.id
            LEFT JOIN vehicles v ON w.vehicle_id = v.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($unitType) {
            $sql .= " AND w.unit_type = ?";
            $params[] = $unitType;
        }
        
        if ($startDate) {
            $sql .= " AND w.entry_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND w.entry_date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY w.crane_id, w.vehicle_id ORDER BY total_cost DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
