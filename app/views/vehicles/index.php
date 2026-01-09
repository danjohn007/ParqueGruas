<!-- Listado de vehículos -->

<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?php echo BASE_URL; ?>/vehicles/create" 
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
            <i class="fas fa-plus mr-2"></i>Registrar Vehículo
        </a>
    </div>
</div>

<!-- Filtros de búsqueda -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-search mr-2"></i>Búsqueda de Vehículos
    </h3>
    <form method="GET" action="<?php echo BASE_URL; ?>/vehicles" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Placa</label>
            <input type="text" 
                   name="plate" 
                   value="<?php echo $filters['plate'] ?? ''; ?>"
                   placeholder="ABC-123-D"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Propietario</label>
            <input type="text" 
                   name="owner_name" 
                   value="<?php echo $filters['owner_name'] ?? ''; ?>"
                   placeholder="Nombre del propietario"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
            <input type="text" 
                   name="brand" 
                   value="<?php echo $filters['brand'] ?? ''; ?>"
                   placeholder="Nissan, Toyota, etc."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
            <select name="vehicle_type" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="auto" <?php echo ($filters['vehicle_type'] ?? '') === 'auto' ? 'selected' : ''; ?>>Automóvil</option>
                <option value="moto" <?php echo ($filters['vehicle_type'] ?? '') === 'moto' ? 'selected' : ''; ?>>Motocicleta</option>
                <option value="camioneta" <?php echo ($filters['vehicle_type'] ?? '') === 'camioneta' ? 'selected' : ''; ?>>Camioneta</option>
                <option value="camion" <?php echo ($filters['vehicle_type'] ?? '') === 'camion' ? 'selected' : ''; ?>>Camión</option>
                <option value="otro" <?php echo ($filters['vehicle_type'] ?? '') === 'otro' ? 'selected' : ''; ?>>Otro</option>
            </select>
        </div>
        
        <div class="md:col-span-4 flex gap-2">
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-search mr-2"></i>Buscar
            </button>
            <a href="<?php echo BASE_URL; ?>/vehicles" 
               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times mr-2"></i>Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Tabla de vehículos -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Placa
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Marca/Modelo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Año
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Color
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tipo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Propietario
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($vehicles)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron vehículos</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="<?php echo BASE_URL; ?>/vehicles/details/<?php echo $vehicle['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    <?php echo htmlspecialchars($vehicle['plate']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php echo $vehicle['year'] ?? 'N/A'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php echo htmlspecialchars($vehicle['color']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $typeLabels = [
                                    'auto' => 'Automóvil',
                                    'moto' => 'Moto',
                                    'camioneta' => 'Camioneta',
                                    'camion' => 'Camión',
                                    'otro' => 'Otro'
                                ];
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?php echo $typeLabels[$vehicle['vehicle_type']] ?? $vehicle['vehicle_type']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php echo htmlspecialchars($vehicle['owner_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo BASE_URL; ?>/vehicles/details/<?php echo $vehicle['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3" 
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/vehicles/edit/<?php echo $vehicle['id']; ?>" 
                                   class="text-green-600 hover:text-green-900 mr-3" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <a href="<?php echo BASE_URL; ?>/vehicles/delete/<?php echo $vehicle['id']; ?>" 
                                       class="text-red-600 hover:text-red-900" 
                                       title="Eliminar"
                                       onclick="return confirm('¿Está seguro de eliminar este vehículo?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (!empty($vehicles)): ?>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Total: <strong><?php echo count($vehicles); ?></strong> vehículo(s)
            </p>
        </div>
    <?php endif; ?>
</div>
