<?php ob_start(); ?>

<h1>Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Personas</h3>
        <p class="stat-value"><?= number_format($stats['total_personas']) ?></p>
    </div>

    <div class="stat-card">
        <h3>Total Colegiados</h3>
        <p class="stat-value"><?= number_format($stats['total_colegiados']) ?></p>
    </div>

    <div class="stat-card stat-success">
        <h3>Habilitados</h3>
        <p class="stat-value"><?= number_format($stats['colegiados_habilitados']) ?></p>
    </div>

    <div class="stat-card stat-danger">
        <h3>Inhabilitados</h3>
        <p class="stat-value"><?= number_format($stats['colegiados_inhabilitados']) ?></p>
    </div>

    <div class="stat-card stat-warning">
        <h3>Cuotas Pendientes</h3>
        <p class="stat-value"><?= number_format($stats['cuotas_pendientes']) ?></p>
    </div>

    <div class="stat-card stat-danger">
        <h3>Cuotas Vencidas</h3>
        <p class="stat-value"><?= number_format($stats['cuotas_vencidas']) ?></p>
    </div>

    <div class="stat-card stat-success">
        <h3>Recaudación Hoy</h3>
        <p class="stat-value"><?= formatMoney($stats['recaudacion_hoy']) ?></p>
    </div>

    <div class="stat-card stat-info">
        <h3>Recaudación Mes</h3>
        <p class="stat-value"><?= formatMoney($stats['recaudacion_mes']) ?></p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Últimos Pagos</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($ultimos_pagos)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Colegiado</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimos_pagos as $pago): ?>
                                <tr>
                                    <td><?= formatDate($pago['fecha_pago'], 'd/m/Y H:i') ?></td>
                                    <td><?= e($pago['apellido_paterno'] . ' ' . $pago['apellido_materno']) ?></td>
                                    <td><?= formatMoney($pago['monto_total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay pagos registrados</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Principales Deudores</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($deudores)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Colegiado</th>
                                <th>Meses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deudores as $deudor): ?>
                                <tr>
                                    <td><?= e($deudor['apellido_paterno'] . ' ' . $deudor['apellido_materno']) ?></td>
                                    <td><span class="badge badge-danger"><?= $deudor['meses_impagos'] ?> meses</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay deudores</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Dashboard';
require __DIR__ . '/../layouts/main.php';
?>
