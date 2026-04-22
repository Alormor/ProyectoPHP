<?php

namespace Core;

class Router
{
    private array $routes = [];
    private array $params = [];
    
    public function add($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $this->pathToRegex($path),
            'handler' => $handler
        ]; 
    }
    
    public function get($path, $handler)
    {
        $this->add('GET', $path, $handler);
    }
    
    public function post($path, $handler)
    {
        $this->add('POST', $path, $handler);
    }
    
    public function put($path, $handler)
    {
        $this->add('PUT', $path, $handler);
    }
    
    public function delete($path, $handler)
    {
        $this->add('DELETE', $path, $handler);
    }
    
    public function match($method, $uri)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                // Guardar parámetros extraídos
                array_shift($matches); // Remover coincidencia completa
                $this->params = $matches;
                return $route['handler'];
            }
        }
        return null;
    }
    
    public function getParams()
    {
        return $this->params;
    }
    
    private function pathToRegex($path)
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
