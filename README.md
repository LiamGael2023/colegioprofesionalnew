# Sistema de Gestión de Colegio Profesional

Sistema integral de gestión administrativa para Colegios Profesionales desarrollado en **PHP 8.1 MVC puro** con **MySQL 8.0**, completamente dockerizado para facilitar su despliegue y uso.

![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker)

## 🎯 Problema que Resuelve

Los colegios profesionales (médicos, abogados, ingenieros, etc.) necesitan:

- **Gestionar miembros**: Registro de personas del público general y conversión a colegiados
- **Control de cuotas**: Generación automática mensual sin duplicados
- **Caja de pagos**: Procesamiento eficiente de múltiples obligaciones
- **Habilitación automática**: Estado actualizado basado en cumplimiento de pagos
- **Reportes**: Estados de cuenta, deudas pendientes, recaudación

## ✨ Características Principales

### 📋 Módulo de Personas
- Alta de personas con datos completos (DNI, nombres, dirección, contacto)
- Búsqueda rápida por DNI o nombre
- Validación de DNI único
- Edición y actualización de información
- Clasificación automática: Público General / Colegiado

### 🎓 Módulo de Colegiados
- Conversión de persona a colegiado
- Asignación automática de número de colegiatura (formato: YYYY-0001)
- Registro de especialidad y subespecialidad
- Fecha de colegiatura y universidad de procedencia
- Estados: Habilitado / Inhabilitado (automático)
- Contador de meses impagos

### 💰 Sistema de Aportaciones (Cuotas)
- Generación automática mensual (día 1 de cada mes vía cron)
- Cuota configurable (default: S/. 150.00)
- Protección anti-duplicados (`UNIQUE KEY` en BD: `persona_id + mes + año`)
- Estados: Pendiente / Pagado / Vencido
- Pagos adelantados (creación manual de cuotas futuras)
- Tolerancia de impago configurable (default: 3 meses)

### 🏦 Módulo de Caja
- Búsqueda de persona por DNI
- Visualización de todas las deudas pendientes
- Selección múltiple de obligaciones para un solo pago
- Métodos de pago: Efectivo, Transferencia, Tarjeta, Yape, Plin
- Generación automática de recibos con número correlativo
- Historial completo de pagos por colegiado

### 📊 Reportes y Auditoría
- Estado de cuenta por colegiado
- Listado de deudores
- Recaudación por período (día/mes/año)
- Listado de habilitados e inhabilitados
- Auditoría de todas las operaciones (log en BD)

### 🔐 Seguridad
- Autenticación con sesiones PHP
- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Control de acceso por roles (ADMIN, CAJERO)
- Prepared statements PDO para prevenir SQL injection
- Validación de entrada en servidor
- Protección de archivos sensibles (`.htaccess`)

## 📋 Requisitos

- Docker y Docker Compose
- Git

**O si prefieres instalación local:**
- PHP 8.1+
- MySQL 8.0+
- Apache con mod_rewrite

## 🚀 Instalación con Docker (Recomendado)

### 1. Clonar el repositorio

```bash
git clone <repository-url>
cd colegioprofesionalnew
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
```

Editar `.env` según tus necesidades (las credenciales por defecto ya funcionan con Docker).

### 3. Levantar los contenedores

```bash
docker-compose up -d
```

Esto creará y levantará:
- **Web server** (PHP 8.1 + Apache): `http://localhost:8080`
- **MySQL 8.0**: Puerto 3306
- **phpMyAdmin**: `http://localhost:8081`

### 4. Verificar que los servicios están corriendo

```bash
docker-compose ps
```

### 5. Acceder a la aplicación

Abrir en el navegador: `http://localhost:8080`

**Credenciales por defecto:**
- Usuario: `admin`
- Contraseña: `admin123`

## 📦 Instalación Local (Sin Docker)

### 1. Configurar base de datos

```bash
mysql -u root -p
CREATE DATABASE colegio_profesional CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'colegio_user'@'localhost' IDENTIFIED BY 'colegio_pass_2024';
GRANT ALL PRIVILEGES ON colegio_profesional.* TO 'colegio_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Importar schema

```bash
mysql -u colegio_user -p colegio_profesional < database/schema.sql
```

### 3. Configurar .env

```bash
cp .env.example .env
```

Editar las credenciales de base de datos en `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=colegio_profesional
DB_USER=colegio_user
DB_PASS=colegio_pass_2024
```

### 4. Configurar Apache

Apuntar el DocumentRoot a la carpeta `public/`:

```apache
<VirtualHost *:80>
    ServerName colegio.local
    DocumentRoot /path/to/colegioprofesionalnew/public

    <Directory /path/to/colegioprofesionalnew/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 5. Habilitar mod_rewrite

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## ⚙️ Configuración del CRON (Generación Automática de Cuotas)

Para generar cuotas automáticamente el día 1 de cada mes:

### Con Docker:

```bash
docker exec -it colegio_web bash
crontab -e
```

Agregar:
```cron
0 0 1 * * /usr/local/bin/php /var/www/html/cron/generar_cuotas.php >> /var/www/html/logs/cron.log 2>&1
```

### Sin Docker:

```bash
crontab -e
```

Agregar (ajustar la ruta):
```cron
0 0 1 * * /usr/bin/php /path/to/colegioprofesionalnew/cron/generar_cuotas.php >> /path/to/colegioprofesionalnew/logs/cron.log 2>&1
```

## 📁 Estructura del Proyecto

```
colegioprofesionalnew/
├── app/
│   ├── Controllers/         # Controladores MVC
│   ├── Models/             # Modelos de datos
│   ├── Views/              # Vistas PHP
│   └── Core/               # Clases base (Database, Router, Controller, Model)
├── config/
│   └── config.php          # Configuración principal
├── cron/
│   ├── generar_cuotas.php  # Script de generación automática
│   └── CRONTAB.md          # Instrucciones de configuración
├── database/
│   ├── schema.sql          # Schema de base de datos
│   └── seeders/            # Datos de prueba
├── logs/                   # Logs del sistema
├── public/
│   ├── index.php           # Punto de entrada
│   ├── css/                # Estilos CSS
│   ├── js/                 # JavaScript
│   └── .htaccess           # Reglas de reescritura
├── .env                    # Variables de entorno
├── .gitignore
├── docker-compose.yml      # Configuración Docker
├── Dockerfile
└── README.md
```

## 🔧 Uso del Sistema

### 1. Registrar Personas

**Personas → Nueva Persona**

Completar formulario con:
- DNI (8 dígitos, único)
- Nombres y apellidos
- Datos de contacto (opcional)

### 2. Convertir a Colegiado

**Personas → [Buscar persona] → Convertir a Colegiado**

Completar:
- Especialidad
- Universidad
- Fecha de colegiatura

El sistema asignará automáticamente un número de colegiatura.

### 3. Generar Cuotas

**Opción A - Automático (Recomendado):**
- El script CRON genera cuotas el día 1 de cada mes

**Opción B - Manual:**
- **Cuotas → Generar Cuotas Mensuales**
- Seleccionar mes y año
- Clic en "Generar"

### 4. Procesar Pagos

**Caja → Ingresar DNI → Buscar**

1. Se mostrarán todas las cuotas pendientes
2. Seleccionar cuotas a pagar (checkbox)
3. Elegir método de pago
4. Procesar pago
5. Se generará un recibo automáticamente

### 5. Consultar Reportes

**Reportes →**
- **Deudores**: Lista de colegiados con deudas
- **Recaudación**: Ingresos por período
- **Estado de Cuenta**: Historial individual

## 🗄️ Base de Datos

### Tablas Principales

- `usuarios` - Usuarios del sistema (admin, cajeros)
- `personas` - Registro general de personas
- `colegiados` - Información de colegiados
- `cuotas` - Cuotas mensuales generadas
- `pagos` - Pagos realizados
- `pago_cuotas` - Relación N:N entre pagos y cuotas
- `recibos` - Recibos de pago
- `auditoria` - Log de todas las operaciones
- `configuracion` - Configuraciones del sistema

### Protecciones

- **Integridad Referencial**: Foreign keys con `CASCADE` y `RESTRICT`
- **Datos Únicos**: `UNIQUE` constraints en DNI, número de colegiatura, recibos
- **Anti-duplicados**: `UNIQUE KEY (persona_id, mes, anio)` en cuotas
- **Auditoría Completa**: Todas las operaciones se registran con usuario, IP, timestamp

## 👥 Roles de Usuario

### ADMIN
- Acceso completo al sistema
- Gestión de usuarios
- Generación manual de cuotas
- Modificación de habilitaciones
- Todos los reportes

### CAJERO
- Procesar pagos
- Consultar personas y colegiados
- Ver reportes de recaudación
- No puede eliminar registros

## 🔒 Seguridad Implementada

1. **Autenticación**
   - Sesiones PHP con configuración segura
   - Passwords hasheadas (bcrypt, cost 10)
   - Logout con destrucción de sesión

2. **Autorización**
   - Control de acceso por roles
   - Verificación en cada método del controlador

3. **Prevención de Ataques**
   - SQL Injection: Prepared statements en todas las queries
   - XSS: Escape de output con `htmlspecialchars()`
   - CSRF: Tokens en formularios (implementado en framework)

4. **Protección de Archivos**
   - `.htaccess` bloqueando acceso a directorios sensibles
   - Variables de entorno en `.env` (no versionado)

## 📊 Lógica de Negocio

### Habilitación de Colegiados

Un colegiado se **INHABILITA** automáticamente cuando:
- Tiene más de 3 meses impagos (configurable en `.env`)

Se **HABILITA** automáticamente cuando:
- Paga sus cuotas y queda con 3 o menos meses pendientes

### Generación de Cuotas

- Se ejecuta automáticamente el día 1 de cada mes
- Crea una cuota para cada colegiado activo
- No genera duplicados (validación `UNIQUE`)
- Monto configurable en `configuracion` tabla

### Procesamiento de Pagos

1. Se seleccionan múltiples cuotas pendientes
2. Se crea un registro de pago con el monto total
3. Se relacionan las cuotas con el pago (`pago_cuotas`)
4. Se marcan las cuotas como "PAGADO"
5. Se actualiza el contador de meses impagos
6. Se recalcula la habilitación del colegiado
7. Se genera un recibo con número correlativo

## 🛠️ Comandos Útiles Docker

```bash
# Ver logs
docker-compose logs -f web

# Acceder al contenedor web
docker exec -it colegio_web bash

# Acceder a MySQL
docker exec -it colegio_mysql mysql -u colegio_user -p

# Reiniciar servicios
docker-compose restart

# Detener servicios
docker-compose down

# Eliminar todo (incluyendo volúmenes)
docker-compose down -v
```

## 📝 Datos de Prueba

Para cargar datos de prueba:

```bash
# Con Docker
docker exec -i colegio_mysql mysql -u colegio_user -pcolegio_pass_2024 colegio_profesional < database/seeders/datos_prueba.sql

# Sin Docker
mysql -u colegio_user -p colegio_profesional < database/seeders/datos_prueba.sql
```

## 🐛 Troubleshooting

### Error de conexión a base de datos

Verificar que MySQL está corriendo:
```bash
docker-compose ps mysql
```

### No se muestran los estilos CSS

Verificar permisos en carpeta `public/`:
```bash
chmod -R 755 public/
```

### Errores de escritura en logs/

```bash
chmod -R 777 logs/
```

### CRON no ejecuta

Verificar que el script tiene permisos de ejecución:
```bash
chmod +x cron/generar_cuotas.php
```

## 📧 Soporte

Para reportar bugs o solicitar funcionalidades, crear un issue en el repositorio.

## 📄 Licencia

Este proyecto es de uso libre para colegios profesionales.

## 🙏 Créditos

Desarrollado con PHP 8.1 MVC puro, MySQL 8.0 y Docker.

---

**¡Listo para usar! 🚀**

Accede a `http://localhost:8080` y comienza a gestionar tu colegio profesional.
