<!-- Vista principal de reportes -->

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Reporte de Ingresos -->
    <a href="<?php echo BASE_URL; ?>/reports/revenue" 
       class="card bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
        <div class="flex items-center mb-4">
            <div class="bg-green-100 rounded-full p-4 mr-4">
                <i class="fas fa-dollar-sign text-3xl text-green-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Reporte de Ingresos</h3>
                <p class="text-sm text-gray-600">Análisis de recaudación</p>
            </div>
        </div>
        <p class="text-gray-600 mb-4">
            Consulta los ingresos por período, métodos de pago y estadísticas financieras.
        </p>
        <div class="flex items-center text-blue-600 font-medium">
            Ver reporte <i class="fas fa-arrow-right ml-2"></i>
        </div>
    </a>
    
    <!-- Reporte de Operaciones -->
    <a href="<?php echo BASE_URL; ?>/reports/operations" 
       class="card bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
        <div class="flex items-center mb-4">
            <div class="bg-blue-100 rounded-full p-4 mr-4">
                <i class="fas fa-chart-bar text-3xl text-blue-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Reporte de Operaciones</h3>
                <p class="text-sm text-gray-600">Estadísticas operativas</p>
            </div>
        </div>
        <p class="text-gray-600 mb-4">
            Revisa las operaciones diarias, uso de grúas y estadísticas por municipio.
        </p>
        <div class="flex items-center text-blue-600 font-medium">
            Ver reporte <i class="fas fa-arrow-right ml-2"></i>
        </div>
    </a>
    
    <!-- Exportar Datos -->
    <div class="card bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center mb-4">
            <div class="bg-purple-100 rounded-full p-4 mr-4">
                <i class="fas fa-file-export text-3xl text-purple-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Exportar Datos</h3>
                <p class="text-sm text-gray-600">Descarga en CSV</p>
            </div>
        </div>
        <form method="GET" action="<?php echo BASE_URL; ?>/reports/exportCSV" class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de reporte</label>
                <select name="type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="impounds">Registros de corralón</option>
                    <option value="payments">Pagos</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                <input type="date" name="date_from" value="<?php echo date('Y-m-01'); ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                <input type="date" name="date_to" value="<?php echo date('Y-m-t'); ?>" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" 
                    class="w-full bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-download mr-2"></i>Descargar CSV
            </button>
        </form>
    </div>
</div>

<!-- Accesos rápidos -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
        Información sobre Reportes
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h4 class="font-medium text-gray-800 mb-2">Reporte de Ingresos</h4>
            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                <li>Ingresos totales por período</li>
                <li>Desglose por método de pago</li>
                <li>Gráficas de ingresos diarios</li>
                <li>Comparativas mensuales</li>
                <li>Listado detallado de pagos</li>
            </ul>
        </div>
        
        <div>
            <h4 class="font-medium text-gray-800 mb-2">Reporte de Operaciones</h4>
            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                <li>Vehículos ingresados por período</li>
                <li>Estadísticas por municipio</li>
                <li>Uso de grúas y disponibilidad</li>
                <li>Promedio de días en corralón</li>
                <li>Tipos de infracciones más comunes</li>
            </ul>
        </div>
        
        <div>
            <h4 class="font-medium text-gray-800 mb-2">Exportación de Datos</h4>
            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                <li>Formato CSV compatible con Excel</li>
                <li>Datos completos del período seleccionado</li>
                <li>UTF-8 con BOM para caracteres especiales</li>
                <li>Listo para análisis externo</li>
            </ul>
        </div>
        
        <div>
            <h4 class="font-medium text-gray-800 mb-2">Próximamente</h4>
            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                <li>Reportes en PDF</li>
                <li>Envío automático por correo</li>
                <li>Reportes programados</li>
                <li>Análisis predictivo</li>
            </ul>
        </div>
    </div>
</div>
