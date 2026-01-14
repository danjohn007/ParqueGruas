# Parque de Grúas - Sistema Integral de Gestión v2.0

Sistema completo de gestión para parques de grúas (corralones) desarrollado en PHP puro con arquitectura MVC, diseñado para administrar vehículos infractores, grúas, servicios, facturación, comisiones y más.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-cyan)
![License](https://img.shields.io/badge/license-MIT-green)
![Version](https://img.shields.io/badge/version-2.0.0-success)

## 🆕 Novedades Versión 2.0

La versión 2.0 introduce mejoras sustanciales con nuevos módulos empresariales:

### 🎯 Módulos Nuevos
- **Empresas** - Gestión completa de clientes corporativos y aliados con datos fiscales
- **Choferes** - Módulo independiente para operadores (separado de grúas)
- **Corralones** - Catálogo multi-corralón con control de ocupación
- **Servicios** - Módulo central para gestión de servicios de grúa
- **Cotizaciones** - Generación, seguimiento y aceptación de cotizaciones
- **Facturación Electrónica** - Integración con Facturama (CFDI 4.0)
- **Carta Porte** - Complemento de transporte para facturación
- **Comisiones** - Cálculo automático y reportes de comisiones
- **Taller** - Órdenes de mantenimiento para flota
- **Auditoría** - Registro de acciones críticas del sistema

### 🔄 Flujo de Trabajo Moderno
```
Cotización → Servicio → Asignación → Ejecución → Facturación → Cobro
```

### 📊 Nuevas Capacidades
- Facturación electrónica con timbrado SAT
- Gestión multi-empresa con reportes independientes
- Cálculo automático de comisiones por chofer/grúa/empresa
- Control de múltiples corralones
- Seguimiento completo del ciclo de servicio
- Exportación avanzada (CSV/Excel)

## 🚀 Características Principales

### Módulos Base (v1.0)
- **Dashboard** - Panel de control con estadísticas en tiempo real y gráficas
- **Gestión de Vehículos** - Alta, baja, búsqueda y registro de vehículos
- **Corralón** - Control de ingresos, egresos y almacenamiento
- **Grúas** - Administración de flota de grúas y operadores
- **Pagos** - Procesamiento de pagos con múltiples métodos
- **Reportes** - Generación de reportes y estadísticas
- **Calendario** - Programación de mantenimientos e inspecciones
- **Usuarios** - Gestión de usuarios con roles (admin, operador, visualizador)
- **Configuración** - Módulo de configuración del sistema (solo admin)
- **API HikVision** - Integración con dispositivos de videovigilancia

### Módulos Empresariales (v2.0)
- **Empresas (Companies)** - CRUD completo con datos fiscales, reportes por empresa
- **Choferes (Drivers)** - Gestión independiente, historial, licencias, comisiones
- **Corralones (Yards)** - Catálogo, ocupación, reportes de movimientos
- **Servicios (Services)** - Gestión completa del ciclo de vida del servicio
- **Cotizaciones (Quotes)** - Generación, PDF, aceptación/rechazo
- **Facturas (Invoices)** - Integración Facturama, CFDI 4.0, Carta Porte
- **Comisiones (Commissions)** - Reglas, cálculo automático, cortes, pagos
- **Taller (Workshop)** - Órdenes de trabajo, refacciones, costos por unidad
- **Auditoría (Audit Log)** - Trazabilidad de acciones críticas

### Tecnologías Utilizadas
- **Backend**: PHP 7.4+ (sin framework)
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Estilos**: Tailwind CSS (diseño minimalista y responsivo)
- **Gráficas**: Chart.js y ApexCharts
- **Calendario**: FullCalendar.js
- **Iconos**: Font Awesome 6

### Características Técnicas
- ✅ Arquitectura MVC limpia y organizada
- ✅ URL amigables con mod_rewrite
- ✅ URL Base auto-configurable
- ✅ Autenticación segura con `password_hash()`
- ✅ Sesiones seguras con cookies HTTP-only
- ✅ Preparación de consultas SQL (PDO) para prevenir inyección SQL
- ✅ Diseño responsivo para móviles, tablets y escritorio
- ✅ Integración con Facturama API (CFDI 4.0)
- ✅ Sistema de auditoría completo
- ✅ Cálculo automático de comisiones
- ✅ Soporte multi-corralón
- ✅ Exportación avanzada (CSV/Excel)
- ✅ Datos de ejemplo del estado de Querétaro

## 📋 Requisitos del Sistema

### Servidor
- Apache 2.4+ con `mod_rewrite` habilitado
- PHP 7.4 o superior con extensiones:
  - PDO y PDO_MySQL
  - mbstring y json
  - curl (para integración Facturama)
- MySQL 5.7 o superior

### Desarrollo/Producción
- Sistema operativo: Linux, Windows o macOS
- Memoria RAM: Mínimo 512MB
- Espacio en disco: Mínimo 100MB

## 🔧 Instalación

### Paso 1: Clonar el Repositorio
```bash
git clone https://github.com/danjohn007/ParqueGruas.git
cd ParqueGruas
```

### Paso 2: Configurar Apache

#### Opción A: Instalación en directorio raíz
Apuntar el DocumentRoot a la carpeta `public`:
```apache
<VirtualHost *:80>
    ServerName parquegruas.local
    DocumentRoot /ruta/al/proyecto/ParqueGruas/public
    
    <Directory /ruta/al/proyecto/ParqueGruas/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Opción B: Instalación en subdirectorio
Copiar el proyecto a `/var/www/html/parquegruas` o similar. El sistema detectará automáticamente la URL base.

**Importante**: Asegúrese de que `mod_rewrite` esté habilitado:
```bash
# En Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2

# En CentOS/RHEL
# mod_rewrite viene habilitado por defecto
sudo systemctl restart httpd
```

### Paso 3: Crear la Base de Datos

1. Acceder a MySQL:
```bash
mysql -u root -p
```

2. Crear la base de datos:
```sql
CREATE DATABASE parque_gruas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. Crear usuario (opcional pero recomendado):
```sql
CREATE USER 'parque_user'@'localhost' IDENTIFIED BY 'tu_contraseña_segura';
GRANT ALL PRIVILEGES ON parque_gruas.* TO 'parque_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

4. Importar el esquema y datos de ejemplo:
```bash
# INSTALACIÓN NUEVA - Versión 2.0 (recomendado)
mysql -u root -p parque_gruas < sql/parque_gruas_extended.sql

# Luego aplicar mejoras de v2.0
mysql -u root -p parque_gruas < sql/migrations/001_system_improvements.sql
```

**Para usuarios de v1.0:** Si ya tiene el sistema instalado, consulte [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md) para migrar a v2.0.

### Paso 4: Configurar Credenciales

Editar el archivo `config/config.php` y configurar las credenciales de la base de datos:

```php
// Configuración de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'parque_gruas');
define('DB_USER', 'parque_user');      // Tu usuario de MySQL
define('DB_PASS', 'tu_contraseña');    // Tu contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');
```

### Paso 5: Establecer Permisos

```bash
# Dar permisos de escritura al directorio de logs
chmod -R 775 logs/
chown -R www-data:www-data logs/  # En Ubuntu/Debian

# En CentOS/RHEL
chown -R apache:apache logs/
```

### Paso 6: Verificar Instalación

Acceder a la página de prueba de conexión:
```
http://tu-dominio/test_connection.php
```

Esta página verificará:
- ✅ Versión de PHP
- ✅ URL Base detectada
- ✅ Extensiones PDO
- ✅ Conexión a la base de datos
- ✅ Tablas creadas correctamente
- ✅ Permisos de escritura
- ✅ Configuración de Apache

### Paso 7: Acceder al Sistema

URL principal:
```
http://tu-dominio/
```

**Credenciales por defecto:**
- **Usuario**: `admin`
- **Contraseña**: `admin123`

**⚠️ IMPORTANTE**: Cambiar la contraseña del administrador inmediatamente después del primer acceso en la sección de Perfil de Usuario.

## 📂 Estructura del Proyecto

```
ParqueGruas/
├── app/
│   ├── controllers/          # Controladores MVC
│   │   ├── Controller.php    # Controlador base
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── VehiclesController.php
│   │   ├── ImpoundsController.php
│   │   ├── CranesController.php
│   │   ├── PaymentsController.php
│   │   ├── SettingsController.php
│   │   ├── CalendarController.php
│   │   └── ReportsController.php
│   ├── models/               # Modelos de datos
│   │   ├── Model.php         # Modelo base
│   │   ├── User.php
│   │   ├── Vehicle.php
│   │   ├── Crane.php
│   │   ├── Impound.php
│   │   ├── Payment.php
│   │   ├── Setting.php
│   │   ├── Company.php       # 🆕 v2.0
│   │   ├── Driver.php        # 🆕 v2.0
│   │   ├── Yard.php          # 🆕 v2.0
│   │   ├── Service.php       # 🆕 v2.0
│   │   ├── Quote.php         # 🆕 v2.0
│   │   ├── Invoice.php       # 🆕 v2.0
│   │   ├── Commission.php    # 🆕 v2.0
│   │   ├── WorkshopOrder.php # 🆕 v2.0
│   │   └── AuditLog.php      # 🆕 v2.0
│   ├── services/             # 🆕 Servicios de integración
│   │   └── FacturamaService.php
│   └── views/                # Vistas
│       ├── layouts/          # Plantillas
│       ├── auth/             # Login/Logout
│       ├── dashboard/        # Panel principal
│       ├── vehicles/         # Vehículos
│       ├── cranes/           # Grúas
│       ├── users/            # Usuarios
│       ├── impounds/         # Corralón
│       ├── payments/         # Pagos
│       ├── settings/         # Configuración
│       ├── calendar/         # Calendario
│       └── reports/          # Reportes
├── config/
│   ├── config.php            # Configuración general
│   ├── Database.php          # Clase de conexión DB
│   └── Router.php            # Enrutador de URLs
├── public/                   # Directorio público (DocumentRoot)
│   ├── index.php             # Punto de entrada
│   ├── test_connection.php   # Test de conexión
│   ├── .htaccess             # Reglas de reescritura
│   ├── css/                  # Estilos personalizados
│   ├── js/                   # JavaScript personalizado
│   └── assets/               # Imágenes y recursos
├── sql/
│   ├── parque_gruas.sql          # Esquema básico con datos de ejemplo
│   ├── parque_gruas_extended.sql # Esquema con datos extendidos (recomendado)
│   └── migrations/               # 🆕 Migraciones de BD
│       └── 001_system_improvements.sql
├── logs/                     # Archivos de log
├── MIGRATION_GUIDE.md        # 🆕 Guía de migración v1.0 → v2.0
├── .htaccess                 # Reescritura raíz
├── .gitignore
└── README.md
```

## 🎨 Capturas de Pantalla

### Dashboard
Panel principal con estadísticas en tiempo real, gráficas de ingresos y estado de grúas.

### Gestión de Corralón
Control completo de ingresos y egresos de vehículos con cálculo automático de costos.

### Sistema de Pagos
Procesamiento de pagos con múltiples métodos y generación de recibos.

### Calendario
Programación de mantenimientos, inspecciones y eventos con vista mensual/semanal/diaria.

## 👥 Usuarios y Roles

El sistema incluye 3 niveles de usuario:

### Administrador (`admin`)
- Acceso completo a todos los módulos
- Gestión de usuarios
- Configuración del sistema
- Eliminación de registros

### Operador (`operator`)
- Registro de vehículos e infracciones
- Procesamiento de pagos
- Gestión de grúas
- Consulta de reportes

### Visualizador (`viewer`)
- Solo lectura de información
- Consulta de estadísticas
- Sin permisos de modificación

## 🔐 Seguridad

- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Protección contra inyección SQL con PDO prepared statements
- Sesiones seguras con cookies HTTP-only
- Validación de entrada en todos los formularios
- Protección CSRF en formularios críticos
- Headers de seguridad (X-Frame-Options, X-XSS-Protection)

## 🌐 API HikVision

El sistema incluye soporte para integración con dispositivos HikVision:
- Registro de múltiples dispositivos (cámaras, NVR, DVR, control de acceso)
- Configuración de IP, puerto y credenciales
- Estado de conexión en tiempo real

Para configurar dispositivos, acceder a: `Admin > API HikVision`

## 📊 Datos de Ejemplo

### Versión Básica (parque_gruas.sql)
- 3 usuarios (admin, 2 operadores)
- 5 grúas operativas
- 8 vehículos registrados
- 8 registros de corralón
- 3 pagos procesados
- 4 dispositivos HikVision
- Eventos de calendario

### Versión Extendida (parque_gruas_extended.sql) - **Recomendado**
- 5 usuarios (admin, 3 operadores, 1 visualizador)
- 8 grúas operativas
- 20 vehículos registrados con información completa
- 15 registros de corralón con diferentes estados
- 4 pagos procesados
- 6 dispositivos HikVision
- Múltiples eventos de calendario
- Configuraciones del sistema preestablecidas

## 🛠️ Solución de Problemas

### Error: "Call to undefined function apache_get_modules()"
**Solución**: Comentar la verificación de mod_rewrite en `test_connection.php` si se ejecuta en servidor distinto a Apache.

### Error 404 en todas las URLs
**Solución**: Verificar que `mod_rewrite` esté habilitado y que el archivo `.htaccess` esté presente.

### Error de conexión a la base de datos
**Solución**: Verificar credenciales en `config/config.php` y que el usuario tenga permisos.

### Las rutas CSS/JS no cargan
**Solución**: Verificar que el DocumentRoot apunte a la carpeta `public` o ajustar la configuración de BASE_URL.

## 🔄 Actualizaciones

### Migración de v1.0 a v2.0

Si ya tiene el sistema v1.0 instalado, consulte la [Guía de Migración](MIGRATION_GUIDE.md) completa.

**Pasos resumidos:**
1. Hacer backup de base de datos y archivos
2. Actualizar código: `git pull origin main`
3. Ejecutar migración: `mysql -u root -p parque_gruas < sql/migrations/001_system_improvements.sql`
4. Configurar nuevos módulos en Admin > Configuración

### Actualizaciones futuras

```bash
git pull origin main
# Revisar sql/migrations/ para migraciones de base de datos
# Limpiar caché si es necesario
```

## 🆕 Novedades Versión 2.0 - Guía Completa

### 1. Módulo de Empresas (Companies)

Gestión de clientes corporativos y convenios con datos fiscales completos.

**Características:**
- CRUD completo de empresas
- Datos fiscales: RFC, régimen, forma de pago, uso CFDI
- Reportes independientes por empresa
- Historial de servicios y facturación
- Estadísticas de negocio por cliente

**Casos de uso:**
- Empresas aseguradoras con convenios
- Gobiernos municipales/estatales
- Empresas de logística
- Clientes corporativos frecuentes

### 2. Módulo de Choferes (Drivers)

Gestión independiente de operadores separada del módulo de grúas.

**Características:**
- Perfil completo: licencia, vigencia, documentación
- Historial de servicios realizados
- Cálculo automático de comisiones
- Alertas de vencimiento de licencias
- Estadísticas de desempeño

**Beneficios:**
- Un chofer puede operar múltiples grúas
- Seguimiento individual de comisiones
- Control de licencias y documentación
- Análisis de productividad

### 3. Módulo de Corralones (Yards)

Gestión multi-corralón con control de ocupación y movimientos.

**Características:**
- Catálogo de múltiples corralones
- Capacidad y ocupación en tiempo real
- Reportes de ingresos/egresos
- Tiempos de permanencia
- Asignación automática por ubicación

**Casos de uso:**
- Múltiples ubicaciones de corralones
- Control de capacidad por sucursal
- Reportes independientes por corralón

### 4. Módulo de Servicios (Services) - Core

Gestión completa del ciclo de vida de servicios de grúa.

**Flujo de trabajo:**
```
1. Solicitud → 2. Cotización → 3. Aceptación → 4. Asignación 
→ 5. Ejecución → 6. Culminación → 7. Facturación → 8. Cobro
```

**Características:**
- Tipos: arrastre, traslado, rescate, auxilio
- Asignación de chofer y unidad
- Tracking de origen/destino
- Cálculo automático de costos
- Evidencia fotográfica y documentos
- Estados del servicio con workflow

**Tipos de servicio:**
- **Arrastre**: Vehículo infractor
- **Traslado**: Movimiento autorizado
- **Rescate**: Emergencias en carretera
- **Auxilio**: Asistencia vial

### 5. Módulo de Cotizaciones (Quotes)

Generación profesional de cotizaciones con seguimiento.

**Características:**
- Generación automática de folios
- PDF descargable
- Vigencia de cotización
- Estados: pendiente, aceptada, rechazada, vencida
- Conversión automática a servicio

**Workflow:**
1. Generar cotización
2. Enviar al cliente
3. Cliente acepta/rechaza
4. Si acepta → se crea servicio automáticamente

### 6. Módulo de Facturación Electrónica (Invoices)

Integración completa con Facturama para CFDI 4.0.

**Características:**
- Timbrado SAT automático
- Descarga de PDF y XML
- Complemento Carta Porte
- Cancelación de facturas
- Multi-empresa
- Pagos parciales

**Configuración Facturama:**
```
Admin > Configuración > Facturama API
- API Key: [tu_api_key]
- API Secret: [tu_api_secret]
- Modo: Sandbox (pruebas) / Producción
```

**Documentación:** https://facturama.mx/api-facturacion-electronica

### 7. Carta Porte (Complemento CFDI)

Cumplimiento con requisitos SAT para traslado de bienes.

**Características:**
- Integrado en facturación
- Datos de origen y destino
- Información del vehículo
- Permisos SCT
- Seguros obligatorios

**Cuándo usar:**
- Traslados de vehículos
- Servicios que requieren transporte
- Cumplimiento normativo SAT

### 8. Módulo de Comisiones (Commissions)

Cálculo automático y gestión de comisiones por servicio.

**Tipos de reglas:**
- Por chofer (% o monto fijo)
- Por grúa/unidad
- Por empresa/convenio
- Por tipo de servicio
- Reglas generales

**Workflow:**
1. Servicio culminado → comisión calculada automáticamente
2. Revisión y aprobación
3. Corte de comisiones por periodo
4. Generación de reportes
5. Registro de pagos

**Reportes:**
- Comisiones por chofer
- Cortes quincenales/mensuales
- Exportación a Excel/CSV
- Historial de pagos

### 9. Módulo de Taller (Workshop)

Gestión de mantenimiento preventivo y correctivo de flota.

**Características:**
- Órdenes de trabajo
- Refacciones y mano de obra
- Costos por unidad
- Historial de mantenimientos
- Alertas preventivas

**Tipos de trabajo:**
- Preventivo (programado)
- Correctivo (reparaciones)
- Emergencia (urgente)
- Inspección

**Beneficios:**
- Control de costos de mantenimiento
- Disponibilidad de flota
- Frecuencia de fallas
- Planificación de mantenimientos

### 10. Sistema de Auditoría (Audit Log)

Trazabilidad completa de acciones críticas.

**Registra:**
- Usuario que realiza la acción
- Fecha y hora
- Tipo de acción (crear/editar/eliminar/cancelar)
- Módulo afectado
- Valores anteriores y nuevos
- IP y navegador

**Módulos auditados:**
- Servicios
- Facturas
- Pagos
- Comisiones
- Catálogos importantes

## 📊 Reportes Nuevos v2.0

### Servicios
- Por estado, fecha, empresa, chofer, unidad
- Exportación CSV/Excel
- Análisis de rentabilidad

### Facturación
- Facturas emitidas vs pagadas
- Por empresa y periodo
- Pendientes de cobro

### Comisiones
- Por chofer y periodo
- Cortes de comisiones
- Comisiones pagadas vs pendientes

### Taller
- Costos por unidad
- Disponibilidad de flota
- Frecuencia de mantenimientos

### Corralones
- Ocupación actual
- Ingresos/egresos
- Tiempos de permanencia
- Por corralón

## 🔐 Seguridad v2.0

- ✅ Auditoría completa de acciones críticas
- ✅ Validación de RFC (Facturama)
- ✅ Protección de datos fiscales
- ✅ Control de acceso por roles a nuevos módulos
- ✅ Logs de integración API (Facturama)
- ✅ Cifrado de credenciales API

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

Desarrollado para la gestión integral de parques de grúas en México.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📞 Soporte

Para reportar bugs o solicitar features, por favor crear un issue en GitHub.

---

**Nota**: Este sistema fue desarrollado con tecnologías open source y está optimizado para entornos de producción con Apache + PHP + MySQL.
