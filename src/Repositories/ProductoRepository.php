<?php

namespace Repositories;

use Core\BaseDatos;

/**
 * ProductoRepository - Repositorio para gestionar operaciones CRUD de productos
 *
 * @package Repositories
 * @uses BaseDatos
 */
class ProductoRepository extends Repository
{
    protected $table = 'productos';
    protected $db;

    /**
     * Constructor de ProductoRepository
     */
    public function __construct()
    {
        parent::__construct();
        $this->db = BaseDatos::getInstancia();
    }

    /**
     * Obtiene todos los productos
     *
     * @return array Array de productos
     */
    public function findAll()
    {
        try {
            $sql = "SELECT * FROM {$this->table}";

            if ($this->db->ejecutar($sql)) {
                return $this->db->extraer_todos();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function findAllActive(){
        try {
            $sql = "SELECT * FROM {$this->table} WHERE activo = 1";

            if ($this->db->ejecutar($sql)) {
                return $this->db->extraer_todos();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obtiene un producto específico por su identificador
     *
     * @param int $id Identificador del producto
     * @return array|null Datos del producto o null
     */
    public function find($id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
            $params = [
                ':id' => ['valor' => $id, 'tipo' => \PDO::PARAM_INT]
            ];

            if ($this->db->ejecutar($sql, $params)) {
                return $this->db->extraer_registro();
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Obtiene todos los productos de una categoría
     *
     * @param int $categoria_id Identificador de la categoría
     * @return array Array de productos de la categoría
     */
    public function findByCategoria($categoria_id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE categoria_id = :categoria_id";
            $params = [
                ':categoria_id' => ['valor' => $categoria_id, 'tipo' => \PDO::PARAM_INT]
            ];

            if ($this->db->ejecutar($sql, $params)) {
                return $this->db->extraer_todos();
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Crea un nuevo producto
     *
     * @param array $data Array con los datos del producto
     * @return int|false Identificador del producto creado o false
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table}
                    (categoria_id, nombre, descripcion, precio, precio_oferta, stock, activo, imagen)
                    VALUES
                    (:categoria_id, :nombre, :descripcion, :precio, :precio_oferta, :stock, :activo, :imagen)";

            $params = [
                ':categoria_id' => ['valor' => $data['categoria_id']],
                ':nombre' => ['valor' => $data['nombre']],
                ':descripcion' => ['valor' => $data['descripcion'] ?? null],
                ':precio' => ['valor' => $data['precio']],
                ':precio_oferta' => ['valor' => $data['precio_oferta'] ?? null],
                ':stock' => ['valor' => $data['stock'] ?? 0],
                ':activo' => ['valor' => $data['activo'] ?? 1],
                ':imagen' => ['valor' => $data['imagen'] ?? null],
            ];

            if ($this->db->ejecutar($sql, $params)) {
                return $this->db->ultimoIdInsertado();
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Actualiza un producto existente
     *
     * @param int $id Identificador del producto
     * @param array $data Array con los datos a actualizar
     * @return bool True si se actualiza correctamente, false en caso contrario
     */
    public function update($id, $data)
    {
        try {
            $setClauses = [];
            $params = [':id' => ['valor' => $id, 'tipo' => \PDO::PARAM_INT]];

            foreach ($data as $key => $value) {
                $setClauses[] = "{$key} = :{$key}";
                $params[":{$key}"] = ['valor' => $value];
            }

            if (empty($setClauses)) {
                return false;
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = :id";

            if ($this->db->ejecutar($sql, $params)) {
                return $this->db->filasAfectadas() > 0;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Elimina un producto
     *
     * @param int $id Identificador del producto a eliminar
     * @return bool True si se elimina correctamente, false en caso contrario
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $params = [
                ':id' => ['valor' => $id, 'tipo' => \PDO::PARAM_INT]
            ];

            if ($this->db->ejecutar($sql, $params)) {
                return $this->db->filasAfectadas() > 0;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Decrementa el stock de un producto
     *
     * @param int $id Identificador del producto
     * @param int $cantidad Cantidad a decrementar
     * @return bool True si se actualiza correctamente, false en caso contrario
     */
    public function decrementarStock($id, $cantidad)
    {
        try {
            $id = (int)$id;
            $cantidad = (int)$cantidad;
            if ($cantidad <= 0) {
                return false;
            }
            $sql = "UPDATE {$this->table} SET stock = stock - :cantidad WHERE id = :id";
            $params = [
                ':id' => ['valor' => $id, 'tipo' => \PDO::PARAM_INT],
                ':cantidad' => ['valor' => $cantidad, 'tipo' => \PDO::PARAM_INT],
            ];

            return $this->db->ejecutar($sql, $params) && $this->db->filasAfectadas() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Summary of obtenerStock
     * @param mixed $id
     * @return int|null
     */
    public function obtenerStock($id)
    {
        try {
            $sql = "SELECT stock FROM {$this->table} WHERE id = :id";
            $params = [
                ':id' => ['valor' => $id, 'tipo' => \PDO::PARAM_INT]
            ];

            if ($this->db->ejecutar($sql, $params)) {
                $resultado = $this->db->extraer_registro();
                return $resultado ? (int)$resultado['stock'] : null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Desactiva un producto (lo marca como inactivo)
     *
     * @param int $id Identificador del producto
     * @return bool True si se actualiza correctamente, false en caso contrario
     */
    public function desactivarProducto($id)
    {
        try {
            $sql = "UPDATE {$this->table} SET activo = 0 WHERE id = :id";
            $params = [
                ':id' => ['valor' => $id, 'tipo' => \PDO::PARAM_INT]
            ];

            return $this->db->ejecutar($sql, $params) && $this->db->filasAfectadas() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

}
