<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Login'; ?> - Parque de Grúas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-500 to-blue-700 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <!-- Logo y Título -->
        <div class="text-center mb-8">
            <div class="inline-block bg-white rounded-full p-6 shadow-lg mb-4">
                <i class="fas fa-truck-pickup text-5xl text-blue-600"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Parque de Grúas</h1>
            <p class="text-blue-100">Sistema Integral de Gestión</p>
        </div>
        
        <!-- Card de Login -->
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Iniciar Sesión</h2>
            
            <!-- Mensajes de Error -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <span><?php echo $_SESSION['error']; ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <span><?php echo $_SESSION['success']; ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <!-- Formulario -->
            <form action="<?php echo BASE_URL; ?>/auth/authenticate" method="POST">
                <div class="mb-6">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Usuario
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ingrese su usuario"
                           autofocus>
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Contraseña
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ingrese su contraseña">
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                </button>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center text-white text-sm">
            <p>&copy; <?php echo date('Y'); ?> Parque de Grúas. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
