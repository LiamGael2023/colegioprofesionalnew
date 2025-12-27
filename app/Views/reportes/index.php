<?php ob_start(); ?>

<div class="page-header">
    <h1>Reportes</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3>Deudores</h3>
            </div>
            <div class="card-body">
                <p>Listado de colegiados con cuotas pendientes, ordenados por monto de deuda.</p>
                <a href="<?= url('reportes/deudores') ?>" class="btn btn-primary">Ver Reporte</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3>Recaudación</h3>
            </div>
            <div class="card-body">
                <p>Reporte de recaudación por período, con totales y estadísticas.</p>
                <a href="<?= url('reportes/recaudacion') ?>" class="btn btn-primary">Ver Reporte</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3>Estado de Cuenta</h3>
            </div>
            <div class="card-body">
                <p>Buscar estado de cuenta individual de un colegiado específico.</p>
                <form method="GET" action="<?= url('caja/buscar') ?>">
                    <input type="text" name="dni" placeholder="DNI" class="form-control mb-2" required>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
