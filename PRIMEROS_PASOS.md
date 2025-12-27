# 🚀 Primeros Pasos - Sistema de Gestión de Colegio Profesional

Sigue estos pasos **EN ORDEN** para poner el sistema en funcionamiento.

## ⚠️ IMPORTANTE: Crear el archivo .env

El archivo `.env` **NO está en el repositorio** por seguridad. Debes crearlo manualmente.

### 🪟 **Windows:**

#### Opción 1: Script automático
```bash
crear_env.bat
```

#### Opción 2: Manual
Crea un archivo llamado `.env` en la raíz del proyecto con este contenido:

```env
# Database Configuration
DB_HOST=localhost
DB_PORT=3307
DB_NAME=colegio_profesional
DB_USER=root
DB_PASS=

# Application Configuration
APP_NAME="Sistema de Gestión de Colegio Profesional"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8080

# Session Configuration
SESSION_LIFETIME=7200
SESSION_NAME=COLEGIO_SESSION

# Security
SECURITY_SALT=change_this_to_random_string_in_production

# Cuotas Configuration
CUOTA_MENSUAL=150.00
CUOTA_TOLERANCIA_MESES=3

# Timezone
APP_TIMEZONE=America/Lima
```

### 🐧 **Linux/Mac:**

#### Opción 1: Script automático
```bash
bash crear_env.sh
```

#### Opción 2: Desde .env.example
```bash
cp .env.example .env
```

Luego edita `.env` y cambia:
```env
DB_HOST=localhost
DB_PORT=3307
DB_USER=root
DB_PASS=
```

## 📋 Pasos Completos de Instalación

### Paso 1: ✅ Crear archivo .env
**Ya lo hiciste arriba** ↑

### Paso 2: 🗄️ Crear Base de Datos

Abre **phpMyAdmin** o tu cliente MySQL y ejecuta:

```sql
CREATE DATABASE colegio_profesional
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

### Paso 3: 📥 Importar Schema

#### Desde phpMyAdmin:
1. Selecciona la base de datos `colegio_profesional`
2. Clic en la pestaña **"Importar"**
3. Selecciona el archivo: `database/schema.sql`
4. Clic en **"Continuar"**

#### Desde Terminal:
```bash
mysql -u root -p colegio_profesional < database/schema.sql
```

### Paso 4: 🧪 Probar Conexión

Accede desde el navegador:
```
http://localhost/colegioprofesionalnew/test_conexion.php
```

Deberías ver:
- ✓ Conexión exitosa
- Versión de MySQL
- 9 tablas encontradas
- Usuario admin encontrado

### Paso 5: 🎯 Acceder al Sistema

```
URL: http://localhost/colegioprofesionalnew
Usuario: admin
Contraseña: admin123
```

Si te redirige a `public/index.php`, el sistema está funcionando correctamente.

### Paso 6 (Opcional): 📊 Cargar Datos de Prueba

```bash
mysql -u root -p colegio_profesional < database/seeders/datos_prueba.sql
```

Esto carga:
- 8 personas (5 colegiados, 3 público general)
- 30 cuotas
- 5 pagos
- 5 recibos

## 🔧 Ajustar Configuración

Si tu MySQL usa **puerto 3306** (no 3307), edita `.env`:

```env
DB_PORT=3306
```

Si tienes **contraseña en MySQL**, edita `.env`:

```env
DB_PASS=tu_password_aqui
```

## ❗ Solución de Problemas

### Error: "Archivo .env no encontrado"

1. Verifica que el archivo `.env` existe:
   ```bash
   # Windows (CMD)
   dir .env

   # Linux/Mac
   ls -la .env
   ```

2. Si no existe, créalo con los scripts:
   ```bash
   # Windows
   crear_env.bat

   # Linux/Mac
   bash crear_env.sh
   ```

### Error: "Could not connect to database"

1. Verifica que MySQL está corriendo
2. Revisa las credenciales en `.env`
3. Ejecuta `test_conexion.php` para diagnosticar

### Error: "404 Not Found" o archivos no cargan

**Para XAMPP:**

Edita: `C:\xampp\apache\conf\httpd.conf`

Busca:
```apache
<Directory "C:/xampp/htdocs">
    AllowOverride None
```

Cambia a:
```apache
<Directory "C:/xampp/htdocs">
    AllowOverride All
```

Reinicia Apache.

### Error: "Class not found" o "require_once failed"

Asegúrate de que estás accediendo desde la raíz del proyecto:
```
✓ Correcto: http://localhost/colegioprofesionalnew
✗ Incorrecto: http://localhost/colegioprofesionalnew/public
```

## 📁 Estructura del Proyecto

```
colegioprofesionalnew/
├── .env                    ← ¡Debes crear este archivo!
├── .env.example            ← Plantilla del .env
├── crear_env.bat           ← Script para Windows
├── crear_env.sh            ← Script para Linux/Mac
├── test_conexion.php       ← Probar conexión
├── database/
│   └── schema.sql          ← Importar este archivo
├── public/
│   └── index.php           ← Punto de entrada
└── config/
    └── conexion.php        ← Archivo de conexión
```

## ✅ Checklist de Instalación

- [ ] Crear archivo `.env` (con scripts o manual)
- [ ] Crear base de datos `colegio_profesional`
- [ ] Importar `database/schema.sql`
- [ ] Probar conexión con `test_conexion.php`
- [ ] Acceder al sistema: `http://localhost/colegioprofesionalnew`
- [ ] Login con `admin` / `admin123`
- [ ] (Opcional) Importar datos de prueba

## 🆘 ¿Necesitas Ayuda?

1. **Ejecuta primero:** `test_conexion.php`
2. **Revisa los logs:** `logs/app.log`
3. **Consulta:** `INSTALACION_LOCAL.md`
4. **Ejemplos de código:** `ejemplo_uso_conexion.php`

---

**¡Sigue estos pasos y el sistema funcionará!** 🚀
