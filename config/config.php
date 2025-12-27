<?php
/**
 * Archivo de Configuración Principal
 * Sistema de Gestión de Colegio Profesional
 */

// Evitar acceso directo
defined('APP_PATH') or die('Acceso denegado');

// =====================================================
// CARGAR VARIABLES DE ENTORNO
// =====================================================
function loadEnv($path) {
    if (!file_exists($path)) {
        die('Archivo .env no encontrado');
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorar comentarios
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parsear línea
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Remover comillas si existen
        $value = trim($value, '"\'');

        // Definir constante si no existe
        if (!defined($name)) {
            define($name, $value);
        }
    }
}

// Cargar .env
loadEnv(dirname(__DIR__) . '/.env');

// =====================================================
// CONFIGURACIÓN DE BASE DE DATOS
// =====================================================
define('DB_CONFIG', [
    'host' => DB_HOST ?? 'mysql',
    'port' => DB_PORT ?? '3306',
    'dbname' => DB_NAME ?? 'colegio_profesional',
    'username' => DB_USER ?? 'colegio_user',
    'password' => DB_PASS ?? 'colegio_pass_2024',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
]);

// =====================================================
// CONFIGURACIÓN DE APLICACIÓN
// =====================================================
define('APP_CONFIG', [
    'name' => APP_NAME ?? 'Sistema de Gestión de Colegio Profesional',
    'env' => APP_ENV ?? 'production',
    'debug' => filter_var(APP_DEBUG ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim(APP_URL ?? 'http://localhost:8080', '/'),
    'timezone' => APP_TIMEZONE ?? 'America/Lima',
]);

// =====================================================
// CONFIGURACIÓN DE SESIÓN
// =====================================================
define('SESSION_CONFIG', [
    'name' => SESSION_NAME ?? 'COLEGIO_SESSION',
    'lifetime' => (int)(SESSION_LIFETIME ?? 7200), // 2 horas
    'path' => '/',
    'domain' => '',
    'secure' => false, // true en producción con HTTPS
    'httponly' => true,
    'samesite' => 'Strict'
]);

// =====================================================
// CONFIGURACIÓN DE SEGURIDAD
// =====================================================
define('SECURITY_CONFIG', [
    'salt' => SECURITY_SALT ?? 'change_this_in_production',
    'password_algo' => PASSWORD_BCRYPT,
    'password_cost' => 10,
]);

// =====================================================
// CONFIGURACIÓN DE CUOTAS
// =====================================================
define('CUOTAS_CONFIG', [
    'monto_default' => (float)(CUOTA_MENSUAL ?? 150.00),
    'tolerancia_meses' => (int)(CUOTA_TOLERANCIA_MESES ?? 3),
    'dia_generacion' => 1, // Día del mes para generar cuotas
]);

// =====================================================
// RUTAS DEL SISTEMA
// =====================================================
define('PATHS', [
    'root' => dirname(__DIR__),
    'app' => dirname(__DIR__) . '/app',
    'public' => dirname(__DIR__) . '/public',
    'views' => dirname(__DIR__) . '/app/Views',
    'logs' => dirname(__DIR__) . '/logs',
    'uploads' => dirname(__DIR__) . '/public/uploads',
]);

// =====================================================
// URLS DEL SISTEMA
// =====================================================
define('URLS', [
    'base' => APP_CONFIG['url'],
    'assets' => APP_CONFIG['url'] . '/public',
    'css' => APP_CONFIG['url'] . '/css',
    'js' => APP_CONFIG['url'] . '/js',
    'img' => APP_CONFIG['url'] . '/img',
]);

// =====================================================
// CONFIGURACIÓN DE ZONA HORARIA
// =====================================================
date_default_timezone_set(APP_CONFIG['timezone']);

// =====================================================
// CONFIGURACIÓN DE ERRORES
// =====================================================
if (APP_CONFIG['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', PATHS['logs'] . '/php-errors.log');
}

// =====================================================
// AUTOLOAD DE CLASES
// =====================================================
spl_autoload_register(function ($class) {
    // Convertir namespace a ruta de archivo
    $file = PATHS['app'] . '/' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
        return true;
    }

    return false;
});

// =====================================================
// FUNCIONES AUXILIARES
// =====================================================

/**
 * Función para debugging
 */
function dd(...$vars) {
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}

/**
 * Sanitizar entrada de usuario
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Redireccionar a una URL
 */
function redirect($url, $statusCode = 303) {
    header('Location: ' . $url, true, $statusCode);
    exit();
}

/**
 * Obtener URL base
 */
function url($path = '') {
    return URLS['base'] . '/' . ltrim($path, '/');
}

/**
 * Obtener URL de asset
 */
function asset($path) {
    return URLS['base'] . '/' . ltrim($path, '/');
}

/**
 * Escape output para prevenir XSS
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generar token CSRF
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validar token CSRF
 */
function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Formatear fecha
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Formatear moneda
 */
function formatMoney($amount) {
    return 'S/. ' . number_format($amount, 2, '.', ',');
}

/**
 * Logging
 */
function logger($message, $level = 'INFO', $file = 'app.log') {
    $logFile = PATHS['logs'] . '/' . $file;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Obtener IP del cliente
 */
function getClientIP() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    return $ip;
}

/**
 * Obtener User Agent
 */
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
}
