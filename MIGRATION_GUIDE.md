# Guía de Migración - Sistema v2.0

## Introducción

Esta guía documenta el proceso de migración del sistema de Parque de Grúas versión 1.0 a la versión 2.0 con mejoras sustanciales.

## Nuevas Funcionalidades

La versión 2.0 incluye los siguientes módulos nuevos:

### Módulos Principales
1. **Empresas (Companies)** - Gestión de clientes corporativos y aliados
2. **Choferes (Drivers)** - Módulo independiente para operadores
3. **Corralones (Yards)** - Catálogo de parques vehiculares
4. **Servicios (Services)** - Núcleo central para gestión de servicios
5. **Cotizaciones (Quotes)** - Generación y seguimiento de cotizaciones
6. **Facturas (Invoices)** - Facturación electrónica con Facturama
7. **Comisiones (Commissions)** - Cálculo y pago de comisiones
8. **Taller (Workshop)** - Órdenes de mantenimiento
9. **Auditoría (Audit Log)** - Registro de acciones críticas

## Prerequisitos

Antes de migrar, asegúrese de:

1. **Hacer backup completo de la base de datos actual**
   ```bash
   mysqldump -u root -p parque_gruas > backup_pre_migration_$(date +%Y%m%d).sql
   ```

2. **Verificar versión de PHP y MySQL**
   - PHP 7.4 o superior
   - MySQL 5.7 o superior

3. **Detener servicios si están en producción**

## Proceso de Migración

### Paso 1: Backup de Seguridad

```bash
# Backup de base de datos
mysqldump -u root -p parque_gruas > backup_parque_gruas_$(date +%Y%m%d_%H%M%S).sql

# Backup de archivos
tar -czf backup_files_$(date +%Y%m%d_%H%M%S).tar.gz /ruta/al/proyecto/ParqueGruas
```

### Paso 2: Actualizar Código

```bash
cd /ruta/al/proyecto/ParqueGruas
git pull origin main
# o descargar la última versión
```

### Paso 3: Ejecutar Migración de Base de Datos

```bash
mysql -u root -p parque_gruas < sql/migrations/001_system_improvements.sql
```

**Importante:** El script de migración:
- Crea todas las tablas nuevas
- NO modifica datos existentes
- Agrega campos a tablas existentes (payments, impounds)
- Crea un corralón principal y vincula impounds existentes

### Paso 4: Verificar Migración

```sql
-- Conectar a MySQL
mysql -u root -p parque_gruas

-- Verificar tablas nuevas
SHOW TABLES;

-- Verificar que existen las nuevas tablas:
-- companies, drivers, yards, services, quotes, invoices, 
-- invoice_items, commission_rules, commissions, 
-- workshop_orders, workshop_items, audit_log

-- Verificar datos iniciales
SELECT * FROM yards;
SELECT * FROM system_settings WHERE setting_key LIKE 'facturama%';

-- Salir
EXIT;
```

### Paso 5: Configurar Sistema

Acceder al panel de administración y configurar:

1. **Configuración General** (Admin > Configuración)
   - Prefijos de folios (servicios, cotizaciones, taller)
   - Tasa de IVA por defecto
   - Serie de facturas

2. **Integración Facturama** (si aplica)
   - Habilitar integración
   - Configurar API Key y Secret
   - Modo sandbox/producción

3. **Corralones**
   - Revisar/editar el corralón principal creado
   - Agregar corralones adicionales si es necesario

4. **Choferes**
   - Migrar información de choferes desde el módulo de grúas
   - Crear perfiles completos de choferes

## Cambios en Funcionalidad Existente

### Módulo de Pagos
- Ahora soporta pagos de: impounds, servicios y facturas
- Campo `payment_type` indica el tipo de pago
- Pagos parciales soportados para facturas

### Módulo de Impounds
- Ahora vinculados a un corralón específico (`yard_id`)
- Los registros existentes se asignan al corralón principal

### Módulo de Grúas
- Los campos `driver_name` y `driver_license` siguen existiendo para compatibilidad
- Se recomienda migrar a usar el módulo de Choferes separado

## Flujo de Trabajo Nuevo: Servicios

El nuevo flujo de trabajo para servicios es:

```
1. Cotización (opcional)
   ↓ (si se acepta)
2. Servicio Aceptado
   ↓
3. Asignación (chofer + grúa)
   ↓
4. En Proceso
   ↓
5. Culminado
   ↓
6. Facturación (si aplica)
   ↓
7. Cobrado
```

### Estados del Servicio
- `cotizado` - Servicio cotizado, esperando respuesta
- `aceptado` - Cotización aceptada o servicio aprobado
- `asignado` - Chofer y unidad asignados
- `en_proceso` - Servicio iniciado
- `culminado` - Servicio completado
- `facturado` - Factura generada
- `cobrado` - Pago recibido
- `cancelado` - Servicio cancelado
- `rechazado` - Cotización rechazada

## Comisiones

### Configurar Reglas de Comisión

Las comisiones se pueden calcular por:
- **Chofer** - Porcentaje o monto fijo por chofer
- **Grúa** - Por unidad específica
- **Empresa** - Por cliente/convenio
- **Tipo de Servicio** - Por tipo (arrastre, traslado, etc.)
- **Fijo** - Regla general

### Flujo de Comisiones
1. Servicio culminado → Comisión calculada automáticamente
2. Revisión y aprobación de comisiones
3. Corte de comisiones por periodo
4. Pago de comisiones
5. Exportación de reportes

## Facturación con Facturama

### Configuración Inicial

1. Crear cuenta en [Facturama](https://www.facturama.mx)
2. Obtener credenciales API (modo sandbox para pruebas)
3. Configurar en sistema:
   ```
   Admin > Configuración > Facturama API
   - API Key: [tu_api_key]
   - API Secret: [tu_api_secret]
   - Modo: Sandbox/Producción
   ```

### Flujo de Facturación
1. Servicio culminado
2. Generar factura (borrador)
3. Agregar conceptos
4. Timbrar factura → Envía a Facturama
5. Obtener UUID, PDF y XML
6. Registrar pago

## Reportes Nuevos

La versión 2.0 incluye reportes adicionales:

1. **Servicios**
   - Por estado, fecha, empresa, chofer
   - Exportación a CSV/Excel

2. **Facturación**
   - Facturas emitidas vs pagadas
   - Por empresa y periodo

3. **Comisiones**
   - Por chofer y periodo
   - Cortes de comisiones

4. **Taller**
   - Costos por unidad
   - Disponibilidad de flota
   - Frecuencia de mantenimientos

5. **Corralones**
   - Ocupación actual
   - Ingresos/egresos
   - Tiempos de permanencia

## Solución de Problemas

### Error al ejecutar migración

**Problema:** Error de foreign key constraints

**Solución:**
```sql
SET FOREIGN_KEY_CHECKS = 0;
-- ejecutar migración
SET FOREIGN_KEY_CHECKS = 1;
```

### Datos no migrados

**Problema:** Impounds sin yard_id

**Solución:**
```sql
UPDATE impounds SET yard_id = (SELECT id FROM yards LIMIT 1) WHERE yard_id IS NULL;
```

### Error de permisos

**Problema:** Usuario sin permisos en nuevas tablas

**Solución:**
```sql
GRANT ALL PRIVILEGES ON parque_gruas.* TO 'parque_user'@'localhost';
FLUSH PRIVILEGES;
```

## Rollback (Reversión)

Si necesita revertir a la versión anterior:

```bash
# 1. Restaurar código
git checkout <commit_anterior>

# 2. Restaurar base de datos
mysql -u root -p parque_gruas < backup_pre_migration_YYYYMMDD.sql

# 3. Reiniciar servicios
sudo systemctl restart apache2
```

## Soporte y Contacto

Para soporte adicional:
- Crear issue en GitHub
- Documentación API: `/api/docs`
- Email: soporte@parquegruas.com

## Próximos Pasos

Después de la migración:

1. **Capacitación de Usuarios**
   - Familiarizarse con nuevos módulos
   - Practicar flujo de servicios
   - Probar facturación en sandbox

2. **Configuración Completa**
   - Agregar empresas/clientes
   - Crear perfiles de choferes
   - Configurar reglas de comisiones

3. **Datos Iniciales**
   - Importar empresas existentes
   - Migrar información de choferes
   - Configurar corralones adicionales

4. **Pruebas**
   - Crear servicio de prueba
   - Generar cotización
   - Procesar factura en sandbox
   - Calcular comisiones

## Changelog

### Versión 2.0.0 (2025-01-14)

**Agregado:**
- Módulo de Empresas (Companies)
- Módulo de Choferes (Drivers) separado
- Módulo de Corralones (Yards)
- Módulo de Servicios (Services) - núcleo central
- Módulo de Cotizaciones (Quotes)
- Módulo de Facturas (Invoices)
- Integración con Facturama API
- Módulo de Comisiones (Commissions)
- Módulo de Taller (Workshop)
- Sistema de Auditoría (Audit Log)
- Soporte para Carta Porte CFDI
- Reportes avanzados por módulo
- Exportación CSV/Excel

**Modificado:**
- Tabla payments: soporta servicios y facturas
- Tabla impounds: vinculación con corralones
- Dashboard: métricas de nuevos módulos

**Mantenido:**
- Toda la funcionalidad existente v1.0
- Compatibilidad con datos existentes
- Estructura MVC
- Sistema de autenticación y roles

---

**Fecha de documento:** 2025-01-14
**Versión del sistema:** 2.0.0
