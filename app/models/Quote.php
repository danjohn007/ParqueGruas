<?php
/**
 * Modelo Quote - Cotizaciones
 */

require_once __DIR__ . '/Model.php';

class Quote extends Model {
    protected $table = 'quotes';
    
    // Generar número de cotización
    public function generateQuoteNumber() {
        $stmt = $this->db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'quote_folio_prefix'");
        $prefix = $stmt->fetch()['setting_value'] ?? 'COT';
        
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE YEAR(created_at) = ?");
        $stmt->execute([$year]);
        $count = $stmt->fetch()['total'] + 1;
        
        return $prefix . '-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }
    
    // Obtener cotizaciones con información relacionada
    public function getAllWithDetails($limit = 50) {
        $stmt = $this->db->prepare("
            SELECT q.*, 
                   c.business_name as company_name,
                   s.folio as service_folio,
                   s.service_type,
                   u.full_name as created_by_name
            FROM {$this->table} q
            LEFT JOIN companies c ON q.company_id = c.id
            LEFT JOIN services s ON q.service_id = s.id
            LEFT JOIN users u ON q.created_by = u.id
            ORDER BY q.quote_date DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    // Obtener por estado
    public function getByStatus($status, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT q.*, 
                   c.business_name as company_name,
                   s.folio as service_folio
            FROM {$this->table} q
            LEFT JOIN companies c ON q.company_id = c.id
            LEFT JOIN services s ON q.service_id = s.id
            WHERE q.status = ?
            ORDER BY q.quote_date DESC
            LIMIT ?
        ");
        $stmt->execute([$status, $limit]);
        return $stmt->fetchAll();
    }
    
    // Aceptar cotización
    public function accept($quoteId) {
        $data = [
            'status' => 'aceptada',
            'accepted_date' => date('Y-m-d')
        ];
        
        $result = $this->update($quoteId, $data);
        
        if ($result) {
            // Actualizar el servicio relacionado a estado 'aceptado'
            $quote = $this->getById($quoteId);
            if ($quote && $quote['service_id']) {
                $stmt = $this->db->prepare("UPDATE services SET status = 'aceptado' WHERE id = ?");
                $stmt->execute([$quote['service_id']]);
            }
        }
        
        return $result;
    }
    
    // Rechazar cotización
    public function reject($quoteId, $reason) {
        $data = [
            'status' => 'rechazada',
            'rejection_reason' => $reason
        ];
        
        $result = $this->update($quoteId, $data);
        
        if ($result) {
            // Actualizar el servicio relacionado a estado 'rechazado'
            $quote = $this->getById($quoteId);
            if ($quote && $quote['service_id']) {
                $stmt = $this->db->prepare("UPDATE services SET status = 'rechazado', cancellation_reason = ? WHERE id = ?");
                $stmt->execute([$reason, $quote['service_id']]);
            }
        }
        
        return $result;
    }
    
    // Marcar como vencida
    public function markExpired() {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = 'vencida' 
            WHERE status = 'pendiente' 
            AND valid_until < CURDATE()
        ");
        return $stmt->execute();
    }
}
