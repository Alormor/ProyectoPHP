<?php

namespace Controllers;

use Core\Controller;

class ErrorController extends Controller
{
    private function render(int $code, string $message): string
    {
        return $this->view('errors/Error', [
            'code' => $code,
            'message' => $message
        ]);
    }

    public function notFound()
    {
        return $this->render(404, 'Página no encontrada');
    }
    
    public function unauthorized()
    {
        return $this->render(401, 'No autorizado');
    }
    
    public function forbidden()
    {
        return $this->render(403, 'Acceso prohibido');
    }
    
    public function serverError()
    {
        return $this->render(500, 'Error del servidor');
    }

    public static function show_error404(): string
    {
        $controller = new self();
        return $controller->render(404, 'Página no encontrada');
    }
}

