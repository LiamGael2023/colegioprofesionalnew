<?php ob_start(); ?>

<div class="page-header">
    <h1>Gestión de Personas</h1>
    <a href="<?= url('personas/crear') ?>" class="btn btn-primary">Nueva Persona</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Buscar por DNI o nombre..." value="<?= e($search ?? '') ?>" class="form-control">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Apellidos y Nombres</th>
                    <th>Celular</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($personas as $persona): ?>
                    <tr>
                        <td><?= e($persona['dni']) ?></td>
                        <td><?= e($persona['apellido_paterno'] . ' ' . $persona['apellido_materno'] . ', ' . $persona['nombres']) ?></td>
                        <td><?= e($persona['celular']) ?></td>
                        <td><?= e($persona['email']) ?></td>
                        <td>
                            <?php if ($persona['es_colegiado']): ?>
                                <span class="badge badge-success">Colegiado</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Público</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= url("personas/editar/{$persona['id']}") ?>" class="btn btn-sm btn-info">Editar</a>
                            <?php if (!$persona['es_colegiado']): ?>
                                <a href="<?= url("colegiados/convertir/{$persona['id']}") ?>" class="btn btn-sm btn-success">Convertir a Colegiado</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?><?= $search ? "&search={$search}" : '' ?>"
                       class="btn btn-sm <?= $i == $pagination['page'] ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
