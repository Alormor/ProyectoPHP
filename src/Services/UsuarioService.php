<?php

namespace Services;

use Repositories\UsuarioRepository;

class UsuarioService extends Service
{
    private $usuarioRepository;
    
    public function __construct()
    {
        parent::__construct();
        $this->usuarioRepository = new UsuarioRepository();
    }
    
    public function registrar($userData)
    {
        try {
            // Verificar que el email no esté ya registrado
            $usuarioExistente = $this->usuarioRepository->findByEmail($userData['email']);
            if ($usuarioExistente) {
                return false;
            }
            
            $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            
            $datosParaGuardar = [
                'nombre' => $userData['nombre'],
                'apellidos' => $userData['apellidos'],
                'email' => $userData['email'],
                'password' => $passwordHash,
                'rol' => 'usuario',  // Los auto-registros siempre son usuarios normales
                'confirmado' => false,
            ];
            
            // TODO: Generar token de confirmación de email
            
            $usuarioId = $this->usuarioRepository->create($datosParaGuardar);
            
            if ($usuarioId) {
                // TODO: Enviar email de bienvenida
                return $usuarioId;
            }
            
            return false;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function crear($userData, $adminId)
    {
        try {
            // Verificar que el admin existe y tiene rol de admin
            $admin = $this->usuarioRepository->find($adminId);
            if (!$admin || $admin['rol'] !== 'admin') {
                return false;
            }
            
            // Verificar que el email no esté ya registrado
            $usuarioExistente = $this->usuarioRepository->findByEmail($userData['email']);
            if ($usuarioExistente) {
                return false;
            }
            
            $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            
            // Validar rol: solo admins pueden asignar roles diferentes a 'usuario'
            $rol = $userData['rol'] ?? 'usuario';
            if (!in_array($rol, ['usuario', 'admin'])) {
                $rol = 'usuario';
            }
            
            $datosParaGuardar = [
                'nombre' => $userData['nombre'],
                'apellidos' => $userData['apellidos'],
                'email' => $userData['email'],
                'password' => $passwordHash,
                'rol' => $rol,
                'confirmado' => true,
            ];
            
            $usuarioId = $this->usuarioRepository->create($datosParaGuardar);
            
            if ($usuarioId) {
                return $usuarioId;
            }
            
            return false;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function buscarPorEmail($email)
    {
        // TODO: Implementar búsqueda de usuario por email
        return null;
    }
    
    public function autenticar($email, $password)
    {
        try {
            // Buscar usuario por email
            $usuario = $this->usuarioRepository->findByEmail($email);
            
            if (!$usuario) {
                return false;
            }
            
            // Verificar que contraseña coincida
            if (!password_verify($password, $usuario['password'])&& !($password == $usuario['password'])){
                return false;
            }
            
            
            // Retornar datos del usuario
            return [
                'id'        => $usuario['id'],
                'nombre'    => $usuario['nombre'],
                'apellidos' => $usuario['apellidos'],
                'email'     => $usuario['email'],
                'rol'       => $usuario['rol'],
            ];
            
        } catch (\Exception $e) {
            return false;
        }
    }
}
?>
