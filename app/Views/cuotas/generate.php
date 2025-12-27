<?php ob_start(); ?>

<div class="page-header">
    <h1>Generar Cuotas Mensuales</h1>
    <a href="<?= url('cuotas') ?>" class="btn btn-secondary">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="alert alert-warning">
            <strong>Atención:</strong> Este proceso generará las cuotas para todos los colegiados activos.
            Si las cuotas ya existen para el mes/año seleccionado, no se generarán duplicados.
        </div>

        <form method="POST" action="<?= url('cuotas/generar') ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Mes *</label>
                        <select name="mes" class="form-control" required>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>>
                                    <?= \Models\Cuota::getNombreMes($m) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Año *</label>
                        <select name="anio" class="form-control" required>
                            <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">Generar Cuotas</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
