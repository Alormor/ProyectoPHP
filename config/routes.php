<?php
use Core\Router;


$router = new Router();

// Rutas de ejemplo
$router->get('/', function() {
    echo 'Página de inicio';
});
