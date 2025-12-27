<?php ob_start(); ?>

<div class="page-header">
    <h1>Reporte de Recaudación</h1>
    <a href="<?= url('reportes') ?>" class="btn btn-secondary">Volver</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Filtrar por Fecha</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <label>Desde:</label>
            <input type="date" name="desde" value="<?= e($desde) ?>" class="form-control mx-2">
            <label>Hasta:</label>
            <input type="date" name="hasta" value="<?= e($hasta) ?>" class="form-control mx-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3>Recaudación Total: <?= formatMoney($total) ?></h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cantidad de Pagos</th>
                    <th>Monto Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recaudacion as $rec): ?>
                    <tr>
                        <td><?= formatDate($rec['fecha']) ?></td>
                        <td><?= $rec['cantidad'] ?></td>
                        <td><?= formatMoney($rec['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td>TOTAL</td>
                    <td><?= array_sum(array_column($recaudacion, 'cantidad')) ?></td>
                    <td><?= formatMoney($total) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
