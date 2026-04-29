<?php

namespace Repositories;

use Core\BaseDatos;

class CarritoRepository extends Repository
{
    protected $table = 'carrito';
    protected $db;

    public function __construct() {
        $this->db = BaseDatos::getInstancia();
    }

    // Obtener todos los productos del carrito de un usuario
    public function findByUser(int $usuario_id): array {
        $sql = "SELECT c.*, p.nombre, p.precio, p.imagen, p.stock 
                FROM carrito c 
                JOIN productos p ON c.producto_id = p.id 
                WHERE c.usuario_id = :usuario_id";
        $params = [':usuario_id' => ['valor' => $usuario_id, 'tipo' => \PDO::PARAM_INT]];
        
        $this->db->ejecutar($sql, $params);
        return $this->db->extraer_todos();
    }

    // Comprobar si el producto ya existe en el carrito
    public function findProductoInCarrito(int $usuario_id, int $producto_id) {
        $sql = "SELECT * FROM {$this->table} WHERE usuario_id = :uid AND producto_id = :pid LIMIT 1";
        $params = [
            ':uid' => ['valor' => $usuario_id, 'tipo' => \PDO::PARAM_INT],
            ':pid' => ['valor' => $producto_id, 'tipo' => \PDO::PARAM_INT]
        ];
        $this->db->ejecutar($sql, $params);
        return $this->db->extraer_registro();
    }

    // Crear un nuevo producto en el carrito
    public function create(array $data): bool {
        $sql = "INSERT INTO {$this->table} (usuario_id, producto_id, cantidad) VALUES (:uid, :pid, :cant)";
        $params = [
            ':uid' => ['valor' => $data['usuario_id']],
            ':pid' => ['valor' => $data['producto_id']],
            ':cant' => ['valor' => $data['cantidad']]
        ];
        return $this->db->ejecutar($sql, $params);
    }

    // Actualizar la cantidad de un producto en el carrito
    public function updateCantidad(int $usuario_id, int $producto_id, int $nueva_cantidad): bool {
        $sql = "UPDATE {$this->table} SET cantidad = :cant WHERE usuario_id = :uid AND producto_id = :pid";
        $params = [
            ':cant' => ['valor' => $nueva_cantidad],
            ':uid' => ['valor' => $usuario_id],
            ':pid' => ['valor' => $producto_id]
        ];
        return $this->db->ejecutar($sql, $params);
    }

    // Eliminar un producto del carrito de un usuario
    public function delete(int $usuario_id, int $producto_id): bool {
        $sql = "DELETE FROM {$this->table} WHERE usuario_id = :uid AND producto_id = :pid";
        $params = [
            ':uid' => ['valor' => $usuario_id],
            ':pid' => ['valor' => $producto_id]
        ];
        return $this->db->ejecutar($sql, $params);
    }

    // Limpiar el carrito de un usuario
    public function deleteAll(int $usuario_id): bool {
        $sql = "DELETE FROM {$this->table} WHERE usuario_id = :uid";
        return $this->db->ejecutar($sql, [':uid' => ['valor' => $usuario_id]]);
    }
}
