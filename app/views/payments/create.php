<!-- Formulario de registro de pago -->

<div class="bg-white rounded-lg shadow-lg p-6">
    <form method="POST" action="<?php echo BASE_URL; ?>/payments/store" class="space-y-6" id="paymentForm">
        <!-- Siguiente recibo -->
        <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-green-900 mb-2">
                <i class="fas fa-receipt mr-2"></i>Número de Recibo
            </h3>
            <p class="text-2xl font-bold text-green-700"><?php echo htmlspecialchars($nextReceipt); ?></p>
        </div>

        <!-- Selección de Registro a Pagar -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-file-invoice-dollar mr-2"></i>Registro a Pagar
            </h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Seleccionar Folio *
                </label>
                <select id="impound_id" name="impound_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        onchange="updatePaymentDetails()">
                    <option value="">-- Seleccione un folio --</option>
                    <?php foreach ($unpaidImpounds as $unpaidImpound): ?>
                        <option value="<?php echo $unpaidImpound['id']; ?>"
                                data-folio="<?php echo htmlspecialchars($unpaidImpound['folio']); ?>"
                                data-plate="<?php echo htmlspecialchars($unpaidImpound['plate']); ?>"
                                data-owner="<?php echo htmlspecialchars($unpaidImpound['owner_name']); ?>"
                                data-amount="<?php echo $unpaidImpound['total_amount']; ?>"
                                <?php echo ($impound && $impound['id'] == $unpaidImpound['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($unpaidImpound['folio']); ?> - 
                            <?php echo htmlspecialchars($unpaidImpound['plate']); ?> - 
                            <?php echo htmlspecialchars($unpaidImpound['owner_name']); ?> - 
                            $<?php echo number_format($unpaidImpound['total_amount'], 2); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="impound-details" class="bg-gray-50 p-4 rounded-lg hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Folio:</span>
                        <span id="detail-folio" class="font-semibold block"></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Placa:</span>
                        <span id="detail-plate" class="font-semibold block"></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Propietario:</span>
                        <span id="detail-owner" class="font-semibold block"></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Monto Total:</span>
                        <span id="detail-amount" class="font-semibold block text-green-600"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Pago -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-dollar-sign mr-2"></i>Detalles del Pago
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto a Pagar *</label>
                    <input type="number" name="amount" id="amount" required step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="0.00"
                           value="<?php echo $impound ? $impound['total_amount'] : ''; ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago *</label>
                    <select name="payment_method" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Efectivo</option>
                        <option value="card">Tarjeta</option>
                        <option value="transfer">Transferencia</option>
                        <option value="check">Cheque</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Cajero *</label>
                    <input type="text" name="cashier_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nombre completo del cajero">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas / Observaciones</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Observaciones adicionales sobre el pago"></textarea>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex gap-4 pt-4">
            <button type="submit" 
                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition shadow-lg">
                <i class="fas fa-check-circle mr-2"></i>Registrar Pago
            </button>
            <a href="<?php echo BASE_URL; ?>/payments" 
               class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
        </div>
    </form>
</div>

<script>
function updatePaymentDetails() {
    const select = document.getElementById('impound_id');
    const option = select.options[select.selectedIndex];
    const detailsDiv = document.getElementById('impound-details');
    const amountInput = document.getElementById('amount');
    
    if (option.value) {
        document.getElementById('detail-folio').textContent = option.dataset.folio || '';
        document.getElementById('detail-plate').textContent = option.dataset.plate || '';
        document.getElementById('detail-owner').textContent = option.dataset.owner || '';
        document.getElementById('detail-amount').textContent = '$' + parseFloat(option.dataset.amount || 0).toFixed(2);
        amountInput.value = option.dataset.amount || '';
        detailsDiv.classList.remove('hidden');
    } else {
        detailsDiv.classList.add('hidden');
        amountInput.value = '';
    }
}

// Si hay un impound preseleccionado, mostrar detalles
<?php if ($impound): ?>
    updatePaymentDetails();
<?php endif; ?>
</script>
