<?php
/**
 * Controlador de API (HikVision y endpoints externos)
 */

require_once __DIR__ . '/Controller.php';

class ApiController extends Controller {
    
    // Vista principal de administración de API
    public function index() {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tiene permisos para acceder a esta sección';
            $this->redirect('/dashboard');
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener dispositivos HikVision
        $stmt = $db->query("SELECT * FROM hikvision_devices ORDER BY device_name");
        $devices = $stmt->fetchAll();
        
        $data = [
            'title' => 'API e Integraciones',
            'subtitle' => 'Gestión de dispositivos HikVision y API',
            'devices' => $devices
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/api/index.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Agregar dispositivo HikVision
    public function addDevice() {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin') {
            $this->json(['error' => 'No autorizado'], 403);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método no permitido'], 405);
        }
        
        $db = Database::getInstance()->getConnection();
        
        $data = [
            'device_name' => $_POST['device_name'] ?? '',
            'device_ip' => $_POST['device_ip'] ?? '',
            'device_port' => $_POST['device_port'] ?? 80,
            'username' => $_POST['username'] ?? 'admin',
            'password' => $_POST['password'] ?? '',
            'device_type' => $_POST['device_type'] ?? 'camera',
            'location' => $_POST['location'] ?? '',
            'status' => 'active'
        ];
        
        $stmt = $db->prepare("
            INSERT INTO hikvision_devices 
            (device_name, device_ip, device_port, username, password, device_type, location, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute(array_values($data))) {
            $_SESSION['success'] = 'Dispositivo agregado exitosamente';
            $this->redirect('/api');
        } else {
            $_SESSION['error'] = 'Error al agregar el dispositivo';
            $this->redirect('/api');
        }
    }
    
    // Eliminar dispositivo HikVision
    public function deleteDevice($id) {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No autorizado';
            $this->redirect('/api');
        }
        
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("DELETE FROM hikvision_devices WHERE id = ?");
        
        if ($stmt->execute([$id])) {
            $_SESSION['success'] = 'Dispositivo eliminado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el dispositivo';
        }
        
        $this->redirect('/api');
    }
    
    // Probar conexión con dispositivo HikVision
    public function testDevice($id) {
        $this->requireAuth();
        
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM hikvision_devices WHERE id = ?");
        $stmt->execute([$id]);
        $device = $stmt->fetch();
        
        if (!$device) {
            $this->json(['success' => false, 'message' => 'Dispositivo no encontrado'], 404);
        }
        
        // Intentar conexión básica (simulada)
        $url = "http://{$device['device_ip']}:{$device['device_port']}/ISAPI/System/deviceInfo";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERPWD, "{$device['username']}:{$device['password']}");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // Actualizar último intento de conexión
        $status = ($httpCode == 200) ? 'active' : 'error';
        $stmt = $db->prepare("UPDATE hikvision_devices SET status = ?, last_connection = NOW() WHERE id = ?");
        $stmt->execute([$status, $id]);
        
        if ($httpCode == 200) {
            $this->json(['success' => true, 'message' => 'Conexión exitosa']);
        } else {
            $this->json(['success' => false, 'message' => "Error de conexión: {$error}"], 500);
        }
    }
    
    // API REST: Consultar folio de impound
    public function checkFolio($folio = null) {
        // Esta API puede ser accedida sin autenticación (para integraciones externas)
        // En producción, implementar autenticación por token
        
        if (!$folio) {
            $this->json(['error' => 'Folio requerido'], 400);
        }
        
        $impoundModel = $this->model('Impound');
        $impound = $impoundModel->getByFolio($folio);
        
        if ($impound) {
            // Actualizar días y total
            $impoundModel->updateStorageAndTotal($impound['id']);
            $impound = $impoundModel->getByFolio($folio);
            
            $response = [
                'success' => true,
                'data' => [
                    'folio' => $impound['folio'],
                    'plate' => $impound['plate'],
                    'brand' => $impound['brand'],
                    'model' => $impound['model'],
                    'owner_name' => $impound['owner_name'],
                    'impound_date' => $impound['impound_date'],
                    'storage_days' => $impound['storage_days'],
                    'total_amount' => $impound['total_amount'],
                    'paid' => (bool)$impound['paid'],
                    'status' => $impound['status']
                ]
            ];
            
            $this->json($response);
        } else {
            $this->json(['success' => false, 'message' => 'Folio no encontrado'], 404);
        }
    }
    
    // API REST: Consultar por placa
    public function checkPlate($plate = null) {
        if (!$plate) {
            $this->json(['error' => 'Placa requerida'], 400);
        }
        
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT i.*, v.plate, v.brand, v.model, v.owner_name
            FROM impounds i
            LEFT JOIN vehicles v ON i.vehicle_id = v.id
            WHERE v.plate = ? AND i.status = 'impounded'
            ORDER BY i.impound_date DESC
            LIMIT 1
        ");
        $stmt->execute([$plate]);
        $impound = $stmt->fetch();
        
        if ($impound) {
            $impoundModel = $this->model('Impound');
            $impoundModel->updateStorageAndTotal($impound['id']);
            
            // Recargar datos actualizados
            $stmt->execute([$plate]);
            $impound = $stmt->fetch();
            
            $response = [
                'success' => true,
                'data' => [
                    'folio' => $impound['folio'],
                    'plate' => $impound['plate'],
                    'brand' => $impound['brand'],
                    'model' => $impound['model'],
                    'owner_name' => $impound['owner_name'],
                    'impound_date' => $impound['impound_date'],
                    'storage_days' => $impound['storage_days'],
                    'total_amount' => $impound['total_amount'],
                    'paid' => (bool)$impound['paid'],
                    'status' => $impound['status'],
                    'infraction_type' => $impound['infraction_type'],
                    'infraction_location' => $impound['infraction_location']
                ]
            ];
            
            $this->json($response);
        } else {
            $this->json(['success' => false, 'message' => 'No se encontró vehículo con esa placa en corralón'], 404);
        }
    }
    
    // Documentación de API
    public function docs() {
        $data = [
            'title' => 'Documentación de API',
            'subtitle' => 'Endpoints disponibles para integraciones'
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/api/docs.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
