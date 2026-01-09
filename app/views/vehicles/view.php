<!-- Detalle de Vehículo -->

<div class="space-y-6">
    <!-- Información del Vehículo -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="border-b pb-4 mb-4 flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Información del Vehículo</h3>
                <p class="text-xl text-blue-600 font-semibold mt-2">
                    <?php echo htmlspecialchars($vehicle['plate']); ?>
                </p>
            </div>
            <div>
                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'operator'): ?>
                <a href="<?php echo BASE_URL; ?>/vehicles/edit/<?php echo $vehicle['id']; ?>" 
                   class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition inline-block">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Datos del Vehículo</h4>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Placa:</span>
                        <span class="font-semibold block text-lg">
                            <?php echo htmlspecialchars($vehicle['plate']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Marca:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($vehicle['brand'] ?: 'N/A'); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Modelo:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($vehicle['model'] ?: 'N/A'); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Año:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($vehicle['year'] ?: 'N/A'); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Color:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($vehicle['color'] ?: 'N/A'); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Tipo:</span>
                        <span class="font-semibold block">
                            <?php 
                            $types = [
                                'auto' => 'Automóvil',
                                'moto' => 'Motocicleta',
                                'camioneta' => 'Camioneta',
                                'camion' => 'Camión',
                                'otro' => 'Otro'
                            ];
                            echo $types[$vehicle['vehicle_type']] ?? $vehicle['vehicle_type'];
                            ?>
                        </span>
                    </div>
                    <?php if (!empty($vehicle['vin'])): ?>
                    <div>
                        <span class="text-gray-600">VIN:</span>
                        <span class="font-semibold block font-mono">
                            <?php echo htmlspecialchars($vehicle['vin']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Información del Propietario</h4>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Nombre:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($vehicle['owner_name'] ?: 'N/A'); ?>
                        </span>
                    </div>
                    <?php if (!empty($vehicle['owner_phone'])): ?>
                    <div>
                        <span class="text-gray-600">Teléfono:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($vehicle['owner_phone']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($vehicle['owner_address'])): ?>
                    <div>
                        <span class="text-gray-600">Dirección:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($vehicle['owner_address']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($vehicle['notes'])): ?>
        <div class="mt-6 pt-4 border-t">
            <h4 class="text-sm font-semibold text-gray-600 uppercase mb-2">Observaciones</h4>
            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($vehicle['notes'])); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="mt-4 pt-4 border-t text-sm text-gray-500">
            Registrado el: <?php echo date('d/m/Y', strtotime($vehicle['created_at'])); ?>
        </div>
    </div>

    <!-- Historial de Impounds -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-history mr-2"></i>Historial de Corralón
        </h3>
        
        <?php if (empty($impoundHistory)): ?>
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-2"></i>
                <p>Este vehículo no tiene registros en el corralón</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Ingreso</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Infracción</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grúa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($impoundHistory as $impound): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="<?php echo BASE_URL; ?>/impounds/details/<?php echo $impound['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        <?php echo htmlspecialchars($impound['folio']); ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <?php echo date('d/m/Y', strtotime($impound['impound_date'])); ?>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <?php echo htmlspecialchars($impound['infraction_type']); ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <?php echo htmlspecialchars($impound['crane_number'] ?? 'N/A'); ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?php if ($impound['status'] === 'impounded'): ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                            En Corralón
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            Liberado
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold">
                                    $<?php echo number_format($impound['total_amount'], 2); ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <a href="<?php echo BASE_URL; ?>/impounds/details/<?php echo $impound['id']; ?>" 
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
        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'operator'): ?>
        <a href="<?php echo BASE_URL; ?>/vehicles/edit/<?php echo $vehicle['id']; ?>" 
           class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 transition shadow-lg">
            <i class="fas fa-edit mr-2"></i>Editar Vehículo
        </a>
        <?php endif; ?>
        <a href="<?php echo BASE_URL; ?>/vehicles" 
           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Listado
        </a>
    </div>
</div>
