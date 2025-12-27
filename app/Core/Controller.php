<?php

namespace Core;

/**
 * Clase Controller - Controlador base
 */
class Controller {
    protected $db;

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Cargar vista
     */
    protected function view($view, $data = []) {
        // Extraer datos para usar en la vista
        extract($data);

        // Ruta del archivo de vista
        $viewFile = PATHS['views'] . '/' . str_replace('.', '/', $view) . '.php';

        // Verificar que existe la vista
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Vista no encontrada: {$viewFile}");
        }
    }

    /**
     * Cargar modelo
     */
    protected function model($model) {
        $modelClass = "Models\\{$model}";

        if (class_exists($modelClass)) {
            return new $modelClass();
        } else {
            die("Modelo no encontrado: {$modelClass}");
        }
    }

    /**
     * Retornar JSON
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Validar sesión
     */
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            redirect(url('auth/login'));
        }
    }

    /**
     * Validar rol
     */
    protected function requireRole($role) {
        $this->requireAuth();

        if ($_SESSION['user_rol'] !== $role) {
            $this->json(['error' => 'Acceso denegado'], 403);
        }
    }

    /**
     * Obtener usuario actual
     */
    protected function getUser() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Verificar si es POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verificar si es GET
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Obtener datos POST
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Obtener datos GET
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Validar CSRF token
     */
    protected function validateCsrf() {
        if (!$this->isPost()) {
            return true;
        }

        $token = $this->post('csrf_token');
        if (!csrf_verify($token)) {
            $this->json(['error' => 'Token CSRF inválido'], 403);
        }

        return true;
    }

    /**
     * Establecer mensaje flash
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Obtener mensaje flash
     */
    protected function getFlash($type) {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }

    /**
     * Validar datos
     */
    protected function validate($data, $rules) {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $rulesArray = explode('|', $fieldRules);

            foreach ($rulesArray as $rule) {
                // Required
                if ($rule === 'required' && empty($value)) {
                    $errors[$field][] = "El campo {$field} es requerido";
                }

                // Email
                if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "El campo {$field} debe ser un email válido";
                }

                // Numeric
                if ($rule === 'numeric' && !empty($value) && !is_numeric($value)) {
                    $errors[$field][] = "El campo {$field} debe ser numérico";
                }

                // Min length
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (!empty($value) && strlen($value) < $min) {
                        $errors[$field][] = "El campo {$field} debe tener al menos {$min} caracteres";
                    }
                }

                // Max length
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (!empty($value) && strlen($value) > $max) {
                        $errors[$field][] = "El campo {$field} no debe superar {$max} caracteres";
                    }
                }
            }
        }

        return empty($errors) ? true : $errors;
    }
}
