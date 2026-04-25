<?php

namespace Controllers;

use Core\BaseDatos;
use Core\Controller;
use Request\UserRequest;
use Services\UsuarioService;

class AuthController extends Controller
{
    private UsuarioService $service;
    public function __construct()
    {
        $conexion = BaseDatos::getInstancia();
        $this->service = new UsuarioService();
    }
    
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
            $usuarioService = new UsuarioService();
            $resultado = $usuarioService->registrar($userData);
            
            match ($resultado) {
                "creado" => (function() use ($userData) {
                    $_SESSION['register'] = 'success';
                    $_SESSION['message'] = "¡Registro casi completado! Te hemos enviado un correo a {$userData['email']}.";
                    $this->redirect('/registro');
                })(),
                
                "reenviado" => (function() use ($userData) {
                    $_SESSION['register'] = 'success';
                    $_SESSION['message'] = "Ya tenías una cuenta pendiente. Te hemos enviado un nuevo código a {$userData['email']}.";
                    $this->redirect('/registro');
                })(),
                
                false => (function() {
                    $_SESSION['errors'] = ['Correo en uso.'];
                    $this->redirect('/registro');
                })(),
            };
            
            exit;
            
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            header('Location: ' . BASE_URL . '/registro');
            exit();
        }
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
            
            $usuarioService = new UsuarioService();
            $usuario = $usuarioService->autenticar($email, $password);
            
            if ($usuario === "no_confirmado") {
                $_SESSION['errors'] = ['Tu cuenta no está confirmada. Por favor, revisa tu correo o regístrate de nuevo para recibir otro enlace.'];
                $this->redirect('/login');
            }elseif ($usuario) {
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

    public function confirmar()
    {
        $token = $_GET['token'] ?? null;
        $status = 'error';

        // Si el token existe, se confirma la cuenta
        if ($token) {
            $resultado = $this->service->confirmarCuenta($token);
            
            if ($resultado === true) {
                $status = 'success';
            } elseif ($resultado === "expirado") {
                $status = 'expired';
            }
        }
        
        // Se carga la vista pasándole el estado
        return $this->view('auth/confirmacion', [
            'status' => $status,
            'title' => 'Confirmación de cuenta'
        ]);
    }

}
?>
