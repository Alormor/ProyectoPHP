<?php

namespace Repositories;

use Core\BaseDatos;

/**
 * CarritoRepository - Repositorio para gestionar operaciones del carrito de compra
 *
 * @package Repositories
 * @uses BaseDatos
 */
class CarritoRepository extends Repository
{
    protected $table = 'carrito';
    protected $db;

    /**
     * Constructor de CarritoRepository
     */
    public function __construct() {
        $this->db = BaseDatos::getInstancia();
    }

    /**
     * Obtiene todos los productos del carrito de un usuario con detalles
     *
     * @param int $usuario_id Identificador del usuario
     * @return array Array de productos del carrito
     */
    public function findByUser(int $usuario_id): array {
        $sql = "SELECT c.*, p.nombre, COALESCE(p.precio_oferta, p.precio) AS precio, p.imagen, p.stock
                FROM carrito c
                JOIN productos p ON c.producto_id = p.id
                WHERE c.usuario_id = :usuario_id";
        $params = [':usuario_id' => ['valor' => $usuario_id, 'tipo' => \PDO::PARAM_INT]];

        $this->db->ejecutar($sql, $params);
        return $this->db->extraer_todos();
    }

    /**
     * Verifica si un producto ya existe en el carrito del usuario
     *
     * @param int $usuario_id Identificador del usuario
     * @param int $producto_id Identificador del producto
     * @return array|null Datos del carrito si existe, null en caso contrario
     */
    public function findProductoInCarrito(int $usuario_id, int $producto_id) {
        $sql = "SELECT * FROM {$this->table} WHERE usuario_id = :uid AND producto_id = :pid LIMIT 1";
        $params = [
            ':uid' => ['valor' => $usuario_id, 'tipo' => \PDO::PARAM_INT],
            ':pid' => ['valor' => $producto_id, 'tipo' => \PDO::PARAM_INT]
        ];
        $this->db->ejecutar($sql, $params);
        return $this->db->extraer_registro();
    }

    /**
     * Crea un nuevo producto en el carrito
     *
     * @param array $data Array con datos del producto (usuario_id, producto_id, cantidad)
     * @return bool True si se crea correctamente, false en caso contrario
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO {$this->table} (usuario_id, producto_id, cantidad) VALUES (:uid, :pid, :cant)";
        $params = [
            ':uid' => ['valor' => $data['usuario_id']],
            ':pid' => ['valor' => $data['producto_id']],
            ':cant' => ['valor' => $data['cantidad']]
        ];
        return $this->db->ejecutar($sql, $params);
    }

    /**
     * Actualiza la cantidad de un producto en el carrito
     *
     * @param int $usuario_id Identificador del usuario
     * @param int $producto_id Identificador del producto
     * @param int $nueva_cantidad Nueva cantidad del producto
     * @return bool True si se actualiza correctamente, false en caso contrario
     */
    public function updateCantidad(int $usuario_id, int $producto_id, int $nueva_cantidad): bool {
        $sql = "UPDATE {$this->table} SET cantidad = :cant WHERE usuario_id = :uid AND producto_id = :pid";
        $params = [
            ':cant' => ['valor' => $nueva_cantidad],
            ':uid' => ['valor' => $usuario_id],
            ':pid' => ['valor' => $producto_id]
        ];
        return $this->db->ejecutar($sql, $params);
    }

    /**
     * Elimina un producto del carrito de un usuario
     *
     * @param int $usuario_id Identificador del usuario
     * @param int $producto_id Identificador del producto a eliminar
     * @return bool True si se elimina correctamente, false en caso contrario
     */
    public function delete(int $usuario_id, int $producto_id): bool {
        $sql = "DELETE FROM {$this->table} WHERE usuario_id = :uid AND producto_id = :pid";
        $params = [
            ':uid' => ['valor' => $usuario_id],
            ':pid' => ['valor' => $producto_id]
        ];
        return $this->db->ejecutar($sql, $params);
    }

    /**
     * Limpia el carrito de un usuario (elimina todos los productos)
     *
     * @param int $usuario_id Identificador del usuario
     * @return bool True si se limpia correctamente, false en caso contrario
     */
    public function deleteAll(int $usuario_id): bool {
        $sql = "DELETE FROM {$this->table} WHERE usuario_id = :uid";
        return $this->db->ejecutar($sql, [':uid' => ['valor' => $usuario_id]]);
    }

    /**
     * Obtiene los datos de un producto para el carrito de invitados
     * 
     * @param int $producto_id
     * @return array|null
    */
    public function findProductoDatos(int $producto_id) {
        $sql = "SELECT id, nombre, COALESCE(precio_oferta, precio) AS precio, imagen 
                FROM productos 
                WHERE id = :pid LIMIT 1";
        
        $params = [
            ':pid' => ['valor' => $producto_id, 'tipo' => \PDO::PARAM_INT]
        ];

        $this->db->ejecutar($sql, $params);
        return $this->db->extraer_registro();
    }
}
