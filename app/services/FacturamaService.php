<?php
/**
 * Servicio de Integración con Facturama API
 * Facturación Electrónica CFDI 4.0
 * 
 * Documentación: https://facturama.mx/api-facturacion-electronica
 */

class FacturamaService {
    private $apiKey;
    private $apiSecret;
    private $sandbox;
    private $baseUrl;
    
    public function __construct() {
        // Cargar configuración desde base de datos
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'facturama%'");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $this->apiKey = $settings['facturama_api_key'] ?? '';
        $this->apiSecret = $settings['facturama_api_secret'] ?? '';
        $this->sandbox = ($settings['facturama_sandbox_mode'] ?? 'true') === 'true';
        
        // URL base según modo
        $this->baseUrl = $this->sandbox 
            ? 'https://apisandbox.facturama.mx' 
            : 'https://api.facturama.mx';
    }
    
    /**
     * Verificar si está habilitada la integración
     */
    public function isEnabled() {
        return !empty($this->apiKey) && !empty($this->apiSecret);
    }
    
    /**
     * Crear factura (CFDI)
     */
    public function createInvoice($invoiceData) {
        if (!$this->isEnabled()) {
            return ['error' => 'Facturama API no está configurada'];
        }
        
        $endpoint = '/api/Cfdi';
        $url = $this->baseUrl . $endpoint;
        
        $cfdiData = $this->buildCfdiStructure($invoiceData);
        
        return $this->makeRequest('POST', $url, $cfdiData);
    }
    
    /**
     * Construir estructura CFDI 4.0 para Facturama
     */
    private function buildCfdiStructure($invoiceData) {
        return [
            'Serie' => $invoiceData['series'] ?? 'A',
            'Currency' => 'MXN',
            'ExpeditionPlace' => $invoiceData['expedition_place'] ?? '76000',
            'PaymentForm' => $invoiceData['payment_form'] ?? '01',
            'PaymentMethod' => $invoiceData['payment_method'] ?? 'PUE',
            'CfdiType' => 'I',
            'Receiver' => [
                'Rfc' => $invoiceData['receiver']['rfc'],
                'Name' => $invoiceData['receiver']['name'],
                'CfdiUse' => $invoiceData['receiver']['cfdi_use'] ?? 'G03',
                'FiscalRegime' => $invoiceData['receiver']['fiscal_regime'] ?? '601',
                'TaxZipCode' => $invoiceData['receiver']['zip_code'] ?? '00000'
            ],
            'Items' => $this->buildItems($invoiceData['items'])
        ];
    }
    
    private function buildItems($items) {
        $cfdiItems = [];
        
        foreach ($items as $item) {
            $cfdiItems[] = [
                'ProductCode' => $item['product_code'] ?? '78101800',
                'UnitCode' => $item['unit_code'] ?? 'E48',
                'Unit' => 'Servicio',
                'Description' => $item['description'],
                'Quantity' => floatval($item['quantity'] ?? 1),
                'UnitPrice' => floatval($item['unit_price']),
                'Subtotal' => floatval($item['subtotal']),
                'Discount' => floatval($item['discount'] ?? 0),
                'Taxes' => [
                    [
                        'Name' => 'IVA',
                        'Rate' => floatval($item['tax_rate'] ?? 16) / 100,
                        'IsRetention' => false,
                        'Total' => floatval($item['tax_amount'])
                    ]
                ],
                'Total' => floatval($item['total'])
            ];
        }
        
        return $cfdiItems;
    }
    
    public function getInvoice($facturamaId) {
        if (!$this->isEnabled()) {
            return ['error' => 'Facturama API no está configurada'];
        }
        
        $endpoint = '/api/Cfdi/' . $facturamaId;
        $url = $this->baseUrl . $endpoint;
        
        return $this->makeRequest('GET', $url);
    }
    
    public function cancelInvoice($facturamaId, $motive = '02', $substitution = null) {
        if (!$this->isEnabled()) {
            return ['error' => 'Facturama API no está configurada'];
        }
        
        $endpoint = '/api/Cfdi/' . $facturamaId;
        $url = $this->baseUrl . $endpoint;
        
        $data = [
            'Motive' => $motive,
            'Substitution' => $substitution
        ];
        
        return $this->makeRequest('DELETE', $url, $data);
    }
    
    private function makeRequest($method, $url, $data = null, $raw = false) {
        $ch = curl_init();
        
        $auth = base64_encode($this->apiKey . ':' . $this->apiSecret);
        
        $headers = [
            'Authorization: Basic ' . $auth,
            'Content-Type: application/json'
        ];
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Always verify SSL in production, use proper sandbox certificates
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            return ['error' => $error];
        }
        
        if ($raw) {
            return $response;
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            return [
                'error' => true,
                'http_code' => $httpCode,
                'message' => $result['Message'] ?? 'Error desconocido',
                'details' => $result
            ];
        }
        
        return $result;
    }
}
