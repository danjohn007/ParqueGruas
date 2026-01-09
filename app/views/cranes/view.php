<!-- Detalle de Grúa -->

<div class="space-y-6">
    <!-- Información de la Grúa -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="border-b pb-4 mb-4 flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Información de la Grúa</h3>
                <p class="text-xl text-blue-600 font-semibold mt-2">
                    <?php echo htmlspecialchars($crane['crane_number']); ?>
                </p>
            </div>
            <div>
                <a href="<?php echo BASE_URL; ?>/cranes/edit/<?php echo $crane['id']; ?>" 
                   class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition inline-block">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Datos de la Grúa</h4>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Número de Grúa:</span>
                        <span class="font-semibold block text-lg">
                            <?php echo htmlspecialchars($crane['crane_number']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Placa:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($crane['plate']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Marca:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($crane['brand'] ?: 'N/A'); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Modelo:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($crane['model'] ?: 'N/A'); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Año:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($crane['year'] ?: 'N/A'); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Capacidad:</span>
                        <span class="font-semibold block">
                            <?php echo $crane['capacity_tons'] ? htmlspecialchars($crane['capacity_tons']) . ' toneladas' : 'N/A'; ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Estado:</span>
                        <span class="font-semibold block">
                            <?php 
                            $statusLabels = [
                                'available' => 'Disponible',
                                'in_service' => 'En servicio',
                                'maintenance' => 'Mantenimiento',
                                'inactive' => 'Inactiva'
                            ];
                            $statusColors = [
                                'available' => 'bg-green-100 text-green-800',
                                'in_service' => 'bg-yellow-100 text-yellow-800',
                                'maintenance' => 'bg-red-100 text-red-800',
                                'inactive' => 'bg-gray-100 text-gray-800'
                            ];
                            ?>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $statusColors[$crane['status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo $statusLabels[$crane['status']] ?? $crane['status']; ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Información del Conductor</h4>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Conductor:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($crane['driver_name'] ?: 'N/A'); ?>
                        </span>
                    </div>
                    <?php if (!empty($crane['driver_license'])): ?>
                    <div>
                        <span class="text-gray-600">Licencia:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($crane['driver_license']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3 mt-6">Mantenimiento</h4>
                <div class="space-y-2">
                    <?php if (!empty($crane['last_maintenance'])): ?>
                    <div>
                        <span class="text-gray-600">Último Mantenimiento:</span>
                        <span class="font-semibold block">
                            <?php echo date('d/m/Y', strtotime($crane['last_maintenance'])); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($crane['next_maintenance'])): ?>
                    <div>
                        <span class="text-gray-600">Próximo Mantenimiento:</span>
                        <span class="font-semibold block">
                            <?php echo date('d/m/Y', strtotime($crane['next_maintenance'])); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($crane['notes'])): ?>
        <div class="mt-6 pt-4 border-t">
            <h4 class="text-sm font-semibold text-gray-600 uppercase mb-2">Observaciones</h4>
            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($crane['notes'])); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="mt-4 pt-4 border-t text-sm text-gray-500">
            Registrada el: <?php echo date('d/m/Y', strtotime($crane['created_at'])); ?>
        </div>
    </div>

    <!-- Historial de Servicios -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-history mr-2"></i>Historial de Servicios
        </h3>
        
        <?php if (empty($serviceHistory)): ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-2"></i>
                <p>Esta grúa no tiene servicios registrados</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehículo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Infracción</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($serviceHistory as $service): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="<?php echo BASE_URL; ?>/impounds/details/<?php echo $service['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        <?php echo htmlspecialchars($service['folio']); ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <?php echo date('d/m/Y', strtotime($service['impound_date'])); ?>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <?php echo htmlspecialchars($service['plate'] ?? 'N/A'); ?>
                                    <?php if (!empty($service['brand'])): ?>
                                        <br><span class="text-gray-500 text-xs">
                                            <?php echo htmlspecialchars($service['brand'] . ' ' . $service['model']); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <?php echo htmlspecialchars($service['infraction_type']); ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?php if ($service['status'] === 'impounded'): ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                            En Corralón
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            Liberado
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <a href="<?php echo BASE_URL; ?>/impounds/details/<?php echo $service['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Botones de Acción -->
    <div class="flex gap-4">
        <a href="<?php echo BASE_URL; ?>/cranes/edit/<?php echo $crane['id']; ?>" 
           class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 transition shadow-lg">
            <i class="fas fa-edit mr-2"></i>Editar Grúa
        </a>
        <a href="<?php echo BASE_URL; ?>/cranes" 
           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Listado
        </a>
    </div>
</div>
