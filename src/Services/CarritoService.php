<?php

namespace Services;

use Repositories\CarritoRepository;

/**
 * CarritoService - Servicio para gestionar las operaciones del carrito de compra
 *
 * @package Services
 * @uses CarritoRepository
 */
class CarritoService extends Service {
    private CarritoRepository $repository;

    /**
     * Constructor de CarritoService
     */
    public function __construct() {
        $this->repository = new CarritoRepository();
    }

    /**
     * Obtiene el carrito de un usuario con detalles de productos
     *
     * @param int $usuario_id Identificador del usuario
     * @return array Array de productos en el carrito
     */
    public function obtenerCarrito($usuario_id) {
        return $this->repository->findByUser($usuario_id);
    }

    /**
     * Agrega un producto al carrito de un usuario o incrementa su cantidad
     *
     * @param int $usuario_id Identificador del usuario
     * @param int $producto_id Identificador del producto
     * @param int $cantidad Cantidad a agregar (por defecto 1)
     * @return bool True si se agrega correctamente
     */
    public function agregarProducto($usuario_id, $producto_id, $cantidad = 1) {
        $existente = $this->repository->findProductoInCarrito($usuario_id, $producto_id);

        // Si el producto ya existe en el carrito, aumenta su cantidad
        if ($existente) {
            $nueva_cantidad = $existente['cantidad'] + $cantidad;
            return $this->repository->updateCantidad($usuario_id, $producto_id, $nueva_cantidad);
        } else {
            return $this->repository->create([
                'usuario_id' => $usuario_id,
                'producto_id' => $producto_id,
                'cantidad' => $cantidad
            ]);
        }
    }

    /**
     * Elimina completamente un producto del carrito de un usuario
     *
     * @param int $usuario_id Identificador del usuario
     * @param int $producto_id Identificador del producto a eliminar
     * @return bool True si se elimina correctamente
     */
    public function eliminarProducto($usuario_id, $producto_id) {
        return $this->repository->delete($usuario_id, $producto_id);
    }

    /**
     * Vacía el carrito eliminando todos los productos de un usuario
     *
     * @param int $usuario_id Identificador del usuario
     * @return bool True si se vacía correctamente
     */
    public function vaciarCarrito($usuario_id) {
        return $this->repository->deleteAll($usuario_id);
    }

    /**
     * Decrementa la cantidad de un producto del carrito en una unidad
     *
     * @param int $usuario_id Identificador del usuario
     * @param int $producto_id Identificador del producto
     * @return bool True si se actualiza o elimina correctamente
     */
    public function borrarUno($usuario_id, $producto_id) {
        $existente = $this->repository->findProductoInCarrito($usuario_id, $producto_id);
        if ($existente && $existente['cantidad'] > 1) {
            return $this->repository->updateCantidad($usuario_id, $producto_id, $existente['cantidad'] - 1);
        } else {
            return $this->repository->delete($usuario_id, $producto_id);
        }
    }

    public function fusionarCarritoInvitado($usuario_id, $carrito_temporal) {
        foreach ($carrito_temporal as $producto_id => $cantidad) {
            $this->agregarProducto($usuario_id, $producto_id, $cantidad);
        }
    }

    /** Obtiene la información de un producto específico*/
    public function obtenerProductoPorId($producto_id) {
        // Usamos el repositorio para buscar los datos del producto
        return $this->repository->findProductoDatos($producto_id);
    }
}