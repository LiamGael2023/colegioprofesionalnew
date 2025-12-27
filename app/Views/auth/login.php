<?php ob_start(); ?>

<div class="login-container">
    <div class="login-box">
        <h1>Iniciar Sesión</h1>

        <?php if ($error = $this->getFlash('error')): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('auth/login') ?>">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
        </form>

        <div class="login-info">
            <p><strong>Credenciales por defecto:</strong></p>
            <p>Usuario: <code>admin</code></p>
            <p>Contraseña: <code>admin123</code></p>
        </div>
    </div>
</div>

<style>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 200px);
}
.login-box {
    width: 100%;
    max-width: 400px;
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.login-info {
    margin-top: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 0.9rem;
}
</style>

<?php
$content = ob_get_clean();
$title = 'Iniciar Sesión';
require __DIR__ . '/../layouts/main.php';
?>
