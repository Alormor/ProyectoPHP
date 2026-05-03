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

        $baseUrl = $_ENV['BASE_URL'] ?? 'http://localhost';
        $basePath = parse_url($baseUrl, PHP_URL_PATH);
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = trim($uri, '/');
        //var_dump($uri);
        //die();
        

        $params = [];
        $callback = null;

        foreach (self::$routes[$method] ?? [] as $action => $controller) {
            $pattern = preg_replace('#:([\w]+)#', '([^/]+)', $action);

            if (preg_match('#^' . $pattern . '/?$#i', $uri, $matches)) {
                array_shift($matches);
                $params = $matches;
                $callback = $controller;
                break;
            }
        }
        
        if (!$callback) {
            if (PHP_SAPI !== 'cli') {
                http_response_code(404);
            }
            echo ErrorController::show_error404();
            return;
        }

        echo call_user_func_array($callback, $params);
    }

}
