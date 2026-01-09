<?php
/**
 * Controlador de Pagos
 */

require_once __DIR__ . '/Controller.php';

class PaymentsController extends Controller {
    
    // Listar pagos
    public function index() {
        $this->requireAuth();
        
        $paymentModel = $this->model('Payment');
        $payments = $paymentModel->getAllWithDetails();
        
        $data = [
            'title' => 'Gestión de Pagos',
            'subtitle' => 'Registro de pagos realizados',
            'payments' => $payments
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/payments/index.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Formulario de pago
    public function create($impoundId = null) {
        $this->requireAuth();
        
        $impoundModel = $this->model('Impound');
        
        // Si se proporciona un ID, obtener ese impound
        if ($impoundId) {
            $impoundModel->updateStorageAndTotal($impoundId);
            $impound = $impoundModel->getByIdWithDetails($impoundId);
            
            if (!$impound) {
                $_SESSION['error'] = 'Registro no encontrado';
                $this->redirect('/impounds');
            }
            
            if ($impound['paid']) {
                $_SESSION['warning'] = 'Este registro ya tiene un pago registrado';
                $this->redirect('/impounds/details/' . $impoundId);
            }
        } else {
            $impound = null;
        }
        
        // Obtener todos los impounds sin pagar
        $unpaidImpounds = $impoundModel->getByStatus('impounded');
        $unpaidImpounds = array_filter($unpaidImpounds, function($i) { return !$i['paid']; });
        
        $paymentModel = $this->model('Payment');
        $nextReceipt = $paymentModel->generateReceiptNumber();
        
        $data = [
            'title' => 'Registrar Pago',
            'subtitle' => 'Procesar pago de liberación',
            'impound' => $impound,
            'unpaidImpounds' => $unpaidImpounds,
            'nextReceipt' => $nextReceipt
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/payments/create.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Procesar pago
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/payments/create');
        }
        
        $paymentModel = $this->model('Payment');
        $impoundModel = $this->model('Impound');
        
        $impoundId = $_POST['impound_id'] ?? null;
        $amount = $_POST['amount'] ?? 0;
        $paymentMethod = $_POST['payment_method'] ?? 'cash';
        $cashier = $_POST['cashier_name'] ?? '';
        $notes = $_POST['notes'] ?? '';
        
        if (empty($impoundId) || $amount <= 0) {
            $_SESSION['error'] = 'Datos de pago inválidos';
            $this->redirect('/payments/create');
        }
        
        // Verificar que el impound existe y no está pagado
        $impound = $impoundModel->getById($impoundId);
        if (!$impound) {
            $_SESSION['error'] = 'Registro no encontrado';
            $this->redirect('/payments/create');
        }
        
        if ($impound['paid']) {
            $_SESSION['error'] = 'Este registro ya tiene un pago registrado';
            $this->redirect('/impounds/details/' . $impoundId);
        }
        
        // Registrar el pago
        $userId = $_SESSION['user_id'];
        $paymentId = $paymentModel->registerPayment($impoundId, $amount, $paymentMethod, $cashier, $userId);
        
        if ($paymentId) {
            $_SESSION['success'] = 'Pago registrado exitosamente. El vehículo ha sido liberado.';
            $this->redirect('/payments/details/' . $paymentId);
        } else {
            $_SESSION['error'] = 'Error al registrar el pago';
            $this->redirect('/payments/create/' . $impoundId);
        }
    }
    
    // Ver detalle de pago
    public function details($id) {
        $this->requireAuth();
        
        $paymentModel = $this->model('Payment');
        $payment = $paymentModel->getByIdWithDetails($id);
        
        if (!$payment) {
            $_SESSION['error'] = 'Pago no encontrado';
            $this->redirect('/payments');
        }
        
        $data = [
            'title' => 'Detalle de Pago',
            'subtitle' => 'Recibo: ' . $payment['receipt_number'],
            'payment' => $payment
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/payments/view.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Imprimir recibo
    public function printReceipt($id) {
        $this->requireAuth();
        
        $paymentModel = $this->model('Payment');
        $payment = $paymentModel->getByIdWithDetails($id);
        
        if (!$payment) {
            $_SESSION['error'] = 'Pago no encontrado';
            $this->redirect('/payments');
        }
        
        $data = [
            'payment' => $payment
        ];
        
        require_once __DIR__ . '/../views/payments/print.php';
    }
}
