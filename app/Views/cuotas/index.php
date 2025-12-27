<?php ob_start(); ?>

<div class="page-header">
    <h1>Gestión de Cuotas</h1>
    <a href="<?= url('cuotas/generar') ?>" class="btn btn-primary">Generar Cuotas Mensuales</a>
</div>

<div class="stats-grid">
    <div class="stat-card stat-warning">
        <h3>Cuotas Pendientes</h3>
        <p class="stat-value"><?= number_format($cuotas_pendientes) ?></p>
    </div>

    <div class="stat-card stat-danger">
        <h3>Cuotas Vencidas</h3>
        <p class="stat-value"><?= number_format($cuotas_vencidas) ?></p>
    </div>

    <div class="stat-card stat-info">
        <h3>Monto Pendiente</h3>
        <p class="stat-value"><?= formatMoney($monto_pendiente) ?></p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3>Información</h3>
    </div>
    <div class="card-body">
        <p>Las cuotas se generan automáticamente el día 1 de cada mes mediante un script CRON.</p>
        <p>También puede generar cuotas manualmente desde el botón "Generar Cuotas Mensuales".</p>
        <p><strong>Monto de cuota actual:</strong> <?= formatMoney(CUOTAS_CONFIG['monto_default']) ?></p>
        <p><strong>Tolerancia de impago:</strong> <?= CUOTAS_CONFIG['tolerancia_meses'] ?> meses</p>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
