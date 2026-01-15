<?php
/**
 * Modelo Invoice - Facturas
 */

require_once __DIR__ . '/Model.php';

class Invoice extends Model {
    protected $table = 'invoices';
    
    // Generar número de factura
    public function generateInvoiceNumber() {
        $stmt = $this->db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'invoice_series'");
        $series = $stmt->fetch()['setting_value'] ?? 'A';
        
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE YEAR(created_at) = ? AND series = ?");
        $stmt->execute([$year, $series]);
        $count = $stmt->fetch()['total'] + 1;
        
        return $series . '-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }
    
    // Obtener facturas con detalles
    public function getAllWithDetails($limit = 50) {
        $stmt = $this->db->prepare("
            SELECT i.*, 
                   c.business_name as company_name,
                   c.rfc as company_rfc,
                   s.folio as service_folio,
                   u.full_name as created_by_name
            FROM {$this->table} i
            INNER JOIN companies c ON i.company_id = c.id
            LEFT JOIN services s ON i.service_id = s.id
            LEFT JOIN users u ON i.created_by = u.id
            ORDER BY i.invoice_date DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    // Obtener por estado
    public function getByStatus($status, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT i.*, 
                   c.business_name as company_name,
                   s.folio as service_folio
            FROM {$this->table} i
            INNER JOIN companies c ON i.company_id = c.id
            LEFT JOIN services s ON i.service_id = s.id
            WHERE i.status = ?
            ORDER BY i.invoice_date DESC
            LIMIT ?
        ");
        $stmt->execute([$status, $limit]);
        return $stmt->fetchAll();
    }
    
    // Obtener conceptos de una factura
    public function getItems($invoiceId) {
        $stmt = $this->db->prepare("
            SELECT * FROM invoice_items
            WHERE invoice_id = ?
            ORDER BY item_order
        ");
        $stmt->execute([$invoiceId]);
        return $stmt->fetchAll();
    }
    
    // Agregar concepto a factura
    public function addItem($invoiceId, $itemData) {
        $itemData['invoice_id'] = $invoiceId;
        
        // Calcular totales del concepto
        $quantity = floatval($itemData['quantity']);
        $unitPrice = floatval($itemData['unit_price']);
        $discount = floatval($itemData['discount'] ?? 0);
        $taxRate = floatval($itemData['tax_rate'] ?? 16.00);
        
        $subtotal = ($quantity * $unitPrice) - $discount;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;
        
        $itemData['subtotal'] = $subtotal;
        $itemData['tax_amount'] = $taxAmount;
        $itemData['total'] = $total;
        
        $stmt = $this->db->prepare("
            INSERT INTO invoice_items 
            (invoice_id, item_order, product_code, unit_code, description, quantity, unit_price, discount, subtotal, tax_rate, tax_amount, total)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $itemData['invoice_id'],
            $itemData['item_order'] ?? 0,
            $itemData['product_code'] ?? null,
            $itemData['unit_code'] ?? null,
            $itemData['description'],
            $quantity,
            $unitPrice,
            $discount,
            $subtotal,
            $taxRate,
            $taxAmount,
            $total
        ]);
        
        if ($result) {
            $this->recalculateTotals($invoiceId);
        }
        
        return $result;
    }
    
    // Recalcular totales de factura
    public function recalculateTotals($invoiceId) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(subtotal) as subtotal,
                SUM(tax_amount) as tax_amount,
                SUM(total) as total
            FROM invoice_items
            WHERE invoice_id = ?
        ");
        $stmt->execute([$invoiceId]);
        $totals = $stmt->fetch();
        
        return $this->update($invoiceId, [
            'subtotal' => $totals['subtotal'] ?? 0,
            'tax_amount' => $totals['tax_amount'] ?? 0,
            'total_amount' => $totals['total'] ?? 0
        ]);
    }
    
    // Marcar como timbrada (con UUID de Facturama)
    public function markAsTimbrada($invoiceId, $uuid, $facturamaId, $pdfUrl = null, $xmlUrl = null) {
        $data = [
            'status' => 'timbrada',
            'uuid' => $uuid,
            'facturama_id' => $facturamaId
        ];
        
        if ($pdfUrl) $data['pdf_url'] = $pdfUrl;
        if ($xmlUrl) $data['xml_url'] = $xmlUrl;
        
        $result = $this->update($invoiceId, $data);
        
        // Actualizar servicio a estado 'facturado'
        if ($result) {
            $invoice = $this->getById($invoiceId);
            if ($invoice && $invoice['service_id']) {
                $stmt = $this->db->prepare("UPDATE services SET status = 'facturado' WHERE id = ?");
                $stmt->execute([$invoice['service_id']]);
            }
        }
        
        return $result;
    }
    
    // Cancelar factura
    public function cancel($invoiceId, $reason) {
        $data = [
            'status' => 'cancelada',
            'cancellation_date' => date('Y-m-d'),
            'cancellation_reason' => $reason
        ];
        
        return $this->update($invoiceId, $data);
    }
    
    // Estadísticas de facturación
    public function getStats($startDate = null, $endDate = null) {
        $sql = "
            SELECT 
                COUNT(*) as total_invoices,
                SUM(CASE WHEN status = 'timbrada' THEN 1 ELSE 0 END) as timbradas,
                SUM(CASE WHEN status = 'pagada' THEN 1 ELSE 0 END) as pagadas,
                SUM(CASE WHEN status = 'timbrada' OR status = 'pagada' THEN total_amount ELSE 0 END) as total_amount,
                SUM(CASE WHEN status = 'pagada' THEN total_amount ELSE 0 END) as paid_amount
            FROM {$this->table}
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND invoice_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND invoice_date <= ?";
            $params[] = $endDate;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}
