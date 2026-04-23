<?php

namespace Repositories;

use PDOException;
use RuntimeException;
use Core\BaseDatos;
use Models\Usuario;

class UsuarioRepository extends Repository
{
    protected $table = 'usuarios';

    public function __construct(
        private readonly BaseDatos $conexion
    ){}
    
    public function create(Usuario $usuario)
    {
        try{
            $sql = "INSERT INTO usuarios (email, password) 
            VALUES (:email, :password)";

            $param = [
                ":email" => ['valor' => $usuario->getEmail()],
                ":password" => ['valor' => $usuario->getPassword()],
            ];

            $exito = $this->conexion->ejecutar($sql, $param);

            if($exito){
                $nuevoId = $this->conexion->ultimoIdInsertado();
                if($nuevoId > 0){
                    $usuario->setId($nuevoId);
                }
            }

            return $exito;
        }catch (PDOException $e) {
            throw new RuntimeException(
                "Error al insertar al realizar el registro: {$e->getMessage()}",
                previous: $e
            );
        }
    }
    
        public function findByEmail(string $email): ?Usuario
    {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $params = [
                ":email" => ['valor' => $email]
            ];
            
            $this->conexion->ejecutar($sql, $params);
            $fila = $this->conexion->extraer_registro();

            if (!$fila) {
                return null;
            }

            return Usuario::fromArray($fila);
        } catch (PDOException $e) {
            throw new RuntimeException("Error al buscar el usuario: {$e->getMessage()}", previous: $e);
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
?>
