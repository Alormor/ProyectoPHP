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
            'base_url' => BASE_URL
        ];
        
        return $this->view('home', $data);
    }
}

?>
