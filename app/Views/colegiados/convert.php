<?php ob_start(); ?>

<div class="page-header">
    <h1>Convertir a Colegiado</h1>
    <a href="<?= url('personas') ?>" class="btn btn-secondary">Volver</a>
</div>

<div class="card">
    <div class="card-header">
        <h3>Datos de la Persona</h3>
    </div>
    <div class="card-body">
        <p><strong>DNI:</strong> <?= e($persona['dni']) ?></p>
        <p><strong>Nombre:</strong> <?= e($persona['apellido_paterno'] . ' ' . $persona['apellido_materno'] . ', ' . $persona['nombres']) ?></p>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3>Datos de Colegiatura</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url("colegiados/convertir/{$persona['id']}") ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Especialidad</label>
                        <input type="text" name="especialidad" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Subespecialidad</label>
                        <input type="text" name="subespecialidad" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Universidad</label>
                        <input type="text" name="universidad" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fecha de Colegiatura</label>
                        <input type="date" name="fecha_colegiatura" value="<?= date('Y-m-d') ?>" class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Convertir a Colegiado</button>
                <a href="<?= url('personas') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
