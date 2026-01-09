<!-- Reporte de Ingresos -->

<div class="mb-6">
    <form method="GET" action="<?php echo BASE_URL; ?>/reports/revenue" 
          class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-calendar mr-2"></i>Seleccionar Período
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                <input type="date" name="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                <input type="date" name="date_to" value="<?php echo htmlspecialchars($dateTo); ?>"
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

<!-- Resumen de Ingresos -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Recaudado</p>
                <p class="text-3xl font-bold text-green-600">
                    $<?php echo number_format($totalRevenue, 2); ?>
                </p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-dollar-sign text-3xl text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total de Pagos</p>
                <p class="text-3xl font-bold text-blue-600">
                    <?php echo count($payments); ?>
                </p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-receipt text-3xl text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Promedio por Pago</p>
                <p class="text-3xl font-bold text-purple-600">
                    $<?php echo count($payments) > 0 ? number_format($totalRevenue / count($payments), 2) : '0.00'; ?>
                </p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <i class="fas fa-chart-line text-3xl text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas por Método de Pago -->
<?php if (!empty($paymentStats)): ?>
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-credit-card mr-2"></i>Desglose por Método de Pago
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php foreach ($paymentStats as $stat): ?>
            <?php 
            $methodLabels = [
                'cash' => ['nombre' => 'Efectivo', 'icon' => 'money-bill-wave', 'color' => 'green'],
                'card' => ['nombre' => 'Tarjeta', 'icon' => 'credit-card', 'color' => 'blue'],
                'transfer' => ['nombre' => 'Transferencia', 'icon' => 'exchange-alt', 'color' => 'purple']
            ];
            $method = $methodLabels[$stat['payment_method']] ?? ['nombre' => $stat['payment_method'], 'icon' => 'money', 'color' => 'gray'];
            ?>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i class="fas fa-<?php echo $method['icon']; ?> text-<?php echo $method['color']; ?>-600 mr-2"></i>
                    <span class="font-medium text-gray-700"><?php echo $method['nombre']; ?></span>
                </div>
                <p class="text-2xl font-bold text-<?php echo $method['color']; ?>-600">
                    $<?php echo number_format($stat['total'], 2); ?>
                </p>
                <p class="text-sm text-gray-500">
                    <?php echo $stat['count']; ?> pago(s)
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Listado de Pagos -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-list mr-2"></i>Detalle de Pagos del Período
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recibo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Placa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Propietario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron pagos en este período</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php echo date('d/m/Y H:i', strtotime($payment['payment_date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="<?php echo BASE_URL; ?>/payments/details/<?php echo $payment['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    <?php echo htmlspecialchars($payment['receipt_number']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($payment['folio']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($payment['plate']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php echo htmlspecialchars($payment['owner_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php 
                                $methodLabels = [
                                    'cash' => 'Efectivo',
                                    'card' => 'Tarjeta',
                                    'transfer' => 'Transferencia'
                                ];
                                echo $methodLabels[$payment['payment_method']] ?? $payment['payment_method'];
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                $<?php echo number_format($payment['amount'], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Botones de Acción -->
<div class="mt-6 flex gap-4">
    <a href="<?php echo BASE_URL; ?>/reports/exportCSV?type=payments&date_from=<?php echo $dateFrom; ?>&date_to=<?php echo $dateTo; ?>" 
       class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition shadow-lg">
        <i class="fas fa-download mr-2"></i>Exportar a CSV
    </a>
    <a href="<?php echo BASE_URL; ?>/reports" 
       class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
        <i class="fas fa-arrow-left mr-2"></i>Volver a Reportes
    </a>
</div>
