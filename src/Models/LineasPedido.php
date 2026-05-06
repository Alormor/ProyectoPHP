<?php

namespace Models;

/**
 * LineasPedido - Modelo para líneas de detalle de un pedido
 *
 * @package Models
 */
class LineasPedido{

    public function __construct(
        private ?int $id = null,
        private ?int $pedido_id = null,
        private ?int $producto_id = null,
        private int $unidades = 0,
        private float $precio_unitario = 0.0,
        private float $subtotal_linea = 0.0
    ){}


    /**
     * Crea una instancia de LineasPedido desde un array de datos
     *
     * @param array $data Array con los datos de la línea de pedido
     * @return self
     */
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

    /**
     * Obtiene el identificador de la línea de pedido
     *
     * @return int|null
     */
    public function getId(): ?int { return $this->id; }

    /**
     * Obtiene el identificador del pedido
     *
     * @return int|null
     */
    public function getPedidoId(): ?int { return $this->pedido_id; }

    /**
     * Obtiene el identificador del producto
     *
     * @return int|null
     */
    public function getProductoId(): ?int { return $this->producto_id; }

    /**
     * Obtiene la cantidad de unidades en la línea
     *
     * @return int
     */
    public function getUnidades(): int { return $this->unidades; }

    /**
     * Obtiene el precio unitario del producto
     *
     * @return float
     */
    public function getPrecioUnitario(): float { return $this->precio_unitario; }

    /**
     * Obtiene el subtotal de la línea de pedido
     *
     * @return float
     */
    public function getSubtotalLinea(): float { return $this->subtotal_linea; }

    /**
     * Establece el identificador de la línea de pedido
     *
     * @param int|null $id Identificador de la línea
     * @return void
     */
    public function setId(?int $id): void { $this->id = $id; }

    /**
     * Establece el identificador del pedido
     *
     * @param int|null $pedido_id Identificador del pedido
     * @return void
     */
    public function setPedidoId(?int $pedido_id): void { $this->pedido_id = $pedido_id; }

    /**
     * Establece el identificador del producto
     *
     * @param int|null $producto_id Identificador del producto
     * @return void
     */
    public function setProductoId(?int $producto_id): void { $this->producto_id = $producto_id; }

    /**
     * Establece la cantidad de unidades
     *
     * @param int $unidades Cantidad de unidades
     * @return void
     */
    public function setUnidades(int $unidades): void { $this->unidades = $unidades; }

    /**
     * Establece el precio unitario
     *
     * @param float $precio_unitario Precio unitario del producto
     * @return void
     */
    public function setPrecioUnitario(float $precio_unitario): void { $this->precio_unitario = $precio_unitario; }

    /**
     * Establece el subtotal de la línea
     *
     * @param float $subtotal_linea Subtotal de la línea
     * @return void
     */
    public function setSubtotalLinea(float $subtotal_linea): void { $this->subtotal_linea = $subtotal_linea; }
}