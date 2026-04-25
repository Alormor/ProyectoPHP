<?php

namespace Models;

class Usuario{
    
    public function __construct(
        private ?int $id = null,
        private string $nombre = '',
        private string $apellidos = '',
        private string $email = '',
        private string $password = '',
        private string $rol = 'usuario',
        private bool $confirmado = false,
        private ?string $token = null,
        private ?string $token_exp = null
    ){}

    public static function fromArray(array $data): self
    {
        $id = (isset($data['id']) && $data['id'] !== '') ? (int)$data['id'] : null;
        return new self(
            id: $id,
            nombre: $data['nombre'] ?? '',
            apellidos: $data['apellidos'] ?? '',
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            rol: $data['rol'] ?? 'usuario',
            confirmado: (bool)($data['confirmado'] ?? false),
            token: $data['token'] ?? null,
            token_exp: $data['token_exp'] ?? null
        );
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getApellidos(): string { return $this->apellidos; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getRol(): string { return $this->rol; }
    public function isConfirmado(): bool { return $this->confirmado; }
    public function getToken(): string { return $this->token; }
    public function getToken_exp(): string { return $this->token_exp; }

    // Setters
    public function setId(?int $id): void { $this->id = $id; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setApellidos(string $apellidos): void { $this->apellidos = $apellidos; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setRol(string $rol): void { $this->rol = $rol; }
    public function setConfirmado(bool $confirmado): void { $this->confirmado = $confirmado; }
    public function setToken(string $token): void { $this->token = $token; }
    public function setToken_exp(string $token_exp): void { $this->token_exp = $token_exp; }
}