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
            // TODO: Verificar que el email no esté ya registrado
            
            $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            
            $datosParaGuardar = [
                'nombre' => $userData['nombre'],
                'apellidos' => $userData['apellidos'],
                'email' => $userData['email'],
                'password' => $passwordHash,
                'rol' => 'user',
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
            // Verificar que el admin existe
            $admin = $this->usuarioRepository->find($adminId);
            if (!$admin || $admin['rol'] !== 'admin') {
                return false;
            }
            
            // TODO: Verificar que el email no esté ya registrado
            
            $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            
            $datosParaGuardar = [
                'nombre' => $userData['nombre'],
                'apellidos' => $userData['apellidos'],
                'email' => $userData['email'],
                'password' => $passwordHash,
                'rol' => $userData['rol'] ?? 'user',
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
        // TODO: Implementar autenticación
        return false;
    }
}
?>
