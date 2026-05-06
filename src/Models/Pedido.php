<?php

namespace Models;

use DateTime;

/**
 * Pedido - Modelo para gestionar pedidos de compra
 *
 * @package Models
 * @uses DateTime
 */
class Pedido{

    public function __construct(
        private ?int $id = null,
        private ?int $usuario_id = null,
        private string $provincia = "",
        private string $localidad = "",
        private string $direccion = "",
        private float $subtotal = 0,
        private float $impuestos = 0,
        private float $coste_total = 0,
        private string $estado = "pendiente",
        private DateTime $fecha_pedido = new DateTime()
    ){}

    /**
     * Crea una instancia de Pedido desde un array de datos
     *
     * @param array $data Array con los datos del pedido
     * @return self
     */
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

    /**
     * Obtiene el identificador del pedido
     *
     * @return int|null
     */
    public function getId(): ?int { return $this->id; }

    /**
     * Obtiene el identificador del usuario propietario del pedido
     *
     * @return int|null
     */
    public function getUsuarioId(): ?int { return $this->usuario_id; }

    /**
     * Obtiene la provincia del envío
     *
     * @return string
     */
    public function getProvincia(): string { return $this->provincia; }

    /**
     * Obtiene la localidad del envío
     *
     * @return string
     */
    public function getLocalidad(): string { return $this->localidad; }

    /**
     * Obtiene la dirección del envío
     *
     * @return string
     */
    public function getDireccion(): string { return $this->direccion; }

    /**
     * Obtiene el subtotal del pedido
     *
     * @return float
     */
    public function getSubtotal(): float { return $this->subtotal; }

    /**
     * Obtiene los impuestos del pedido
     *
     * @return float
     */
    public function getImpuestos(): float { return $this->impuestos; }

    /**
     * Obtiene el coste total del pedido
     *
     * @return float
     */
    public function getCosteTotal(): float { return $this->coste_total; }

    /**
     * Obtiene el estado del pedido
     *
     * @return string
     */
    public function getEstado(): string { return $this->estado; }

    /**
     * Obtiene la fecha del pedido
     *
     * @return DateTime
     */
    public function getFechaPedido(): DateTime { return $this->fecha_pedido; }

    /**
     * Establece el identificador del pedido
     *
     * @param int|null $id Identificador del pedido
     * @return void
     */
    public function setId(?int $id): void { $this->id = $id; }

    /**
     * Establece el identificador del usuario
     *
     * @param int|null $usuario_id Identificador del usuario
     * @return void
     */
    public function setUsuarioId(?int $usuario_id): void { $this->usuario_id = $usuario_id; }

    /**
     * Establece la provincia del envío
     *
     * @param string $provincia Provincia de envío
     * @return void
     */
    public function setProvincia(string $provincia): void { $this->provincia = $provincia; }

    /**
     * Establece la localidad del envío
     *
     * @param string $localidad Localidad de envío
     * @return void
     */
    public function setLocalidad(string $localidad): void { $this->localidad = $localidad; }

    /**
     * Establece la dirección del envío
     *
     * @param string $direccion Dirección de envío
     * @return void
     */
    public function setDireccion(string $direccion): void { $this->direccion = $direccion; }

    /**
     * Establece el subtotal del pedido
     *
     * @param float $subtotal Subtotal del pedido
     * @return void
     */
    public function setSubtotal(float $subtotal): void { $this->subtotal = $subtotal; }

    /**
     * Establece los impuestos del pedido
     *
     * @param float $impuestos Impuestos del pedido
     * @return void
     */
    public function setImpuestos(float $impuestos): void { $this->impuestos = $impuestos; }

    /**
     * Establece el coste total del pedido
     *
     * @param float $coste_total Coste total del pedido
     * @return void
     */
    public function setCosteTotal(float $coste_total): void { $this->coste_total = $coste_total; }

    /**
     * Establece el estado del pedido
     *
     * @param string $estado Estado del pedido (pendiente, completado, cancelado)
     * @return void
     */
    public function setEstado(string $estado): void { $this->estado = $estado; }

    /**
     * Establece la fecha del pedido
     *
     * @param DateTime $fecha_pedido Fecha del pedido
     * @return void
     */
    public function setFechaPedido(DateTime $fecha_pedido): void { $this->fecha_pedido = $fecha_pedido; }
}
