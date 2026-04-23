<?php

namespace Core;

use Controllers\ErrorController;

class Router {
    private static array $routes = [];

    /** Registra una ruta en el sistema */
    public static function add(string $method, string $action, callable $controller): void {
        $action = trim($action, '/');
        self::$routes[strtoupper($method)][$action] = $controller;
    }
    /**
     * Se encarga de obtener el sufijo de la URL que permitirá seleccionar
     * la ruta y mostrar el resultado de ejecutar la función pasada al metodo add
     * para esa ruta usando call_user_func()
     */
    public static function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // Remover BASE_URL del URI
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = trim($uri, '/');

        $params = [];
        $callback = null;

        // Buscamos coincidencia exacta o con parámetros en el array con las rutas registradas
        foreach (self::$routes[$method] ?? [] as $action => $controller) {
            // Reemplazamos, en la ruta, lo que empiece por : seguido de letras o número(ej: 'user/:id')
            // por una expresión regular, así localiza user/seguido de cualquier cosa que no es barra inclinada
            $pattern = preg_replace('#:([\w]+)#', '([^/]+)', $action);

            if (preg_match('#^' . $pattern . '$#i', $uri, $matches)) {
                array_shift($matches); // Quitamos la coincidencia completa deja solo los parámetros
                $params = $matches;
                $callback = $controller;//tenemos la ruta
                break;
            }
        }

        if (!$callback) {//la ruta no está registrada
            if (PHP_SAPI !== 'cli') {
                http_response_code(404);
            }
            echo ErrorController::show_error404();
            return;
        }

        // Ejecutamos el controlador pasando los parámetros detectados
        echo call_user_func_array($callback, $params);
    }

}
