<?php
/**
 * Modelo de Pagos
 */

require_once __DIR__ . '/Model.php';

class Payment extends Model {
    protected $table = 'payments';
    
    // Obtener con detalles del impound, servicio o factura
    public function getAllWithDetails() {
        $stmt = $this->db->query("
            SELECT p.*, 
                   i.folio as impound_folio, 
                   s.folio as service_folio,
                   inv.invoice_number,
                   v.plate, v.owner_name,
                   u.full_name as user_name,
                   c.business_name as company_name
            FROM {$this->table} p
            LEFT JOIN impounds i ON p.impound_id = i.id
            LEFT JOIN services s ON p.service_id = s.id
            LEFT JOIN invoices inv ON p.invoice_id = inv.id
            LEFT JOIN vehicles v ON i.vehicle_id = v.id
            LEFT JOIN companies c ON inv.company_id = c.id
            LEFT JOIN users u ON p.user_id = u.id
            ORDER BY p.payment_date DESC
        ");
        return $stmt->fetchAll();
    }
    
    // Obtener por ID con detalles
    public function getByIdWithDetails($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   i.folio as impound_folio, i.total_amount as impound_total,
                   s.folio as service_folio, s.total_amount as service_total,
                   inv.invoice_number, inv.total_amount as invoice_total,
                   v.plate, v.brand, v.model, v.owner_name,
                   c.business_name as company_name,
                   u.full_name as user_name
            FROM {$this->table} p
            LEFT JOIN impounds i ON p.impound_id = i.id
            LEFT JOIN services s ON p.service_id = s.id
            LEFT JOIN invoices inv ON p.invoice_id = inv.id
            LEFT JOIN vehicles v ON i.vehicle_id = v.id
            LEFT JOIN companies c ON inv.company_id = c.id
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // Generar número de recibo
    public function generateReceiptNumber() {
        $year = date('Y');
        $stmt = $this->db->prepare("
            SELECT receipt_number FROM {$this->table} 
            WHERE receipt_number LIKE ? 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute(['REC-' . $year . '-%']);
        $lastReceipt = $stmt->fetch();
        
        if ($lastReceipt) {
            $number = (int)substr($lastReceipt['receipt_number'], -3) + 1;
        } else {
            $number = 1;
        }
        
        return 'REC-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
    
    // Registrar pago de servicio
    public function registerServicePayment($serviceId, $amount, $method, $cashier, $userId, $referenceNumber = null) {
        $receiptNumber = $this->generateReceiptNumber();
        
        $paymentData = [
            'service_id' => $serviceId,
            'receipt_number' => $receiptNumber,
            'payment_method' => $method,
            'payment_type' => 'service',
            'reference_number' => $referenceNumber,
            'amount' => $amount,
            'payment_date' => date('Y-m-d H:i:s'),
            'cashier_name' => $cashier,
            'user_id' => $userId
        ];
        
        $this->db->beginTransaction();
        
        try {
            $this->create($paymentData);
            $paymentId = $this->db->lastInsertId();
            
            // Actualizar estado del servicio a 'cobrado'
            $stmt = $this->db->prepare("UPDATE services SET status = 'cobrado' WHERE id = ?");
            $stmt->execute([$serviceId]);
            
            $this->db->commit();
            return $paymentId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    // Registrar pago de factura
    public function registerInvoicePayment($invoiceId, $amount, $method, $cashier, $userId, $referenceNumber = null) {
        $receiptNumber = $this->generateReceiptNumber();
        
        // Obtener factura
        $stmt = $this->db->prepare("SELECT * FROM invoices WHERE id = ?");
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch();
        
        if (!$invoice) return false;
        
        $remainingBalance = $invoice['total_amount'] - $amount;
        
        $paymentData = [
            'invoice_id' => $invoiceId,
            'receipt_number' => $receiptNumber,
            'payment_method' => $method,
            'payment_type' => 'invoice',
            'reference_number' => $referenceNumber,
            'amount' => $amount,
            'remaining_balance' => $remainingBalance,
            'payment_date' => date('Y-m-d H:i:s'),
            'cashier_name' => $cashier,
            'user_id' => $userId
        ];
        
        $this->db->beginTransaction();
        
        try {
            $this->create($paymentData);
            $paymentId = $this->db->lastInsertId();
            
            // Si pago completo, actualizar estado de factura
            if ($remainingBalance <= 0) {
                $stmt = $this->db->prepare("UPDATE invoices SET status = 'pagada' WHERE id = ?");
                $stmt->execute([$invoiceId]);
                
                // Actualizar servicio relacionado
                if ($invoice['service_id']) {
                    $stmt = $this->db->prepare("UPDATE services SET status = 'cobrado' WHERE id = ?");
                    $stmt->execute([$invoice['service_id']]);
                }
            }
            
            $this->db->commit();
            return $paymentId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    // Registrar pago
    public function registerPayment($impoundId, $amount, $method, $cashier, $userId) {
        $receiptNumber = $this->generateReceiptNumber();
        
        $paymentData = [
            'impound_id' => $impoundId,
            'receipt_number' => $receiptNumber,
            'amount' => $amount,
            'payment_method' => $method,
            'payment_date' => date('Y-m-d H:i:s'),
            'cashier_name' => $cashier,
            'user_id' => $userId
        ];
        
        $this->db->beginTransaction();
        
        try {
            // Crear el pago
            $this->create($paymentData);
            $paymentId = $this->db->lastInsertId();
            
            // Actualizar el impound
            $stmt = $this->db->prepare("
                UPDATE impounds 
                SET paid = 1, payment_id = ?, status = 'released', release_date = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$paymentId, $impoundId]);
            
            $this->db->commit();
            return $paymentId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    // Obtener pagos por período
    public function getByPeriod($dateFrom, $dateTo) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   i.folio as impound_folio,
                   s.folio as service_folio,
                   inv.invoice_number,
                   v.plate, v.owner_name,
                   c.business_name as company_name
            FROM {$this->table} p
            LEFT JOIN impounds i ON p.impound_id = i.id
            LEFT JOIN services s ON p.service_id = s.id
            LEFT JOIN invoices inv ON p.invoice_id = inv.id
            LEFT JOIN vehicles v ON i.vehicle_id = v.id
            LEFT JOIN companies c ON inv.company_id = c.id
            WHERE DATE(p.payment_date) BETWEEN ? AND ?
            ORDER BY p.payment_date DESC
        ");
        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll();
    }
    
    // Estadísticas de pagos
    public function getStats($dateFrom = null, $dateTo = null) {
        $sql = "
            SELECT 
                COUNT(*) as total_payments,
                SUM(amount) as total_amount,
                AVG(amount) as average_amount,
                payment_method,
                COUNT(*) as method_count
            FROM {$this->table}
        ";
        
        $params = [];
        if ($dateFrom && $dateTo) {
            $sql .= " WHERE DATE(payment_date) BETWEEN ? AND ?";
            $params = [$dateFrom, $dateTo];
        }
        
        $sql .= " GROUP BY payment_method";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Total recaudado por período
    public function getTotalRevenue($dateFrom = null, $dateTo = null) {
        $sql = "SELECT SUM(amount) as total FROM {$this->table}";
        $params = [];
        
        if ($dateFrom && $dateTo) {
            $sql .= " WHERE DATE(payment_date) BETWEEN ? AND ?";
            $params = [$dateFrom, $dateTo];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }
}
