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
            $sql = "INSERT INTO usuarios (nombre, apellidos, email, password, rol, confirmado, token, token_exp)
            VALUES (:nombre, :apellidos, :email, :password, :rol, :confirmado, :token, :token_exp)";

            $param = [
                ":nombre" => ['valor' => $usuario->getNombre()],
                ":apellidos" => ['valor' => $usuario->getApellidos()],
                ":email" => ['valor' => $usuario->getEmail()],
                ":password" => ['valor' => $usuario->getPassword()],
                ":rol" => ['valor' => $usuario->getRol()],
                ":confirmado" => ['valor' => $usuario->isConfirmado(), 'tipo' => \PDO::PARAM_BOOL],
                ":token" => ['valor' => $usuario->getToken()],
                ":token_exp" => ['valor' => $usuario->getToken_exp()],
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

            if ($this->conexion->ejecutar($sql, $params)) {
                return $this->conexion->extraer_registro();
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

            if ($this->conexion->ejecutar($sql)) {
                return $this->conexion->extraer_todos();
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

            if ($this->conexion->ejecutar($sql, $params)) {
                return $this->conexion->filasAfectadas() > 0;
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

            if ($this->conexion->ejecutar($sql, $params)) {
                return $this->conexion->filasAfectadas() > 0;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function findByToken(string $token): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE token = :token LIMIT 1";
        $param = [":token" => ['valor' => $token]];
        
        $this->conexion->ejecutar($sql, $param);
        $resultado = $this->conexion->extraer_registro();

        if ($resultado) {
            return Usuario::fromArray($resultado);
        }
        
        return null;
    }

    // Confirma una cuenta si el token es correcto y no ha expirado
    public function confirmarUsuario(int $id): bool
    {
        $sql = "UPDATE usuarios SET confirmado = 1, token = NULL WHERE id = :id";
        $param = [":id" => ['valor' => $id]];
        
        return $this->conexion->ejecutar($sql, $param);
    }

    // Actualiza el token y su expiración
    public function updateRegistro(Usuario $usuario): bool
    {
        // Actualizamos contraseña, token y expiración
        $sql = "UPDATE usuarios SET password = :password, token = :token, token_exp = :token_exp WHERE id = :id";
        $param = [
            ":password"  => ['valor' => $usuario->getPassword()],
            ":token"     => ['valor' => $usuario->getToken()],
            ":token_exp" => ['valor' => $usuario->getToken_exp()],
            ":id"        => ['valor' => $usuario->getId()],
        ];
        
        return $this->conexion->ejecutar($sql, $param);
    }

    //
    public function guardarTokenPassword($email, $token, $expiracion) {
        $sql = "UPDATE usuarios SET token = :token, token_exp = :exp WHERE email = :email";
        $param =[
            ":email" => ['valor' => $email],
            ":token" => ['valor' => $token],
            ":exp" => ['valor' => $expiracion],
        ];
        
        $this->conexion->ejecutar($sql, $param);
    }

    public function validarToken($token) {
        $sql = "SELECT email FROM usuarios WHERE token = :token AND token_exp > NOW()";
        $param = [
            ":token" => ['valor' => $token]
        ];
        $this->conexion->ejecutar($sql, $param);
        $resultado = $this->conexion->extraer_registro();
        return $resultado ? $resultado['email'] : false;
    }

    public function cambiarPassword($email, $password){
        $sql = "UPDATE usuarios SET password = :password, token = NULL, token_exp = NULL WHERE email = :email";
        $param = [
            ":email" => ['valor' => $email],
            ":password" => ['valor' => $password],
        ];
        return $this->conexion->ejecutar($sql, $param);
    }

}
?>
