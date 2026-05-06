<?php

namespace Models;

/**
 * Categoria - Modelo para categorías de productos
 *
 * @package Models
 */
class Categoria{
    public function __construct(
        private ?int $id = null,
        private string $nombre = '',
        private ?string $descripcion = null,
    ){}

    /**
     * Crea una instancia de Categoria desde un array de datos
     *
     * @param array $data Array con los datos de la categoría
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id = (isset($data['id']) && $data['id'] !== '') ? (int)$data['id'] : null;
        return new self(
            id: $id,
            nombre: $data['nombre'] ?? '',
            descripcion: $data['descripcion'] ?? null
        );
    }

    /**
     * Obtiene el identificador de la categoría
     *
     * @return int|null
     */
    public function getId(): ?int { return $this->id; }

    /**
     * Obtiene el nombre de la categoría
     *
     * @return string
     */
    public function getNombre(): string { return $this->nombre; }

    /**
     * Obtiene la descripción de la categoría
     *
     * @return string|null
     */
    public function getDescripcion(): string { return $this->descripcion; }

    /**
     * Establece el identificador de la categoría
     *
     * @param int|null $id Identificador de la categoría
     * @return void
     */
    public function setId(?int $id): void { $this->id = $id; }

    /**
     * Establece el nombre de la categoría
     *
     * @param string $nombre Nombre de la categoría
     * @return void
     */
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }

    /**
     * Establece la descripción de la categoría
     *
     * @param string $descripcion Descripción de la categoría
     * @return void
     */
    public function setDescripcion(string $descripcion): void { $this->descripcion = $descripcion; }
}