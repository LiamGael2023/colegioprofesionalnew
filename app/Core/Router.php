<?php

namespace Core;

/**
 * Clase Router - Manejo de rutas del sistema
 */
class Router {
    private $routes = [];
    private $defaultController = 'Home';
    private $defaultMethod = 'index';

    /**
     * Agregar ruta GET
     */
    public function get($route, $callback) {
        $this->addRoute('GET', $route, $callback);
    }

    /**
     * Agregar ruta POST
     */
    public function post($route, $callback) {
        $this->addRoute('POST', $route, $callback);
    }

    /**
     * Agregar ruta
     */
    private function addRoute($method, $route, $callback) {
        $this->routes[$method][$route] = $callback;
    }

    /**
     * Procesar la URL actual
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $url = $this->getUrl();

        // Intentar encontrar coincidencia exacta
        if (isset($this->routes[$method][$url])) {
            return $this->executeCallback($this->routes[$method][$url]);
        }

        // Intentar encontrar coincidencia con parámetros
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = $this->getPattern($route);
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remover match completo
                return $this->executeCallback($callback, $matches);
            }
        }

        // Usar sistema de rutas por defecto (Controller/Method/Params)
        return $this->defaultRouting($url);
    }

    /**
     * Obtener URL limpia
     */
    private function getUrl() {
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return $url;
    }

    /**
     * Convertir ruta a patrón regex
     */
    private function getPattern($route) {
        // Convertir {param} a regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $route);
        return '#^' . $pattern . '$#';
    }

    /**
     * Ejecutar callback de ruta
     */
    private function executeCallback($callback, $params = []) {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        if (is_string($callback)) {
            list($controller, $method) = explode('@', $callback);
            return $this->callControllerMethod($controller, $method, $params);
        }
    }

    /**
     * Routing por defecto: Controller/Method/Params
     */
    private function defaultRouting($url) {
        $urlParts = $url ? explode('/', $url) : [];

        // Obtener controller
        $controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) : $this->defaultController;
        $controllerClass = "Controllers\\{$controllerName}Controller";

        // Obtener method
        $method = !empty($urlParts[1]) ? $urlParts[1] : $this->defaultMethod;

        // Obtener params
        $params = array_slice($urlParts, 2);

        // Llamar al controlador
        return $this->callControllerMethod($controllerClass, $method, $params);
    }

    /**
     * Llamar método de controlador
     */
    private function callControllerMethod($controllerClass, $method, $params = []) {
        // Verificar que existe el controlador
        if (!class_exists($controllerClass)) {
            $this->showError404("Controlador no encontrado: {$controllerClass}");
            return;
        }

        // Instanciar controlador
        $controller = new $controllerClass();

        // Verificar que existe el método
        if (!method_exists($controller, $method)) {
            $this->showError404("Método no encontrado: {$controllerClass}::{$method}");
            return;
        }

        // Llamar al método con parámetros
        return call_user_func_array([$controller, $method], $params);
    }

    /**
     * Mostrar error 404
     */
    private function showError404($message = null) {
        http_response_code(404);

        if (APP_CONFIG['debug'] && $message) {
            echo "<h1>404 - Página no encontrada</h1>";
            echo "<p>{$message}</p>";
        } else {
            echo "<h1>404 - Página no encontrada</h1>";
        }
    }
}
