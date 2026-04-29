<?php

namespace Controllers;

use Core\BaseDatos;
use Request\UserRequest;
use Services\UsuarioService;
use Repositories\UsuarioRepository;

class UsuarioController extends AdminController
{

    public function index()
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        try {
            $usuarioService = new UsuarioService(BaseDatos::getInstancia());
            $usuarioRepository = new \Repositories\UsuarioRepository(BaseDatos::getInstancia());
            $usuarios = $usuarioRepository->findAll();

            $data = $this->prepararDatosVista(
                'Gestión de Usuarios',
                'Administra todos los usuarios del sistema.',
                [
                    'usuarios' => $usuarios,
                    'es_admin' => true
                ]
            );
            return $this->view('usuarios/index', $data);
        } catch (\Exception $e) {
            $this->guardarError('Error al cargar los usuarios: ' . $e->getMessage());
            $this->redirect('/');
            return;
        }
    }

    public function show($id)
    {
        $data = [
            'title' => 'Detalle de Usuario',
            'message' => "Mostrando detalles del usuario con ID: $id",
            'showHeader' => true,
            'showFooter' => true
        ];
        return $this->view('usuarios/userprofile', $data);
    }

    public function create()
    {
        if (!$this->verificarPermisosAdmin()) {
            return;
        }

        $data = $this->prepararDatosVista(
            'Crear Usuario',
            'Crear nueva cuenta de usuario',
            ['es_admin' => true]
        );

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

            $errors = [];

            // Obtener datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $email = trim(strtolower($_POST['email'] ?? ''));
            $password = trim($_POST['password'] ?? '');
            $password_confirm = trim($_POST['password_confirm'] ?? '');
            $rol = trim(strtolower($_POST['rol'] ?? 'usuario'));

            // Validar datos
            if (empty($nombre)) {
                $errors[] = 'El nombre es requerido';
            }

            if (empty($apellidos)) {
                $errors[] = 'Los apellidos son requeridos';
            }

            if (empty($email)) {
                $errors[] = 'El email es requerido';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El formato del email no es válido';
            }

            if (empty($password)) {
                $errors[] = 'La contraseña es requerida';
            } elseif (strlen($password) < 8) {
                $errors[] = 'La contraseña debe tener al menos 8 caracteres';
            }

            if (empty($password_confirm)) {
                $errors[] = 'Debe confirmar la contraseña';
            }

            if (!empty($password) && !empty($password_confirm)) {
                if ($password !== $password_confirm) {
                    $errors[] = 'Las contraseñas no coinciden';
                }
            }

            if (!in_array($rol, ['usuario', 'admin'])) {
                $errors[] = 'El rol no es válido';
            }

            // Si hay errores, guardarlos en sesión y volver al formulario
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = [
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'email' => $email,
                    'rol' => $rol
                ];
                $this->redirect('/admin/usuarios/crear');
                return;
            }

            // Crear usuario en BD
            $usuarioService = new UsuarioService(BaseDatos::getInstancia());
            $resultado = $usuarioService->crear([
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email,
                'password' => $password,
                'rol' => $rol
            ], $_SESSION['usuario']['id']);

            if ($resultado) {
                $_SESSION['success'] = 'Usuario creado correctamente.';
                unset($_SESSION['form_data']);
                $this->redirect('/admin/usuarios/crear');
                return;
            } else {
                $_SESSION['errors'] = ['Error al crear el usuario. El email podría estar duplicado.'];
                $_SESSION['form_data'] = [
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'email' => $email,
                    'rol' => $rol
                ];
                $this->redirect('/admin/usuarios/crear');
                return;
            }

        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            $this->redirect('/admin/usuarios/crear');
            return;
        }
    }

    public function edit($id, $context = 'admin')
    {
        // Verificar permisos según contexto
        if ($context === 'admin') {
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
                $_SESSION['errors'] = ['No tienes permisos para editar usuarios.'];
                $this->redirect('/');
                return;
            }
        } elseif ($context === 'profile') {
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id'] != $id) {
                $_SESSION['errors'] = ['No tienes permiso para editar este perfil.'];
                $this->redirect('/');
                return;
            }
        }

        try {
            $usuarioRepository = new UsuarioRepository(BaseDatos::getInstancia());
            $usuario = $usuarioRepository->find($id);

            if (!$usuario) {
                $_SESSION['errors'] = ['El usuario no existe.'];
                $this->redirect($context === 'admin' ? '/admin/usuarios' : '/');
                return;
            }

            $view = 'usuarios/edit';
            $title = $context === 'admin' ? 'Editar Usuario' : 'Editar Mi Perfil';
            $message = $context === 'admin' ? 'Edita los datos del usuario' : 'Actualiza tus datos personales';

            $data = [
                'title' => $title,
                'message' => $message,
                'usuario' => $usuario,
                'es_admin' => $context === 'admin',
                'es_perfil' => $context === 'profile',
                'showHeader' => true,
                'showFooter' => true
            ];
            return $this->view($view, $data);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error al cargar el usuario: ' . $e->getMessage()];
            $this->redirect($context === 'admin' ? '/admin/usuarios' : '/');
            return;
        }
    }

    public function update($id, $context = 'admin')
    {
        try {
            // Verificar permisos según contexto
            if ($context === 'admin') {
                if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
                    $_SESSION['errors'] = ['No tienes permisos para editar usuarios.'];
                    $this->redirect('/');
                    return;
                }
            } elseif ($context === 'profile') {
                if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id'] != $id) {
                    $_SESSION['errors'] = ['No tienes permiso para editar este perfil.'];
                    $this->redirect('/');
                    return;
                }
            }

            $errors = [];
            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $email = trim(strtolower($_POST['email'] ?? ''));
            $rol = $context === 'admin' ? trim(strtolower($_POST['rol'] ?? 'usuario')) : null;

            // Validar datos
            if (empty($nombre)) {
                $errors[] = 'El nombre es requerido';
            }
            if (empty($apellidos)) {
                $errors[] = 'Los apellidos son requeridos';
            }
            if (empty($email)) {
                $errors[] = 'El email es requerido';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'El formato del email no es válido';
            }
            if ($context === 'admin' && !in_array($rol, ['usuario', 'admin'])) {
                $errors[] = 'El rol no es válido';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = array_filter([
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'email' => $email,
                    'rol' => $context === 'admin' ? $rol : null
                ]);
                $redirect = $context === 'admin' ? "/admin/usuarios/$id/editar" : "/profile/$id/editar";
                $this->redirect($redirect);
                return;
            }

            $usuarioRepository = new UsuarioRepository(BaseDatos::getInstancia());
            $usuarioActual = $usuarioRepository->find($id);

            if (!$usuarioActual) {
                $_SESSION['errors'] = ['El usuario no existe.'];
                $this->redirect($context === 'admin' ? '/admin/usuarios' : '/');
                return;
            }

            // Validaciones adicionales para admin
            if ($context === 'admin') {
                if ($usuarioActual['rol'] === 'admin' && $rol !== 'admin') {
                    $_SESSION['errors'] = ['No puedes cambiar el rol de un administrador.'];
                    $_SESSION['form_data'] = ['nombre' => $nombre, 'apellidos' => $apellidos, 'email' => $email, 'rol' => $rol];
                    $this->redirect("/admin/usuarios/$id/editar");
                    return;
                }
                if ($usuarioActual['rol'] === 'admin' && $email !== $usuarioActual['email']) {
                    $_SESSION['errors'] = ['No puedes cambiar el email de un administrador.'];
                    $_SESSION['form_data'] = ['nombre' => $nombre, 'apellidos' => $apellidos, 'email' => $email, 'rol' => $rol];
                    $this->redirect("/admin/usuarios/$id/editar");
                    return;
                }
            }

            // Verificar email duplicado
            if ($email !== $usuarioActual['email']) {
                $usuarioExistente = $usuarioRepository->findByEmail($email);
                if ($usuarioExistente) {
                    $_SESSION['errors'] = ['El email ya está en uso.'];
                    $_SESSION['form_data'] = array_filter([
                        'nombre' => $nombre,
                        'apellidos' => $apellidos,
                        'email' => $email,
                        'rol' => $context === 'admin' ? $rol : null
                    ]);
                    $redirect = $context === 'admin' ? "/admin/usuarios/$id/editar" : "/profile/$id/editar";
                    $this->redirect($redirect);
                    return;
                }
            }

            // Preparar datos para actualizar
            $updateData = ['nombre' => $nombre, 'apellidos' => $apellidos, 'email' => $email];
            if ($context === 'admin') {
                $updateData['rol'] = $rol;
            }

            $resultado = $usuarioRepository->update($id, $updateData);

            if ($resultado) {
                if ($context === 'profile') {
                    $_SESSION['usuario']['nombre'] = $nombre;
                    $_SESSION['usuario']['apellidos'] = $apellidos;
                    $_SESSION['usuario']['email'] = $email;
                }
                $_SESSION['success'] = $context === 'admin' ? 'Usuario actualizado correctamente.' : 'Tu perfil ha sido actualizado correctamente.';
                unset($_SESSION['form_data']);
                $redirect = $context === 'admin' ? '/admin/usuarios' : '/';
                $this->redirect($redirect);
                return;
            } else {
                $_SESSION['errors'] = ['Error al actualizar ' . ($context === 'admin' ? 'el usuario' : 'tu perfil') . '.'];
                $_SESSION['form_data'] = array_filter([
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'email' => $email,
                    'rol' => $context === 'admin' ? $rol : null
                ]);
                $redirect = $context === 'admin' ? "/admin/usuarios/$id/editar" : "/profile/$id/editar";
                $this->redirect($redirect);
                return;
            }

        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            $redirect = $context === 'admin' ? "/admin/usuarios/$id/editar" : "/profile/$id/editar";
            $this->redirect($redirect);
            return;
        }
    }

    public function confirmDelete($id, $context = 'admin')
    {
        // Verificar permisos según contexto
        if ($context === 'admin') {
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
                $_SESSION['errors'] = ['No tienes permisos para eliminar usuarios.'];
                $this->redirect('/');
                return;
            }
            // Evitar que un admin se elimine a sí mismo
            if ($_SESSION['usuario']['id'] == $id) {
                $_SESSION['errors'] = ['No puedes eliminar tu propia cuenta.'];
                $this->redirect('/admin/usuarios');
                return;
            }
        } elseif ($context === 'profile') {
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id'] != $id) {
                $_SESSION['errors'] = ['No tienes permiso para eliminar esta cuenta.'];
                $this->redirect('/');
                return;
            }
        }

        try {
            $usuarioRepository = new UsuarioRepository(BaseDatos::getInstancia());
            $usuario = $usuarioRepository->find($id);

            if (!$usuario) {
                $_SESSION['errors'] = ['El usuario no existe.'];
                $this->redirect($context === 'admin' ? '/admin/usuarios' : '/');
                return;
            }

            $data = [
                'title' => $context === 'admin' ? 'Confirmar Eliminación' : 'Confirmar Eliminación de Cuenta',
                'message' => $context === 'admin' ? 'Confirma que deseas eliminar este usuario' : '¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.',
                'usuario' => $usuario,
                'es_admin' => $context === 'admin',
                'es_perfil' => $context === 'profile',
                'showHeader' => true,
                'showFooter' => true
            ];
            return $this->view('usuarios/delete-confirm', $data);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error al cargar el usuario: ' . $e->getMessage()];
            $this->redirect($context === 'admin' ? '/admin/usuarios' : '/');
            return;
        }
    }

    public function delete($id, $context = 'admin')
    {
        try {
            // Verificar permisos según contexto
            if ($context === 'admin') {
                if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
                    $_SESSION['errors'] = ['No tienes permisos para eliminar usuarios.'];
                    $this->redirect('/');
                    return;
                }
                if ($_SESSION['usuario']['id'] == $id) {
                    $_SESSION['errors'] = ['No puedes eliminar tu propia cuenta desde aquí.'];
                    $this->redirect('/admin/usuarios');
                    return;
                }
            } elseif ($context === 'profile') {
                if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id'] != $id) {
                    $_SESSION['errors'] = ['No tienes permiso para eliminar esta cuenta.'];
                    $this->redirect('/');
                    return;
                }
            }

            $usuarioRepository = new UsuarioRepository(BaseDatos::getInstancia());
            $usuario = $usuarioRepository->find($id);

            if (!$usuario) {
                $_SESSION['errors'] = ['El usuario no existe.'];
                $this->redirect($context === 'admin' ? '/admin/usuarios' : '/');
                return;
            }

            // No permitir eliminar a otros admins (solo para contexto admin)
            if ($context === 'admin' && $usuario['rol'] === 'admin') {
                $_SESSION['errors'] = ['No puedes eliminar a otros administradores.'];
                $this->redirect('/admin/usuarios');
                return;
            }

            $resultado = $usuarioRepository->delete($id);

            if ($resultado) {
                if ($context === 'profile') {
                    session_destroy();
                    $_SESSION = [];
                    $_SESSION['success'] = 'Tu cuenta ha sido eliminada correctamente.';
                    $this->redirect('/login');
                } else {
                    $_SESSION['success'] = "Usuario '{$usuario['nombre']} {$usuario['apellidos']}' eliminado correctamente.";
                    $this->redirect('/admin/usuarios');
                }
                return;
            } else {
                $_SESSION['errors'] = ['Error al eliminar ' . ($context === 'admin' ? 'el usuario' : 'tu cuenta') . '.'];
                $this->redirect($context === 'admin' ? '/admin/usuarios' : '/');
                return;
            }

        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            $this->redirect($context === 'admin' ? '/admin/usuarios' : '/');
            return;
        }
    }


}

