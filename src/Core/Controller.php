<?php

namespace Core;

class Controller
{
    protected function view($file, $data = [])
    {
        extract($data);
        $contentFile = dirname(__DIR__) . "/Views/{$file}.php";
        
        if (!file_exists($contentFile)) {
            http_response_code(404);
            $data['code'] = 404;
            $data['message'] = 'Página no encontrada';
            extract($data);
            $contentFile = dirname(__DIR__) . "/Views/errors/Error.php";
            
            if (!file_exists($contentFile)) {
                return "404 - Página no encontrada";
            }
        }
        
        $layoutFile = dirname(__DIR__) . "/Views/layout/app.php";
        
        ob_start();
        include $layoutFile;
        return ob_get_clean();
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
