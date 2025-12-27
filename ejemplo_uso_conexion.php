<?php
/**
 * Ejemplo de Uso del Archivo de Conexión
 * Sistema de Gestión de Colegio Profesional
 */

// =====================================================
// EJEMPLO 1: Incluir y usar la conexión
// =====================================================

require_once __DIR__ . '/config/conexion.php';

// Ahora tienes disponible la variable $conexion

// =====================================================
// EJEMPLO 2: Consulta SELECT
// =====================================================

// Obtener todos los colegiados
$sql = "SELECT * FROM personas WHERE es_colegiado = 1 LIMIT 10";
$stmt = $conexion->query($sql);
$colegiados = $stmt->fetchAll();

echo "<h2>Colegiados Registrados:</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>DNI</th><th>Nombre Completo</th><th>Email</th></tr>";

foreach ($colegiados as $col) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($col['dni']) . "</td>";
    echo "<td>" . htmlspecialchars($col['apellido_paterno'] . ' ' . $col['apellido_materno'] . ', ' . $col['nombres']) . "</td>";
    echo "<td>" . htmlspecialchars($col['email']) . "</td>";
    echo "</tr>";
}

echo "</table>";

// =====================================================
// EJEMPLO 3: Consulta con parámetros (Prepared Statement)
// =====================================================

$dni_buscar = '12345678';

$sql = "SELECT * FROM personas WHERE dni = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$dni_buscar]);
$persona = $stmt->fetch();

if ($persona) {
    echo "<h2>Persona encontrada:</h2>";
    echo "<p><strong>Nombre:</strong> " . htmlspecialchars($persona['nombres']) . "</p>";
    echo "<p><strong>DNI:</strong> " . htmlspecialchars($persona['dni']) . "</p>";
} else {
    echo "<p>No se encontró persona con DNI: $dni_buscar</p>";
}

// =====================================================
// EJEMPLO 4: INSERT
// =====================================================

/*
$sql = "INSERT INTO personas (dni, apellido_paterno, apellido_materno, nombres, creado_por)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$resultado = $stmt->execute([
    '87654321',
    'Pérez',
    'García',
    'Carlos Alberto',
    1
]);

if ($resultado) {
    echo "Persona registrada con ID: " . $conexion->lastInsertId();
}
*/

// =====================================================
// EJEMPLO 5: UPDATE
// =====================================================

/*
$sql = "UPDATE personas SET celular = ?, email = ? WHERE dni = ?";
$stmt = $conexion->prepare($sql);
$resultado = $stmt->execute(['999888777', 'nuevo@email.com', '12345678']);

if ($resultado) {
    echo "Persona actualizada correctamente";
}
*/

// =====================================================
// EJEMPLO 6: DELETE
// =====================================================

/*
$sql = "DELETE FROM personas WHERE dni = ? AND es_colegiado = 0";
$stmt = $conexion->prepare($sql);
$resultado = $stmt->execute(['87654321']);

if ($resultado) {
    echo "Persona eliminada correctamente";
}
*/

// =====================================================
// EJEMPLO 7: Transacciones
// =====================================================

/*
try {
    $conexion->beginTransaction();

    // Múltiples operaciones
    $sql1 = "INSERT INTO personas (dni, apellido_paterno, apellido_materno, nombres) VALUES (?, ?, ?, ?)";
    $stmt1 = $conexion->prepare($sql1);
    $stmt1->execute(['11111111', 'López', 'Ruiz', 'Ana María']);

    $sql2 = "INSERT INTO colegiados (persona_id, numero_colegiatura, fecha_colegiatura) VALUES (?, ?, ?)";
    $stmt2 = $conexion->prepare($sql2);
    $stmt2->execute([$conexion->lastInsertId(), '2024-9999', date('Y-m-d')]);

    // Si todo salió bien, confirmar
    $conexion->commit();
    echo "Transacción completada";

} catch (Exception $e) {
    // Si hubo error, revertir
    $conexion->rollBack();
    echo "Error: " . $e->getMessage();
}
*/

// =====================================================
// EJEMPLO 8: Función helper para queries simples
// =====================================================

function ejecutarQuery($sql, $params = []) {
    global $conexion;

    try {
        $stmt = $conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        die("Error en query: " . $e->getMessage());
    }
}

// Uso:
// $resultados = ejecutarQuery("SELECT * FROM personas WHERE dni = ?", ['12345678'])->fetchAll();

// =====================================================
// EJEMPLO 9: Función para obtener un registro
// =====================================================

function obtenerUno($sql, $params = []) {
    global $conexion;

    try {
        $stmt = $conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// Uso:
// $persona = obtenerUno("SELECT * FROM personas WHERE dni = ?", ['12345678']);

// =====================================================
// EJEMPLO 10: Función para contar registros
// =====================================================

function contar($tabla, $condicion = '1=1', $params = []) {
    global $conexion;

    $sql = "SELECT COUNT(*) as total FROM {$tabla} WHERE {$condicion}";
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $resultado = $stmt->fetch();
    return $resultado['total'];
}

// Uso:
$total_colegiados = contar('personas', 'es_colegiado = 1');
echo "<h2>Total de colegiados: $total_colegiados</h2>";

?>
