<!-- Formulario de edición de usuario -->

<div class="bg-white rounded-lg shadow-lg p-6">
    <form method="POST" action="<?php echo BASE_URL; ?>/users/update/<?php echo $user['id']; ?>" class="space-y-6">
        <!-- Información de Acceso -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user-lock mr-2"></i>Información de Acceso
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Usuario *</label>
                    <input type="text" name="username" required
                           value="<?php echo htmlspecialchars($user['username']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nombre de usuario">
                    <p class="text-xs text-gray-500 mt-1">Cambiar el nombre de usuario puede afectar el acceso al sistema</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                    <input type="password" name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Dejar en blanco para mantener la actual">
                    <p class="text-xs text-gray-500 mt-1">Solo completar si desea cambiar la contraseña</p>
                </div>
            </div>
        </div>

        <!-- Información Personal -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user mr-2"></i>Información Personal
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                    <input type="text" name="full_name" required
                           value="<?php echo htmlspecialchars($user['full_name']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nombre completo del usuario">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" required
                           value="<?php echo htmlspecialchars($user['email']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="correo@ejemplo.com">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                    <input type="text" name="phone"
                           value="<?php echo htmlspecialchars($user['phone']); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="(442) 123-4567">
                </div>
            </div>
        </div>

        <!-- Rol y Estado (solo para admin) -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-user-shield mr-2"></i>Rol y Estado
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rol *</label>
                    <select name="role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="operator" <?php echo $user['role'] === 'operator' ? 'selected' : ''; ?>>Operador</option>
                        <option value="viewer" <?php echo $user['role'] === 'viewer' ? 'selected' : ''; ?>>Visualizador</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <strong>Administrador:</strong> Acceso completo<br>
                        <strong>Operador:</strong> Gestión de operaciones diarias<br>
                        <strong>Visualizador:</strong> Solo consulta
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Los usuarios inactivos no pueden acceder al sistema</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Información de registro -->
        <div class="border-t pt-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Información de Registro</h4>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><strong>Creado:</strong> <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></p>
                    <?php if ($user['updated_at'] !== $user['created_at']): ?>
                    <p><strong>Última actualización:</strong> <?php echo date('d/m/Y H:i', strtotime($user['updated_at'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex gap-4 pt-4">
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
                <i class="fas fa-save mr-2"></i>Guardar Cambios
            </button>
            <a href="<?php echo BASE_URL; ?>/users/details/<?php echo $user['id']; ?>" 
               class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
            
            <?php if ($_SESSION['role'] === 'admin' && $_SESSION['user_id'] != $user['id']): ?>
            <a href="<?php echo BASE_URL; ?>/users/delete/<?php echo $user['id']; ?>" 
               class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition shadow-lg ml-auto"
               onclick="return confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.');">
                <i class="fas fa-trash mr-2"></i>Eliminar Usuario
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>
