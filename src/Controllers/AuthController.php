<?php

namespace Controllers;

use Core\BaseDatos;
use Core\Controller;
use Request\UserRequest;
use Services\UsuarioService;

class AuthController extends Controller
{
    public function register()
    {
        // Preparar datos para la vista
        $data = [
            'title' => 'Registro de Usuario',
            'message' => 'Crear una nueva cuenta'
        ];
        
        // Renderizar la vista del formulario de registro
        return $this->view('usuarios/formRegistro', $data);
    }
    
    public function save()
    {
        try {
            $userRequest = new UserRequest();
            
            if (!$userRequest->validate_and_sanitize()) {
                $_SESSION['errors'] = $userRequest->getErrors();
                header('Location: ' . BASE_URL . '/registro');
                exit();
            }
            
            $userData = $userRequest->getSanitized();
            $usuarioService = new UsuarioService(BaseDatos::getInstancia());
            $resultado = $usuarioService->registrar($userData);
            
            if ($resultado) {
                $_SESSION['register'] = 'success';
                $_SESSION['message'] = 'Usuario registrado correctamente. Por favor inicia sesión.';
                header('Location: ' . BASE_URL . '/login');
                exit();
            } else {
                $_SESSION['errors'] = ['Error al registrar el usuario. Intenta de nuevo.'];
                header('Location: ' . BASE_URL . '/registro');
                exit();
            }
            
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            header('Location: ' . BASE_URL . '/registro');
            exit();
        }
    }
    
    public function create()
    {
        // Solo los administradores pueden crear usuarios
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos para crear usuarios.'];
            header('Location: /');
            exit();
        }
        
        $data = [
            'title' => 'Crear Usuario',
            'message' => 'Crear nueva cuenta de usuario',
            'es_admin' => true
        ];
        
        return $this->view('usuarios/formCreate', $data);
    }
    
    public function store()
    {
        try {
            // Verificar permisos de administrador
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
                $_SESSION['errors'] = ['No tienes permisos para crear usuarios.'];
                header('Location: /');
                exit();
            }
            
            $userRequest = new UserRequest();
            
            if (!$userRequest->validate_and_sanitize('admin')) {
                $_SESSION['errors'] = $userRequest->getErrors();
                header('Location: /admin/usuarios/crear');
                exit();
            }
            
            $userData = $userRequest->getSanitized();
            $usuarioService = new UsuarioService(BaseDatos::getInstancia());
            $resultado = $usuarioService->crear($userData, $_SESSION['usuario']['id']);
            
            if ($resultado) {
                $_SESSION['success'] = 'Usuario creado correctamente.';
                header('Location: /admin/usuarios');
                exit();
            } else {
                $_SESSION['errors'] = ['Error al crear el usuario. Intenta de nuevo.'];
                header('Location: /admin/usuarios/crear');
                exit();
            }
            
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            header('Location: /admin/usuarios/crear');
            exit();
        }
    }
    
    public function login()
    {
        $data = [
            'title' => 'Iniciar Sesión',
            'message' => 'Accede a tu cuenta'
        ];
        
        return $this->view('usuarios/formLogin', $data);
    }
    
    public function authenticate()
    {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $_SESSION['errors'] = ['Email y contraseña son requeridos'];
                $this->redirect('/login');
                return;
            }
            
            $usuarioService = new UsuarioService(BaseDatos::getInstancia());
            $usuario = $usuarioService->autenticar($email, $password);
            
            if ($usuario) {
                $_SESSION['usuario'] = $usuario;
                $_SESSION['success'] = 'Sesión iniciada correctamente';
                $this->redirect('/');
            } else {
                $_SESSION['errors'] = ['Email o contraseña incorrectos'];
                $this->redirect('/login');
            }
            
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error: ' . $e->getMessage()];
            $this->redirect('/login');
        }
    }
    
    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit();
    }
}
?>
