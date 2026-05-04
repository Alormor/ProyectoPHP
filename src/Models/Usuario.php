<?php

namespace Models;

/**
 * Usuario - Modelo para usuarios del sistema
 *
 * @package Models
 */
class Usuario{

    public function __construct(
        private ?int $id = null,
        private string $nombre = '',
        private string $apellidos = '',
        private string $email = '',
        private string $password = '',
        private string $direccion = '',
        private string $rol = 'usuario',
        private bool $confirmado = false,
        private ?string $token = null,
        private ?string $token_exp = null
    ){}

    /**
     * Crea una instancia de Usuario desde un array de datos
     *
     * @param array $data Array con los datos del usuario
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id = (isset($data['id']) && $data['id'] !== '') ? (int)$data['id'] : null;
        return new self(
            id: $id,
            nombre: $data['nombre'] ?? '',
            apellidos: $data['apellidos'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            direccion: $data['direccion'] ?? '',
            rol: $data['rol'] ?? 'usuario',
            confirmado: (bool)($data['confirmado'] ?? false),
            token: $data['token'] ?? null,
            token_exp: $data['token_exp'] ?? null
        );
    }

    /**
     * Obtiene el identificador del usuario
     *
     * @return int|null
     */
    public function getId(): ?int { return $this->id; }

    /**
     * Obtiene el nombre del usuario
     *
     * @return string
     */
    public function getNombre(): string { return $this->nombre; }

    /**
     * Obtiene los apellidos del usuario
     *
     * @return string
     */
    public function getApellidos(): string { return $this->apellidos; }

    /**
     * Obtiene el email del usuario
     *
     * @return string
     */
    public function getEmail(): string { return $this->email; }

    /**
     * Obtiene la contraseña del usuario
     *
     * @return string
     */
    public function getPassword(): string { return $this->password; }

    /**
     * Obtiene la dirección del usuario
     *
     * @return string
     */
    public function getDireccion(): string { return $this->direccion; }

    /**
     * Obtiene el rol del usuario
     *
     * @return string
     */
    public function getRol(): string { return $this->rol; }

    /**
     * Verifica si el usuario está confirmado
     *
     * @return bool
     */
    public function isConfirmado(): bool { return $this->confirmado; }

    /**
     * Obtiene el token de autenticación
     *
     * @return string|null
     */
    public function getToken(): ?string { return $this->token; }

    /**
     * Obtiene la expiración del token
     *
     * @return string|null
     */
    public function getToken_exp(): ?string { return $this->token_exp; }

    /**
     * Establece el identificador del usuario
     *
     * @param int|null $id Identificador del usuario
     * @return void
     */
    public function setId(?int $id): void { $this->id = $id; }

    /**
     * Establece el nombre del usuario
     *
     * @param string $nombre Nombre del usuario
     * @return void
     */
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }

    /**
     * Establece los apellidos del usuario
     *
     * @param string $apellidos Apellidos del usuario
     * @return void
     */
    public function setApellidos(string $apellidos): void { $this->apellidos = $apellidos; }

    /**
     * Establece el email del usuario
     *
     * @param string $email Email del usuario
     * @return void
     */
    public function setEmail(string $email): void { $this->email = $email; }

    /**
     * Establece la contraseña del usuario
     *
     * @param string $password Contraseña del usuario
     * @return void
     */
    public function setPassword(string $password): void { $this->password = $password; }

    /**
     * Establece la dirección del usuario
     *
     * @param string $direccion Dirección del usuario
     * @return void
     */
    public function setDireccion(string $direccion): void { $this->direccion = $direccion; }

    /**
     * Establece el rol del usuario
     *
     * @param string $rol Rol del usuario (usuario, admin)
     * @return void
     */
    public function setRol(string $rol): void { $this->rol = $rol; }

    /**
     * Establece el estado de confirmación del usuario
     *
     * @param bool $confirmado True si está confirmado, false en caso contrario
     * @return void
     */
    public function setConfirmado(bool $confirmado): void { $this->confirmado = $confirmado; }

    /**
     * Establece el token de autenticación
     *
     * @param string $token Token de autenticación
     * @return void
     */
    public function setToken(string $token): void { $this->token = $token; }

    /**
     * Establece la expiración del token
     *
     * @param string $token_exp Expiración del token
     * @return void
     */
    public function setToken_exp(string $token_exp): void { $this->token_exp = $token_exp; }
}