<?php ob_start(); ?>

<div class="page-header">
    <h1>Detalle de Colegiado</h1>
    <a href="<?= url('colegiados') ?>" class="btn btn-secondary">Volver</a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Datos Personales</h3>
            </div>
            <div class="card-body">
                <p><strong>DNI:</strong> <?= e($persona['dni']) ?></p>
                <p><strong>Nombre:</strong> <?= e($persona['apellido_paterno'] . ' ' . $persona['apellido_materno'] . ', ' . $persona['nombres']) ?></p>
                <p><strong>Email:</strong> <?= e($persona['email']) ?></p>
                <p><strong>Celular:</strong> <?= e($persona['celular']) ?></p>
                <p><strong>Dirección:</strong> <?= e($persona['direccion']) ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Datos de Colegiatura</h3>
            </div>
            <div class="card-body">
                <p><strong>N° Colegiatura:</strong> <?= e($colegiado['numero_colegiatura']) ?></p>
                <p><strong>Especialidad:</strong> <?= e($colegiado['especialidad']) ?></p>
                <p><strong>Universidad:</strong> <?= e($colegiado['universidad']) ?></p>
                <p><strong>Fecha Colegiatura:</strong> <?= formatDate($colegiado['fecha_colegiatura']) ?></p>
                <p><strong>Estado:</strong>
                    <?php if ($colegiado['habilitado']): ?>
                        <span class="badge badge-success">Habilitado</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Inhabilitado</span>
                    <?php endif; ?>
                </p>
                <p><strong>Meses Impagos:</strong> <span class="badge badge-<?= $colegiado['meses_impagos'] > 0 ? 'danger' : 'success' ?>"><?= $colegiado['meses_impagos'] ?></span></p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3>Historial de Cuotas</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($resumen) && $resumen['total_pendientes'] > 0): ?>
            <div class="alert alert-warning">
                <strong>Deuda Pendiente:</strong> <?= formatMoney($resumen['monto_total']) ?> (<?= $resumen['total_pendientes'] ?> cuotas)
            </div>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Mes/Año</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cuotas as $cuota): ?>
                    <tr>
                        <td><?= \Models\Cuota::getNombreMes($cuota['mes']) ?> <?= $cuota['anio'] ?></td>
                        <td><?= formatMoney($cuota['monto']) ?></td>
                        <td>
                            <span class="badge badge-<?= $cuota['estado'] == 'PAGADO' ? 'success' : ($cuota['estado'] == 'VENCIDO' ? 'danger' : 'warning') ?>">
                                <?= $cuota['estado'] ?>
                            </span>
                        </td>
                        <td><?= $cuota['fecha_pago'] ? formatDate($cuota['fecha_pago']) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
