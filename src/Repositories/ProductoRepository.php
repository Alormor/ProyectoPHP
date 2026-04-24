<?php

namespace Repositories;

use Core\BaseDatos;

class ProductoRepository extends Repository
{
    protected $table = 'productos';
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = BaseDatos::getInstancia();
    }

    public function findAll()
    {
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

    public function find($id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id AND activo = 1 LIMIT 1";
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

    public function findByCategoria($categoria_id)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE categoria_id = :categoria_id AND activo = 1";
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
}
