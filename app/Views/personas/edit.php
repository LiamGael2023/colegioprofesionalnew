<?php ob_start(); ?>

<div class="page-header">
    <h1>Editar Persona</h1>
    <a href="<?= url('personas') ?>" class="btn btn-secondary">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= url("personas/editar/{$persona['id']}") ?>">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>DNI</label>
                        <input type="text" value="<?= e($persona['dni']) ?>" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Apellido Paterno *</label>
                        <input type="text" name="apellido_paterno" value="<?= e($persona['apellido_paterno']) ?>" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Apellido Materno *</label>
                        <input type="text" name="apellido_materno" value="<?= e($persona['apellido_materno']) ?>" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Nombres *</label>
                        <input type="text" name="nombres" value="<?= e($persona['nombres']) ?>" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="<?= e($persona['fecha_nacimiento']) ?>" class="form-control">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sexo</label>
                        <select name="sexo" class="form-control">
                            <option value="">Seleccionar...</option>
                            <option value="M" <?= $persona['sexo'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $persona['sexo'] == 'F' ? 'selected' : '' ?>>Femenino</option>
                            <option value="O" <?= $persona['sexo'] == 'O' ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Celular</label>
                        <input type="text" name="celular" value="<?= e($persona['celular']) ?>" class="form-control">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="<?= e($persona['telefono']) ?>" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= e($persona['email']) ?>" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" name="direccion" value="<?= e($persona['direccion']) ?>" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Distrito</label>
                        <input type="text" name="distrito" value="<?= e($persona['distrito']) ?>" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Provincia</label>
                        <input type="text" name="provincia" value="<?= e($persona['provincia']) ?>" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Departamento</label>
                        <input type="text" name="departamento" value="<?= e($persona['departamento']) ?>" class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="<?= url('personas') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
