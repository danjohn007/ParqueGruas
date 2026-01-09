<!-- Configuración del Sistema -->

<div class="space-y-6">
    <form method="POST" action="<?php echo BASE_URL; ?>/settings/update" class="space-y-6">
        
        <!-- Nombre del Sitio y Logotipo -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-globe mr-3 text-blue-600"></i>
                Nombre del Sitio y Logotipo
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Sitio</label>
                    <input type="text" name="site_name" 
                           value="<?php echo htmlspecialchars($settings['site_name'] ?? 'Parque de Grúas'); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nombre de su organización">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL del Logotipo</label>
                    <input type="text" name="site_logo" 
                           value="<?php echo htmlspecialchars($settings['site_logo'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="https://ejemplo.com/logo.png">
                    <p class="text-xs text-gray-500 mt-1">URL completa de la imagen del logotipo</p>
                </div>
            </div>
        </div>

        <!-- Configuración de Correo -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-envelope mr-3 text-blue-600"></i>
                Configuración del Correo Electrónico
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Principal</label>
                    <input type="email" name="email_from" 
                           value="<?php echo htmlspecialchars($settings['email_from'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="admin@parquegruas.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Remitente</label>
                    <input type="text" name="email_from_name" 
                           value="<?php echo htmlspecialchars($settings['email_from_name'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Parque de Grúas">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Servidor SMTP</label>
                    <input type="text" name="smtp_host" 
                           value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="smtp.gmail.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Puerto SMTP</label>
                    <input type="number" name="smtp_port" 
                           value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="587">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Usuario SMTP</label>
                    <input type="text" name="smtp_username" 
                           value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="usuario@gmail.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña SMTP</label>
                    <input type="password" name="smtp_password" 
                           value="<?php echo htmlspecialchars($settings['smtp_password'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="••••••••">
                </div>
            </div>
        </div>

        <!-- Teléfonos de Contacto y Horarios -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-phone mr-3 text-blue-600"></i>
                Teléfonos de Contacto y Horarios
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono Principal</label>
                    <input type="text" name="contact_phone_1" 
                           value="<?php echo htmlspecialchars($settings['contact_phone_1'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="(442) 123-4567">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono Secundario</label>
                    <input type="text" name="contact_phone_2" 
                           value="<?php echo htmlspecialchars($settings['contact_phone_2'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="(442) 765-4321">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Horarios de Atención</label>
                    <textarea name="business_hours" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Lunes a Viernes: 8:00 AM - 6:00 PM&#10;Sábados: 9:00 AM - 2:00 PM"><?php echo htmlspecialchars($settings['business_hours'] ?? ''); ?></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                    <input type="text" name="contact_address" 
                           value="<?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Av. Constituyentes 1000, Querétaro, Qro.">
                </div>
            </div>
        </div>

        <!-- Estilos Principales de Color -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-palette mr-3 text-blue-600"></i>
                Estilos Principales de Color
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color Primario</label>
                    <div class="flex gap-2">
                        <input type="color" name="primary_color" 
                               value="<?php echo htmlspecialchars($settings['primary_color'] ?? '#3b82f6'); ?>"
                               class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                        <input type="text" 
                               value="<?php echo htmlspecialchars($settings['primary_color'] ?? '#3b82f6'); ?>"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               readonly>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Color principal del sistema</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color Secundario</label>
                    <div class="flex gap-2">
                        <input type="color" name="secondary_color" 
                               value="<?php echo htmlspecialchars($settings['secondary_color'] ?? '#1e40af'); ?>"
                               class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                        <input type="text" 
                               value="<?php echo htmlspecialchars($settings['secondary_color'] ?? '#1e40af'); ?>"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               readonly>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Color secundario del sistema</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color de Acento</label>
                    <div class="flex gap-2">
                        <input type="color" name="accent_color" 
                               value="<?php echo htmlspecialchars($settings['accent_color'] ?? '#06b6d4'); ?>"
                               class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                        <input type="text" 
                               value="<?php echo htmlspecialchars($settings['accent_color'] ?? '#06b6d4'); ?>"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               readonly>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Color de acento del sistema</p>
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Los cambios de color requieren recargar la página para verse reflejados en toda la interfaz.
                </p>
            </div>
        </div>

        <!-- Configuración de PayPal -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fab fa-paypal mr-3 text-blue-600"></i>
                Configuración de PayPal
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modo de PayPal</label>
                    <select name="paypal_mode" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="sandbox" <?php echo ($settings['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : ''; ?>>Sandbox (Pruebas)</option>
                        <option value="live" <?php echo ($settings['paypal_mode'] ?? 'sandbox') === 'live' ? 'selected' : ''; ?>>Live (Producción)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                    <input type="text" name="paypal_client_id" 
                           value="<?php echo htmlspecialchars($settings['paypal_client_id'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Client ID de PayPal">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                    <input type="password" name="paypal_secret" 
                           value="<?php echo htmlspecialchars($settings['paypal_secret'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Secret Key de PayPal">
                </div>
            </div>
        </div>

        <!-- API para QR Masivos -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-qrcode mr-3 text-blue-600"></i>
                API para Generar QR Masivos
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor de API</label>
                    <input type="text" name="qr_api_provider" 
                           value="<?php echo htmlspecialchars($settings['qr_api_provider'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="QR Code Generator API, GoQR, etc.">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                    <input type="text" name="qr_api_key" 
                           value="<?php echo htmlspecialchars($settings['qr_api_key'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="API Key proporcionada por el proveedor">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Endpoint de API</label>
                    <input type="text" name="qr_api_endpoint" 
                           value="<?php echo htmlspecialchars($settings['qr_api_endpoint'] ?? ''); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="https://api.qrserver.com/v1/create-qr-code/">
                </div>
            </div>
        </div>

        <!-- Configuraciones Globales -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-cog mr-3 text-blue-600"></i>
                Configuraciones Globales Recomendadas
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Costo de Grúa Base (MXN)</label>
                    <input type="number" name="base_tow_cost" step="0.01"
                           value="<?php echo htmlspecialchars($settings['base_tow_cost'] ?? '800.00'); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="800.00">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Costo de Almacenaje por Día (MXN)</label>
                    <input type="number" name="storage_cost_per_day" step="0.01"
                           value="<?php echo htmlspecialchars($settings['storage_cost_per_day'] ?? '100.00'); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="100.00">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Zona Horaria</label>
                    <select name="timezone" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="America/Mexico_City" <?php echo ($settings['timezone'] ?? 'America/Mexico_City') === 'America/Mexico_City' ? 'selected' : ''; ?>>América/Ciudad de México</option>
                        <option value="America/Tijuana" <?php echo ($settings['timezone'] ?? '') === 'America/Tijuana' ? 'selected' : ''; ?>>América/Tijuana</option>
                        <option value="America/Monterrey" <?php echo ($settings['timezone'] ?? '') === 'America/Monterrey' ? 'selected' : ''; ?>>América/Monterrey</option>
                        <option value="America/Cancun" <?php echo ($settings['timezone'] ?? '') === 'America/Cancun' ? 'selected' : ''; ?>>América/Cancún</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Formato de Fecha</label>
                    <select name="date_format" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="d/m/Y" <?php echo ($settings['date_format'] ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                        <option value="m/d/Y" <?php echo ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                        <option value="Y-m-d" <?php echo ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Moneda</label>
                    <select name="currency" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="MXN" <?php echo ($settings['currency'] ?? 'MXN') === 'MXN' ? 'selected' : ''; ?>>MXN - Peso Mexicano</option>
                        <option value="USD" <?php echo ($settings['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD - Dólar Estadounidense</option>
                        <option value="EUR" <?php echo ($settings['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex gap-4">
                <button type="submit" 
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
                    <i class="fas fa-save mr-2"></i>Guardar Configuración
                </button>
                <a href="<?php echo BASE_URL; ?>/dashboard" 
                   class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
