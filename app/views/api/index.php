<!-- Gestión de API e Integraciones -->

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Dispositivos HikVision -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-video text-blue-500 mr-2"></i>
                    Dispositivos HikVision
                </h3>
                <button onclick="showAddDeviceModal()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                    <i class="fas fa-plus mr-2"></i>Agregar Dispositivo
                </button>
            </div>
            
            <?php if (empty($devices)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-video text-5xl mb-3"></i>
                    <p>No hay dispositivos configurados</p>
                    <p class="text-sm">Agregue dispositivos HikVision para monitoreo</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($devices as $device): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                                        <?php
                                        $icons = [
                                            'camera' => 'fa-video',
                                            'dvr' => 'fa-server',
                                            'nvr' => 'fa-database',
                                            'access_control' => 'fa-door-open'
                                        ];
                                        ?>
                                        <i class="fas <?php echo $icons[$device['device_type']] ?? 'fa-video'; ?> text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-800">
                                            <?php echo htmlspecialchars($device['device_name']); ?>
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            <?php echo $device['device_ip']; ?>:<?php echo $device['device_port']; ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo htmlspecialchars($device['location']); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <?php 
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-gray-100 text-gray-800',
                                        'error' => 'bg-red-100 text-red-800'
                                    ];
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusColors[$device['status']]; ?>">
                                        <?php echo ucfirst($device['status']); ?>
                                    </span>
                                    
                                    <button onclick="testDevice(<?php echo $device['id']; ?>)" 
                                            class="text-blue-600 hover:text-blue-800 px-2" 
                                            title="Probar conexión">
                                        <i class="fas fa-plug"></i>
                                    </button>
                                    
                                    <a href="<?php echo BASE_URL; ?>/api/deleteDevice/<?php echo $device['id']; ?>" 
                                       class="text-red-600 hover:text-red-800 px-2" 
                                       title="Eliminar"
                                       onclick="return confirm('¿Eliminar este dispositivo?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Enlaces de API -->
    <div>
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-plug text-green-500 mr-2"></i>
                API REST
            </h3>
            
            <div class="space-y-3 text-sm">
                <a href="<?php echo BASE_URL; ?>/api/docs" 
                   class="block p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                    <i class="fas fa-book text-blue-600 mr-2"></i>
                    Documentación de API
                </a>
                
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="font-medium text-gray-700 mb-2">Endpoints disponibles:</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>• GET /api/checkFolio/{folio}</li>
                        <li>• GET /api/checkPlate/{placa}</li>
                        <li>• POST /calendar/createEvent</li>
                        <li>• GET /calendar/getEvents</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <h4 class="font-medium text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-2"></i>
                Información
            </h4>
            <p class="text-sm text-blue-700">
                Los dispositivos HikVision se integran automáticamente para videovigilancia del corralón.
            </p>
        </div>
    </div>
</div>

<!-- Modal para agregar dispositivo -->
<div id="addDeviceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Agregar Dispositivo HikVision</h3>
            
            <form action="<?php echo BASE_URL; ?>/api/addDevice" method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Dispositivo *</label>
                        <input type="text" name="device_name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dirección IP *</label>
                        <input type="text" name="device_ip" required placeholder="192.168.1.64"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Puerto</label>
                        <input type="number" name="device_port" value="80"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                        <input type="text" name="username" value="admin"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña *</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select name="device_type"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="camera">Cámara</option>
                            <option value="dvr">DVR</option>
                            <option value="nvr">NVR</option>
                            <option value="access_control">Control de Acceso</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ubicación</label>
                        <input type="text" name="location" placeholder="Entrada principal"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" onclick="closeAddDeviceModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Agregar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAddDeviceModal() {
    document.getElementById('addDeviceModal').classList.remove('hidden');
}

function closeAddDeviceModal() {
    document.getElementById('addDeviceModal').classList.add('hidden');
}

async function testDevice(deviceId) {
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/api/testDevice/' + deviceId);
        const result = await response.json();
        
        if (result.success) {
            alert('✓ Conexión exitosa al dispositivo');
            location.reload();
        } else {
            alert('✗ Error: ' + result.message);
        }
    } catch (error) {
        alert('✗ Error de conexión: ' + error.message);
    } finally {
        button.innerHTML = originalContent;
        button.disabled = false;
    }
}
</script>
