<?php

namespace Services;

use Repositories\UsuarioRepository;
use Core\BaseDatos;
use Models\Usuario;

class UsuarioService extends Service
{
    private UsuarioRepository $repository;
    public function __construct(
    private readonly BaseDatos $conexion
    ){
        $this->repository = new UsuarioRepository($this->conexion);
    }

    
    
    
    public function registrar($userData)
    {
        try {
            $usuario = $this->repository->findByEmail($userData['email']);
            
            if ($usuario) {
                return false;
            }
            
            $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            
            $nuevoUsuario = Usuario::fromArray([
                'email' => $userData['email'],
                'password' => $passwordHash,
                'rol' => 'usuario',
                'confirmado' => false,
            ]);
            
            // TODO: Generar token de confirmación de email
            
            $exito = $this->repository->create($nuevoUsuario);         
            
            if ($exito) {
                // TODO: Enviar email de bienvenida
                return $nuevoUsuario->getId();
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
            $admin = $this->repository->find($adminId);
            if (!$admin || $admin['rol'] !== 'admin') {
                return false;
            }
            
            // TODO: Verificar que el email no esté ya registrado
            
            $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            
            $nuevoUsuario = Usuario::fromArray([
                'email' => $userData['email'],
                'password' => $passwordHash,
                'rol' => $userData['rol'] ?? 'usuario',
                'confirmado' => true,
            ]);
            
            $exito = $this->repository->create($nuevoUsuario);
            
            if ($exito) {
                return $nuevoUsuario->getId();
            }
            
            return false;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function autenticar($email, $password)
    {
        try {
            // Buscar usuario por email
            $usuario = $this->repository->findByEmail($email);
            
            if (!$usuario) {
                return false;
            }
            
            // Verificar que contraseña coincida
            if (!password_verify($password, $usuario->getPassword())) {
                return false;
            }
            
            // Retornar datos del usuario
            return [
                'id'        => $usuario->getId(),
                'nombre'    => $usuario->getNombre(),
                'apellidos' => $usuario->getApellidos(),
                'email'     => $usuario->getEmail(),
                'rol'       => $usuario->getRol(),
            ];
            
        } catch (\Exception $e) {
            return false;
        }
    }
}
?>
