<?php

namespace Repositories;

use PDOException;
use RuntimeException;
use Core\BaseDatos;
use Models\Usuario;

/**
 * UsuarioRepository - Repositorio para gestionar operaciones CRUD de usuarios
 *
 * @package Repositories
 * @uses BaseDatos
 * @uses Usuario
 */
class UsuarioRepository extends Repository
{
    protected $table = 'usuarios';

    /**
     * Constructor de UsuarioRepository
     *
     * @param BaseDatos $conexion Instancia de la conexión a base de datos
     */
    public function __construct(
        private readonly BaseDatos $conexion
    ){}

    /**
     * Crea un nuevo usuario en la base de datos
     *
     * @param Usuario $usuario Objeto Usuario a crear
     * @return bool True si se crea correctamente
     * @throws RuntimeException Si hay error en la inserción
     */
    
    public function create(Usuario $usuario)
    {   
        
        try{
            $sql = "INSERT INTO usuarios (nombre, apellidos, email, password, direccion, rol, confirmado, token, token_exp)
            VALUES (:nombre, :apellidos, :email, :password, :direccion, :rol, :confirmado, :token, :token_exp)";

            $param = [
                ":nombre" => ['valor' => $usuario->getNombre()],
                ":apellidos" => ['valor' => $usuario->getApellidos()],
                ":email" => ['valor' => $usuario->getEmail()],
                ":password" => ['valor' => $usuario->getPassword()],
                ":direccion" => ['valor' => $usuario->getDireccion()],
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

    /**
     * Obtiene un usuario por su email
     *
     * @param string $email Email del usuario
     * @return Usuario|null Objeto Usuario o null
     * @throws RuntimeException Si hay error en la búsqueda
     */
    public function findByEmail(string $email): ?Usuario
    {
        try {
            // Normalizar el email para evitar discrepancias por espacios o mayúsculas
            $email = trim(strtolower($email));

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

    /**
     * Obtiene un usuario por su identificador
     *
     * @param int $id Identificador del usuario
     * @return array|null Datos del usuario o null
     */
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

    /**
     * Obtiene todos los usuarios
     *
     * @return array Array de usuarios
     */
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

    /**
     * Actualiza un usuario existente
     *
     * @param int $id Identificador del usuario
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

            if ($this->conexion->ejecutar($sql, $params)) {
                return $this->conexion->filasAfectadas() > 0;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Elimina un usuario
     *
     * @param int $id Identificador del usuario a eliminar
     * @return bool True si se elimina correctamente, false en caso contrario
     */
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

    /**
     * Obtiene un usuario por su token de autenticación
     *
     * @param string $token Token de autenticación
     * @return Usuario|null Objeto Usuario o null
     */
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

    /**
     * Confirma una cuenta de usuario
     *
     * @param int $id Identificador del usuario
     * @return bool True si se confirma correctamente
     */
    public function confirmarUsuario(int $id): bool
    {
        $sql = "UPDATE usuarios SET confirmado = 1, token = NULL WHERE id = :id";
        $param = [":id" => ['valor' => $id]];

        return $this->conexion->ejecutar($sql, $param);
    }

    /**
     * Actualiza el registro del usuario (contraseña y tokens)
     *
     * @param Usuario $usuario Objeto Usuario con datos actualizados
     * @return bool True si se actualiza correctamente
     */
    public function updateRegistro(Usuario $usuario): bool
    {
        $sql = "UPDATE usuarios SET password = :password, token = :token, token_exp = :token_exp WHERE id = :id";
        $param = [
            ":password"  => ['valor' => $usuario->getPassword()],
            ":token"     => ['valor' => $usuario->getToken()],
            ":token_exp" => ['valor' => $usuario->getToken_exp()],
            ":id"        => ['valor' => $usuario->getId()],
        ];

        return $this->conexion->ejecutar($sql, $param);
    }

    /**
     * Guarda un token de recuperación de contraseña
     *
     * @param string $email Email del usuario
     * @param string $token Token de recuperación
     * @param string $expiracion Fecha de expiración del token
     * @return bool True si se guarda correctamente
     */
    public function guardarTokenPassword($email, $token, $expiracion): bool {
        $sql = "UPDATE usuarios SET token = :token, token_exp = :exp WHERE email = :email";
        $param =[
            ":email" => ['valor' => $email],
            ":token" => ['valor' => $token],
            ":exp" => ['valor' => $expiracion],
        ];

        return $this->conexion->ejecutar($sql, $param);
    }

    /**
     * Valida un token de recuperación de contraseña
     *
     * @param string $token Token a validar
     * @return string|false Email del usuario si es válido, false en caso contrario
     */
    public function validarToken($token) {
        $sql = "SELECT email FROM usuarios WHERE token = :token AND token_exp > NOW()";
        $param = [
            ":token" => ['valor' => $token]
        ];
        $this->conexion->ejecutar($sql, $param);
        $resultado = $this->conexion->extraer_registro();
        return $resultado ? $resultado['email'] : false;
    }

    /**
     * Cambia la contraseña de un usuario
     *
     * @param string $email Email del usuario
     * @param string $password Nueva contraseña
     * @return bool True si se cambia correctamente
     */
    public function cambiarPassword($email, $password){
        $sql = "UPDATE usuarios SET password = :password, token = NULL, token_exp = NULL WHERE email = :email";
        $param = [
            ":email" => ['valor' => $email],
            ":password" => ['valor' => $password],
        ];
        return $this->conexion->ejecutar($sql, $param);
    }

    /**
     * Actualiza la dirección de un usuario
     *
     * @param int $id Identificador del usuario
     * @param string $direccion Nueva dirección
     * @return bool True si se actualiza correctamente
     */
    public function updateDireccion(int $id, string $direccion): bool
    {
        $sql = "UPDATE usuarios SET direccion = :direccion WHERE id = :id";
        $param = [
            ":direccion" => ['valor' => $direccion],
            ":id" => ['valor' => $id, 'tipo' => \PDO::PARAM_INT],
        ];

        return $this->conexion->ejecutar($sql, $param);
    }
}

