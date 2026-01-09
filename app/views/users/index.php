<!-- Gestión de usuarios -->

<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="<?php echo BASE_URL; ?>/users/create" 
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow-lg">
            <i class="fas fa-plus mr-2"></i>Nuevo Usuario
        </a>
    </div>
</div>

<!-- Tabla de usuarios -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Completo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="<?php echo BASE_URL; ?>/users/details/<?php echo $user['id']; ?>" 
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $roleColors = [
                                'admin' => 'bg-purple-100 text-purple-800',
                                'operator' => 'bg-blue-100 text-blue-800',
                                'viewer' => 'bg-gray-100 text-gray-800'
                            ];
                            $roleLabels = [
                                'admin' => 'Administrador',
                                'operator' => 'Operador',
                                'viewer' => 'Visualizador'
                            ];
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $roleColors[$user['role']]; ?>">
                                <?php echo $roleLabels[$user['role']]; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800',
                                'inactive' => 'bg-red-100 text-red-800'
                            ];
                            $statusLabels = [
                                'active' => 'Activo',
                                'inactive' => 'Inactivo'
                            ];
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusColors[$user['status']]; ?>">
                                <?php echo $statusLabels[$user['status']]; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?php echo BASE_URL; ?>/users/details/<?php echo $user['id']; ?>" 
                               class="text-blue-600 hover:text-blue-900 mr-3" 
                               title="Ver perfil">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>/users/edit/<?php echo $user['id']; ?>" 
                               class="text-green-600 hover:text-green-900 mr-3" 
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="<?php echo BASE_URL; ?>/users/delete/<?php echo $user['id']; ?>" 
                                   class="text-red-600 hover:text-red-900" 
                                   title="Eliminar"
                                   onclick="return confirm('¿Está seguro de eliminar este usuario?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
        <p class="text-sm text-gray-600">
            Total: <strong><?php echo count($users); ?></strong> usuario(s)
        </p>
    </div>
</div>
