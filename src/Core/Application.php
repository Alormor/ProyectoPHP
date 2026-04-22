<?php

namespace Core;

class Application
{
    private Router $router;
    private string $method;
    private string $uri;
    
    public function __construct()
    {
        $this->router = new Router();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        
        // Remover BASE_URL del URI
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if (strpos($this->uri, $basePath) === 0) {
            $this->uri = substr($this->uri, strlen($basePath));
        }
        
        // Asegurar que inicia con /
        if (empty($this->uri)) {
            $this->uri = '/';
        }
        
        // Registrar rutas
        $this->registerRoutes();
    }
    
    private function registerRoutes()
    {
        // Rutas principales
        $this->router->get('/', 'HomeController@index');
        
        // Rutas de autenticación
        $this->router->get('/registro', 'AuthController@register');
        $this->router->post('/registro', 'AuthController@save');
        
        $this->router->get('/login', 'AuthController@login');
        $this->router->post('/login', 'AuthController@authenticate');
        $this->router->get('/logout', 'AuthController@logout');
        
        // Rutas admin
        $this->router->get('/admin/usuarios/crear', 'AuthController@create');
        $this->router->post('/admin/usuarios', 'AuthController@store');
        
        // Rutas de usuarios
        $this->router->get('/usuarios', 'UsuarioController@index');
        $this->router->get('/usuarios/:id', 'UsuarioController@show');
        
        // Rutas de productos
        $this->router->get('/productos', 'ProductoController@index');
        $this->router->get('/productos/:id', 'ProductoController@show');
        
        // Rutas de categorías
        $this->router->get('/categorias', 'CategoriaController@index');
        $this->router->get('/categorias/:id', 'CategoriaController@show');
    }
    
    public function run()
    {
        $handler = $this->router->match($this->method, $this->uri);
        
        if ($handler === null) {
            $this->handleNotFound();
            return;
        }
        
        [$controller, $action] = explode('@', $handler);
        $controllerClass = "Controllers\\{$controller}";
        
        try {
            // Verificar que la clase existe
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller no encontrado: {$controllerClass}");
            }
            
            $controllerInstance = new $controllerClass();
            
            // Extraer parámetros de la URL
            $params = $this->router->getParams();
            
            // Llamar el método con parámetros
            if (empty($params)) {
                $controllerInstance->$action();
            } else {
                call_user_func_array([$controllerInstance, $action], $params);
            }
            
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }
    
    private function handleNotFound()
    {
        http_response_code(404);
        try {
            $controller = new \Controllers\ErrorController();
            $controller->notFound();
        } catch (\Exception $e) {
            echo '404 - Página no encontrada';
        }
    }
    
    private function handleError(\Exception $e)
    {
        if ($_ENV['APP_DEBUG'] === 'true') {
            echo '<h1>Error</h1>';
            echo '<p>' . $e->getMessage() . '</p>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        } else {
            http_response_code(500);
            echo '500 - Error del servidor';
        }
    }
}

?>
