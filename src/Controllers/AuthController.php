<?php

namespace Controllers;

use Core\BaseDatos;
use Core\Controller;
use Request\UserRequest;
use Services\UsuarioService;
use Services\MailService;
use Services\CarritoService;
use Models\Usuario;

/**
 * AuthController - Controlador para autenticación y gestión de cuentas
 *
 * @package Controllers
 * @uses Controller
 * @uses UsuarioService
 * @uses UserRequest
 */
class AuthController extends Controller
{
    private UsuarioService $service;

    /**
     * Constructor de AuthController
     */
    public function __construct()
    {
        $this->service = new UsuarioService();
    }

    /**
     * Muestra el formulario de registro
     *
     * @return string Vista del formulario de registro
     */
    public function register()
    {
        $data = [
            'title' => 'Registro de Usuario',
            'message' => 'Crear una nueva cuenta',
            'showHeader' => false,
            'showFooter' => false
        ];

        return $this->view('usuarios/formRegistro', $data);
    }

    /**
     * Registra un nuevo usuario o reenvía confirmación
     *
     * @return void Redirige al formulario de registro
     */
    public function save()
    {
        try {
            $oldInput = [
                'nombre' => $_POST['data']['nombre'] ?? '',
                'apellidos' => $_POST['data']['apellidos'] ?? '',
                'direccion' => $_POST['data']['direccion'] ?? '',
                'email' => $_POST['data']['email'] ?? ''
            ];

            $userRequest = new UserRequest();
            
            if (!$userRequest->validate_and_sanitize()) {
                $errs = $userRequest->getErrors();
                $_SESSION['errors'] = $errs;
                $_SESSION['old_register'] = $oldInput;
                header('Location: ' . $_ENV['BASE_URL'] . '/registro');
                exit();
            }

            $userData = $userRequest->getSanitized();
            $usuarioService = new UsuarioService();
            $resultado = $usuarioService->registrar($userData);
            
            match ($resultado) {
                "creado" => (function() use ($userData) {
                    unset($_SESSION['old_register']);
                    $_SESSION['register'] = 'success';
                    $_SESSION['message'] = "¡Registro casi completado! Te hemos enviado un correo a {$userData['email']}.";
                    $this->redirect('/registro');
                })(),
                
                "reenviado" => (function() use ($userData) {
                    unset($_SESSION['old_register']);
                    $_SESSION['register'] = 'success';
                    $_SESSION['message'] = "Ya tenías una cuenta pendiente. Te hemos enviado un nuevo código a {$userData['email']}.";
                    $this->redirect('/registro');
                })(),

                "correo_en_uso" => (function() use ($oldInput) {
                    $_SESSION['errors'] = ['Ese correo ya está registrado.'];
                    $_SESSION['old_register'] = $oldInput;
                    $this->redirect('/registro');
                })(),
                
                false => (function() use ($oldInput) {
                    $_SESSION['errors'] = ['No se pudo completar el registro. Inténtalo de nuevo.'];
                    $_SESSION['old_register'] = $oldInput;
                    $this->redirect('/registro');
                })(),
            };
            
            exit;
            
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            $_SESSION['old_register'] = $oldInput ?? [];
            header('Location: ' . $_ENV['BASE_URL'] . '/registro');
            exit();
        }
    }


    /**
     * Muestra el formulario de login
     *
     * @return string Vista del formulario de login
     */
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

    /**
     * Autentica un usuario con email y contraseña
     *
     * @return void Redirige según el resultado de autenticación
     */
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
                if(!empty($_SESSION['carrito_temporal'])){
                    $carritoService = new CarritoService();
                    foreach ($_SESSION['carrito_temporal'] as $producto_id => $cantidad){
                        $carritoService -> agregarProducto($usuario['id'], $producto_id, $cantidad);
                    }

                    //limpiar carrito temporal
                    unset($_SESSION['carrito_temporal']);
                }

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

    /**
     * Cierra la sesión del usuario
     *
     * @return void Redirige a la página de inicio
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('/');
        return;
    }

    /**
     * Confirma la cuenta de un usuario mediante token
     *
     * @return string Vista de confirmación
     */
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
            'title' => 'Confirmación de cuenta',
            'showHeader' => false,
            'showFooter' => false
        ]);
    }

    /**
     * Solicita reset de contraseña
     *
     * @return string Vista del formulario de contraseña olvidada
     */
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            if ($this->service->solicitarPassword($email)) {
                $_SESSION['success'] = "Se ha enviado un enlace para restablecer tu contraseña.";
            } else {
                $_SESSION['errors'] = ["El correo no existe."];
            }
        }
        return $this->view('usuarios/passOlvidada');
    }

    /**
     * Restablece la contraseña de un usuario mediante token
     *
     * @return string Vista del reset de contraseña
     */
    public function resetPassword()
    {
        $token = $_GET['token'] ?? $_POST['token'] ?? null;
        if (!$token || !$this->service->validarTokenReset($token)) {
            return $this->view('auth/confirmacion', ['status' => 'expired', 'title' => 'Enlace caducado']);
        }
        // Si el usuario envía el formulario con la nueva contraseña
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            if ($password === $confirm && !empty($password)) {
                $email = $this->service->validarTokenReset($token);
                if ($this->service->completarReset($email, $password)) {
                    // Enviar notificación al usuario de que la contraseña fue cambiada
                    $mailService = new MailService();
                    $mailService->enviarCorreoCambioPassword($email);
                }

                return $this->view('auth/confirmacionPass', [
                    'title' => 'Exito',
                    'showHeader' => false,
                    'showFooter' => false
                ]);
            }
            $_SESSION['errors'] = ["Las contraseñas no coinciden."];
        }
        // Si es GET, mostramos el formulario de "Nueva Contraseña"
        return $this->view('usuarios/resetPassword', ['token' => $token]);
    }
}
