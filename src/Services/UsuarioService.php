<?php

namespace Services;

use Repositories\UsuarioRepository;
use Core\BaseDatos;
use Models\Usuario;
use Services\MailService;

/**
 * UsuarioService - Servicio para gestionar autenticación, registro y operaciones de usuarios
 *
 * @package Services
 * @uses UsuarioRepository
 * @uses BaseDatos
 * @uses Usuario
 * @uses MailService
 */
class UsuarioService extends Service
{
    private UsuarioRepository $repository;

    /**
     * Constructor de UsuarioService
     */
    public function __construct()
    {
        $this->repository = new UsuarioRepository(BaseDatos::getInstancia());
    }

    /**
     * Registra un nuevo usuario o reenvía confirmación si existe sin confirmar
     *
     * @param array $userData Array con 'email' y 'password'
     * @return string|bool 'creado', 'reenviado' o false
     */
    public function registrar($userData)
    {
        try {
            $usuarioExistente = $this->repository->findByEmail($userData['email']);

            $passwordHash = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            $token = bin2hex(random_bytes(16));
            $token_exp = date('Y-m-d H:i:s', strtotime('+10 minutes'));

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

    /**
     * Crea un nuevo usuario desde el panel admin
     *
     * @param array $userData Array con datos del usuario
     * @param int $adminId Identificador del admin que crea el usuario
     * @return int|bool Identificador del usuario creado o false
     */
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
                'nombre' => $userData['nombre'] ?? '',
                'apellidos' => $userData['apellidos'] ?? '',
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

    /**
     * Autentica un usuario validando email y contraseña
     *
     * @param string $email Email del usuario
     * @param string $password Contraseña del usuario
     * @return array|bool Array con datos del usuario o false/string de error
     */
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
                'direccion' => $usuario->getDireccion(),
                'rol'       => $usuario->getRol(),
            ];

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Confirma la cuenta de un usuario mediante token
     *
     * @param string $token Token de confirmación
     * @return bool|string True si se confirma, 'expirado' o false
     */
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

    /**
     * Solicita reset de contraseña enviando email con token
     *
     * @param string $email Email del usuario
     * @return bool True si se envía correctamente
     */
    public function solicitarPassword($email)
    {
        $usuario = $this->repository->findByEmail($email);
        if ($usuario) {
            $token = bin2hex(random_bytes(32));
            $expiracion = date("Y-m-d H:i:s", strtotime('+10 minutes'));

            $this->repository->guardarTokenPassword($email, $token, $expiracion);
            $mailService = new MailService();
            return $mailService->enviarEmailReset($email, $token);
        }
        return false;
    }

    /**
     * Valida un token de reset de contraseña
     *
     * @param string $token Token a validar
     * @return string|false Email si es válido, false en caso contrario
     */
    public function validarTokenReset($token) {
        return $this->repository->validarToken($token);
    }

    /**
     * Completa el reset de contraseña de un usuario
     *
     * @param string $email Email del usuario
     * @param string $password Nueva contraseña
     * @return bool True si se cambia correctamente
     */
    public function completarReset($email, $password) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        return $this->repository->cambiarPassword($email, $passwordHash);
    }
}
