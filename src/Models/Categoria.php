<?php

namespace Models;

class Categoria{
    public function __construct(
        private ?int $id = null,
        private string $nombre = '',
        private ?string $descripcion = null,
    ){}

    public static function fromArray(array $data): self
    {
        $id = (isset($data['id']) && $data['id'] !== '') ? (int)$data['id'] : null;
        return new self(
            id: $id,
            nombre: $data['nombre'] ?? '',
            descripcion: $data['descripcion'] ?? null
        );
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): string { return $this->descripcion; }

    // Setters
    public function setId(?int $id): void { $this->id = $id; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setDescripcion(string $descripcion): void { $this->descripcion = $descripcion; }
}