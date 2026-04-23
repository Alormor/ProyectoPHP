<?php

namespace Controllers;

use Core\Controller;

class ErrorController extends Controller
{
    public function notFound()
    {
        $data = [
            'code' => 404,
            'message' => 'Página no encontrada'
        ];
        
        return $this->view('errors/404', $data);
    }
    
    public function unauthorized()
    {
        $data = [
            'code' => 401,
            'message' => 'No autorizado'
        ];
        
        return $this->view('errors/401', $data);
    }
    
    public function forbidden()
    {
        $data = [
            'code' => 403,
            'message' => 'Acceso prohibido'
        ];
        
        return $this->view('errors/403', $data);
    }
    
    public function serverError()
    {
        $data = [
            'code' => 500,
            'message' => 'Error del servidor'
        ];
        
        return $this->view('errors/500', $data);
    }

    public static function show_error404(): string
    {
        $controller = new self();
        return $controller->notFound();
    }
}

