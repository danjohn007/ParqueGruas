<?php
/**
 * Settings Controller
 */

require_once __DIR__ . '/Controller.php';

class SettingsController extends Controller {
    
    // View settings page
    public function index() {
        $this->requireAuth();
        
        // Only admin can access settings
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tiene permisos para acceder a la configuración del sistema';
            $this->redirect('/dashboard');
        }
        
        $settingModel = $this->model('Setting');
        $settings = $settingModel->getAllAsArray();
        
        $data = [
            'title' => 'Configuración del Sistema',
            'subtitle' => 'Administración de configuraciones generales',
            'settings' => $settings
        ];
        
        ob_start();
        require_once __DIR__ . '/../views/settings/index.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
    
    // Update settings
    public function update() {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'No tiene permisos';
            $this->redirect('/settings');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings');
        }
        
        $settingModel = $this->model('Setting');
        
        // Define settings to update
        $settingsToUpdate = [
            // Site settings
            'site_name' => ['value' => $_POST['site_name'] ?? '', 'description' => 'Nombre del sitio web'],
            'site_logo' => ['value' => $_POST['site_logo'] ?? '', 'description' => 'URL del logotipo del sitio'],
            
            // Email settings
            'email_from' => ['value' => $_POST['email_from'] ?? '', 'description' => 'Email principal del sistema'],
            'email_from_name' => ['value' => $_POST['email_from_name'] ?? '', 'description' => 'Nombre del remitente de emails'],
            'smtp_host' => ['value' => $_POST['smtp_host'] ?? '', 'description' => 'Host del servidor SMTP'],
            'smtp_port' => ['value' => $_POST['smtp_port'] ?? '', 'description' => 'Puerto del servidor SMTP'],
            'smtp_username' => ['value' => $_POST['smtp_username'] ?? '', 'description' => 'Usuario SMTP'],
            'smtp_password' => ['value' => $_POST['smtp_password'] ?? '', 'description' => 'Contraseña SMTP'],
            
            // Contact settings
            'contact_phone_1' => ['value' => $_POST['contact_phone_1'] ?? '', 'description' => 'Teléfono de contacto principal'],
            'contact_phone_2' => ['value' => $_POST['contact_phone_2'] ?? '', 'description' => 'Teléfono de contacto secundario'],
            'business_hours' => ['value' => $_POST['business_hours'] ?? '', 'description' => 'Horarios de atención'],
            'contact_address' => ['value' => $_POST['contact_address'] ?? '', 'description' => 'Dirección de contacto'],
            
            // Color scheme
            'primary_color' => ['value' => $_POST['primary_color'] ?? '#3b82f6', 'description' => 'Color primario del sistema'],
            'secondary_color' => ['value' => $_POST['secondary_color'] ?? '#1e40af', 'description' => 'Color secundario del sistema'],
            'accent_color' => ['value' => $_POST['accent_color'] ?? '#06b6d4', 'description' => 'Color de acento del sistema'],
            
            // PayPal settings
            'paypal_mode' => ['value' => $_POST['paypal_mode'] ?? 'sandbox', 'description' => 'Modo de PayPal (sandbox/live)'],
            'paypal_client_id' => ['value' => $_POST['paypal_client_id'] ?? '', 'description' => 'Client ID de PayPal'],
            'paypal_secret' => ['value' => $_POST['paypal_secret'] ?? '', 'description' => 'Secret de PayPal'],
            
            // QR API settings
            'qr_api_provider' => ['value' => $_POST['qr_api_provider'] ?? '', 'description' => 'Proveedor de API QR'],
            'qr_api_key' => ['value' => $_POST['qr_api_key'] ?? '', 'description' => 'API Key para generación de QR'],
            'qr_api_endpoint' => ['value' => $_POST['qr_api_endpoint'] ?? '', 'description' => 'Endpoint de API QR'],
            
            // Global settings
            'storage_cost_per_day' => ['value' => $_POST['storage_cost_per_day'] ?? '100.00', 'description' => 'Costo de almacenaje por día'],
            'base_tow_cost' => ['value' => $_POST['base_tow_cost'] ?? '800.00', 'description' => 'Costo base del servicio de grúa'],
            'timezone' => ['value' => $_POST['timezone'] ?? 'America/Mexico_City', 'description' => 'Zona horaria del sistema'],
            'date_format' => ['value' => $_POST['date_format'] ?? 'd/m/Y', 'description' => 'Formato de fecha'],
            'currency' => ['value' => $_POST['currency'] ?? 'MXN', 'description' => 'Moneda del sistema'],
        ];
        
        // Update each setting
        $success = true;
        foreach ($settingsToUpdate as $key => $data) {
            // Skip empty password/secret fields to preserve existing values
            if (in_array($key, ['smtp_password', 'paypal_secret']) && empty($data['value'])) {
                continue;
            }
            
            if (!$settingModel->setSetting($key, $data['value'], $data['description'])) {
                $success = false;
                break;
            }
        }
        
        if ($success) {
            $_SESSION['success'] = 'Configuración actualizada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al actualizar la configuración';
        }
        
        $this->redirect('/settings');
    }
}
