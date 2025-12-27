# Instalación Local (Sin Docker)

Si prefieres usar MySQL local en lugar de Docker, sigue estos pasos:

## 📋 Requisitos

- PHP 8.1 o superior
- MySQL 8.0 o superior (o XAMPP/WAMP/MAMP)
- Apache con mod_rewrite habilitado
- Extensiones PHP: pdo, pdo_mysql, mysqli

## 🚀 Pasos de Instalación

### 1. Configurar la Base de Datos

Abre tu cliente MySQL (phpMyAdmin, MySQL Workbench, o terminal):

```sql
-- Crear base de datos
CREATE DATABASE colegio_profesional CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Si usas un usuario específico (opcional):
CREATE USER 'colegio_user'@'localhost' IDENTIFIED BY 'tu_password';
GRANT ALL PRIVILEGES ON colegio_profesional.* TO 'colegio_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Importar el Schema

Desde la línea de comandos:

```bash
mysql -u root -p colegio_profesional < database/schema.sql
```

O desde phpMyAdmin:
1. Selecciona la base de datos `colegio_profesional`
2. Ve a la pestaña "Importar"
3. Selecciona el archivo `database/schema.sql`
4. Haz clic en "Continuar"

### 3. Configurar .env

Copia y edita el archivo de configuración:

```bash
cp .env.example .env
```

Edita `.env` con tus credenciales locales:

```env
# Para MySQL Local
DB_HOST=localhost        # o 127.0.0.1
DB_PORT=3306            # o 3307 si usas puerto diferente
DB_NAME=colegio_profesional
DB_USER=root            # o tu usuario MySQL
DB_PASS=                # tu password (vacío si no tienes)
```

### 4. Configurar Apache

#### Opción A: XAMPP/WAMP/MAMP

1. Copia el proyecto a la carpeta `htdocs`:
   ```bash
   # XAMPP Windows
   C:\xampp\htdocs\colegioprofesionalnew

   # XAMPP Linux/Mac
   /opt/lampp/htdocs/colegioprofesionalnew

   # WAMP
   C:\wamp64\www\colegioprofesionalnew

   # MAMP
   /Applications/MAMP/htdocs/colegioprofesionalnew
   ```

2. Accede a: `http://localhost/colegioprofesionalnew`

#### Opción B: Virtual Host (Recomendado)

Edita el archivo de configuración de Apache:

**Windows XAMPP:** `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
**Linux:** `/etc/apache2/sites-available/colegio.conf`
**Mac:** `/Applications/MAMP/conf/apache/extra/httpd-vhosts.conf`

Agrega:

```apache
<VirtualHost *:80>
    ServerName colegio.local
    DocumentRoot "C:/xampp/htdocs/colegioprofesionalnew/public"
    # O la ruta correspondiente en tu sistema

    <Directory "C:/xampp/htdocs/colegioprofesionalnew/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/colegio-error.log"
    CustomLog "logs/colegio-access.log" common
</VirtualHost>
```

Edita el archivo `hosts`:

**Windows:** `C:\Windows\System32\drivers\etc\hosts`
**Linux/Mac:** `/etc/hosts`

Agrega:
```
127.0.0.1   colegio.local
```

Reinicia Apache y accede a: `http://colegio.local`

### 5. Verificar Permisos

Asegúrate de que Apache tenga permisos de escritura en:

```bash
chmod -R 775 logs/
chmod -R 775 public/uploads/  # si existe
```

### 6. Cargar Datos de Prueba (Opcional)

```bash
mysql -u root -p colegio_profesional < database/seeders/datos_prueba.sql
```

### 7. Acceder al Sistema

```
URL: http://localhost/colegioprofesionalnew (o http://colegio.local)
Usuario: admin
Contraseña: admin123
```

## 🔧 Configuración del CRON (Generación Automática)

### Windows (Programador de Tareas)

1. Abre "Programador de tareas"
2. Crear tarea básica
3. Nombre: "Generar Cuotas Colegio"
4. Desencadenador: Mensualmente, día 1, a las 00:00
5. Acción: Iniciar programa
   - Programa: `C:\xampp\php\php.exe`
   - Argumentos: `C:\xampp\htdocs\colegioprofesionalnew\cron\generar_cuotas.php`

### Linux/Mac (crontab)

```bash
crontab -e
```

Agrega:
```cron
0 0 1 * * /usr/bin/php /ruta/completa/colegioprofesionalnew/cron/generar_cuotas.php >> /ruta/completa/logs/cron.log 2>&1
```

## 🐛 Solución de Problemas

### Error: "Could not connect to database"

- Verifica que MySQL esté corriendo
- Comprueba las credenciales en `.env`
- Verifica el puerto (3306 o 3307)

### Error: "404 Not Found" o CSS no carga

- Verifica que `mod_rewrite` esté habilitado
- Comprueba que el archivo `.htaccess` exista en `public/`
- En XAMPP: edita `httpd.conf` y busca `AllowOverride None` → cámbialo a `AllowOverride All`

### Error: "Cannot write to logs/"

```bash
chmod -R 777 logs/
```

### Error: "Class not found"

Verifica que las extensiones PHP estén habilitadas en `php.ini`:
```ini
extension=pdo_mysql
extension=mysqli
```

## 📞 Soporte

Si tienes problemas, revisa:
1. Logs de Apache: `xampp/apache/logs/error.log`
2. Logs del sistema: `logs/app.log`
3. Error de PHP: Habilita display_errors en `php.ini` para desarrollo

---

**¡Listo para usar!** 🚀
