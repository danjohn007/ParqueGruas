<!-- Listado de pagos -->

<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?php echo BASE_URL; ?>/payments/create" 
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
            <i class="fas fa-plus mr-2"></i>Registrar Pago
        </a>
    </div>
</div>

<!-- Tabla de pagos -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recibo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Folio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Placa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Propietario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No se encontraron pagos registrados</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="<?php echo BASE_URL; ?>/payments/details/<?php echo $payment['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    <?php echo htmlspecialchars($payment['receipt_number']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <a href="<?php echo BASE_URL; ?>/impounds/details/<?php echo $payment['impound_id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <?php echo htmlspecialchars($payment['folio']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($payment['plate']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php echo htmlspecialchars($payment['owner_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                $<?php echo number_format($payment['amount'], 2); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $methodLabels = [
                                    'cash' => 'Efectivo',
                                    'card' => 'Tarjeta',
                                    'transfer' => 'Transferencia'
                                ];
                                $methodColors = [
                                    'cash' => 'bg-green-100 text-green-800',
                                    'card' => 'bg-blue-100 text-blue-800',
                                    'transfer' => 'bg-purple-100 text-purple-800'
                                ];
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $methodColors[$payment['payment_method']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo $methodLabels[$payment['payment_method']] ?? $payment['payment_method']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php echo date('d/m/Y H:i', strtotime($payment['payment_date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo BASE_URL; ?>/payments/details/<?php echo $payment['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3" 
                                   title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo BASE_URL; ?>/payments/printReceipt/<?php echo $payment['id']; ?>" 
                                   class="text-green-600 hover:text-green-900" 
                                   title="Imprimir recibo"
                                   target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (!empty($payments)): ?>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600">
                    Total: <strong><?php echo count($payments); ?></strong> pago(s)
                </p>
                <p class="text-sm text-gray-600">
                    Total recaudado: <strong class="text-green-600">$<?php echo number_format(array_sum(array_column($payments, 'amount')), 2); ?></strong>
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>
