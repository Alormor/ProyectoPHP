<?php

namespace Services;

use Repositories\UsuarioRepository;
use Core\BaseDatos;
use Models\Usuario;
use Services\MailService;

class UsuarioService extends Service
{
    private UsuarioRepository $repository;
    public function __construct()
    {
        $this->repository = new UsuarioRepository(BaseDatos::getInstancia());
    }

    public function registrar($userData)
    {
        try {
            $usuarioExistente = $this->repository->findByEmail($userData['email']);
            
            $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            $token = bin2hex(random_bytes(16));
            $token_exp = date('Y-m-d H:i:s', strtotime('+1 minutes'));

            if ($usuarioExistente) {
                // Si ya está confirmado, no se le deja registrar
                if ($usuarioExistente->isConfirmado()) {
                    return false; 
                } 
                
                // Se actualiza password, token y expiración
                $usuarioExistente->setPassword($passwordHash);
                $usuarioExistente->setToken($token);
                $usuarioExistente->setToken_exp($token_exp);
                $this->repository->updateRegistro($usuarioExistente);
                
                $mailService = new MailService();
                $mailService->enviarCorreoConfirmacion($userData['email'], $token);
                
                return "reenviado";
            }

            $nuevoUsuario = Usuario::fromArray([
                'email' => $userData['email'],
                'password' => $passwordHash,
                'token' => $token,
                'token_exp' => $token_exp
            ]);
                        
            $exito = $this->repository->create($nuevoUsuario);         
            
            if ($exito) {
                $mailService = new MailService();
                $mailService->enviarCorreoConfirmacion($userData['email'], $token);
                return "creado";
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
            
            // Verificar que el email no esté ya registrado
            $usuarioExistente = $this->repository->findByEmail($userData['email']);
            if ($usuarioExistente) {
                return false;
            }
            
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

            if (!$usuario->isConfirmado()) {
                return "no_confirmado";
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

    public function confirmarCuenta($token)
    {
        $usuario = $this->repository->findByToken($token);

        // Comprobar si el token existe y no ha expirado
        if ($usuario) {
            $ahora = new \DateTime();
            $fechaExpira = new \DateTime($usuario->getToken_exp());
            
            if ($ahora > $fechaExpira) {
                return "expirado";
            }
            
            return $this->repository->confirmarUsuario($usuario->getId());
        }

        return false;
    }
}
?>
