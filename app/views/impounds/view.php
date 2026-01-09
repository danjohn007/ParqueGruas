<!-- Detalle de Registro de Corralón -->

<div class="space-y-6">
    <!-- Información del Folio -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="border-b pb-4 mb-4">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Registro de Corralón</h3>
                    <p class="text-xl text-blue-600 font-semibold mt-2">
                        <?php echo htmlspecialchars($impound['folio']); ?>
                    </p>
                </div>
                <div>
                    <?php if ($impound['status'] === 'impounded'): ?>
                        <span class="inline-block bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg font-semibold">
                            <i class="fas fa-warehouse mr-2"></i>EN CORRALÓN
                        </span>
                    <?php else: ?>
                        <span class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-lg font-semibold">
                            <i class="fas fa-check-circle mr-2"></i>LIBERADO
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Información del Vehículo -->
            <div>
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Información del Vehículo</h4>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Placa:</span>
                        <span class="font-semibold block text-lg">
                            <?php echo htmlspecialchars($impound['plate']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Vehículo:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['brand'] . ' ' . $impound['model']); ?>
                            <?php if (!empty($impound['year'])): ?>
                                (<?php echo htmlspecialchars($impound['year']); ?>)
                            <?php endif; ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Color:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['color'] ?: 'N/A'); ?>
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
                            echo $types[$impound['vehicle_type']] ?? $impound['vehicle_type'];
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Información del Propietario -->
            <div>
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Propietario</h4>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Nombre:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['owner_name'] ?: 'N/A'); ?>
                        </span>
                    </div>
                    <?php if (!empty($impound['owner_phone'])): ?>
                    <div>
                        <span class="text-gray-600">Teléfono:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['owner_phone']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($impound['owner_address'])): ?>
                    <div>
                        <span class="text-gray-600">Dirección:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['owner_address']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de la Infracción -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>Información de la Infracción
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Tipo de Infracción:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['infraction_type']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Ubicación:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['infraction_location']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Municipio:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['municipality']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Fecha de Ingreso:</span>
                        <span class="font-semibold block">
                            <?php echo date('d/m/Y H:i', strtotime($impound['impound_date'])); ?>
                        </span>
                    </div>
                    <?php if ($impound['status'] === 'released' && !empty($impound['release_date'])): ?>
                    <div>
                        <span class="text-gray-600">Fecha de Liberación:</span>
                        <span class="font-semibold block">
                            <?php echo date('d/m/Y H:i', strtotime($impound['release_date'])); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div>
                <div class="space-y-2">
                    <?php if (!empty($impound['officer_name'])): ?>
                    <div>
                        <span class="text-gray-600">Oficial:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['officer_name']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($impound['officer_badge'])): ?>
                    <div>
                        <span class="text-gray-600">Placa:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['officer_badge']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($impound['crane_number'])): ?>
                    <div>
                        <span class="text-gray-600">Grúa:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['crane_number']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($impound['driver_name'])): ?>
                    <div>
                        <span class="text-gray-600">Conductor de Grúa:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($impound['driver_name']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($impound['observations'])): ?>
        <div class="mt-6 pt-4 border-t">
            <h4 class="text-sm font-semibold text-gray-600 uppercase mb-2">Observaciones</h4>
            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($impound['observations'])); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Información de Costos -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-dollar-sign mr-2"></i>Costos y Estado de Pago
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Costo de Arrastre:</span>
                    <span class="font-semibold">$<?php echo number_format($impound['tow_cost'], 2); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Días de Almacenaje:</span>
                    <span class="font-semibold"><?php echo $impound['storage_days']; ?> días</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Costo por Día:</span>
                    <span class="font-semibold">$<?php echo number_format($impound['storage_cost_per_day'], 2); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal Almacenaje:</span>
                    <span class="font-semibold">$<?php echo number_format($impound['storage_days'] * $impound['storage_cost_per_day'], 2); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Multa:</span>
                    <span class="font-semibold">$<?php echo number_format($impound['fine_amount'], 2); ?></span>
                </div>
                <div class="flex justify-between pt-2 border-t-2 border-gray-300">
                    <span class="text-gray-800 font-bold text-lg">TOTAL:</span>
                    <span class="font-bold text-2xl text-green-600">$<?php echo number_format($impound['total_amount'], 2); ?></span>
                </div>
            </div>
            
            <div>
                <?php if ($impound['paid']): ?>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                            <span class="font-semibold text-green-800">PAGADO</span>
                        </div>
                        <?php if (!empty($impound['receipt_number'])): ?>
                        <div class="space-y-1 text-sm">
                            <div>
                                <span class="text-gray-600">Recibo:</span>
                                <a href="<?php echo BASE_URL; ?>/payments/details/<?php echo $impound['payment_id']; ?>"
                                   class="font-semibold block text-blue-600 hover:text-blue-800">
                                    <?php echo htmlspecialchars($impound['receipt_number']); ?>
                                </a>
                            </div>
                            <div>
                                <span class="text-gray-600">Fecha de Pago:</span>
                                <span class="font-semibold block">
                                    <?php echo date('d/m/Y H:i', strtotime($impound['payment_date'])); ?>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600">Método:</span>
                                <span class="font-semibold block">
                                    <?php 
                                    $methods = [
                                        'cash' => 'Efectivo',
                                        'card' => 'Tarjeta',
                                        'transfer' => 'Transferencia',
                                        'check' => 'Cheque'
                                    ];
                                    echo $methods[$impound['payment_method']] ?? $impound['payment_method'];
                                    ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-exclamation-circle text-red-600 text-2xl mr-3"></i>
                            <span class="font-semibold text-red-800">PENDIENTE DE PAGO</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            Este registro no ha sido pagado. El vehículo permanecerá en el corralón hasta que se registre el pago.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="flex gap-4">
        <?php if ($impound['status'] === 'impounded' && !$impound['paid']): ?>
        <a href="<?php echo BASE_URL; ?>/payments/create/<?php echo $impound['id']; ?>" 
           class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition shadow-lg">
            <i class="fas fa-dollar-sign mr-2"></i>Registrar Pago
        </a>
        <?php endif; ?>
        
        <?php if ($impound['paid'] && !empty($impound['payment_id'])): ?>
        <a href="<?php echo BASE_URL; ?>/payments/details/<?php echo $impound['payment_id']; ?>" 
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
            <i class="fas fa-receipt mr-2"></i>Ver Recibo de Pago
        </a>
        <?php endif; ?>
        
        <a href="<?php echo BASE_URL; ?>/impounds" 
           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Listado
        </a>
    </div>
</div>
