<?php

namespace Request;

class Request
{
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    public function all()
    {
        return array_merge($_GET, $_POST);
    }
    
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public function uri()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}
?>
