<?php
/**
 * Punto de entrada de la aplicación
 * Sistema de Gestión de Colegio Profesional
 */

// Definir constante de path raíz
define('APP_PATH', dirname(__DIR__));

// Cargar configuración
require_once APP_PATH . '/config/config.php';

// Iniciar sesión
session_name(SESSION_CONFIG['name']);
session_set_cookie_params([
    'lifetime' => SESSION_CONFIG['lifetime'],
    'path' => SESSION_CONFIG['path'],
    'domain' => SESSION_CONFIG['domain'],
    'secure' => SESSION_CONFIG['secure'],
    'httponly' => SESSION_CONFIG['httponly'],
    'samesite' => SESSION_CONFIG['samesite']
]);
session_start();

// Crear instancia del router
$router = new Core\Router();

// =====================================================
// DEFINIR RUTAS DE LA APLICACIÓN
// =====================================================

// Rutas de autenticación
$router->get('', 'Controllers\HomeController@index');
$router->get('auth/login', 'Controllers\AuthController@login');
$router->post('auth/login', 'Controllers\AuthController@doLogin');
$router->get('auth/logout', 'Controllers\AuthController@logout');

// Rutas de personas
$router->get('personas', 'Controllers\PersonasController@index');
$router->get('personas/crear', 'Controllers\PersonasController@create');
$router->post('personas/crear', 'Controllers\PersonasController@store');
$router->get('personas/editar/{id}', 'Controllers\PersonasController@edit');
$router->post('personas/editar/{id}', 'Controllers\PersonasController@update');
$router->post('personas/eliminar/{id}', 'Controllers\PersonasController@delete');

// Rutas de colegiados
$router->get('colegiados', 'Controllers\ColegiadosController@index');
$router->get('colegiados/convertir/{id}', 'Controllers\ColegiadosController@convert');
$router->post('colegiados/convertir/{id}', 'Controllers\ColegiadosController@doConvert');
$router->get('colegiados/ver/{id}', 'Controllers\ColegiadosController@show');
$router->post('colegiados/actualizar-habilitacion', 'Controllers\ColegiadosController@updateHabilitacion');

// Rutas de cuotas
$router->get('cuotas', 'Controllers\CuotasController@index');
$router->get('cuotas/generar', 'Controllers\CuotasController@generate');
$router->post('cuotas/generar', 'Controllers\CuotasController@doGenerate');
$router->get('cuotas/adelantar/{id}', 'Controllers\CuotasController@adelantar');
$router->post('cuotas/adelantar/{id}', 'Controllers\CuotasController@doAdelantar');

// Rutas de caja
$router->get('caja', 'Controllers\CajaController@index');
$router->post('caja/buscar', 'Controllers\CajaController@search');
$router->post('caja/procesar-pago', 'Controllers\CajaController@procesarPago');
$router->get('caja/recibo/{id}', 'Controllers\CajaController@verRecibo');

// Rutas de reportes
$router->get('reportes', 'Controllers\ReportesController@index');
$router->get('reportes/deudores', 'Controllers\ReportesController@deudores');
$router->get('reportes/recaudacion', 'Controllers\ReportesController@recaudacion');
$router->get('reportes/estado-cuenta/{id}', 'Controllers\ReportesController@estadoCuenta');

// =====================================================
// PROCESAR RUTA
// =====================================================
$router->dispatch();
