<?php

namespace Models;

class LineasPedido{

    public function __construct(
        private int|null $id = null,
        private int|null $pedido_id = null,
        private int|null $producto_id = null,
        private int $unidades = 0,
        private float $precio_unitario = 0.0,
        private float $subtotal_linea = 0.0
    ){}


    public static function fromArray(array $data): self{
        $id = (isset($data['id']) && $data['id'] !== '') ? (int)$data['id'] : null;
        $unidades = (int)($data['unidades'] ?? 1);
        $precio = (float)($data['precio_unitario'] ?? 0.0);
        
        return new self(
            id: $id,
            pedido_id: $data['pedido_id'] ?? null,
            producto_id: $data['producto_id'] ?? null,
            unidades: $unidades,
            precio_unitario: $precio,
            subtotal_linea: $unidades * $precio
        );
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getPedidoId(): ?int { return $this->pedido_id; }
    public function getProductoId(): ?int { return $this->producto_id; }
    public function getUnidades(): int { return $this->unidades; }
    public function getPrecioUnitario(): float { return $this->precio_unitario; }
    public function getSubtotalLinea(): float { return $this->subtotal_linea; }

    // Setters
    public function setId(?int $id): void { $this->id = $id; }
    public function setPedidoId(?int $pedido_id): void { $this->pedido_id = $pedido_id; }
    public function setProductoId(?int $producto_id): void { $this->producto_id = $producto_id; }
    public function setUnidades(int $unidades): void { $this->unidades = $unidades; }
    public function setPrecioUnitario(float $precio_unitario): void { $this->precio_unitario = $precio_unitario; }
    public function setSubtotalLinea(float $subtotal_linea): void { $this->subtotal_linea = $subtotal_linea; }
}