<!-- Dashboard principal con estadísticas y gráficas -->

<!-- Tarjetas de estadísticas principales -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Vehículos en Corralón -->
    <div class="card bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Vehículos en Corralón</p>
                <p class="text-3xl font-bold mt-2"><?php echo $stats['impounded_now']; ?></p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <i class="fas fa-warehouse text-3xl"></i>
            </div>
        </div>
        <div class="mt-4 text-sm">
            <span class="text-blue-100">Total registros: <?php echo $stats['total_impounds']; ?></span>
        </div>
    </div>
    
    <!-- Pendientes de Pago -->
    <div class="card bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm font-medium">Pendientes de Pago</p>
                <p class="text-3xl font-bold mt-2"><?php echo $stats['pending_payment']; ?></p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <i class="fas fa-exclamation-triangle text-3xl"></i>
            </div>
        </div>
        <div class="mt-4 text-sm">
            <span class="text-yellow-100">Requieren atención</span>
        </div>
    </div>
    
    <!-- Grúas Disponibles -->
    <div class="card bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Grúas Disponibles</p>
                <p class="text-3xl font-bold mt-2"><?php echo $stats['available_cranes']; ?></p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <i class="fas fa-truck-pickup text-3xl"></i>
            </div>
        </div>
        <div class="mt-4 text-sm">
            <span class="text-green-100">Total: <?php echo $stats['total_cranes']; ?> grúas</span>
        </div>
    </div>
    
    <!-- Ingresos del Mes -->
    <div class="card bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Ingresos del Mes</p>
                <p class="text-3xl font-bold mt-2">$<?php echo number_format($monthRevenue, 2); ?></p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-4">
                <i class="fas fa-money-bill-wave text-3xl"></i>
            </div>
        </div>
        <div class="mt-4 text-sm">
            <span class="text-purple-100">Hoy: $<?php echo number_format($todayRevenue, 2); ?></span>
        </div>
    </div>
</div>

<!-- Gráficas y tablas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Gráfica de Ingresos Semanales -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-blue-500 mr-2"></i>
            Ingresos de la Última Semana
        </h3>
        <div style="position: relative; height: 250px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    
    <!-- Gráfica de Estado de Grúas -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-pie text-green-500 mr-2"></i>
            Estado de Grúas
        </h3>
        <div style="position: relative; height: 250px;">
            <canvas id="cranesChart"></canvas>
        </div>
    </div>
</div>

<!-- Alertas y Notificaciones -->
<?php if (!empty($maintenanceDue)): ?>
<div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg shadow mb-8">
    <h3 class="text-lg font-semibold text-yellow-800 mb-3">
        <i class="fas fa-tools mr-2"></i>
        Grúas con Mantenimiento Próximo
    </h3>
    <div class="space-y-2">
        <?php foreach ($maintenanceDue as $crane): ?>
            <div class="flex items-center justify-between bg-white p-3 rounded">
                <div>
                    <span class="font-medium text-gray-800"><?php echo $crane['crane_number']; ?></span>
                    <span class="text-gray-600 ml-2">(<?php echo $crane['brand'] . ' ' . $crane['model']; ?>)</span>
                </div>
                <div class="text-sm text-gray-600">
                    Próximo: <?php echo date('d/m/Y', strtotime($crane['next_maintenance'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Tablas de datos recientes -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Últimos Ingresos al Corralón -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-list text-blue-500 mr-2"></i>
                Últimos Ingresos al Corralón
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehículo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($recentImpounds as $impound): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                <a href="<?php echo BASE_URL; ?>/impounds/details/<?php echo $impound['id']; ?>">
                                    <?php echo $impound['folio']; ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo $impound['plate']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php echo date('d/m/Y', strtotime($impound['impound_date'])); ?>
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
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <a href="<?php echo BASE_URL; ?>/impounds" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Ver todos los registros <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <!-- Últimos Pagos -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-receipt text-green-500 mr-2"></i>
                Últimos Pagos Realizados
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recibo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehículo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($recentPayments as $payment): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                                <?php echo $payment['receipt_number']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo $payment['plate']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                $<?php echo number_format($payment['amount'], 2); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php 
                                $methods = [
                                    'cash' => 'Efectivo',
                                    'card' => 'Tarjeta',
                                    'transfer' => 'Transferencia',
                                    'check' => 'Cheque'
                                ];
                                echo $methods[$payment['payment_method']] ?? $payment['payment_method'];
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <a href="<?php echo BASE_URL; ?>/payments" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Ver todos los pagos <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>

<script>
// Gráfica de ingresos semanales
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($weekRevenue, 'label')); ?>,
        datasets: [{
            label: 'Ingresos ($)',
            data: <?php echo json_encode(array_column($weekRevenue, 'revenue')); ?>,
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 2,
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Gráfica de estado de grúas
const cranesCtx = document.getElementById('cranesChart').getContext('2d');
const cranesData = <?php echo json_encode($cranesByStatus); ?>;
const cranesChart = new Chart(cranesCtx, {
    type: 'doughnut',
    data: {
        labels: cranesData.map(item => {
            const labels = {
                'available': 'Disponibles',
                'in_service': 'En servicio',
                'maintenance': 'Mantenimiento',
                'inactive': 'Inactivas'
            };
            return labels[item.status] || item.status;
        }),
        datasets: [{
            data: cranesData.map(item => item.count),
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(251, 146, 60, 0.8)',
                'rgba(156, 163, 175, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
