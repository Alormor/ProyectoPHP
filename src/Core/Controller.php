<?php

namespace Core;

class Controller
{
    protected $view;
    
    public function __construct()
    {
        // Inicializar controller
    }
    
    protected function view($file, $data = [])
    {
        extract($data);
        include "../src/Views/$file.php";
    }
    
    protected function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
    
    protected function redirect($path)
    {
        header("Location: $path");
        exit;
    }
}
?>
