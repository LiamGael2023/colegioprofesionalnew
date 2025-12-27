# Uso del Archivo de Conexión Simple

Este proyecto incluye un archivo de conexión simplificado para facilitar el desarrollo de scripts PHP adicionales.

## 📁 Archivos de Conexión

### `config/conexion.php`
Archivo de conexión principal que:
- Carga las variables del archivo `.env`
- Crea una conexión PDO a MySQL
- Retorna el objeto `$conexion` listo para usar

### `test_conexion.php`
Script para probar que la conexión funciona correctamente.

### `ejemplo_uso_conexion.php`
Ejemplos prácticos de cómo usar la conexión.

## 🚀 Uso Básico

### 1. Incluir el archivo de conexión

```php
<?php
require_once __DIR__ . '/config/conexion.php';

// Ahora tienes disponible la variable $conexion
```

### 2. Ejecutar consultas SELECT

```php
// Consulta simple
$sql = "SELECT * FROM personas LIMIT 10";
$stmt = $conexion->query($sql);
$personas = $stmt->fetchAll();

foreach ($personas as $persona) {
    echo $persona['nombres'] . "<br>";
}
```

### 3. Consultas con parámetros (Prepared Statements)

```php
// Búsqueda por DNI
$dni = '12345678';
$sql = "SELECT * FROM personas WHERE dni = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$dni]);
$persona = $stmt->fetch();

if ($persona) {
    echo "Encontrado: " . $persona['nombres'];
}
```

### 4. INSERT

```php
$sql = "INSERT INTO personas (dni, apellido_paterno, apellido_materno, nombres)
        VALUES (?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$resultado = $stmt->execute([
    '87654321',
    'Pérez',
    'García',
    'Juan Carlos'
]);

if ($resultado) {
    $id = $conexion->lastInsertId();
    echo "Registro creado con ID: $id";
}
```

### 5. UPDATE

```php
$sql = "UPDATE personas SET email = ? WHERE dni = ?";
$stmt = $conexion->prepare($sql);
$resultado = $stmt->execute(['nuevo@email.com', '12345678']);

if ($resultado) {
    echo "Actualizado: " . $stmt->rowCount() . " filas";
}
```

### 6. DELETE

```php
$sql = "DELETE FROM personas WHERE dni = ?";
$stmt = $conexion->prepare($sql);
$resultado = $stmt->execute(['87654321']);

if ($resultado) {
    echo "Eliminado correctamente";
}
```

### 7. Transacciones

```php
try {
    $conexion->beginTransaction();

    // Operación 1
    $sql1 = "INSERT INTO personas (dni, nombres) VALUES (?, ?)";
    $stmt1 = $conexion->prepare($sql1);
    $stmt1->execute(['11111111', 'Juan']);

    // Operación 2
    $sql2 = "INSERT INTO colegiados (persona_id, numero_colegiatura) VALUES (?, ?)";
    $stmt2 = $conexion->prepare($sql2);
    $stmt2->execute([$conexion->lastInsertId(), '2024-9999']);

    // Confirmar todo
    $conexion->commit();

} catch (Exception $e) {
    // Revertir en caso de error
    $conexion->rollBack();
    echo "Error: " . $e->getMessage();
}
```

## 🔧 Funciones Helper

Puedes crear funciones auxiliares para facilitar el uso:

```php
// Función para ejecutar queries
function ejecutarQuery($sql, $params = []) {
    global $conexion;
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// Función para obtener un registro
function obtenerUno($sql, $params = []) {
    global $conexion;
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

// Función para contar
function contar($tabla, $where = '1=1', $params = []) {
    global $conexion;
    $sql = "SELECT COUNT(*) as total FROM {$tabla} WHERE {$where}";
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch()['total'];
}
```

## 🧪 Probar la Conexión

### Desde Navegador

```
http://localhost/colegioprofesionalnew/test_conexion.php
```

### Desde Terminal

```bash
php test_conexion.php
```

Deberías ver:
- ✓ Conexión exitosa
- Versión de MySQL
- Lista de tablas
- Usuario admin verificado

## 📝 Crear Scripts Personalizados

### Ejemplo: Script para listar deudores

Crea `mis_scripts/listar_deudores.php`:

```php
<?php
require_once __DIR__ . '/../config/conexion.php';

$sql = "SELECT p.dni, p.nombres, c.numero_colegiatura, c.meses_impagos
        FROM personas p
        INNER JOIN colegiados c ON c.persona_id = p.id
        WHERE c.meses_impagos > 0
        ORDER BY c.meses_impagos DESC";

$stmt = $conexion->query($sql);
$deudores = $stmt->fetchAll();

echo "Deudores encontrados: " . count($deudores) . "\n\n";

foreach ($deudores as $d) {
    echo "DNI: {$d['dni']} - {$d['nombres']} - Meses impagos: {$d['meses_impagos']}\n";
}
```

### Ejemplo: Script para generar reporte CSV

Crea `mis_scripts/exportar_colegiados.php`:

```php
<?php
require_once __DIR__ . '/../config/conexion.php';

$sql = "SELECT p.dni, p.apellido_paterno, p.apellido_materno, p.nombres,
               c.numero_colegiatura, c.especialidad
        FROM personas p
        INNER JOIN colegiados c ON c.persona_id = p.id
        WHERE c.habilitado = 1";

$stmt = $conexion->query($sql);

// Crear CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="colegiados.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['DNI', 'Apellidos', 'Nombres', 'N° Colegiatura', 'Especialidad']);

while ($row = $stmt->fetch()) {
    fputcsv($output, [
        $row['dni'],
        $row['apellido_paterno'] . ' ' . $row['apellido_materno'],
        $row['nombres'],
        $row['numero_colegiatura'],
        $row['especialidad']
    ]);
}

fclose($output);
```

## 🔒 Seguridad

El archivo de conexión implementa:

✅ **Prepared Statements**: Previene SQL Injection
✅ **PDO Exception Mode**: Captura errores
✅ **Charset UTF8MB4**: Soporte completo de caracteres
✅ **Variables de entorno**: Credenciales protegidas en `.env`

### Buenas Prácticas

```php
// ✅ CORRECTO - Usar prepared statements
$stmt = $conexion->prepare("SELECT * FROM personas WHERE dni = ?");
$stmt->execute([$dni]);

// ❌ INCORRECTO - Nunca concatenar variables directamente
$sql = "SELECT * FROM personas WHERE dni = '$dni'"; // NO HACER ESTO
```

## 📊 Compatibilidad

- ✅ Compatible con el sistema MVC principal
- ✅ Se puede usar en scripts independientes
- ✅ Funciona con Docker y MySQL local
- ✅ Lee configuración desde `.env` automáticamente

## 🆘 Solución de Problemas

### Error: "Could not connect to database"

1. Verifica que MySQL esté corriendo
2. Revisa las credenciales en `.env`
3. Ejecuta `test_conexion.php` para diagnosticar

### Error: "Table doesn't exist"

1. Asegúrate de haber importado `database/schema.sql`
2. Verifica que el nombre de la BD en `.env` sea correcto

### Error: "Access denied"

1. Verifica el usuario y contraseña en `.env`
2. Asegúrate de que el usuario tenga permisos en la BD

## 🎯 Resumen

```php
// 1. Incluir
require_once 'config/conexion.php';

// 2. Usar
$stmt = $conexion->prepare("SELECT * FROM personas");
$stmt->execute();
$datos = $stmt->fetchAll();

// 3. ¡Listo!
```

---

Para más ejemplos, consulta `ejemplo_uso_conexion.php`
