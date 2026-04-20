<?php

namespace Models;

class Producto{
    public function __construct(
        private int|null $id = null,
        private int|null $categoria_id = null,
        private string $nombre = '',
        private string|null $descripcion = null,
        private float $precio = 0.0,
        private float|null $precio_oferta = null,
        private int $stock = 0,
        private int $activo = 1,
        private string|null $imagen = null
    ){}

    public static function fromArray(array $data): self
    {
        $id = (isset($data['id']) && $data['id'] !== '') ? (int)$data['id'] : null;
        return new self(
            id: $id,
            categoria_id: $data['categoria_id'] ?? null,
            nombre: $data['nombre'] ?? '',
            descripcion: $data['descripcion'] ?? null,
            precio: $data['precio'] ?? 0,
            precio_oferta: $data['precio_oferta'] ?? null,
            stock: $data['stock'] ?? 0,
            activo: $data['activo'] ?? 1,
            imagen: $data['imagen'] ?? null
        );
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getCategoriaId(): ?int { return $this->categoria_id; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): ?string { return $this->descripcion; }
    public function getPrecio(): float { return $this->precio; }
    public function getPrecioOferta(): ?float { return $this->precio_oferta; }
    public function getStock(): int { return $this->stock; }
    public function getActivo(): int { return $this->activo; }
    public function getImagen(): ?string { return $this->imagen; }

    // Setters
    public function setId(?int $id): void { $this->id = $id; }
    public function setCategoriaId(?int $categoria_id): void { $this->categoria_id = $categoria_id; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setDescripcion(?string $descripcion): void { $this->descripcion = $descripcion; }
    public function setPrecio(float $precio): void { $this->precio = $precio; }
    public function setPrecioOferta(?float $precio_oferta): void { $this->precio_oferta = $precio_oferta; }
    public function setStock(int $stock): void { $this->stock = $stock; }
    public function setActivo(int $activo): void { $this->activo = $activo; }
    public function setImagen(?string $imagen): void { $this->imagen = $imagen; }
}
