<?php ob_start(); ?>

<div class="page-header">
    <h1>Gestión de Colegiados</h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Buscar..." value="<?= e($search ?? '') ?>" class="form-control">
            <select name="habilitado" class="form-control">
                <option value="">Todos</option>
                <option value="1" <?= ($habilitado ?? '') == '1' ? 'selected' : '' ?>>Habilitados</option>
                <option value="0" <?= ($habilitado ?? '') == '0' ? 'selected' : '' ?>>Inhabilitados</option>
            </select>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>N° Colegiatura</th>
                    <th>DNI</th>
                    <th>Apellidos y Nombres</th>
                    <th>Especialidad</th>
                    <th>Meses Impagos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($colegiados as $col): ?>
                    <tr>
                        <td><?= e($col['numero_colegiatura']) ?></td>
                        <td><?= e($col['dni']) ?></td>
                        <td><?= e($col['apellido_paterno'] . ' ' . $col['apellido_materno'] . ', ' . $col['nombres']) ?></td>
                        <td><?= e($col['especialidad']) ?></td>
                        <td>
                            <?php if ($col['meses_impagos'] > 0): ?>
                                <span class="badge badge-danger"><?= $col['meses_impagos'] ?></span>
                            <?php else: ?>
                                <span class="badge badge-success">Al día</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($col['habilitado']): ?>
                                <span class="badge badge-success">Habilitado</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inhabilitado</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= url("colegiados/ver/{$col['id']}") ?>" class="btn btn-sm btn-info">Ver</a>
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
