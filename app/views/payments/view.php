<!-- Detalle de Pago -->

<div class="space-y-6">
    <!-- Información del Recibo -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="border-b pb-4 mb-4">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Recibo de Pago</h3>
                    <p class="text-xl text-blue-600 font-semibold mt-2">
                        <?php echo htmlspecialchars($payment['receipt_number']); ?>
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-lg font-semibold">
                        <i class="fas fa-check-circle mr-2"></i>PAGADO
                    </span>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Información del Pago</h4>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Fecha de Pago:</span>
                        <span class="font-semibold block">
                            <?php echo date('d/m/Y H:i', strtotime($payment['payment_date'])); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Monto Pagado:</span>
                        <span class="font-semibold block text-2xl text-green-600">
                            $<?php echo number_format($payment['amount'], 2); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Método de Pago:</span>
                        <span class="font-semibold block">
                            <?php 
                            $methods = [
                                'cash' => 'Efectivo',
                                'card' => 'Tarjeta',
                                'transfer' => 'Transferencia',
                                'check' => 'Cheque'
                            ];
                            echo $methods[$payment['payment_method']] ?? $payment['payment_method'];
                            ?>
                        </span>
                    </div>
                    <?php if (!empty($payment['cashier_name'])): ?>
                    <div>
                        <span class="text-gray-600">Cajero:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($payment['cashier_name']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Folio de Corralón</h4>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Folio:</span>
                        <a href="<?php echo BASE_URL; ?>/impounds/details/<?php echo $payment['impound_id']; ?>"
                           class="font-semibold block text-blue-600 hover:text-blue-800">
                            <?php echo htmlspecialchars($payment['folio']); ?>
                        </a>
                    </div>
                    <div>
                        <span class="text-gray-600">Placa:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($payment['plate']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Vehículo:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($payment['brand'] . ' ' . $payment['model']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Propietario:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($payment['owner_name']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($payment['notes'])): ?>
        <div class="mt-6 pt-4 border-t">
            <h4 class="text-sm font-semibold text-gray-600 uppercase mb-2">Observaciones</h4>
            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($payment['notes'])); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Botones de Acción -->
    <div class="flex gap-4">
        <a href="<?php echo BASE_URL; ?>/payments/print/<?php echo $payment['id']; ?>" 
           target="_blank"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
            <i class="fas fa-print mr-2"></i>Imprimir Recibo
        </a>
        <a href="<?php echo BASE_URL; ?>/impounds/details/<?php echo $payment['impound_id']; ?>" 
           class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-file-alt mr-2"></i>Ver Folio Completo
        </a>
        <a href="<?php echo BASE_URL; ?>/payments" 
           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Listado
        </a>
    </div>
</div>
