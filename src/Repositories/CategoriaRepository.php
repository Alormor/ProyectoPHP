<?php

namespace Repositories;

use Core\BaseDatos;

class CategoriaRepository extends Repository
{
    protected $table = 'categorias';
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = BaseDatos::getInstancia();
    }

    public function findAll()
    {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY nombre ASC";

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

    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table} (nombre, descripcion) VALUES (:nombre, :descripcion)";
            $params = [
                ':nombre' => ['valor' => $data['nombre']],
                ':descripcion' => ['valor' => $data['descripcion'] ?? null]
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
            $params = [':id' => ['valor' => $id, 'tipo' => \PDO::PARAM_INT]];

            if ($this->db->ejecutar($sql, $params)) {
                return $this->db->filasAfectadas() > 0;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
