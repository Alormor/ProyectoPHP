<?php

namespace Controllers;

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
            'message' => 'Crear una nueva cuenta',
            'showHeader' => false,
            'showFooter' => false
        ];
        
        // Renderizar la vista del formulario de registro
        return $this->view('usuarios/formregistro', $data);
    }
    
    public function save()
    {
        try {
            $userRequest = new UserRequest();
            
            if (!$userRequest->validate_and_sanitize()) {
                $_SESSION['errors'] = $userRequest->getErrors();
                $this->redirect('/registro');
                return;
            }
            
            $userData = $userRequest->getSanitized();
            $usuarioService = new UsuarioService();
            $resultado = $usuarioService->registrar($userData);
            
            if ($resultado) {
                $_SESSION['register'] = 'success';
                $_SESSION['message'] = 'Usuario registrado correctamente. Por favor inicia sesión.';
                $this->redirect('/login');
                return;
            } else {
                $_SESSION['errors'] = ['Error al registrar el usuario. Intenta de nuevo.'];
                $this->redirect('/registro');
                return;
            }
            
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            $this->redirect('/registro');
            return;
        }
    }
    
    public function create()
    {
        // Solo los administradores pueden crear usuarios
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos para crear usuarios.'];
            $this->redirect('/');
            return;
        }
        
        $data = [
            'title' => 'Crear Usuario',
            'message' => 'Crear nueva cuenta de usuario',
            'es_admin' => true,
            'showHeader' => false,
            'showFooter' => false
        ];
        
        return $this->view('usuarios/formcreate', $data);
    }
    
    public function store()
    {
        try {
            // Verificar permisos de administrador
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
                $_SESSION['errors'] = ['No tienes permisos para crear usuarios.'];
                $this->redirect('/');
                return;
            }
            
            $userRequest = new UserRequest();
            
            if (!$userRequest->validate_and_sanitize('admin')) {
                $_SESSION['errors'] = $userRequest->getErrors();
                $this->redirect('/admin/usuarios/crear');
                return;
            }
            
            $userData = $userRequest->getSanitized();
            $usuarioService = new UsuarioService();
            $resultado = $usuarioService->crear($userData, $_SESSION['usuario']['id']);
            
            if ($resultado) {
                $_SESSION['success'] = 'Usuario creado correctamente.';
                $this->redirect('/admin/usuarios');
                return;
            } else {
                $_SESSION['errors'] = ['Error al crear el usuario. Intenta de nuevo.'];
                $this->redirect('/admin/usuarios/crear');
                return;
            }
            
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            $this->redirect('/admin/usuarios/crear');
            return;
        }
    }
    
    public function login()
    {
        $data = [
            'title' => 'Iniciar Sesión',
            'message' => 'Accede a tu cuenta',
            'showHeader' => false,
            'showFooter' => false
        ];
        
        return $this->view('usuarios/formlogin', $data);
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
            
            $usuarioService = new UsuarioService();
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
        $this->redirect('/');
        return;
    }
}
?>
