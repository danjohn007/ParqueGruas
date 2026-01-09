<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Sistema de Gestión'; ?> - Parque de Grúas</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- ApexCharts (alternativa elegante) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    
    <style>
        /* Estilos personalizados minimalistas */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .sidebar-link {
            transition: all 0.3s ease;
        }
        
        .sidebar-link:hover {
            background-color: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
        }
        
        .sidebar-link.active {
            background-color: rgba(59, 130, 246, 0.15);
            border-left: 4px solid #3b82f6;
            font-weight: 600;
        }
        
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-50">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-center h-20 border-b border-gray-200">
                <h1 class="text-xl font-bold text-blue-600">
                    <i class="fas fa-truck-pickup mr-2"></i>
                    Parque de Grúas
                </h1>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <a href="<?php echo BASE_URL; ?>/dashboard" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home w-6"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/impounds" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-warehouse w-6"></i>
                    <span class="ml-3">Corralón</span>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/vehicles" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-car w-6"></i>
                    <span class="ml-3">Vehículos</span>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/cranes" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-truck-pickup w-6"></i>
                    <span class="ml-3">Grúas</span>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/payments" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-money-bill-wave w-6"></i>
                    <span class="ml-3">Pagos</span>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/reports" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-chart-bar w-6"></i>
                    <span class="ml-3">Reportes</span>
                </a>
                
                <a href="<?php echo BASE_URL; ?>/calendar" 
                   class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:text-blue-600">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span class="ml-3">Calendario</span>
                </a>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                        Administración
                    </p>
                    
                    <a href="<?php echo BASE_URL; ?>/users" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:text-blue-600">
                        <i class="fas fa-users w-6"></i>
                        <span class="ml-3">Usuarios</span>
                    </a>
                    
                    <a href="<?php echo BASE_URL; ?>/settings" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:text-blue-600">
                        <i class="fas fa-cog w-6"></i>
                        <span class="ml-3">Configuración</span>
                    </a>
                    
                    <a href="<?php echo BASE_URL; ?>/api" 
                       class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:text-blue-600">
                        <i class="fas fa-plug w-6"></i>
                        <span class="ml-3">API HikVision</span>
                    </a>
                </div>
                <?php endif; ?>
            </nav>
            
            <!-- User Info -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                            <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-700">
                            <?php echo $_SESSION['full_name'] ?? 'Usuario'; ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?php echo ucfirst($_SESSION['role'] ?? 'user'); ?>
                        </p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/auth/logout" 
                       class="text-gray-400 hover:text-red-500" 
                       title="Cerrar sesión">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm h-20 flex items-center justify-between px-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">
                    <?php echo $title ?? 'Dashboard'; ?>
                </h2>
                <?php if (isset($subtitle)): ?>
                    <p class="text-sm text-gray-500 mt-1"><?php echo $subtitle; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    <i class="far fa-clock mr-2"></i>
                    <?php echo date('d/m/Y H:i'); ?>
                </div>
            </div>
        </header>
        
        <!-- Content Area -->
        <main class="p-8">
            <!-- Mensajes Flash -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <span><?php echo $_SESSION['success']; ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <span><?php echo $_SESSION['error']; ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['warning'])): ?>
                <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3"></i>
                        <span><?php echo $_SESSION['warning']; ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['warning']); ?>
            <?php endif; ?>
            
            <!-- Page Content -->
            <?php echo $content ?? ''; ?>
        </main>
    </div>
    
    <script>
        // Marcar link activo en la navegación
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const links = document.querySelectorAll('.sidebar-link');
            
            links.forEach(link => {
                const href = link.getAttribute('href');
                if (currentPath.includes(href.split('/').pop())) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
