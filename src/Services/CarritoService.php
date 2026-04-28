<?php

namespace Services;

use Repositories\CarritoRepository;

class CarritoService extends Service {
    private CarritoRepository $repository;

    public function __construct() {
        $this->repository = new CarritoRepository();
    }

    // Obtener el carrito de un usuario
    public function obtenerCarrito($usuario_id) {
        return $this->repository->findByUser($usuario_id);
    }

    // Agregar un producto al carrito de un usuario
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

    // Eliminar un producto del carrito de un usuario
    public function eliminarProducto($usuario_id, $producto_id) {
        return $this->repository->delete($usuario_id, $producto_id);
    }

    // Vaciar el carrito de un usuario
    public function vaciarCarrito($usuario_id) {
        return $this->repository->deleteAll($usuario_id);
    }

    // Elimina una unidad de un producto del carrito
    public function borrarUno($usuario_id, $producto_id) {
    $existente = $this->repository->findProductoInCarrito($usuario_id, $producto_id);
    if ($existente && $existente['cantidad'] > 1) {
        return $this->repository->updateCantidad($usuario_id, $producto_id, $existente['cantidad'] - 1);
    } else {
        return $this->repository->delete($usuario_id, $producto_id);
    }
}

}
