<!-- Listado de registros de corralón -->

<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?php echo BASE_URL; ?>/impounds/create" 
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
            <i class="fas fa-plus mr-2"></i>Nuevo Ingreso
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-filter mr-2"></i>Filtrar Registros
    </h3>
    <form method="GET" action="<?php echo BASE_URL; ?>/impounds" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Folio</label>
            <input type="text" name="folio" value="<?php echo $filters['folio'] ?? ''; ?>"
                   placeholder="QRO-2025-001"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Placa</label>
            <input type="text" name="plate" value="<?php echo $filters['plate'] ?? ''; ?>"
                   placeholder="ABC-123-D"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="impounded" <?php echo ($filters['status'] ?? '') === 'impounded' ? 'selected' : ''; ?>>En corralón</option>
                <option value="released" <?php echo ($filters['status'] ?? '') === 'released' ? 'selected' : ''; ?>>Liberado</option>
                <option value="pending" <?php echo ($filters['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pendiente</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Municipio</label>
            <input type="text" name="municipality" value="<?php echo $filters['municipality'] ?? ''; ?>"
                   placeholder="Querétaro"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha desde</label>
            <input type="date" name="date_from" value="<?php echo $filters['date_from'] ?? ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha hasta</label>
            <input type="date" name="date_to" value="<?php echo $filters['date_to'] ?? ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="md:col-span-3 flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-search mr-2"></i>Buscar
            </button>
            <a href="<?php echo BASE_URL; ?>/impounds" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times mr-2"></i>Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Tabla de registros -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehículo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Propietario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Infracción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Ingreso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($impounds)): ?>
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron registros</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($impounds as $impound): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="<?php echo BASE_URL; ?>/impounds/view/<?php echo $impound['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    <?php echo htmlspecialchars($impound['folio']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($impound['plate']); ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?php echo htmlspecialchars($impound['brand'] . ' ' . $impound['model']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php echo htmlspecialchars($impound['owner_name']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php echo htmlspecialchars(substr($impound['infraction_type'], 0, 30)) . (strlen($impound['infraction_type']) > 30 ? '...' : ''); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php echo date('d/m/Y H:i', strtotime($impound['impound_date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                <?php echo $impound['storage_days']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                <span class="<?php echo $impound['paid'] ? 'text-green-600' : 'text-red-600'; ?>">
                                    $<?php echo number_format($impound['total_amount'], 2); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $statusColors = [
                                    'impounded' => 'bg-yellow-100 text-yellow-800',
                                    'released' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-red-100 text-red-800'
                                ];
                                $statusLabels = [
                                    'impounded' => 'En corralón',
                                    'released' => 'Liberado',
                                    'pending' => 'Pendiente'
                                ];
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusColors[$impound['status']]; ?>">
                                    <?php echo $statusLabels[$impound['status']]; ?>
                                </span>
                                <?php if (!$impound['paid']): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 mt-1">
                                        Sin pagar
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo BASE_URL; ?>/impounds/view/<?php echo $impound['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3" 
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($impound['status'] === 'impounded' && !$impound['paid']): ?>
                                    <a href="<?php echo BASE_URL; ?>/payments/create/<?php echo $impound['id']; ?>" 
                                       class="text-green-600 hover:text-green-900" 
                                       title="Registrar pago">
                                        <i class="fas fa-dollar-sign"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (!empty($impounds)): ?>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-between items-center">
            <p class="text-sm text-gray-600">
                Total: <strong><?php echo count($impounds); ?></strong> registro(s)
            </p>
            <div class="text-sm text-gray-600">
                <?php
                $totalAmount = array_sum(array_column($impounds, 'total_amount'));
                $paidAmount = array_sum(array_map(function($i) { return $i['paid'] ? $i['total_amount'] : 0; }, $impounds));
                ?>
                Total general: <strong class="text-gray-800">$<?php echo number_format($totalAmount, 2); ?></strong> | 
                Pagado: <strong class="text-green-600">$<?php echo number_format($paidAmount, 2); ?></strong> | 
                Pendiente: <strong class="text-red-600">$<?php echo number_format($totalAmount - $paidAmount, 2); ?></strong>
            </div>
        </div>
    <?php endif; ?>
</div>
