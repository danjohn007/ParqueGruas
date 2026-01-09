<?php
/**
 * Controlador de Reportes
 */

require_once __DIR__ . '/Controller.php';

class ReportsController extends Controller {
    
    // Vista principal de reportes
    public function index() {
        $this->requireAuth();
        
        $data = [
            'title' => 'Reportes y Estadísticas',
            'subtitle' => 'Generación de reportes del sistema'
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/reports/index.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Reporte de ingresos
    public function revenue() {
        $this->requireAuth();
        
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-t');
        
        $impoundModel = $this->model('Impound');
        $paymentModel = $this->model('Payment');
        
        // Obtener ingresos por día
        $revenueByDay = $impoundModel->revenueByPeriod($dateFrom, $dateTo);
        
        // Obtener estadísticas de pagos
        $paymentStats = $paymentModel->getStats($dateFrom, $dateTo);
        
        // Total recaudado
        $totalRevenue = $paymentModel->getTotalRevenue($dateFrom, $dateTo);
        
        // Pagos del período
        $payments = $paymentModel->getByPeriod($dateFrom, $dateTo);
        
        $data = [
            'title' => 'Reporte de Ingresos',
            'subtitle' => 'Del ' . date('d/m/Y', strtotime($dateFrom)) . ' al ' . date('d/m/Y', strtotime($dateTo)),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'revenueByDay' => $revenueByDay,
            'paymentStats' => $paymentStats,
            'totalRevenue' => $totalRevenue,
            'payments' => $payments
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/reports/revenue.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Reporte de operaciones
    public function operations() {
        $this->requireAuth();
        
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-t');
        
        $impoundModel = $this->model('Impound');
        $craneModel = $this->model('Crane');
        
        // Estadísticas por municipio
        $statsByMunicipality = $impoundModel->statsByMunicipality();
        
        // Estadísticas de grúas
        $craneStats = $craneModel->countByStatus();
        
        // Impounds del período
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT DATE(impound_date) as date, 
                   COUNT(*) as total_impounds,
                   SUM(CASE WHEN status = 'released' THEN 1 ELSE 0 END) as released,
                   SUM(CASE WHEN status = 'impounded' THEN 1 ELSE 0 END) as impounded
            FROM impounds
            WHERE DATE(impound_date) BETWEEN ? AND ?
            GROUP BY DATE(impound_date)
            ORDER BY date
        ");
        $stmt->execute([$dateFrom, $dateTo]);
        $dailyStats = $stmt->fetchAll();
        
        $data = [
            'title' => 'Reporte de Operaciones',
            'subtitle' => 'Del ' . date('d/m/Y', strtotime($dateFrom)) . ' al ' . date('d/m/Y', strtotime($dateTo)),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'statsByMunicipality' => $statsByMunicipality,
            'craneStats' => $craneStats,
            'dailyStats' => $dailyStats
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/reports/operations.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Exportar a CSV
    public function exportCSV() {
        $this->requireAuth();
        
        $type = $_GET['type'] ?? 'impounds';
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-t');
        
        $db = Database::getInstance()->getConnection();
        
        // Headers para descarga
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_' . $type . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        if ($type === 'impounds') {
            // Encabezados
            fputcsv($output, ['Folio', 'Placa', 'Marca', 'Modelo', 'Propietario', 'Infracción', 'Municipio', 'Fecha Ingreso', 'Días', 'Total', 'Pagado', 'Estado']);
            
            // Datos
            $stmt = $db->prepare("
                SELECT i.folio, v.plate, v.brand, v.model, v.owner_name, 
                       i.infraction_type, i.municipality, i.impound_date, 
                       i.storage_days, i.total_amount, i.paid, i.status
                FROM impounds i
                LEFT JOIN vehicles v ON i.vehicle_id = v.id
                WHERE DATE(i.impound_date) BETWEEN ? AND ?
                ORDER BY i.impound_date DESC
            ");
            $stmt->execute([$dateFrom, $dateTo]);
            
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['folio'],
                    $row['plate'],
                    $row['brand'],
                    $row['model'],
                    $row['owner_name'],
                    $row['infraction_type'],
                    $row['municipality'],
                    $row['impound_date'],
                    $row['storage_days'],
                    $row['total_amount'],
                    $row['paid'] ? 'Sí' : 'No',
                    $row['status']
                ]);
            }
        } elseif ($type === 'payments') {
            // Encabezados
            fputcsv($output, ['Recibo', 'Folio', 'Placa', 'Propietario', 'Monto', 'Método', 'Fecha', 'Cajero']);
            
            // Datos
            $stmt = $db->prepare("
                SELECT p.receipt_number, i.folio, v.plate, v.owner_name,
                       p.amount, p.payment_method, p.payment_date, p.cashier_name
                FROM payments p
                LEFT JOIN impounds i ON p.impound_id = i.id
                LEFT JOIN vehicles v ON i.vehicle_id = v.id
                WHERE DATE(p.payment_date) BETWEEN ? AND ?
                ORDER BY p.payment_date DESC
            ");
            $stmt->execute([$dateFrom, $dateTo]);
            
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['receipt_number'],
                    $row['folio'],
                    $row['plate'],
                    $row['owner_name'],
                    $row['amount'],
                    $row['payment_method'],
                    $row['payment_date'],
                    $row['cashier_name']
                ]);
            }
        }
        
        fclose($output);
        exit;
    }
}
