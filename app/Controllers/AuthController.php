<?php

namespace Controllers;

use Core\Controller;
use Models\Usuario;
use Models\Auditoria;

/**
 * Controlador de Autenticación
 */
class AuthController extends Controller {

    /**
     * Mostrar formulario de login
     */
    public function login() {
        // Si ya está autenticado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            redirect(url(''));
        }

        $this->view('auth.login');
    }

    /**
     * Procesar login
     */
    public function doLogin() {
        if (!$this->isPost()) {
            redirect(url('auth/login'));
        }

        // Obtener datos
        $username = sanitize($this->post('username'));
        $password = $this->post('password');

        // Validar
        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Usuario y contraseña son requeridos');
            redirect(url('auth/login'));
        }

        // Autenticar
        $usuarioModel = new Usuario();
        $user = $usuarioModel->authenticate($username, $password);

        if (!$user) {
            // Registrar intento fallido en auditoría
            $auditoriaModel = new Auditoria();
            $auditoriaModel->registrar(
                null,
                'AUTH',
                'LOGIN_FALLIDO',
                null,
                null,
                null,
                ['username' => $username]
            );

            $this->setFlash('error', 'Credenciales inválidas');
            redirect(url('auth/login'));
        }

        // Establecer sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_nombre'] = $user['nombre_completo'];
        $_SESSION['user_rol'] = $user['rol'];
        $_SESSION['user_email'] = $user['email'];

        // Registrar login exitoso en auditoría
        $auditoriaModel = new Auditoria();
        $auditoriaModel->registrar(
            $user['id'],
            'AUTH',
            'LOGIN_EXITOSO',
            'usuarios',
            $user['id']
        );

        // Redirigir al dashboard
        redirect(url(''));
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        // Registrar logout en auditoría
        if (isset($_SESSION['user_id'])) {
            $auditoriaModel = new Auditoria();
            $auditoriaModel->registrar(
                $_SESSION['user_id'],
                'AUTH',
                'LOGOUT'
            );
        }

        // Destruir sesión
        session_destroy();

        // Redirigir al login
        redirect(url('auth/login'));
    }
}
