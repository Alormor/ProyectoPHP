<?php

namespace Core;

class Router
{
    protected $routes = [];
    
    public function add($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
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
    
    public function match($method, $uri)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->match_path($route['path'], $uri)) {
                return $route['handler'];
            }
        }
        return null;
    }
    
    protected function match_path($pattern, $uri)
    {
        return $pattern === $uri;
    }
}
?>
