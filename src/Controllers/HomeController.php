<?php

namespace Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Bienvenido',
            'message' => 'Bienvenido a tu aplicación'
        ];
        
        return $this->view('home', $data);
    }
}
?>
