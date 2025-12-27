<?php
/**
 * Script CRON - Generar Cuotas Mensuales
 * Ejecutar el día 1 de cada mes
 * Crontab: 0 0 1 * * /usr/local/bin/php /var/www/html/cron/generar_cuotas.php
 */

// Definir path raíz
define('APP_PATH', dirname(__DIR__));

// Cargar configuración
require_once APP_PATH . '/config/config.php';

// Registrar inicio
logger('=== INICIO GENERACIÓN AUTOMÁTICA DE CUOTAS ===', 'INFO', 'cron.log');

try {
    // Instanciar modelo de cuotas
    $cuotaModel = new \Models\Cuota();

    // Generar cuotas del mes actual
    $mes = (int) date('n');
    $anio = (int) date('Y');

    logger("Generando cuotas para {$mes}/{$anio}", 'INFO', 'cron.log');

    $resultado = $cuotaModel->generarCuotasMensuales($mes, $anio);

    logger(
        "Resultado: {$resultado['generadas']} cuotas generadas de {$resultado['total_colegiados']} colegiados. Errores: {$resultado['errores']}",
        'INFO',
        'cron.log'
    );

    // Marcar cuotas vencidas
    logger('Marcando cuotas vencidas...', 'INFO', 'cron.log');
    $cuotaModel->marcarVencidas();
    logger('Cuotas vencidas actualizadas', 'INFO', 'cron.log');

    // Actualizar meses impagos de todos los colegiados
    logger('Actualizando meses impagos de colegiados...', 'INFO', 'cron.log');

    $colegiadoModel = new \Models\Colegiado();
    $colegiados = $colegiadoModel->all();

    foreach ($colegiados as $colegiado) {
        $pendientes = $cuotaModel->count(
            "persona_id = ? AND estado = 'PENDIENTE'",
            [$colegiado['persona_id']]
        );

        $colegiadoModel->actualizarMesesImpagos($colegiado['id'], $pendientes);
    }

    logger('Meses impagos actualizados', 'INFO', 'cron.log');

    // Registrar en auditoría
    $auditoriaModel = new \Models\Auditoria();
    $auditoriaModel->registrar(
        null,
        'CRON',
        'GENERAR_CUOTAS_AUTOMATICO',
        'cuotas',
        null,
        null,
        [
            'mes' => $mes,
            'anio' => $anio,
            'resultado' => $resultado
        ]
    );

    logger('=== FIN GENERACIÓN AUTOMÁTICA DE CUOTAS (EXITOSO) ===', 'INFO', 'cron.log');

    echo "Proceso completado exitosamente\n";
    echo "Cuotas generadas: {$resultado['generadas']}\n";
    echo "Total colegiados: {$resultado['total_colegiados']}\n";
    echo "Errores: {$resultado['errores']}\n";

    exit(0);

} catch (\Exception $e) {
    logger('ERROR en generación de cuotas: ' . $e->getMessage(), 'ERROR', 'cron.log');
    logger('=== FIN GENERACIÓN AUTOMÁTICA DE CUOTAS (CON ERRORES) ===', 'ERROR', 'cron.log');

    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
