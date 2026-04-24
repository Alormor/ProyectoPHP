<?php
use Core\Router;

// Rutas principales
Router::add('GET', '/', function() {
    $controller = new \Controllers\HomeController();
    return $controller->index();
});

// Rutas de autenticación
Router::add('GET', '/registro', function() {
    $controller = new \Controllers\AuthController();
    return $controller->register();
});

Router::add('POST', '/registro', function() {
    $controller = new \Controllers\AuthController();
    return $controller->save();
});

/*// Ruta para mostrar el formulario y procesar el signin
Router::add('GET', '/registro', static function() {
    (new Controllers\AuthController())->register();
});
Router::add('POST', '/registro', static function() {
    (new Controllers\AuthController())->save();
}); */

Router::add('GET', '/login', function() {
    $controller = new \Controllers\AuthController();
    return $controller->login();
});

Router::add('POST', '/login', function() {
    $controller = new \Controllers\AuthController();
    return $controller->authenticate();
});

Router::add('GET', '/logout', function() {
    $controller = new \Controllers\AuthController();
    return $controller->logout();
});

// Rutas admin
Router::add('GET', '/admin/usuarios', function() {
    $controller = new \Controllers\UsuarioController();
    return $controller->index();
});

Router::add('GET', '/admin/usuarios/crear', function() {
    $controller = new \Controllers\UsuarioController();
    return $controller->create();
});

Router::add('POST', '/admin/usuarios', function() {
    $controller = new \Controllers\UsuarioController();
    return $controller->store();
});

Router::add('GET', '/admin/usuarios/:id/editar', function($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->edit($id);
});

Router::add('POST', '/admin/usuarios/:id', function($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->update($id);
});

Router::add('GET', '/admin/usuarios/:id/confirmar-eliminacion', function($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->confirmDelete($id);
});

Router::add('POST', '/admin/usuarios/:id/eliminar', function($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->delete($id);
});

// Rutas para perfil de usuario
Router::add('GET', '/profile/:id/confirmar-eliminacion', function($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->confirmDeleteProfile($id);
});

Router::add('POST', '/profile/:id/eliminar', function($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->deleteProfile($id);
});

// Rutas de usuarios
Router::add('GET', '/usuarios', function() {
    $controller = new \Controllers\UsuarioController();
    return $controller->index();
});

Router::add('GET', '/usuarios/:id', function($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->show($id);
});

// Rutas de productos
Router::add('GET', '/productos', function() {
    $controller = new \Controllers\ProductoController();
    return $controller->index();
});

Router::add('GET', '/productos/:id', function($id) {
    $controller = new \Controllers\ProductoController();
    return $controller->show($id);
});

// Rutas de categorías
Router::add('GET', '/categorias', function() {
    $controller = new \Controllers\CategoriaController();
    return $controller->index();
});

Router::add('GET', '/categorias/:id', function($id) {
    $controller = new \Controllers\CategoriaController();
    return $controller->show($id);
});

