<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago - <?php echo htmlspecialchars($payment['receipt_number']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: white;
        }
        
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header .receipt-number {
            font-size: 20px;
            color: #0066cc;
            font-weight: bold;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-section h3 {
            font-size: 14px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            padding: 5px 0;
        }
        
        .info-row .label {
            width: 150px;
            font-weight: bold;
        }
        
        .info-row .value {
            flex: 1;
        }
        
        .amount-section {
            background: #f0f0f0;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        
        .amount-section .amount {
            font-size: 32px;
            font-weight: bold;
            color: #2d5f2e;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #333;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-around;
            margin-top: 60px;
        }
        
        .signature {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>RECIBO DE PAGO - CORRALÓN VEHICULAR</h1>
            <div class="receipt-number"><?php echo htmlspecialchars($payment['receipt_number']); ?></div>
            <p style="margin-top: 10px; font-size: 12px;">
                Fecha: <?php echo date('d/m/Y H:i', strtotime($payment['payment_date'])); ?>
            </p>
        </div>
        
        <div class="info-section">
            <h3>Información del Vehículo</h3>
            <div class="info-row">
                <div class="label">Folio:</div>
                <div class="value"><?php echo htmlspecialchars($payment['folio']); ?></div>
            </div>
            <div class="info-row">
                <div class="label">Placa:</div>
                <div class="value"><?php echo htmlspecialchars($payment['plate']); ?></div>
            </div>
            <div class="info-row">
                <div class="label">Vehículo:</div>
                <div class="value"><?php echo htmlspecialchars($payment['brand'] . ' ' . $payment['model'] . ' ' . ($payment['year'] ?? '')); ?></div>
            </div>
            <div class="info-row">
                <div class="label">Color:</div>
                <div class="value"><?php echo htmlspecialchars($payment['color'] ?? ''); ?></div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>Información del Propietario</h3>
            <div class="info-row">
                <div class="label">Nombre:</div>
                <div class="value"><?php echo htmlspecialchars($payment['owner_name']); ?></div>
            </div>
            <?php if (!empty($payment['owner_phone'])): ?>
            <div class="info-row">
                <div class="label">Teléfono:</div>
                <div class="value"><?php echo htmlspecialchars($payment['owner_phone']); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="amount-section">
            <div style="font-size: 16px; margin-bottom: 10px;">MONTO TOTAL PAGADO</div>
            <div class="amount">$<?php echo number_format($payment['amount'], 2); ?></div>
        </div>
        
        <div class="info-section">
            <h3>Detalles del Pago</h3>
            <div class="info-row">
                <div class="label">Método de Pago:</div>
                <div class="value">
                    <?php 
                    $methods = [
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        'check' => 'Cheque'
                    ];
                    echo $methods[$payment['payment_method']] ?? $payment['payment_method'];
                    ?>
                </div>
            </div>
            <?php if (!empty($payment['cashier_name'])): ?>
            <div class="info-row">
                <div class="label">Cajero:</div>
                <div class="value"><?php echo htmlspecialchars($payment['cashier_name']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($payment['notes'])): ?>
            <div class="info-row">
                <div class="label">Observaciones:</div>
                <div class="value"><?php echo nl2br(htmlspecialchars($payment['notes'])); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="signature-section">
            <div class="signature">
                <div class="signature-line"></div>
                <p>Firma del Cajero</p>
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <p>Firma de Recibido</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Este documento es un comprobante de pago oficial.</p>
            <p>Conserve este recibo para cualquier aclaración.</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            Imprimir Recibo
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">
            Cerrar
        </button>
    </div>
</body>
</html>
