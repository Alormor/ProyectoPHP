<?php

namespace Controllers;

use Core\Controller;

/**
 * ErrorController - Controlador para mostrar páginas de error
 *
 * @package Controllers
 * @uses Controller
 */
class ErrorController extends Controller
{
    /**
     * Renderiza una página de error genérica
     *
     * @param int $code Código de error HTTP
     * @param string $message Mensaje de error a mostrar
     * @return string Vista renderizada del error
     */
    private function render(int $code, string $message): string
    {
        return $this->view('errors/Error', [
            'code' => $code,
            'message' => $message
        ]);
    }

    /**
     * Muestra la página de error 404 (no encontrado)
     *
     * @return string Vista renderizada del error 404
     */
    public static function show_error404(): string
    {
        $controller = new self();
        return $controller->render(404, 'Página no encontrada');
    }
}

