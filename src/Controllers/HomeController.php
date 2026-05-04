<?php

namespace Controllers;

use Core\Controller;

/**
 * HomeController - Controlador para la página de inicio
 *
 * @package Controllers
 * @uses Controller
 */
class HomeController extends Controller
{
    /**
     * Muestra la página de inicio
     *
     * @return string Vista renderizada de la página de inicio
     */
    public function index()
    {
        $data = [
            'title' => 'Bienvenido',
            'message' => 'Bienvenido a tu aplicación',
            'showHeader' => true,
            'showFooter' => true
        ];

        return $this->view('home', $data);
    }
}
