<?php ob_start(); ?>

<div class="page-header">
    <h1>Reporte de Deudores</h1>
    <a href="<?= url('reportes') ?>" class="btn btn-secondary">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>N° Colegiatura</th>
                    <th>DNI</th>
                    <th>Apellidos y Nombres</th>
                    <th>Cuotas Pendientes</th>
                    <th>Monto Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deudores as $deudor): ?>
                    <tr>
                        <td><?= e($deudor['numero_colegiatura']) ?></td>
                        <td><?= e($deudor['dni']) ?></td>
                        <td><?= e($deudor['apellido_paterno'] . ' ' . $deudor['apellido_materno'] . ', ' . $deudor['nombres']) ?></td>
                        <td><span class="badge badge-warning"><?= $deudor['cuotas_pendientes'] ?></span></td>
                        <td><?= formatMoney($deudor['monto_total']) ?></td>
                        <td>
                            <?php if ($deudor['habilitado']): ?>
                                <span class="badge badge-success">Habilitado</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inhabilitado</span>
                            <?php endif; ?>
                        </td>
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
