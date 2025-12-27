<?php ob_start(); ?>

<div class="page-header">
    <h1>Caja - Procesar Pagos</h1>
</div>

<div class="card">
    <div class="card-header">
        <h3>Buscar Persona por DNI</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('caja/buscar') ?>">
            <div class="form-group">
                <input type="text" name="dni" placeholder="Ingrese DNI" class="form-control" maxlength="8" required autofocus>
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($persona)): ?>
    <div class="card mt-3">
        <div class="card-header">
            <h3>Datos de la Persona</h3>
        </div>
        <div class="card-body">
            <p><strong>DNI:</strong> <?= e($persona['dni']) ?></p>
            <p><strong>Nombre:</strong> <?= e($persona['apellido_paterno'] . ' ' . $persona['apellido_materno'] . ', ' . $persona['nombres']) ?></p>
        </div>
    </div>

    <?php if (!empty($cuotas_pendientes)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3>Cuotas Pendientes</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Total Deuda:</strong> <?= formatMoney($resumen_deuda['monto_total'] ?? 0) ?> (<?= $resumen_deuda['total_pendientes'] ?? 0 ?> cuotas)
                </div>

                <form method="POST" action="<?= url('caja/procesar-pago') ?>" id="formPago">
                    <input type="hidden" name="persona_id" value="<?= $persona['id'] ?>">

                    <table class="table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Mes/Año</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cuotas_pendientes as $cuota): ?>
                                <tr>
                                    <td><input type="checkbox" name="cuotas_ids[]" value="<?= $cuota['id'] ?>" class="cuota-check"></td>
                                    <td><?= \Models\Cuota::getNombreMes($cuota['mes']) ?> <?= $cuota['anio'] ?></td>
                                    <td><?= formatMoney($cuota['monto']) ?></td>
                                    <td><span class="badge badge-warning"><?= $cuota['estado'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Método de Pago *</label>
                                <select name="metodo_pago" class="form-control" required>
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="TRANSFERENCIA">Transferencia</option>
                                    <option value="TARJETA">Tarjeta</option>
                                    <option value="YAPE">Yape</option>
                                    <option value="PLIN">Plin</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>N° Operación</label>
                                <input type="text" name="numero_operacion" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg">Procesar Pago</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-success mt-3">No tiene cuotas pendientes</div>
    <?php endif; ?>

    <?php if (!empty($historial_pagos)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3>Historial de Pagos</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Cajero</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial_pagos as $pago): ?>
                            <tr>
                                <td><?= formatDate($pago['fecha_pago'], 'd/m/Y H:i') ?></td>
                                <td><?= formatMoney($pago['monto_total']) ?></td>
                                <td><?= e($pago['metodo_pago']) ?></td>
                                <td><?= e($pago['cajero']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.cuota-check').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
