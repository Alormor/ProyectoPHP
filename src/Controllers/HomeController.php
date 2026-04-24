<?php

namespace Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Bienvenido',
            'message' => 'Bienvenido a tu aplicación',
            'base_url' => $_ENV['BASE_URL'],
            'showHeader' => true,
            'showFooter' => true
        ];
        
        return $this->view('home', $data);
    }
}

?>
