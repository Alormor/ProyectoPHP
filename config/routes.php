<?php
use Core\Router;

// Rutas principales
Router::add('GET', '/', function () {
    $controller = new \Controllers\HomeController();
    return $controller->index();
});

// Rutas de autenticación
Router::add('GET', '/registro', function () {
    $controller = new \Controllers\AuthController();
    return $controller->register();
});

Router::add('POST', '/registro', function () {
    $controller = new \Controllers\AuthController();
    return $controller->save();
});

Router::add('GET', '/login', function () {
    $controller = new \Controllers\AuthController();
    return $controller->login();
});

Router::add('POST', '/login', function () {
    $controller = new \Controllers\AuthController();
    return $controller->authenticate();
});

Router::add('GET', '/logout', function () {
    $controller = new \Controllers\AuthController();
    return $controller->logout();
});

//Ruta para confirmar cuenta
Router::add('GET', '/confirmar-cuenta', function () {
    $controller = new \Controllers\AuthController();
    return $controller->confirmar();
});


// Rutas admin
Router::add('GET', '/admin', function () {
    $controller = new \Controllers\AdminController();
    return $controller->dashboard();
});

Router::add('GET', '/admin/dashboard', function () {
    $controller = new \Controllers\AdminController();
    return $controller->dashboard();
});

Router::add('GET', '/admin/estadisticas', function () {
    $controller = new \Controllers\AdminController();
    return $controller->estadisticas();
});

Router::add('GET', '/admin/reportes', function () {
    $controller = new \Controllers\AdminController();
    return $controller->reportes();
});

Router::add('GET', '/admin/configuracion', function () {
    $controller = new \Controllers\AdminController();
    return $controller->configuracion();
});

Router::add('POST', '/admin/configuracion', function () {
    $controller = new \Controllers\AdminController();
    return $controller->guardarConfiguracion();
});

Router::add('GET', '/admin/usuarios', function () {
    $controller = new \Controllers\UsuarioController();
    return $controller->index();
});

Router::add('GET', '/admin/usuarios/crear', function () {
    $controller = new \Controllers\UsuarioController();
    return $controller->create();
});

Router::add('POST', '/admin/usuarios', function () {
    $controller = new \Controllers\UsuarioController();
    return $controller->store();
});

Router::add('GET', '/admin/usuarios/:id/editar', function ($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->edit($id);
});

Router::add('POST', '/admin/usuarios/:id', function ($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->update($id);
});

Router::add('GET', '/admin/usuarios/:id/confirmar-eliminacion', function ($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->confirmDelete($id);
});

Router::add('POST', '/admin/usuarios/:id/eliminar', function ($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->delete($id);
});

// Rutas para perfil de usuario
Router::add('GET', '/profile/:id/editar', function($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->edit($id, 'profile');
});

Router::add('POST', '/profile/:id', function($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->update($id, 'profile');
});

Router::add('GET', '/profile/:id/confirmar-eliminacion', function ($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->confirmDelete($id, 'profile');
});

Router::add('POST', '/profile/:id/eliminar', function ($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->delete($id, 'profile');
});

// Rutas de usuarios
Router::add('GET', '/usuarios', function () {
    $controller = new \Controllers\UsuarioController();
    return $controller->index();
});

Router::add('GET', '/usuarios/:id', function ($id) {
    $controller = new \Controllers\UsuarioController();
    return $controller->show($id);
});

// Rutas admin de productos
Router::add('GET', '/admin/productos/gestionar', function () {
    $controller = new \Controllers\ProductoController();
    return $controller->index();
});

Router::add('GET', '/admin/productos/crear', function () {
    $controller = new \Controllers\ProductoController();
    return $controller->create();
});

Router::add('POST', '/admin/productos', function () {
    $controller = new \Controllers\ProductoController();
    return $controller->store();
});

Router::add('GET', '/admin/productos/:id/editar', function ($id) {
    $controller = new \Controllers\ProductoController();
    return $controller->edit($id);
});

Router::add('POST', '/admin/productos/:id', function ($id) {
    $controller = new \Controllers\ProductoController();
    return $controller->update($id);
});

Router::add('POST', '/admin/productos/:id/eliminar', function ($id) {
    $controller = new \Controllers\ProductoController();
    return $controller->delete($id);
});

// Rutas de productos
Router::add('GET', '/productos', function () {
    $controller = new \Controllers\ProductoController();
    return $controller->index();
});

Router::add('GET', '/productos/:id', function ($id) {
    $controller = new \Controllers\ProductoController();
    return $controller->show($id);
});

// Rutas de categorías

// Rutas admin de categorías
Router::add('GET', '/admin/categorias/gestionar', function () {
    $controller = new \Controllers\CategoriaController();
    return $controller->gestion();
});

Router::add('GET', '/admin/categorias/crear', function () {
    $controller = new \Controllers\CategoriaController();
    return $controller->create();
});

Router::add('POST', '/admin/categorias', function () {
    $controller = new \Controllers\CategoriaController();
    return $controller->store();
});

Router::add('GET', '/admin/categorias/:id/editar', function ($id) {
    $controller = new \Controllers\CategoriaController();
    return $controller->edit($id);
});

Router::add('POST', '/admin/categorias/:id', function ($id) {
    $controller = new \Controllers\CategoriaController();
    return $controller->update($id);
});

Router::add('POST', '/admin/categorias/:id/eliminar', function ($id) {
    $controller = new \Controllers\CategoriaController();
    return $controller->delete($id);
});

Router::add('GET', '/categorias/:id', function ($id) {
    $controller = new \Controllers\CategoriaController();
    return $controller->show($id);
});

//Rutas del carrito
Router::add('GET', '/carrito', function () {
    return (new \Controllers\CarritoController())->index();
});

Router::add('POST', '/carrito/agregar', function () {
    return (new \Controllers\CarritoController())->agregar();
});

Router::add('GET', '/carrito/eliminar/:id', function ($id) {
    return (new \Controllers\CarritoController())->eliminar($id);
});

Router::add('GET', '/carrito/vaciar', function () {
    return (new \Controllers\CarritoController())->vaciar();
});

Router::add('GET', '/carrito/incrementar/:id', function ($id) {
    return (new \Controllers\CarritoController())->incrementar($id);
});

Router::add('GET', '/carrito/decrementar/:id', function ($id) {
    return (new \Controllers\CarritoController())->decrementar($id);
});

