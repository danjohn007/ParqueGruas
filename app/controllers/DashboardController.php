<?php
/**
 * Controlador del Dashboard
 */

require_once __DIR__ . '/Controller.php';

class DashboardController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        // Cargar modelos
        $impoundModel = $this->model('Impound');
        $vehicleModel = $this->model('Vehicle');
        $craneModel = $this->model('Crane');
        $paymentModel = $this->model('Payment');
        
        // Obtener estadísticas
        $stats = [
            'total_impounds' => $impoundModel->count(),
            'impounded_now' => $impoundModel->count("status = 'impounded'"),
            'released_total' => $impoundModel->count("status = 'released'"),
            'pending_payment' => $impoundModel->count("paid = 0"),
            'total_vehicles' => $vehicleModel->count(),
            'total_cranes' => $craneModel->count(),
            'available_cranes' => $craneModel->count("status = 'available'"),
            'total_payments' => $paymentModel->count(),
        ];
        
        // Estadísticas por estado de impounds
        $impoundsByStatus = $impoundModel->countByStatus();
        
        // Estadísticas por estado de grúas
        $cranesByStatus = $craneModel->countByStatus();
        
        // Últimos registros
        $recentImpounds = $impoundModel->getAllWithDetails();
        $recentImpounds = array_slice($recentImpounds, 0, 5);
        
        // Pagos recientes
        $recentPayments = $paymentModel->getAllWithDetails();
        $recentPayments = array_slice($recentPayments, 0, 5);
        
        // Ingresos del mes actual
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');
        $monthRevenue = $paymentModel->getTotalRevenue($firstDayOfMonth, $lastDayOfMonth);
        
        // Ingresos del día
        $today = date('Y-m-d');
        $todayRevenue = $paymentModel->getTotalRevenue($today, $today);
        
        // Grúas que requieren mantenimiento
        $maintenanceDue = $craneModel->getMaintenanceDue(30);
        
        // Ingresos por día de la semana actual (para gráfica)
        $weekRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $revenue = $paymentModel->getTotalRevenue($date, $date);
            $weekRevenue[] = [
                'date' => $date,
                'label' => date('D d', strtotime($date)),
                'revenue' => $revenue
            ];
        }
        
        $data = [
            'title' => 'Dashboard',
            'subtitle' => 'Resumen general del sistema',
            'stats' => $stats,
            'impoundsByStatus' => $impoundsByStatus,
            'cranesByStatus' => $cranesByStatus,
            'recentImpounds' => $recentImpounds,
            'recentPayments' => $recentPayments,
            'monthRevenue' => $monthRevenue,
            'todayRevenue' => $todayRevenue,
            'maintenanceDue' => $maintenanceDue,
            'weekRevenue' => $weekRevenue
        ];
        
        // Cargar vista
        ob_start();
        require_once __DIR__ . '/../views/dashboard/index.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
