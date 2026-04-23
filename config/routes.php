<?php
use Core\Router;

// Rutas de ejemplo
Router::add('GET', '/', static function() {
    echo 'Página de inicio';
});

// Ruta para mostrar el formulario y procesar el signin
Router::add('GET', '/registro', static function() {
    (new Controllers\AuthController())->register();
});
Router::add('POST', '/registro', static function() {
    (new Controllers\AuthController())->save();
});