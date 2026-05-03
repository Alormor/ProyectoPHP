<?php

namespace Repositories;

use PDOException;
use RuntimeException;
use Core\BaseDatos;
use Models\Pedido;

class PedidoRepository extends Repository
{
    protected $table = 'pedidos';

    public function __construct(
        private readonly BaseDatos $conexion
    ){}

    public function create(Pedido $pedido)
    {
        try{
            $sql = "INSERT INTO pedidos (usuario_id, provincia, localidad, direccion, subtotal, impuestos, coste_total, estado, fecha_pedido)
            VALUES (:usuario_id, :provincia, :localidad, :direccion, :subtotal, :impuestos, :coste_total, :estado, :fecha_pedido)";

            $param = [
                ":usuario_id" => ['valor' => $pedido->getUsuarioId(), 'tipo' => \PDO::PARAM_INT],
                ":provincia" => ['valor' => $pedido->getProvincia()],
                ":localidad" => ['valor' => $pedido->getLocalidad()],
                ":direccion" => ['valor' => $pedido->getDireccion()],
                ":subtotal" => ['valor' => $pedido->getSubtotal()],
                ":impuestos" => ['valor' => $pedido->getImpuestos()],
                ":coste_total" => ['valor' => $pedido->getCosteTotal()],
                ":estado" => ['valor' => $pedido->getEstado()],
                ":fecha_pedido" => ['valor' => $pedido->getFechaPedido()->format('Y-m-d H:i:s')],
            ];

            $exito = $this->conexion->ejecutar($sql, $param);

            if($exito){
                $nuevoId = $this->conexion->ultimoIdInsertado();
                if($nuevoId > 0){
                    $pedido->setId($nuevoId);
                }
            }

            return $exito;
        }catch (PDOException $e) {
            throw new RuntimeException(
                "Error al insertar el pedido: {$e->getMessage()}",
                previous: $e
            );
        }
    }

    public function findByUsuario(int $usuarioId): array
    {
        $sql = "SELECT * FROM pedidos 
                WHERE usuario_id = :usuario_id 
                ORDER BY fecha_pedido DESC";

        $param = [
            ":usuario_id" => ['valor' => $usuarioId, 'tipo' => \PDO::PARAM_INT]
        ];

        $this->conexion->ejecutar($sql, $param);

        return $this->conexion->extraer_todos();
    }

   public function find($id)
    {
        $sql = "SELECT * FROM pedidos WHERE id = :id";
        $param = [
            ":id" => ['valor' => $id, 'tipo' => \PDO::PARAM_INT]
        ];

        $this->conexion->ejecutar($sql, $param);
        
        return $this->conexion->extraer_todos(); 
    }
}