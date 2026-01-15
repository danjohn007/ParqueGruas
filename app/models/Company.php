<?php
/**
 * Modelo Company - Empresas/Clientes
 */

require_once __DIR__ . '/Model.php';

class Company extends Model {
    protected $table = 'companies';
    
    // Obtener todas las empresas activas
    public function getAllActive() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY business_name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Buscar por RFC
    public function getByRFC($rfc) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE rfc = ?");
        $stmt->execute([$rfc]);
        return $stmt->fetch();
    }
    
    // Obtener servicios de una empresa
    public function getServices($companyId, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT s.*, d.full_name as driver_name, c.crane_number
            FROM services s
            LEFT JOIN drivers d ON s.driver_id = d.id
            LEFT JOIN cranes c ON s.crane_id = c.id
            WHERE s.company_id = ?
            ORDER BY s.request_date DESC
            LIMIT ?
        ");
        $stmt->execute([$companyId, $limit]);
        return $stmt->fetchAll();
    }
    
    // Obtener facturas de una empresa
    public function getInvoices($companyId, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT * FROM invoices
            WHERE company_id = ?
            ORDER BY invoice_date DESC
            LIMIT ?
        ");
        $stmt->execute([$companyId, $limit]);
        return $stmt->fetchAll();
    }
    
    // Estadísticas de una empresa
    public function getStats($companyId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_services,
                SUM(CASE WHEN status = 'culminado' OR status = 'facturado' OR status = 'cobrado' THEN 1 ELSE 0 END) as completed_services,
                SUM(CASE WHEN status = 'culminado' OR status = 'facturado' OR status = 'cobrado' THEN total_amount ELSE 0 END) as total_revenue,
                SUM(CASE WHEN status IN ('cotizado', 'aceptado', 'asignado', 'en_proceso') THEN 1 ELSE 0 END) as pending_services
            FROM services
            WHERE company_id = ?
        ");
        $stmt->execute([$companyId]);
        return $stmt->fetch();
    }
}
