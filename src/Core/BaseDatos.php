<?php
declare(strict_types=1);


namespace Core;

use PDO;
use PDOStatement;
use RuntimeException;
use PDOException;



class BaseDatos
{
    private ?PDO $conexion;
    private ?PDOStatement $stmt = null;
    private static ?BaseDatos $instancia = null;


    public function __construct(
        private readonly string $servidor,
        private readonly string $usuario,
        private readonly string $pass,
        private readonly string $name,
        private readonly string $charset,
        private readonly string $port,
    )
    {
        $this->conexion = $this->conectar();
    }

    private function conectar(): PDO
    {
        try {
            $dsn = "mysql:host={$this->servidor};dbname={$this->name};charset={$this->charset};port={$this->port}";
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_FOUND_ROWS => true,
            ];
            return new PDO($dsn, $this->usuario, $this->pass, $opciones);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'No se pudo conectar a la base de datos.',
                previous: $e
            );
        }
    }

    public function extraer_todos(): array
    {
        if (!$this->stmt) {
            return [];
        }

        $resultados = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->stmt->closeCursor();
        $this->stmt = null;
        return $resultados;
    }

    public function extraer_registro(): mixed
    {
        if (!$this->stmt) {
            return false;
        }

        $fila = $this->stmt->fetch(PDO::FETCH_ASSOC);
        if ($fila) {
            $this->stmt->closeCursor();
        }
        return $fila;
    }

    /**
     * Prepara, vincula parámetros y ejecuta una consulta SQL.
     * Los parámetros se pasan como array asociativo.
     * Si no se indica 'tipo', se deduce automáticamente.
     */
    public function ejecutar(string $sql, array $params = []): bool
    {
        $this->stmt = $this->conexion->prepare($sql);

        foreach ($params as $clave => $datos) {
            $valor = $datos['valor'];
            $tipo = $datos['tipo'] ?? match (true) {
                is_null($valor) => PDO::PARAM_NULL,
                is_bool($valor) => PDO::PARAM_BOOL,
                is_int($valor) => PDO::PARAM_INT,
                default => PDO::PARAM_STR,
            };
            $this->stmt->bindValue($clave, $valor, $tipo);
        }

        return $this->stmt->execute();
    }

    public function ultimoIdInsertado(): int
    {
        return (int)$this->conexion->lastInsertId();
    }

    public function filasAfectadas(): int
    {
        return $this->stmt ? $this->stmt->rowCount() : 0;
    }

    public function cierra(): void
    {
        $this->stmt = null;
        $this->conexion = null;
    }

    // Transacciones
    public function iniciarTransaccion(): void
    {
        $this->conexion->beginTransaction();
    }

    public function confirmar(): void
    {
        $this->conexion->commit();
    }

    public function revertir(): void
    {
        $this->conexion->rollBack();
    }

    /** Utilizando el patrón Singleton
     * Creamos una instancia de la clase
     */
    public static function getInstancia(): BaseDatos
    {
        if (self::$instancia === null) {
            self::$instancia = new self(
                $_ENV['DB_HOST'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                $_ENV['DB_NAME'],   
                $_ENV['DB_CHARSET'],
                $_ENV['DB_PORT']
            );
        }
        return self::$instancia;
    }
}