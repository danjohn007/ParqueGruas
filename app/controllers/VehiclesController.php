<?php
/**
 * Controlador de Vehículos
 */

require_once __DIR__ . '/Controller.php';

class VehiclesController extends Controller {
    
    // Listar vehículos
    public function index() {
        $this->requireAuth();
        
        $vehicleModel = $this->model('Vehicle');
        
        // Filtros de búsqueda
        $filters = [];
        if (isset($_GET['plate'])) $filters['plate'] = $_GET['plate'];
        if (isset($_GET['owner_name'])) $filters['owner_name'] = $_GET['owner_name'];
        if (isset($_GET['brand'])) $filters['brand'] = $_GET['brand'];
        if (isset($_GET['vehicle_type'])) $filters['vehicle_type'] = $_GET['vehicle_type'];
        
        $vehicles = empty($filters) ? $vehicleModel->getAll() : $vehicleModel->search($filters);
        
        $data = [
            'title' => 'Gestión de Vehículos',
            'subtitle' => 'Administración de vehículos registrados',
            'vehicles' => $vehicles,
            'filters' => $filters
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/vehicles/index.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Ver detalle de vehículo
    public function details($id) {
        $this->requireAuth();
        
        $vehicleModel = $this->model('Vehicle');
        $impoundModel = $this->model('Impound');
        
        $vehicle = $vehicleModel->getById($id);
        
        if (!$vehicle) {
            $_SESSION['error'] = 'Vehículo no encontrado';
            $this->redirect('/vehicles');
        }
        
        // Obtener historial de impounds del vehículo
        $impoundHistory = $impoundModel->getByVehicleId($id);
        
        $data = [
            'title' => 'Detalle de Vehículo',
            'subtitle' => 'Información completa del vehículo',
            'vehicle' => $vehicle,
            'impoundHistory' => $impoundHistory
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/vehicles/view.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Formulario crear vehículo
    public function create() {
        $this->requireAuth();
        
        $data = [
            'title' => 'Registrar Vehículo',
            'subtitle' => 'Alta de nuevo vehículo'
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/vehicles/create.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Guardar vehículo
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/vehicles/create');
        }
        
        $vehicleModel = $this->model('Vehicle');
        
        $data = [
            'plate' => $_POST['plate'] ?? '',
            'brand' => $_POST['brand'] ?? '',
            'model' => $_POST['model'] ?? '',
            'year' => $_POST['year'] ?? null,
            'color' => $_POST['color'] ?? '',
            'vehicle_type' => $_POST['vehicle_type'] ?? 'auto',
            'owner_name' => $_POST['owner_name'] ?? '',
            'owner_phone' => $_POST['owner_phone'] ?? '',
            'owner_address' => $_POST['owner_address'] ?? '',
            'vin' => $_POST['vin'] ?? '',
            'notes' => $_POST['notes'] ?? ''
        ];
        
        // Validaciones básicas
        if (empty($data['plate'])) {
            $_SESSION['error'] = 'La placa es obligatoria';
            $this->redirect('/vehicles/create');
        }
        
        // Verificar si la placa ya existe
        if ($vehicleModel->getByPlate($data['plate'])) {
            $_SESSION['error'] = 'Ya existe un vehículo con esa placa';
            $this->redirect('/vehicles/create');
        }
        
        if ($vehicleModel->create($data)) {
            $_SESSION['success'] = 'Vehículo registrado exitosamente';
            $this->redirect('/vehicles');
        } else {
            $_SESSION['error'] = 'Error al registrar el vehículo';
            $this->redirect('/vehicles/create');
        }
    }
    
    // Formulario editar vehículo
    public function edit($id) {
        $this->requireAuth();
        
        $vehicleModel = $this->model('Vehicle');
        $vehicle = $vehicleModel->getById($id);
        
        if (!$vehicle) {
            $_SESSION['error'] = 'Vehículo no encontrado';
            $this->redirect('/vehicles');
        }
        
        $data = [
            'title' => 'Editar Vehículo',
            'subtitle' => 'Modificar información del vehículo',
            'vehicle' => $vehicle
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/vehicles/edit.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Actualizar vehículo
    public function update($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/vehicles/edit/' . $id);
        }
        
        $vehicleModel = $this->model('Vehicle');
        
        $data = [
            'plate' => $_POST['plate'] ?? '',
            'brand' => $_POST['brand'] ?? '',
            'model' => $_POST['model'] ?? '',
            'year' => $_POST['year'] ?? null,
            'color' => $_POST['color'] ?? '',
            'vehicle_type' => $_POST['vehicle_type'] ?? 'auto',
            'owner_name' => $_POST['owner_name'] ?? '',
            'owner_phone' => $_POST['owner_phone'] ?? '',
            'owner_address' => $_POST['owner_address'] ?? '',
            'vin' => $_POST['vin'] ?? '',
            'notes' => $_POST['notes'] ?? ''
        ];
        
        if ($vehicleModel->update($id, $data)) {
            $_SESSION['success'] = 'Vehículo actualizado exitosamente';
            $this->redirect('/vehicles/details/' . $id);
        } else {
            $_SESSION['error'] = 'Error al actualizar el vehículo';
            $this->redirect('/vehicles/edit/' . $id);
        }
    }
    
    // Eliminar vehículo
    public function delete($id) {
        $this->requireAuth();
        
        // Solo admin puede eliminar
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tiene permisos para eliminar vehículos';
            $this->redirect('/vehicles');
        }
        
        $vehicleModel = $this->model('Vehicle');
        
        if ($vehicleModel->delete($id)) {
            $_SESSION['success'] = 'Vehículo eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el vehículo';
        }
        
        $this->redirect('/vehicles');
    }
}
