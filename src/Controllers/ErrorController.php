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

    public static function show_error404(): string
    {
        $controller = new self();
        return $controller->render(404, 'Página no encontrada');
    }
}

