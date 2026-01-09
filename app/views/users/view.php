<!-- Detalle de Usuario -->

<div class="space-y-6">
    <!-- Información del Usuario -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="border-b pb-4 mb-4 flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">Perfil de Usuario</h3>
                <p class="text-xl text-blue-600 font-semibold mt-2">
                    <?php echo htmlspecialchars($user['full_name']); ?>
                </p>
            </div>
            <div>
                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $user['id']): ?>
                <a href="<?php echo BASE_URL; ?>/users/edit/<?php echo $user['id']; ?>" 
                   class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition inline-block">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Información de Acceso</h4>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Usuario:</span>
                        <span class="font-semibold block text-lg">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Rol:</span>
                        <span class="font-semibold block">
                            <?php 
                            $roleLabels = [
                                'admin' => 'Administrador',
                                'operator' => 'Operador',
                                'viewer' => 'Visualizador'
                            ];
                            $roleColors = [
                                'admin' => 'bg-purple-100 text-purple-800',
                                'operator' => 'bg-blue-100 text-blue-800',
                                'viewer' => 'bg-gray-100 text-gray-800'
                            ];
                            ?>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $roleColors[$user['role']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo $roleLabels[$user['role']] ?? $user['role']; ?>
                            </span>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Estado:</span>
                        <span class="font-semibold block">
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Información de Contacto</h4>
                <div class="space-y-2">
                    <div>
                        <span class="text-gray-600">Nombre Completo:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Email:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </span>
                    </div>
                    <?php if (!empty($user['phone'])): ?>
                    <div>
                        <span class="text-gray-600">Teléfono:</span>
                        <span class="font-semibold block">
                            <?php echo htmlspecialchars($user['phone']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t text-sm text-gray-500">
            <p>Registrado el: <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></p>
            <?php if ($user['updated_at'] !== $user['created_at']): ?>
            <p>Última actualización: <?php echo date('d/m/Y H:i', strtotime($user['updated_at'])); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Información del Rol -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-user-shield mr-2"></i>Permisos del Rol
        </h3>
        
        <div class="space-y-3">
            <?php if ($user['role'] === 'admin'): ?>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Acceso completo al sistema</span>
                </div>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Gestión de usuarios</span>
                </div>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Gestión de grúas</span>
                </div>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Gestión de vehículos e infracciones</span>
                </div>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Acceso a reportes y estadísticas</span>
                </div>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Configuración del sistema</span>
                </div>
            <?php elseif ($user['role'] === 'operator'): ?>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Registro y gestión de infracciones</span>
                </div>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Gestión de vehículos</span>
                </div>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Consulta de grúas</span>
                </div>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Registro de pagos</span>
                </div>
                <div class="flex items-center text-gray-400">
                    <i class="fas fa-times-circle mr-2"></i>
                    <span>Gestión de usuarios (sin permiso)</span>
                </div>
                <div class="flex items-center text-gray-400">
                    <i class="fas fa-times-circle mr-2"></i>
                    <span>Gestión de grúas (sin permiso)</span>
                </div>
            <?php else: ?>
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Consulta de información</span>
                </div>
                <div class="flex items-center text-gray-400">
                    <i class="fas fa-times-circle mr-2"></i>
                    <span>Modificación de registros (sin permiso)</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="flex gap-4">
        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] == $user['id']): ?>
        <a href="<?php echo BASE_URL; ?>/users/edit/<?php echo $user['id']; ?>" 
           class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 transition shadow-lg">
            <i class="fas fa-edit mr-2"></i>Editar Usuario
        </a>
        <?php endif; ?>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="<?php echo BASE_URL; ?>/users" 
           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Listado
        </a>
        <?php else: ?>
        <a href="<?php echo BASE_URL; ?>/dashboard" 
           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-arrow-left mr-2"></i>Volver al Dashboard
        </a>
        <?php endif; ?>
    </div>
</div>
