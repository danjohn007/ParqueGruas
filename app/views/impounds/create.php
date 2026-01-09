<!-- Formulario de nuevo ingreso al corralón -->

<div class="bg-white rounded-lg shadow-lg p-6">
    <form method="POST" action="<?php echo BASE_URL; ?>/impounds/store" class="space-y-6">
        <!-- Información del Folio -->
        <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">
                <i class="fas fa-barcode mr-2"></i>Folio de Registro
            </h3>
            <p class="text-2xl font-bold text-blue-700"><?php echo htmlspecialchars($nextFolio); ?></p>
        </div>

        <!-- Selección o Registro de Vehículo -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-car mr-2"></i>Información del Vehículo
            </h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Seleccionar vehículo existente
                </label>
                <select id="vehicle_id" name="vehicle_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        onchange="toggleNewVehicleForm()">
                    <option value="">-- Registrar nuevo vehículo --</option>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <option value="<?php echo $vehicle['id']; ?>">
                            <?php echo htmlspecialchars($vehicle['plate'] . ' - ' . $vehicle['brand'] . ' ' . $vehicle['model'] . ' (' . $vehicle['owner_name'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="new-vehicle-form" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Placa *</label>
                    <input type="text" name="new_plate" id="new_plate"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="ABC-123-D">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
                    <input type="text" name="new_brand"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nissan, Toyota, etc.">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modelo</label>
                    <input type="text" name="new_model"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Versa, Corolla, etc.">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                    <input type="number" name="new_year"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="2020">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                    <input type="text" name="new_color"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Blanco, Negro, etc.">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Vehículo</label>
                    <select name="new_vehicle_type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="auto">Automóvil</option>
                        <option value="moto">Motocicleta</option>
                        <option value="camioneta">Camioneta</option>
                        <option value="camion">Camión</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Propietario</label>
                    <input type="text" name="new_owner_name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nombre completo del propietario">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <input type="text" name="new_owner_phone"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="442-123-4567">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                    <input type="text" name="new_owner_address"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Dirección del propietario">
                </div>
            </div>
        </div>

        <!-- Información de la Infracción -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>Información de la Infracción
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Infracción *</label>
                    <input type="text" name="infraction_type" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Estacionamiento indebido, abandono, etc.">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ubicación de la Infracción *</label>
                    <input type="text" name="infraction_location" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Calle, colonia, referencias">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Municipio</label>
                    <input type="text" name="municipality" value="Querétaro"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha y Hora de Ingreso</label>
                    <input type="datetime-local" name="impound_date" value="<?php echo date('Y-m-d\TH:i'); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Información del Oficial -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user-shield mr-2"></i>Información del Oficial
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Oficial</label>
                    <input type="text" name="officer_name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nombre completo">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Placa</label>
                    <input type="text" name="officer_badge"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Número de placa del oficial">
                </div>
            </div>
        </div>

        <!-- Grúa y Costos -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-truck-pickup mr-2"></i>Grúa y Costos
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grúa Asignada</label>
                    <select name="crane_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Sin asignar --</option>
                        <?php foreach ($cranes as $crane): ?>
                            <option value="<?php echo $crane['id']; ?>">
                                <?php echo htmlspecialchars($crane['crane_number'] . ' - ' . $crane['driver_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Costo de Arrastre ($)</label>
                    <input type="number" name="tow_cost" step="0.01" value="800.00"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Costo por Día de Almacenaje ($)</label>
                    <input type="number" name="storage_cost_per_day" step="0.01" value="100.00"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto de Multa ($)</label>
                    <input type="number" name="fine_amount" step="0.01" value="0.00"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="border-t pt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
            <textarea name="observations" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                      placeholder="Observaciones adicionales sobre el ingreso"></textarea>
        </div>

        <!-- Botones -->
        <div class="flex gap-4 border-t pt-6">
            <button type="submit" 
                    class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
                <i class="fas fa-save mr-2"></i>Registrar Ingreso
            </button>
            <a href="<?php echo BASE_URL; ?>/impounds" 
               class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
        </div>
    </form>
</div>

<script>
function toggleNewVehicleForm() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const newVehicleForm = document.getElementById('new-vehicle-form');
    const newPlate = document.getElementById('new_plate');
    
    if (vehicleSelect.value === '') {
        newVehicleForm.style.display = 'grid';
        newPlate.required = true;
    } else {
        newVehicleForm.style.display = 'none';
        newPlate.required = false;
    }
}

// Initialize form state
toggleNewVehicleForm();
</script>
