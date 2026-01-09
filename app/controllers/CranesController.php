<?php
/**
 * Controlador de Grúas
 */

require_once __DIR__ . '/Controller.php';

class CranesController extends Controller {
    
    // Listar grúas
    public function index() {
        $this->requireAuth();
        
        $craneModel = $this->model('Crane');
        $cranes = $craneModel->getAll();
        
        $data = [
            'title' => 'Gestión de Grúas',
            'subtitle' => 'Administración de flota de grúas',
            'cranes' => $cranes
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/cranes/index.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Ver detalle
    public function details($id) {
        $this->requireAuth();
        
        $craneModel = $this->model('Crane');
        $crane = $craneModel->getById($id);
        
        if (!$crane) {
            $_SESSION['error'] = 'Grúa no encontrada';
            $this->redirect('/cranes');
        }
        
        // Obtener historial de servicios
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT i.*, v.plate, v.brand, v.model, v.owner_name
            FROM impounds i
            LEFT JOIN vehicles v ON i.vehicle_id = v.id
            WHERE i.crane_id = ?
            ORDER BY i.impound_date DESC
            LIMIT 20
        ");
        $stmt->execute([$id]);
        $serviceHistory = $stmt->fetchAll();
        
        $data = [
            'title' => 'Detalle de Grúa',
            'subtitle' => $crane['crane_number'],
            'crane' => $crane,
            'serviceHistory' => $serviceHistory
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/cranes/view.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Formulario crear
    public function create() {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tiene permisos para crear grúas';
            $this->redirect('/cranes');
        }
        
        $data = [
            'title' => 'Registrar Grúa',
            'subtitle' => 'Alta de nueva grúa'
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/cranes/create.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Guardar grúa
    public function store() {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tiene permisos';
            $this->redirect('/cranes');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cranes/create');
        }
        
        $craneModel = $this->model('Crane');
        
        $data = [
            'crane_number' => $_POST['crane_number'] ?? '',
            'plate' => $_POST['plate'] ?? '',
            'brand' => $_POST['brand'] ?? '',
            'model' => $_POST['model'] ?? '',
            'year' => $_POST['year'] ?? null,
            'capacity_tons' => $_POST['capacity_tons'] ?? null,
            'status' => $_POST['status'] ?? 'available',
            'driver_name' => $_POST['driver_name'] ?? '',
            'driver_license' => $_POST['driver_license'] ?? '',
            'last_maintenance' => $_POST['last_maintenance'] ?? null,
            'next_maintenance' => $_POST['next_maintenance'] ?? null,
            'notes' => $_POST['notes'] ?? ''
        ];
        
        if (empty($data['crane_number'])) {
            $_SESSION['error'] = 'El número de grúa es obligatorio';
            $this->redirect('/cranes/create');
        }
        
        if ($craneModel->craneNumberExists($data['crane_number'])) {
            $_SESSION['error'] = 'Ya existe una grúa con ese número';
            $this->redirect('/cranes/create');
        }
        
        if ($craneModel->create($data)) {
            $_SESSION['success'] = 'Grúa registrada exitosamente';
            $this->redirect('/cranes');
        } else {
            $_SESSION['error'] = 'Error al registrar la grúa';
            $this->redirect('/cranes/create');
        }
    }
    
    // Formulario editar
    public function edit($id) {
        $this->requireAuth();
        
        $craneModel = $this->model('Crane');
        $crane = $craneModel->getById($id);
        
        if (!$crane) {
            $_SESSION['error'] = 'Grúa no encontrada';
            $this->redirect('/cranes');
        }
        
        $data = [
            'title' => 'Editar Grúa',
            'subtitle' => $crane['crane_number'],
            'crane' => $crane
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/cranes/edit.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Actualizar grúa
    public function update($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cranes/edit/' . $id);
        }
        
        $craneModel = $this->model('Crane');
        
        $data = [
            'crane_number' => $_POST['crane_number'] ?? '',
            'plate' => $_POST['plate'] ?? '',
            'brand' => $_POST['brand'] ?? '',
            'model' => $_POST['model'] ?? '',
            'year' => $_POST['year'] ?? null,
            'capacity_tons' => $_POST['capacity_tons'] ?? null,
            'status' => $_POST['status'] ?? 'available',
            'driver_name' => $_POST['driver_name'] ?? '',
            'driver_license' => $_POST['driver_license'] ?? '',
            'last_maintenance' => $_POST['last_maintenance'] ?? null,
            'next_maintenance' => $_POST['next_maintenance'] ?? null,
            'notes' => $_POST['notes'] ?? ''
        ];
        
        if ($craneModel->update($id, $data)) {
            $_SESSION['success'] = 'Grúa actualizada exitosamente';
            $this->redirect('/cranes/details/' . $id);
        } else {
            $_SESSION['error'] = 'Error al actualizar la grúa';
            $this->redirect('/cranes/edit/' . $id);
        }
    }
}
