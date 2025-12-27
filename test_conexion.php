<?php
/**
 * Script para Probar la Conexión a la Base de Datos
 * Ejecutar: php test_conexion.php
 * O acceder desde navegador: http://localhost/colegioprofesionalnew/test_conexion.php
 */

echo "<h1>Test de Conexión a Base de Datos</h1>";
echo "<hr>";

// Incluir el archivo de conexión
require_once __DIR__ . '/config/conexion.php';

echo "<h2>✓ Conexión Exitosa</h2>";
echo "<p><strong>Base de datos:</strong> " . htmlspecialchars($db_name) . "</p>";
echo "<p><strong>Host:</strong> " . htmlspecialchars($db_host) . ":" . htmlspecialchars($db_port) . "</p>";
echo "<p><strong>Usuario:</strong> " . htmlspecialchars($db_user) . "</p>";

// Probar una consulta
try {
    $stmt = $conexion->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "<p><strong>Versión MySQL:</strong> " . htmlspecialchars($result['version']) . "</p>";

    // Verificar tablas
    $stmt = $conexion->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h3>Tablas encontradas (" . count($tables) . "):</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . htmlspecialchars($table) . "</li>";
    }
    echo "</ul>";

    // Verificar usuario admin
    $stmt = $conexion->query("SELECT COUNT(*) as total FROM usuarios WHERE username = 'admin'");
    $result = $stmt->fetch();

    if ($result['total'] > 0) {
        echo "<p style='color: green;'><strong>✓ Usuario admin encontrado</strong></p>";
        echo "<p>Credenciales de acceso:</p>";
        echo "<ul>";
        echo "<li>Usuario: <strong>admin</strong></li>";
        echo "<li>Contraseña: <strong>admin123</strong></li>";
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'><strong>⚠ Usuario admin no encontrado</strong></p>";
        echo "<p>Asegúrate de haber importado el archivo database/schema.sql</p>";
    }

    echo "<hr>";
    echo "<h3 style='color: green;'>✓ ¡Todo funciona correctamente!</h3>";
    echo "<p><a href='public/index.php'>Ir al sistema →</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Error en la consulta:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><small>Para eliminar este archivo después de probar: <code>rm test_conexion.php</code></small></p>";
?>
