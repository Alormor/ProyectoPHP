<?php

namespace Models;

use DateTime;

class Pedido{

    public function __construct(
        private int|null $id = null,
        private int|null $usuario_id = null,
        private string $provincia = "",
        private string $localidad = "",
        private string $direccion = "",
        private float $subtotal = 0,
        private float $impuestos = 0,
        private float $coste_total = 0,
        private string $estado = "pendiente",
        private DateTime $fecha_pedido = new DateTime()
    ){}

    public static function fromArray(array $data): self
    {
        $id = (isset($data['id']) && $data['id'] !== '') ? (int)$data['id'] : null;
        $fecha = new DateTime();
        if (isset($data['fecha_pedido'])) {
            $fecha = new DateTime($data['fecha_pedido']);
        }
        
        return new self(
            id: $id,
            usuario_id: $data['usuario_id'] ?? null,
            provincia: $data['provincia'] ?? '',
            localidad: $data['localidad'] ?? '',
            direccion: $data['direccion'] ?? '',
            subtotal: $data['subtotal'] ?? 0.0,
            impuestos: $data['impuestos'] ?? 0.0,
            coste_total: $data['coste_total'] ?? 0.0,
            estado: $data['estado'] ?? "pendiente",
            fecha_pedido: $fecha
        );
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUsuarioId(): ?int { return $this->usuario_id; }
    public function getProvincia(): string { return $this->provincia; }
    public function getLocalidad(): string { return $this->localidad; }
    public function getDireccion(): string { return $this->direccion; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getImpuestos(): float { return $this->impuestos; }
    public function getCosteTotal(): float { return $this->coste_total; }
    public function getEstado(): EstadoPedido { return $this->estado; }
    public function getFechaPedido(): DateTime { return $this->fecha_pedido; }

    // Setters
    public function setId(?int $id): void { $this->id = $id; }
    public function setUsuarioId(?int $usuario_id): void { $this->usuario_id = $usuario_id; }
    public function setProvincia(string $provincia): void { $this->provincia = $provincia; }
    public function setLocalidad(string $localidad): void { $this->localidad = $localidad; }
    public function setDireccion(string $direccion): void { $this->direccion = $direccion; }
    public function setSubtotal(float $subtotal): void { $this->subtotal = $subtotal; }
    public function setImpuestos(float $impuestos): void { $this->impuestos = $impuestos; }
    public function setCosteTotal(float $coste_total): void { $this->coste_total = $coste_total; }
    public function setEstado(EstadoPedido $estado): void { $this->estado = $estado; }
    public function setFechaPedido(DateTime $fecha_pedido): void { $this->fecha_pedido = $fecha_pedido; }
}
