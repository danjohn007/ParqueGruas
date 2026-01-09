# Parque de Grúas - Sistema Integral de Gestión

Sistema completo de gestión para parques de grúas (corralones) desarrollado en PHP puro con arquitectura MVC, diseñado para administrar vehículos infractores, grúas, pagos, reportes y más.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-cyan)
![License](https://img.shields.io/badge/license-MIT-green)

## 🚀 Características Principales

### Módulos del Sistema
- **Dashboard** - Panel de control con estadísticas en tiempo real y gráficas
- **Gestión de Vehículos** - Alta, baja, búsqueda y registro de vehículos
- **Corralón** - Control de ingresos, egresos y almacenamiento
- **Grúas** - Administración de flota de grúas y operadores
- **Pagos** - Procesamiento de pagos con múltiples métodos
- **Reportes** - Generación de reportes y estadísticas
- **Calendario** - Programación de mantenimientos e inspecciones
- **Usuarios** - Gestión de usuarios con roles (admin, operador, visualizador)
- **API HikVision** - Integración con dispositivos de videovigilancia

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
- ✅ Datos de ejemplo del estado de Querétaro

## 📋 Requisitos del Sistema

### Servidor
- Apache 2.4+ con `mod_rewrite` habilitado
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Extensiones PHP requeridas:
  - PDO
  - PDO_MySQL
  - mbstring
  - json

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
mysql -u root -p parque_gruas < sql/parque_gruas.sql
```

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

**⚠️ IMPORTANTE**: Cambiar la contraseña del administrador inmediatamente después del primer acceso.

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
│   │   ├── PaymentsController.php
│   │   └── CalendarController.php
│   ├── models/               # Modelos de datos
│   │   ├── Model.php         # Modelo base
│   │   ├── User.php
│   │   ├── Vehicle.php
│   │   ├── Crane.php
│   │   ├── Impound.php
│   │   └── Payment.php
│   └── views/                # Vistas
│       ├── layouts/          # Plantillas
│       ├── auth/             # Login/Logout
│       ├── dashboard/        # Panel principal
│       ├── vehicles/         # Vehículos
│       ├── impounds/         # Corralón
│       ├── payments/         # Pagos
│       └── calendar/         # Calendario
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
│   └── parque_gruas.sql      # Esquema de base de datos
├── logs/                     # Archivos de log
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

El sistema incluye datos de ejemplo del estado de Querétaro:
- 3 usuarios (admin, 2 operadores)
- 5 grúas operativas
- 8 vehículos registrados
- 8 registros de corralón
- 3 pagos procesados
- 4 dispositivos HikVision
- Eventos de calendario

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

Para actualizar el sistema:

```bash
git pull origin main
# Revisar sql/updates/ para migraciones de base de datos
# Limpiar caché si es necesario
```

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
