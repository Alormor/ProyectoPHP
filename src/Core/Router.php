<?php

namespace Core;

class Router
{
    private static array $routes = [];
    private static array $params = [];
    
    public static function add($method, $path, $handler)
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => self::pathToRegex($path),
            'handler' => $handler
        ]; 
    }
    
    public static function get($path, $handler)
    {
        self::add('GET', $path, $handler);
    }
    
    public static function post($path, $handler)
    {
        self::add('POST', $path, $handler);
    }
    
    public static function put($path, $handler)
    {
        self::add('PUT', $path, $handler);
    }
    
    public static function delete($path, $handler)
    {
        self::add('DELETE', $path, $handler);
    }
    
    public static function match($method, $uri)
    {
        foreach (self::$routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                // Guardar parámetros extraídos
                array_shift($matches); // Remover coincidencia completa
                self::$params = $matches;
                return $route['handler'];
            }
        }
        return null;
    }
    
    public static function getParams()
    {
        return self::$params;
    }
    
    private static function pathToRegex($path)
    {
        // Convertir :id a regex
        $pattern = preg_replace('/:([a-zA-Z_][a-zA-Z0-9_]*)/', '([a-zA-Z0-9_-]+)', $path);
        
        // Escapar caracteres especiales excepto paréntesis
        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace('\\(', '(', $pattern);
        $pattern = str_replace('\\)', ')', $pattern);
        
        return '#^' . $pattern . '$#';
    }
}

?>
