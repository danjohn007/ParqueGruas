<?php
/**
 * Modelo de Corralón (Impounds)
 */

require_once __DIR__ . '/Model.php';

class Impound extends Model {
    protected $table = 'impounds';
    
    // Obtener con datos de vehículo y grúa
    public function getAllWithDetails() {
        $stmt = $this->db->query("
            SELECT i.*, 
                   v.plate, v.brand, v.model, v.color, v.owner_name,
                   c.crane_number, c.driver_name
            FROM {$this->table} i
            LEFT JOIN vehicles v ON i.vehicle_id = v.id
            LEFT JOIN cranes c ON i.crane_id = c.id
            ORDER BY i.impound_date DESC
        ");
        return $stmt->fetchAll();
    }
    
    // Obtener por ID con detalles
    public function getByIdWithDetails($id) {
        $stmt = $this->db->prepare("
            SELECT i.*, 
                   v.plate, v.brand, v.model, v.year, v.color, v.vehicle_type,
                   v.owner_name, v.owner_phone, v.owner_address,
                   c.crane_number, c.driver_name, c.driver_license,
                   p.receipt_number, p.payment_date, p.payment_method
            FROM {$this->table} i
            LEFT JOIN vehicles v ON i.vehicle_id = v.id
            LEFT JOIN cranes c ON i.crane_id = c.id
            LEFT JOIN payments p ON i.payment_id = p.id
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // Obtener por folio
    public function getByFolio($folio) {
        $stmt = $this->db->prepare("
            SELECT i.*, 
                   v.plate, v.brand, v.model, v.owner_name
            FROM {$this->table} i
            LEFT JOIN vehicles v ON i.vehicle_id = v.id
            WHERE i.folio = ?
        ");
        $stmt->execute([$folio]);
        return $stmt->fetch();
    }
    
    // Obtener por estado
    public function getByStatus($status) {
        $stmt = $this->db->prepare("
            SELECT i.*, 
                   v.plate, v.brand, v.model, v.owner_name,
                   c.crane_number
            FROM {$this->table} i
            LEFT JOIN vehicles v ON i.vehicle_id = v.id
            LEFT JOIN cranes c ON i.crane_id = c.id
            WHERE i.status = ?
            ORDER BY i.impound_date DESC
        ");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
    
    // Contar por estado
    public function countByStatus() {
        $stmt = $this->db->query("SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status");
        return $stmt->fetchAll();
    }
    
    // Buscar con filtros
    public function search($filters = []) {
        $sql = "
            SELECT i.*, 
                   v.plate, v.brand, v.model, v.owner_name,
                   c.crane_number
            FROM {$this->table} i
            LEFT JOIN vehicles v ON i.vehicle_id = v.id
            LEFT JOIN cranes c ON i.crane_id = c.id
            WHERE 1=1
        ";
        $params = [];
        
        if (!empty($filters['folio'])) {
            $sql .= " AND i.folio LIKE ?";
            $params[] = '%' . $filters['folio'] . '%';
        }
        
        if (!empty($filters['plate'])) {
            $sql .= " AND v.plate LIKE ?";
            $params[] = '%' . $filters['plate'] . '%';
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND i.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['municipality'])) {
            $sql .= " AND i.municipality LIKE ?";
            $params[] = '%' . $filters['municipality'] . '%';
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(i.impound_date) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(i.impound_date) <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY i.impound_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Generar siguiente folio
    public function generateFolio() {
        $year = date('Y');
        $stmt = $this->db->prepare("
            SELECT folio FROM {$this->table} 
            WHERE folio LIKE ? 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute(['QRO-' . $year . '-%']);
        $lastFolio = $stmt->fetch();
        
        if ($lastFolio) {
            $number = (int)substr($lastFolio['folio'], -3) + 1;
        } else {
            $number = 1;
        }
        
        return 'QRO-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
    
    // Calcular total adeudado
    public function calculateTotal($id) {
        $impound = $this->getById($id);
        if (!$impound) return 0;
        
        $towCost = $impound['tow_cost'];
        $storageDays = $impound['storage_days'];
        $storageCostPerDay = $impound['storage_cost_per_day'];
        $fineAmount = $impound['fine_amount'];
        
        return $towCost + ($storageDays * $storageCostPerDay) + $fineAmount;
    }
    
    // Actualizar días de almacenaje y total
    public function updateStorageAndTotal($id) {
        $impound = $this->getById($id);
        if (!$impound || $impound['status'] !== 'impounded') {
            return false;
        }
        
        $impoundDate = new DateTime($impound['impound_date']);
        $today = new DateTime();
        $storageDays = $today->diff($impoundDate)->days;
        
        $totalAmount = $impound['tow_cost'] + 
                      ($storageDays * $impound['storage_cost_per_day']) + 
                      $impound['fine_amount'];
        
        return $this->update($id, [
            'storage_days' => $storageDays,
            'total_amount' => $totalAmount
        ]);
    }
    
    // Estadísticas por municipio
    public function statsByMunicipality() {
        $stmt = $this->db->query("
            SELECT municipality, COUNT(*) as count, SUM(total_amount) as total_amount
            FROM {$this->table}
            GROUP BY municipality
            ORDER BY count DESC
        ");
        return $stmt->fetchAll();
    }
    
    // Ingresos por período
    public function revenueByPeriod($dateFrom, $dateTo) {
        $stmt = $this->db->prepare("
            SELECT DATE(impound_date) as date, 
                   COUNT(*) as impounds,
                   SUM(total_amount) as total,
                   SUM(CASE WHEN paid = 1 THEN total_amount ELSE 0 END) as paid_amount
            FROM {$this->table}
            WHERE DATE(impound_date) BETWEEN ? AND ?
            GROUP BY DATE(impound_date)
            ORDER BY date
        ");
        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll();
    }
}
