<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? APP_CONFIG['name'] ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="<?= url('') ?>"><?= APP_CONFIG['name'] ?></a>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
            <ul class="nav-menu">
                <li><a href="<?= url('') ?>">Dashboard</a></li>
                <li><a href="<?= url('personas') ?>">Personas</a></li>
                <li><a href="<?= url('colegiados') ?>">Colegiados</a></li>
                <li><a href="<?= url('cuotas') ?>">Cuotas</a></li>
                <li><a href="<?= url('caja') ?>">Caja</a></li>
                <li><a href="<?= url('reportes') ?>">Reportes</a></li>
            </ul>
            <div class="nav-user">
                <span>Usuario: <?= e($_SESSION['user_nombre']) ?> (<?= e($_SESSION['user_rol']) ?>)</span>
                <a href="<?= url('auth/logout') ?>" class="btn btn-sm btn-danger">Cerrar Sesión</a>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <div class="alert alert-<?= $type ?>">
                        <?= e($message) ?>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= APP_CONFIG['name'] ?>. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
