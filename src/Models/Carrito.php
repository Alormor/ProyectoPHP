<?php

namespace Models;

/**
 * Carrito - Modelo para gestionar artículos del carrito de compra
 *
 * @package Models
 */
class Carrito {
    public function __construct(
        private ?int $id = null,
        private int $usuario_id = 0,
        private int $producto_id = 0,
        private int $cantidad = 1,
    ){}

    /**
     * Crea una instancia de Carrito desde un array de datos
     *
     * @param array $data Array con los datos del carrito
     * @return self
     */
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

    /**
     * Obtiene el identificador del carrito
     *
     * @return int|null
     */
    public function getId(): ?int { return $this->id; }

    /**
     * Obtiene el identificador del usuario propietario del carrito
     *
     * @return int
     */
    public function getUsuarioId(): int { return $this->usuario_id; }

    /**
     * Obtiene el identificador del producto en el carrito
     *
     * @return int
     */
    public function getProductoId(): int { return $this->producto_id; }

    /**
     * Obtiene la cantidad del producto en el carrito
     *
     * @return int
     */
    public function getCantidad(): int { return $this->cantidad; }

    /**
     * Establece la cantidad del producto en el carrito
     *
     * @param int $cantidad Cantidad del producto
     * @return void
     */
    public function setCantidad(int $cantidad): void { $this->cantidad = $cantidad; }
}
