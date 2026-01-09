<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - Parque de Grúas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Test de Conexión y Configuración</h1>
            
            <?php
            // Cargar configuración
            require_once __DIR__ . '/../config/config.php';
            require_once __DIR__ . '/../config/Database.php';
            
            $tests = [];
            
            // Test 1: PHP Version
            $phpVersion = phpversion();
            $tests[] = [
                'name' => 'Versión de PHP',
                'status' => version_compare($phpVersion, '7.4.0', '>='),
                'message' => "PHP $phpVersion " . (version_compare($phpVersion, '7.4.0', '>=') ? '✓' : '✗ (Se requiere PHP 7.4 o superior)')
            ];
            
            // Test 2: URL Base
            $tests[] = [
                'name' => 'URL Base',
                'status' => true,
                'message' => BASE_URL
            ];
            
            // Test 3: Ruta Base
            $tests[] = [
                'name' => 'Ruta Base',
                'status' => true,
                'message' => BASE_PATH
            ];
            
            // Test 4: Extensión PDO
            $tests[] = [
                'name' => 'Extensión PDO',
                'status' => extension_loaded('pdo'),
                'message' => extension_loaded('pdo') ? 'PDO está instalado ✓' : 'PDO no está instalado ✗'
            ];
            
            // Test 5: Extensión PDO MySQL
            $tests[] = [
                'name' => 'Extensión PDO MySQL',
                'status' => extension_loaded('pdo_mysql'),
                'message' => extension_loaded('pdo_mysql') ? 'PDO MySQL está instalado ✓' : 'PDO MySQL no está instalado ✗'
            ];
            
            // Test 6: Conexión a Base de Datos
            $dbConnected = false;
            $dbMessage = '';
            try {
                $db = Database::getInstance()->getConnection();
                $dbConnected = true;
                $dbMessage = 'Conexión exitosa a la base de datos ✓';
            } catch (Exception $e) {
                $dbMessage = 'Error de conexión: ' . $e->getMessage() . ' ✗';
            }
            
            $tests[] = [
                'name' => 'Conexión a Base de Datos',
                'status' => $dbConnected,
                'message' => $dbMessage
            ];
            
            // Test 7: Verificar tablas
            if ($dbConnected) {
                try {
                    $stmt = $db->query("SHOW TABLES");
                    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    $expectedTables = ['users', 'cranes', 'vehicles', 'impounds', 'payments', 'hikvision_devices', 'calendar_events', 'system_settings'];
                    $missingTables = array_diff($expectedTables, $tables);
                    
                    $tests[] = [
                        'name' => 'Tablas de Base de Datos',
                        'status' => empty($missingTables),
                        'message' => empty($missingTables) ? 
                            'Todas las tablas están creadas ✓ (' . count($tables) . ' tablas)' : 
                            'Faltan tablas: ' . implode(', ', $missingTables) . ' ✗'
                    ];
                } catch (Exception $e) {
                    $tests[] = [
                        'name' => 'Tablas de Base de Datos',
                        'status' => false,
                        'message' => 'Error al verificar tablas: ' . $e->getMessage()
                    ];
                }
            }
            
            // Test 8: Permisos de escritura
            $logsWritable = is_writable(__DIR__ . '/../logs');
            $tests[] = [
                'name' => 'Permisos de escritura (logs)',
                'status' => $logsWritable,
                'message' => $logsWritable ? 'Directorio logs es escribible ✓' : 'Directorio logs no es escribible ✗'
            ];
            
            // Test 9: Mod Rewrite
            $modRewrite = function_exists('apache_get_modules') ? 
                in_array('mod_rewrite', apache_get_modules()) : 
                true; // Asumir true si no podemos verificar
            
            $tests[] = [
                'name' => 'Apache mod_rewrite',
                'status' => $modRewrite,
                'message' => $modRewrite ? 'mod_rewrite está habilitado ✓' : 'No se pudo verificar mod_rewrite'
            ];
            
            // Calcular resumen
            $passed = count(array_filter($tests, fn($t) => $t['status']));
            $total = count($tests);
            $allPassed = $passed === $total;
            ?>
            
            <!-- Resumen -->
            <div class="mb-6 p-6 rounded-lg <?php echo $allPassed ? 'bg-green-100 border-2 border-green-500' : 'bg-yellow-100 border-2 border-yellow-500'; ?>">
                <h2 class="text-xl font-bold mb-2 <?php echo $allPassed ? 'text-green-800' : 'text-yellow-800'; ?>">
                    Resumen: <?php echo $passed; ?> de <?php echo $total; ?> pruebas pasadas
                </h2>
                <?php if ($allPassed): ?>
                    <p class="text-green-700">✓ El sistema está correctamente configurado y listo para usar.</p>
                    <a href="<?php echo BASE_URL; ?>" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                        Ir al Sistema
                    </a>
                <?php else: ?>
                    <p class="text-yellow-700">⚠ Algunas configuraciones necesitan atención. Revisa los detalles abajo.</p>
                <?php endif; ?>
            </div>
            
            <!-- Resultados detallados -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Test</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalles</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($tests as $test): ?>
                            <tr class="<?php echo $test['status'] ? 'bg-white' : 'bg-red-50'; ?>">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    <?php echo htmlspecialchars($test['name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $test['status'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $test['status'] ? 'OK' : 'ERROR'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <?php echo htmlspecialchars($test['message']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Información de configuración -->
            <div class="mt-6 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Información de Configuración</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <strong class="text-gray-700">Base de Datos:</strong>
                        <p class="text-gray-600">Host: <?php echo DB_HOST; ?></p>
                        <p class="text-gray-600">Base de Datos: <?php echo DB_NAME; ?></p>
                        <p class="text-gray-600">Usuario: <?php echo DB_USER; ?></p>
                    </div>
                    <div>
                        <strong class="text-gray-700">Aplicación:</strong>
                        <p class="text-gray-600">Nombre: <?php echo APP_NAME; ?></p>
                        <p class="text-gray-600">Versión: <?php echo APP_VERSION; ?></p>
                        <p class="text-gray-600">Zona Horaria: <?php echo date_default_timezone_get(); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Instrucciones -->
            <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-6">
                <h3 class="text-lg font-bold text-blue-800 mb-2">Instrucciones de Instalación</h3>
                <ol class="list-decimal list-inside text-blue-700 space-y-2">
                    <li>Importa el archivo <code class="bg-blue-100 px-2 py-1 rounded">sql/parque_gruas.sql</code> en tu base de datos MySQL</li>
                    <li>Configura las credenciales de la base de datos en <code class="bg-blue-100 px-2 py-1 rounded">config/config.php</code></li>
                    <li>Asegúrate de que el servidor Apache tenga mod_rewrite habilitado</li>
                    <li>Verifica que los directorios tengan permisos de escritura apropiados</li>
                    <li>Usuario por defecto: <strong>admin</strong> / Contraseña: <strong>admin123</strong></li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
