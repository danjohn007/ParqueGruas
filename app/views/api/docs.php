<!-- Documentación de API -->

<div class="bg-white rounded-lg shadow-lg p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-book text-blue-500 mr-2"></i>
        Documentación de API REST
    </h2>
    
    <div class="prose max-w-none">
        <p class="text-gray-600 mb-6">
            El sistema Parque de Grúas proporciona una API REST para integraciones externas. 
            Todos los endpoints devuelven respuestas en formato JSON.
        </p>
        
        <!-- Endpoint: Consultar por Folio -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-3">Consultar por Folio</h3>
            <div class="mb-4">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-mono text-sm">GET</span>
                <code class="ml-3 text-blue-600">/api/checkFolio/{folio}</code>
            </div>
            
            <p class="text-gray-600 mb-4">
                Consulta el estado de un vehículo en corralón usando su folio.
            </p>
            
            <div class="mb-4">
                <h4 class="font-medium text-gray-700 mb-2">Parámetros de URL:</h4>
                <ul class="list-disc list-inside text-gray-600">
                    <li><code>folio</code> (string, requerido): Número de folio (ej: QRO-2025-001)</li>
                </ul>
            </div>
            
            <div class="mb-4">
                <h4 class="font-medium text-gray-700 mb-2">Ejemplo de Solicitud:</h4>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto"><code>curl -X GET "<?php echo BASE_URL; ?>/api/checkFolio/QRO-2025-001"</code></pre>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-700 mb-2">Ejemplo de Respuesta:</h4>
                <pre class="bg-gray-800 text-white p-4 rounded overflow-x-auto"><code>{
  "success": true,
  "data": {
    "folio": "QRO-2025-001",
    "plate": "ABC-123-D",
    "brand": "Nissan",
    "model": "Versa",
    "owner_name": "José García",
    "impound_date": "2025-01-08 10:30:00",
    "storage_days": 5,
    "total_amount": "1300.00",
    "paid": false,
    "status": "impounded"
  }
}</code></pre>
            </div>
        </div>
        
        <!-- Endpoint: Consultar por Placa -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-3">Consultar por Placa</h3>
            <div class="mb-4">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-mono text-sm">GET</span>
                <code class="ml-3 text-blue-600">/api/checkPlate/{placa}</code>
            </div>
            
            <p class="text-gray-600 mb-4">
                Busca si un vehículo con determinada placa está actualmente en el corralón.
            </p>
            
            <div class="mb-4">
                <h4 class="font-medium text-gray-700 mb-2">Parámetros de URL:</h4>
                <ul class="list-disc list-inside text-gray-600">
                    <li><code>placa</code> (string, requerido): Número de placa del vehículo (ej: ABC-123-D)</li>
                </ul>
            </div>
            
            <div class="mb-4">
                <h4 class="font-medium text-gray-700 mb-2">Ejemplo de Solicitud:</h4>
                <pre class="bg-gray-800 text-green-400 p-4 rounded overflow-x-auto"><code>curl -X GET "<?php echo BASE_URL; ?>/api/checkPlate/ABC-123-D"</code></pre>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-700 mb-2">Ejemplo de Respuesta:</h4>
                <pre class="bg-gray-800 text-white p-4 rounded overflow-x-auto"><code>{
  "success": true,
  "data": {
    "folio": "QRO-2025-001",
    "plate": "ABC-123-D",
    "brand": "Nissan",
    "model": "Versa",
    "owner_name": "José García",
    "impound_date": "2025-01-08 10:30:00",
    "storage_days": 5,
    "total_amount": "1300.00",
    "paid": false,
    "status": "impounded",
    "infraction_type": "Estacionamiento prohibido",
    "infraction_location": "Av. Constituyentes"
  }
}</code></pre>
            </div>
        </div>
        
        <!-- Endpoint: Obtener Eventos del Calendario -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-3">Obtener Eventos del Calendario</h3>
            <div class="mb-4">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-mono text-sm">GET</span>
                <code class="ml-3 text-blue-600">/calendar/getEvents</code>
            </div>
            
            <p class="text-gray-600 mb-4">
                Obtiene los eventos programados en el calendario. Requiere autenticación.
            </p>
            
            <div class="mb-4">
                <h4 class="font-medium text-gray-700 mb-2">Parámetros de Query (opcionales):</h4>
                <ul class="list-disc list-inside text-gray-600">
                    <li><code>start</code> (datetime): Fecha de inicio para filtrar eventos</li>
                    <li><code>end</code> (datetime): Fecha de fin para filtrar eventos</li>
                </ul>
            </div>
        </div>
        
        <!-- Endpoint: Crear Evento -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-3">Crear Evento</h3>
            <div class="mb-4">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded font-mono text-sm">POST</span>
                <code class="ml-3 text-blue-600">/calendar/createEvent</code>
            </div>
            
            <p class="text-gray-600 mb-4">
                Crea un nuevo evento en el calendario. Requiere autenticación.
            </p>
            
            <div class="mb-4">
                <h4 class="font-medium text-gray-700 mb-2">Body (JSON):</h4>
                <pre class="bg-gray-800 text-white p-4 rounded overflow-x-auto"><code>{
  "title": "Mantenimiento Grúa 001",
  "description": "Revisión preventiva",
  "event_type": "maintenance",
  "start_date": "2025-01-15 08:00:00",
  "end_date": "2025-01-15 17:00:00",
  "all_day": false,
  "location": "Taller",
  "color": "#e74c3c"
}</code></pre>
            </div>
        </div>
        
        <!-- Códigos de Respuesta -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6">
            <h3 class="text-xl font-semibold text-blue-800 mb-3">Códigos de Respuesta HTTP</h3>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-blue-200">
                        <th class="text-left py-2 text-blue-800">Código</th>
                        <th class="text-left py-2 text-blue-800">Descripción</th>
                    </tr>
                </thead>
                <tbody class="text-blue-700">
                    <tr class="border-b border-blue-200">
                        <td class="py-2 font-mono">200</td>
                        <td class="py-2">Solicitud exitosa</td>
                    </tr>
                    <tr class="border-b border-blue-200">
                        <td class="py-2 font-mono">400</td>
                        <td class="py-2">Solicitud incorrecta (parámetros faltantes o inválidos)</td>
                    </tr>
                    <tr class="border-b border-blue-200">
                        <td class="py-2 font-mono">403</td>
                        <td class="py-2">No autorizado (requiere autenticación)</td>
                    </tr>
                    <tr class="border-b border-blue-200">
                        <td class="py-2 font-mono">404</td>
                        <td class="py-2">Recurso no encontrado</td>
                    </tr>
                    <tr>
                        <td class="py-2 font-mono">500</td>
                        <td class="py-2">Error interno del servidor</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Notas de Seguridad -->
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6">
            <h3 class="text-xl font-semibold text-yellow-800 mb-3">
                <i class="fas fa-shield-alt mr-2"></i>
                Notas de Seguridad
            </h3>
            <ul class="list-disc list-inside text-yellow-700 space-y-2">
                <li>Las consultas públicas (checkFolio, checkPlate) no requieren autenticación actualmente.</li>
                <li>En producción, se recomienda implementar autenticación por token API.</li>
                <li>Los endpoints del calendario requieren sesión activa del usuario.</li>
                <li>Se recomienda usar HTTPS para todas las comunicaciones en producción.</li>
                <li>Implementar rate limiting para prevenir abuso de la API.</li>
            </ul>
        </div>
    </div>
</div>

<div class="mt-6 text-center">
    <a href="<?php echo BASE_URL; ?>/api" 
       class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Gestión de API
    </a>
</div>
