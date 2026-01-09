<!-- Reporte de Operaciones -->

<div class="mb-6">
    <form method="GET" action="<?php echo BASE_URL; ?>/reports/operations" 
          class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-calendar mr-2"></i>Seleccionar Período
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                <input type="date" name="date_from" value="<?php echo $dateFrom; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                <input type="date" name="date_to" value="<?php echo $dateTo; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Generar Reporte
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Estado de Grúas -->
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-truck-pickup mr-2"></i>Estado de Grúas
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php 
        $statusLabels = [
            'available' => ['nombre' => 'Disponibles', 'color' => 'green', 'icon' => 'check-circle'],
            'in_service' => ['nombre' => 'En Servicio', 'color' => 'yellow', 'icon' => 'spinner'],
            'maintenance' => ['nombre' => 'Mantenimiento', 'color' => 'red', 'icon' => 'wrench'],
            'out_of_service' => ['nombre' => 'Fuera de Servicio', 'color' => 'gray', 'icon' => 'ban']
        ];
        
        foreach ($statusLabels as $status => $info):
            $count = 0;
            foreach ($craneStats as $stat) {
                if ($stat['status'] === $status) {
                    $count = $stat['count'];
                    break;
                }
            }
        ?>
            <div class="border border-gray-200 rounded-lg p-4 text-center">
                <i class="fas fa-<?php echo $info['icon']; ?> text-3xl text-<?php echo $info['color']; ?>-600 mb-2"></i>
                <p class="text-2xl font-bold text-<?php echo $info['color']; ?>-600"><?php echo $count; ?></p>
                <p class="text-sm text-gray-600"><?php echo $info['nombre']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Estadísticas por Municipio -->
<?php if (!empty($statsByMunicipality)): ?>
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-map-marked-alt mr-2"></i>Estadísticas por Municipio
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Municipio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Registros</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">En Corralón</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Liberados</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($statsByMunicipality as $stat): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            <?php echo htmlspecialchars($stat['municipality']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <?php echo $stat['total']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-medium">
                            <?php echo $stat['impounded']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">
                            <?php echo $stat['released']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Estadísticas Diarias -->
<?php if (!empty($dailyStats)): ?>
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-chart-line mr-2"></i>Operaciones Diarias del Período
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Ingresos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Liberados</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permanecen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($dailyStats as $stat): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo date('d/m/Y', strtotime($stat['date'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-medium">
                            <?php echo $stat['total_impounds']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">
                            <?php echo $stat['released']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-medium">
                            <?php echo $stat['impounded']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-gray-50">
                <tr class="font-semibold">
                    <td class="px-6 py-4 text-sm text-gray-900">TOTALES</td>
                    <td class="px-6 py-4 text-sm text-blue-600">
                        <?php echo array_sum(array_column($dailyStats, 'total_impounds')); ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-green-600">
                        <?php echo array_sum(array_column($dailyStats, 'released')); ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-yellow-600">
                        <?php echo array_sum(array_column($dailyStats, 'impounded')); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Resumen General -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total de Ingresos</p>
                <p class="text-3xl font-bold text-blue-600">
                    <?php echo !empty($dailyStats) ? array_sum(array_column($dailyStats, 'total_impounds')) : 0; ?>
                </p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-car text-3xl text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Vehículos Liberados</p>
                <p class="text-3xl font-bold text-green-600">
                    <?php echo !empty($dailyStats) ? array_sum(array_column($dailyStats, 'released')) : 0; ?>
                </p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-check-circle text-3xl text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">En Corralón</p>
                <p class="text-3xl font-bold text-yellow-600">
                    <?php echo !empty($dailyStats) ? array_sum(array_column($dailyStats, 'impounded')) : 0; ?>
                </p>
            </div>
            <div class="bg-yellow-100 rounded-full p-4">
                <i class="fas fa-warehouse text-3xl text-yellow-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Botones de Acción -->
<div class="mt-6 flex gap-4">
    <a href="<?php echo BASE_URL; ?>/reports/exportCSV?type=impounds&date_from=<?php echo $dateFrom; ?>&date_to=<?php echo $dateTo; ?>" 
       class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition shadow-lg">
        <i class="fas fa-download mr-2"></i>Exportar a CSV
    </a>
    <a href="<?php echo BASE_URL; ?>/reports" 
       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Reportes
    </a>
</div>
