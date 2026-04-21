<?php

namespace Core;

class Controller
{
    protected function view($file, $data = [])
    {
        extract($data);
        $viewPath = dirname(__DIR__) . "/Views/{$file}.php";
        
        if (!file_exists($viewPath)) {
            throw new \Exception("Vista no encontrada: {$file}");
        }
        
        include $viewPath;
    }
    
    protected function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($path)
    {
        if (strpos($path, 'http') !== 0 && strpos($path, '/') === 0) {
            $path = BASE_URL . $path;
        }
        header("Location: {$path}");
        exit;
    }
    
    protected function url($path = '')
    {
        if (empty($path)) {
            return BASE_URL;
        }
        
        if (strpos($path, 'http') === 0) {
            return $path;
        }
        
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        
        return BASE_URL . $path;
    }
}

?>
