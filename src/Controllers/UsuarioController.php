<?php

namespace Controllers;

use Core\Controller;
use Core\BaseDatos;
use Request\UserRequest;
use Services\UsuarioService;
use Repositories\UsuarioRepository;

class UsuarioController extends Controller
{

    public function index()
    {
        // Verificar que es admin
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos para acceder a esta página.'];
            $this->redirect('/');
            return;
        }

        try {
            $usuarioService = new UsuarioService(BaseDatos::getInstancia());
            $usuarioRepository = new \Repositories\UsuarioRepository(BaseDatos::getInstancia());
            $usuarios = $usuarioRepository->findAll();

            $data = [
                'title' => 'Gestión de Usuarios',
                'message' => 'Administra todos los usuarios del sistema.',
                'usuarios' => $usuarios,
                'es_admin' => true,
                'showHeader' => true,
                'showFooter' => true
            ];
            return $this->view('usuarios/index', $data);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error al cargar los usuarios: ' . $e->getMessage()];
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
            'showHeader' => true,
            'showFooter' => true
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

    public function edit($id)
    {
        // Verificar que es admin
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos para editar usuarios.'];
            $this->redirect('/');
            return;
        }

        try {
            $usuarioRepository = new UsuarioRepository(BaseDatos::getInstancia());
            $usuario = $usuarioRepository->find($id);

            if (!$usuario) {
                $_SESSION['errors'] = ['El usuario no existe.'];
                $this->redirect('/admin/usuarios');
                return;
            }

            $data = [
                'title' => 'Editar Usuario',
                'message' => 'Edita los datos del usuario',
                'usuario' => $usuario,
                'es_admin' => true,
                'showHeader' => true,
                'showFooter' => true
            ];
            return $this->view('usuarios/edit', $data);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error al cargar el usuario: ' . $e->getMessage()];
            $this->redirect('/admin/usuarios');
            return;
        }
    }

    public function update($id)
    {
        try {
            // Verificar permisos de administrador
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
                $_SESSION['errors'] = ['No tienes permisos para editar usuarios.'];
                $this->redirect('/');
                return;
            }

            $errors = [];

            // Obtener datos del formulario
            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $email = trim(strtolower($_POST['email'] ?? ''));
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
                $this->redirect("/admin/usuarios/$id/editar");
                return;
            }

            // Verificar que el email no esté duplicado (si cambió)
            $usuarioRepository = new UsuarioRepository(BaseDatos::getInstancia());
            $usuarioActual = $usuarioRepository->find($id);

            if (!$usuarioActual) {
                $_SESSION['errors'] = ['El usuario no existe.'];
                $this->redirect('/admin/usuarios');
                return;
            }

            // No permitir cambiar el rol de un admin
            if ($usuarioActual['rol'] === 'admin' && $rol !== 'admin') {
                $_SESSION['errors'] = ['No puedes cambiar el rol de un administrador.'];
                $_SESSION['form_data'] = [
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'email' => $email,
                    'rol' => $rol
                ];
                $this->redirect("/admin/usuarios/$id/editar");
                return;
            }

            // Si el email cambió, verificar que no exista otro usuario con ese email
            if ($email !== $usuarioActual['email']) {
                $usuarioExistente = $usuarioRepository->findByEmail($email);
                if ($usuarioExistente) {
                    $_SESSION['errors'] = ['El email ya está en uso.'];
                    $_SESSION['form_data'] = [
                        'nombre' => $nombre,
                        'apellidos' => $apellidos,
                        'email' => $email,
                        'rol' => $rol
                    ];
                    $this->redirect("/admin/usuarios/$id/editar");
                    return;
                }
            }

            // Actualizar usuario en BD
            $resultado = $usuarioRepository->update($id, [
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'email' => $email,
                'rol' => $rol
            ]);

            if ($resultado) {
                $_SESSION['success'] = 'Usuario actualizado correctamente.';
                unset($_SESSION['form_data']);
                $this->redirect('/admin/usuarios');
                return;
            } else {
                $_SESSION['errors'] = ['Error al actualizar el usuario.'];
                $_SESSION['form_data'] = [
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'email' => $email,
                    'rol' => $rol
                ];
                $this->redirect("/admin/usuarios/$id/editar");
                return;
            }

        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            $this->redirect("/admin/usuarios/$id/editar");
            return;
        }
    }

    public function confirmDelete($id)
    {
        // Verificar que es admin
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos para eliminar usuarios.'];
            $this->redirect('/');
            return;
        }

        try {
            // Evitar que un admin se elimine a sí mismo
            if ($_SESSION['usuario']['id'] == $id) {
                $_SESSION['errors'] = ['No puedes eliminar tu propia cuenta.'];
                $this->redirect('/admin/usuarios');
                return;
            }

            $usuarioRepository = new UsuarioRepository(BaseDatos::getInstancia());
            $usuario = $usuarioRepository->find($id);

            if (!$usuario) {
                $_SESSION['errors'] = ['El usuario no existe.'];
                $this->redirect('/admin/usuarios');
                return;
            }

            $data = [
                'title' => 'Confirmar Eliminación',
                'message' => 'Confirma que deseas eliminar este usuario',
                'usuario' => $usuario,
                'es_admin' => true,
                'showHeader' => true,
                'showFooter' => true
            ];
            return $this->view('usuarios/delete-confirm', $data);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error al cargar el usuario: ' . $e->getMessage()];
            $this->redirect('/admin/usuarios');
            return;
        }
    }

    public function delete($id)
    {
        // Eliminar un usuario (solo admins, pero no otros admins)
        try {
            // Verificar permisos de administrador
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
                $_SESSION['errors'] = ['No tienes permisos para eliminar usuarios.'];
                $this->redirect('/');
                return;
            }

            // Evitar que un admin se elimine a sí mismo
            if ($_SESSION['usuario']['id'] == $id) {
                $_SESSION['errors'] = ['No puedes eliminar tu propia cuenta desde aquí.'];
                $this->redirect('/admin/usuarios');
                return;
            }

            $usuarioRepository = new UsuarioRepository(BaseDatos::getInstancia());
            $usuario = $usuarioRepository->find($id);

            if (!$usuario) {
                $_SESSION['errors'] = ['El usuario no existe.'];
                $this->redirect('/admin/usuarios');
                return;
            }

            // No permitir eliminar a otros admins
            if ($usuario['rol'] === 'admin') {
                $_SESSION['errors'] = ['No puedes eliminar a otros administradores.'];
                $this->redirect('/admin/usuarios');
                return;
            }

            // Eliminar usuario
            $resultado = $usuarioRepository->delete($id);

            if ($resultado) {
                $_SESSION['success'] = "Usuario '{$usuario['nombre']} {$usuario['apellidos']}' eliminado correctamente.";
                $this->redirect('/admin/usuarios');
                return;
            } else {
                $_SESSION['errors'] = ['Error al eliminar el usuario.'];
                $this->redirect('/admin/usuarios');
                return;
            }

        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            $this->redirect('/admin/usuarios');
            return;
        }
    }

    public function confirmDeleteProfile($id)
    {
        // Verificar que el usuario que intenta eliminar su cuenta sea el mismo
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id'] != $id) {
            $_SESSION['errors'] = ['No tienes permiso para eliminar esta cuenta.'];
            $this->redirect('/');
            return;
        }

        try {
            $usuarioRepository = new UsuarioRepository(BaseDatos::getInstancia());
            $usuario = $usuarioRepository->find($id);

            if (!$usuario) {
                $_SESSION['errors'] = ['El usuario no existe.'];
                $this->redirect('/');
                return;
            }

            $data = [
                'title' => 'Confirmar Eliminación de Cuenta',
                'message' => '¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.',
                'usuario' => $usuario,
                'showHeader' => true,
                'showFooter' => true,
                'es_perfil' => true
            ];
            return $this->view('usuarios/delete-confirm', $data);
        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error al cargar tu perfil: ' . $e->getMessage()];
            $this->redirect('/');
            return;
        }
    }

    public function deleteProfile($id)
    {
        // Eliminar la propia cuenta
        try {
            // Verificar que el usuario que intenta eliminar su cuenta sea el mismo
            if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id'] != $id) {
                $_SESSION['errors'] = ['No tienes permiso para eliminar esta cuenta.'];
                $this->redirect('/');
                return;
            }

            $usuarioRepository = new UsuarioRepository(BaseDatos::getInstancia());
            $usuario = $usuarioRepository->find($id);

            if (!$usuario) {
                $_SESSION['errors'] = ['El usuario no existe.'];
                $this->redirect('/');
                return;
            }

            // Eliminar usuario
            $resultado = $usuarioRepository->delete($id);

            if ($resultado) {
                // Cerrar la sesión después de eliminar la cuenta
                session_destroy();
                $_SESSION = [];
                $_SESSION['success'] = 'Tu cuenta ha sido eliminada correctamente.';
                $this->redirect('/login');
                return;
            } else {
                $_SESSION['errors'] = ['Error al eliminar tu cuenta.'];
                $this->redirect('/');
                return;
            }

        } catch (\Exception $e) {
            $_SESSION['errors'] = ['Error del servidor: ' . $e->getMessage()];
            $this->redirect('/');
            return;
        }
    }
}

