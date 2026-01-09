<!-- Formulario de edición de grúa -->

<div class="bg-white rounded-lg shadow-lg p-6">
    <form method="POST" action="<?php echo BASE_URL; ?>/cranes/update/<?php echo $crane['id']; ?>" class="space-y-6">
        <!-- Información de la Grúa -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-truck-pickup mr-2"></i>Información de la Grúa
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Grúa *</label>
                    <input type="text" name="crane_number" required
                           value="<?php echo htmlspecialchars($crane['crane_number']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="GR-001">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Placa *</label>
                    <input type="text" name="plate" required
                           value="<?php echo htmlspecialchars($crane['plate']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="ABC-123-A">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
                    <input type="text" name="brand"
                           value="<?php echo htmlspecialchars($crane['brand']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="International, Freightliner, etc.">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modelo</label>
                    <input type="text" name="model"
                           value="<?php echo htmlspecialchars($crane['model']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="DuraStar, M2 106, etc.">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                    <input type="number" name="year"
                           value="<?php echo htmlspecialchars($crane['year']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="2020" min="1900" max="<?php echo date('Y') + 1; ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Capacidad (toneladas)</label>
                    <input type="number" name="capacity_tons" step="0.5"
                           value="<?php echo htmlspecialchars($crane['capacity_tons']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="3.5">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="available" <?php echo $crane['status'] === 'available' ? 'selected' : ''; ?>>Disponible</option>
                        <option value="in_service" <?php echo $crane['status'] === 'in_service' ? 'selected' : ''; ?>>En servicio</option>
                        <option value="maintenance" <?php echo $crane['status'] === 'maintenance' ? 'selected' : ''; ?>>Mantenimiento</option>
                        <option value="inactive" <?php echo $crane['status'] === 'inactive' ? 'selected' : ''; ?>>Inactiva</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Información del Conductor -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user mr-2"></i>Información del Conductor
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Conductor</label>
                    <input type="text" name="driver_name"
                           value="<?php echo htmlspecialchars($crane['driver_name']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nombre completo del conductor">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Licencia del Conductor</label>
                    <input type="text" name="driver_license"
                           value="<?php echo htmlspecialchars($crane['driver_license']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Número de licencia">
                </div>
            </div>
        </div>

        <!-- Mantenimiento -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-tools mr-2"></i>Información de Mantenimiento
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Último Mantenimiento</label>
                    <input type="date" name="last_maintenance"
                           value="<?php echo htmlspecialchars($crane['last_maintenance']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Próximo Mantenimiento</label>
                    <input type="date" name="next_maintenance"
                           value="<?php echo htmlspecialchars($crane['next_maintenance']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Notas -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-sticky-note mr-2"></i>Información Adicional
            </h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas / Observaciones</label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Cualquier observación adicional sobre la grúa"><?php echo htmlspecialchars($crane['notes']); ?></textarea>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex gap-4 pt-4">
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
                <i class="fas fa-save mr-2"></i>Guardar Cambios
            </button>
            <a href="<?php echo BASE_URL; ?>/cranes/details/<?php echo $crane['id']; ?>" 
               class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
        </div>
    </form>
</div>
