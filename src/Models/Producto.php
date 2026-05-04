<?php

namespace Models;

/**
 * Producto - Modelo para productos del catálogo
 *
 * @package Models
 */
class Producto{
    public function __construct(
        private ?int $id = null,
        private ?int $categoria_id = null,
        private string $nombre = '',
        private ?string $descripcion = null,
        private float $precio = 0.0,
        private ?float $precio_oferta = null,
        private int $stock = 0,
        private int $activo = 1,
        private ?string $imagen = null
    ){}

    /**
     * Crea una instancia de Producto desde un array de datos
     *
     * @param array $data Array con los datos del producto
     * @return self
     */
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

    /**
     * Obtiene el identificador del producto
     *
     * @return int|null
     */
    public function getId(): ?int { return $this->id; }

    /**
     * Obtiene el identificador de la categoría
     *
     * @return int|null
     */
    public function getCategoriaId(): ?int { return $this->categoria_id; }

    /**
     * Obtiene el nombre del producto
     *
     * @return string
     */
    public function getNombre(): string { return $this->nombre; }

    /**
     * Obtiene la descripción del producto
     *
     * @return string|null
     */
    public function getDescripcion(): ?string { return $this->descripcion; }

    /**
     * Obtiene el precio del producto
     *
     * @return float
     */
    public function getPrecio(): float { return $this->precio; }

    /**
     * Obtiene el precio en oferta
     *
     * @return float|null
     */
    public function getPrecioOferta(): ?float { return $this->precio_oferta; }

    /**
     * Obtiene el stock disponible
     *
     * @return int
     */
    public function getStock(): int { return $this->stock; }

    /**
     * Obtiene el estado de activación del producto
     *
     * @return int
     */
    public function getActivo(): int { return $this->activo; }

    /**
     * Obtiene la ruta de la imagen del producto
     *
     * @return string|null
     */
    public function getImagen(): ?string { return $this->imagen; }

    /**
     * Establece el identificador del producto
     *
     * @param int|null $id Identificador del producto
     * @return void
     */
    public function setId(?int $id): void { $this->id = $id; }

    /**
     * Establece el identificador de la categoría
     *
     * @param int|null $categoria_id Identificador de la categoría
     * @return void
     */
    public function setCategoriaId(?int $categoria_id): void { $this->categoria_id = $categoria_id; }

    /**
     * Establece el nombre del producto
     *
     * @param string $nombre Nombre del producto
     * @return void
     */
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }

    /**
     * Establece la descripción del producto
     *
     * @param string|null $descripcion Descripción del producto
     * @return void
     */
    public function setDescripcion(?string $descripcion): void { $this->descripcion = $descripcion; }

    /**
     * Establece el precio del producto
     *
     * @param float $precio Precio del producto
     * @return void
     */
    public function setPrecio(float $precio): void { $this->precio = $precio; }

    /**
     * Establece el precio en oferta
     *
     * @param float|null $precio_oferta Precio en oferta
     * @return void
     */
    public function setPrecioOferta(?float $precio_oferta): void { $this->precio_oferta = $precio_oferta; }

    /**
     * Establece el stock disponible
     *
     * @param int $stock Cantidad en stock
     * @return void
     */
    public function setStock(int $stock): void { $this->stock = $stock; }

    /**
     * Establece el estado de activación del producto
     *
     * @param int $activo 1 para activo, 0 para inactivo
     * @return void
     */
    public function setActivo(int $activo): void { $this->activo = $activo; }

    /**
     * Establece la ruta de la imagen del producto
     *
     * @param string|null $imagen Ruta de la imagen
     * @return void
     */
    public function setImagen(?string $imagen): void { $this->imagen = $imagen; }
}
