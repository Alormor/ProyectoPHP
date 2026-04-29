<?php

namespace Models;

class Carrito {
    public function __construct(
        private ?int $id = null,
        private int $usuario_id = 0,
        private int $producto_id = 0,
        private int $cantidad = 1,
    ){}

    public static function fromArray(array $data): self
    {
        $id = (isset($data['id']) && $data['id'] !== '') ? (int)$data['id'] : null;
        return new self(
            id: $id,
            usuario_id: $data['usuario_id'],
            producto_id: $data['producto_id'],
            cantidad: $data['cantidad']
        );
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUsuarioId(): int { return $this->usuario_id; }
    public function getProductoId(): int { return $this->producto_id; }
    public function getCantidad(): int { return $this->cantidad; }

    // Setters
    public function setCantidad(int $cantidad): void { $this->cantidad = $cantidad; }
}
