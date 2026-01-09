<?php
/**
 * Controlador de Corralón (Impounds)
 */

require_once __DIR__ . '/Controller.php';

class ImpoundsController extends Controller {
    
    // Listar registros del corralón
    public function index() {
        $this->requireAuth();
        
        $impoundModel = $this->model('Impound');
        
        // Filtros
        $filters = [];
        if (isset($_GET['folio'])) $filters['folio'] = $_GET['folio'];
        if (isset($_GET['plate'])) $filters['plate'] = $_GET['plate'];
        if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
        if (isset($_GET['municipality'])) $filters['municipality'] = $_GET['municipality'];
        if (isset($_GET['date_from'])) $filters['date_from'] = $_GET['date_from'];
        if (isset($_GET['date_to'])) $filters['date_to'] = $_GET['date_to'];
        
        $impounds = empty($filters) ? $impoundModel->getAllWithDetails() : $impoundModel->search($filters);
        
        // Actualizar días de almacenaje para impounds activos
        foreach ($impounds as &$impound) {
            if ($impound['status'] === 'impounded') {
                $impoundModel->updateStorageAndTotal($impound['id']);
            }
        }
        
        // Recargar después de actualizar
        $impounds = empty($filters) ? $impoundModel->getAllWithDetails() : $impoundModel->search($filters);
        
        $data = [
            'title' => 'Corralón',
            'subtitle' => 'Gestión de vehículos en corralón',
            'impounds' => $impounds,
            'filters' => $filters
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/impounds/index.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Ver detalle
    public function details($id) {
        $this->requireAuth();
        
        $impoundModel = $this->model('Impound');
        
        // Actualizar días de almacenaje
        $impoundModel->updateStorageAndTotal($id);
        
        $impound = $impoundModel->getByIdWithDetails($id);
        
        if (!$impound) {
            $_SESSION['error'] = 'Registro no encontrado';
            $this->redirect('/impounds');
        }
        
        $data = [
            'title' => 'Detalle de Registro',
            'subtitle' => 'Folio: ' . $impound['folio'],
            'impound' => $impound
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/impounds/view.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Formulario crear registro
    public function create() {
        $this->requireAuth();
        
        $vehicleModel = $this->model('Vehicle');
        $craneModel = $this->model('Crane');
        $impoundModel = $this->model('Impound');
        
        // Obtener datos necesarios
        $vehicles = $vehicleModel->getAll();
        $cranes = $craneModel->getAvailable();
        $nextFolio = $impoundModel->generateFolio();
        
        $data = [
            'title' => 'Nuevo Ingreso al Corralón',
            'subtitle' => 'Registrar nuevo vehículo infractor',
            'vehicles' => $vehicles,
            'cranes' => $cranes,
            'nextFolio' => $nextFolio
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/impounds/create.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Guardar registro
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/impounds/create');
        }
        
        $impoundModel = $this->model('Impound');
        $vehicleModel = $this->model('Vehicle');
        
        // Si se proporcionó información de un nuevo vehículo, crearlo primero
        $vehicleId = $_POST['vehicle_id'] ?? null;
        
        if (empty($vehicleId) && !empty($_POST['new_plate'])) {
            // Crear nuevo vehículo
            $vehicleData = [
                'plate' => $_POST['new_plate'],
                'brand' => $_POST['new_brand'] ?? '',
                'model' => $_POST['new_model'] ?? '',
                'year' => $_POST['new_year'] ?? null,
                'color' => $_POST['new_color'] ?? '',
                'vehicle_type' => $_POST['new_vehicle_type'] ?? 'auto',
                'owner_name' => $_POST['new_owner_name'] ?? '',
                'owner_phone' => $_POST['new_owner_phone'] ?? '',
                'owner_address' => $_POST['new_owner_address'] ?? ''
            ];
            
            if ($vehicleModel->create($vehicleData)) {
                $vehicleId = $vehicleModel->db->lastInsertId();
            } else {
                $_SESSION['error'] = 'Error al registrar el vehículo';
                $this->redirect('/impounds/create');
            }
        }
        
        if (empty($vehicleId)) {
            $_SESSION['error'] = 'Debe seleccionar o registrar un vehículo';
            $this->redirect('/impounds/create');
        }
        
        // Datos del impound
        $data = [
            'folio' => $impoundModel->generateFolio(),
            'vehicle_id' => $vehicleId,
            'crane_id' => $_POST['crane_id'] ?? null,
            'infraction_type' => $_POST['infraction_type'] ?? '',
            'infraction_location' => $_POST['infraction_location'] ?? '',
            'municipality' => $_POST['municipality'] ?? 'Querétaro',
            'impound_date' => $_POST['impound_date'] ?? date('Y-m-d H:i:s'),
            'status' => 'impounded',
            'officer_name' => $_POST['officer_name'] ?? '',
            'officer_badge' => $_POST['officer_badge'] ?? '',
            'tow_cost' => $_POST['tow_cost'] ?? 800.00,
            'storage_days' => 0,
            'storage_cost_per_day' => $_POST['storage_cost_per_day'] ?? 100.00,
            'fine_amount' => $_POST['fine_amount'] ?? 0.00,
            'total_amount' => ($_POST['tow_cost'] ?? 800.00) + ($_POST['fine_amount'] ?? 0.00),
            'observations' => $_POST['observations'] ?? ''
        ];
        
        // Validaciones
        if (empty($data['infraction_type']) || empty($data['infraction_location'])) {
            $_SESSION['error'] = 'Debe especificar el tipo de infracción y la ubicación';
            $this->redirect('/impounds/create');
        }
        
        if ($impoundModel->create($data)) {
            $_SESSION['success'] = 'Registro creado exitosamente. Folio: ' . $data['folio'];
            $this->redirect('/impounds');
        } else {
            $_SESSION['error'] = 'Error al crear el registro';
            $this->redirect('/impounds/create');
        }
    }
    
    // Liberar vehículo (cambiar estado)
    public function release($id) {
        $this->requireAuth();
        
        $impoundModel = $this->model('Impound');
        $impound = $impoundModel->getById($id);
        
        if (!$impound) {
            $_SESSION['error'] = 'Registro no encontrado';
            $this->redirect('/impounds');
        }
        
        if ($impound['status'] === 'released') {
            $_SESSION['warning'] = 'El vehículo ya fue liberado';
            $this->redirect('/impounds/details/' . $id);
        }
        
        if (!$impound['paid']) {
            $_SESSION['error'] = 'No se puede liberar el vehículo sin haber registrado el pago';
            $this->redirect('/impounds/details/' . $id);
        }
        
        $updateData = [
            'status' => 'released',
            'release_date' => date('Y-m-d H:i:s')
        ];
        
        if ($impoundModel->update($id, $updateData)) {
            $_SESSION['success'] = 'Vehículo liberado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al liberar el vehículo';
        }
        
        $this->redirect('/impounds/details/' . $id);
    }
    
    // API: Buscar por folio (para integraciones)
    public function searchByFolio() {
        $this->requireAuth();
        
        $folio = $_GET['folio'] ?? '';
        
        if (empty($folio)) {
            $this->json(['error' => 'Folio requerido'], 400);
        }
        
        $impoundModel = $this->model('Impound');
        $impound = $impoundModel->getByFolio($folio);
        
        if ($impound) {
            $this->json(['success' => true, 'data' => $impound]);
        } else {
            $this->json(['success' => false, 'message' => 'Folio no encontrado'], 404);
        }
    }
}
