<!-- Listado de grúas -->

<div class="mb-6 flex justify-between items-center">
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <div>
            <a href="<?php echo BASE_URL; ?>/cranes/create" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
                <i class="fas fa-plus mr-2"></i>Registrar Grúa
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Tabla de grúas -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Placa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marca/Modelo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conductor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($cranes)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron grúas registradas</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cranes as $crane): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="<?php echo BASE_URL; ?>/cranes/details/<?php echo $crane['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    <?php echo htmlspecialchars($crane['crane_number']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($crane['plate']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($crane['brand'] . ' ' . $crane['model']); ?>
                                <?php if ($crane['year']): ?>
                                    <span class="text-gray-500">(<?php echo $crane['year']; ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo $crane['capacity_tons'] ? $crane['capacity_tons'] . ' ton' : 'N/A'; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php echo htmlspecialchars($crane['driver_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $statusColors = [
                                    'available' => 'bg-green-100 text-green-800',
                                    'in_service' => 'bg-yellow-100 text-yellow-800',
                                    'maintenance' => 'bg-red-100 text-red-800',
                                    'out_of_service' => 'bg-gray-100 text-gray-800'
                                ];
                                $statusLabels = [
                                    'available' => 'Disponible',
                                    'in_service' => 'En servicio',
                                    'maintenance' => 'Mantenimiento',
                                    'out_of_service' => 'Fuera de servicio'
                                ];
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusColors[$crane['status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo $statusLabels[$crane['status']] ?? $crane['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo BASE_URL; ?>/cranes/details/<?php echo $crane['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3" 
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/cranes/edit/<?php echo $crane['id']; ?>" 
                                   class="text-green-600 hover:text-green-900" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (!empty($cranes)): ?>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Total: <strong><?php echo count($cranes); ?></strong> grúa(s) registrada(s)
            </p>
        </div>
    <?php endif; ?>
</div>
