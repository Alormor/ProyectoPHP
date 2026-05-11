<?php

namespace Controllers;

use Core\Controller;
use Services\CarritoService;

/**
 * CarritoController - Controlador para gestionar el carrito de compras
 *
 * @package Controllers
 * @uses Controller
 * @uses CarritoService
 */
class CarritoController extends Controller
{
    private CarritoService $service;

    /**
     * Constructor de CarritoController
     */
    public function __construct() {
        $this->service = new CarritoService();
    }

    /**
     * Muestra el carrito de compras del usuario autenticado
     *
     * @return string Vista del carrito
     */
    public function index() {
        $items = [];

        if (isset($_SESSION['usuario'])) {
            $usuario_id = $_SESSION['usuario']['id'];
            $items = $this->service->obtenerCarrito($usuario_id);
        }
        else{
            if(!empty($_SESSION['carrito_temporal'])){

                foreach($_SESSION['carrito_temporal'] as $producto_id => $cantidad){
                    $producto = $this->service->obtenerProductoPorId($producto_id);

                    if ($producto) {
                        $items[] = [
                            'producto_id' => $producto['id'],
                            'nombre'      => $producto['nombre'],
                            'precio'      => $producto['precio'],
                            'imagen'      => $producto['imagen'],
                            'cantidad'    => $cantidad
                        ];
                    }
                }
            }
        }
        return $this->view('carrito/index', [
            'title' => 'Mi Carrito de Compras',
            'items' => $items,
            'showHeader' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Agrega un producto al carrito del usuario autenticado
     *
     * @return void Redirige a la página del producto
     */
    public function agregar() {
        $producto_id = $_POST['producto_id'] ?? null;

        if (!$producto_id) {
            $this->redirect('/productos');
            return;
        }

        // Usuario autenticado (Guardar en DB)
        if (isset($_SESSION['usuario'])) {
            $usuario_id = $_SESSION['usuario']['id'];
            $this->service->agregarProducto($usuario_id, $producto_id);
        } 
        //Usuario invitado (Guardar en $_SESSION)
        else {
            if (!isset($_SESSION['carrito_temporal'])) {
                $_SESSION['carrito_temporal'] = [];
            }

            // Si el producto ya existe, aumentamos la cantidad, si no, lo añadimos
            if (isset($_SESSION['carrito_temporal'][$producto_id])) {
                $_SESSION['carrito_temporal'][$producto_id]++;
            } else {
                $_SESSION['carrito_temporal'][$producto_id] = 1;
            }
        }

        $_SESSION['success'] = 'Producto añadido al carrito';
        $this->redirect('/productos#prod-' . $producto_id);
    }

    /**
     * Elimina completamente un producto del carrito
     *
     * @param int $producto_id Identificador del producto a eliminar
     * @return void Redirige al carrito
     */
    public function eliminar($producto_id) {
        if (isset($_SESSION['usuario'])) {
            $this->service->eliminarProducto($_SESSION['usuario']['id'], $producto_id);
        } else {
            // borrar producto del carrito sin iniciar sesion
            if (isset($_SESSION['carrito_temporal'][$producto_id])) {
                unset($_SESSION['carrito_temporal'][$producto_id]);
            }
        }
        $this->redirect('/carrito');
    }

    /**
     * Vacía completamente el carrito del usuario
     *
     * @return void Redirige al carrito
     */
    public function vaciar() {
        if (isset($_SESSION['usuario'])) {
            $this->service->vaciarCarrito($_SESSION['usuario']['id']);
        }else {
            // eliminar el carrito del user sin login
            unset($_SESSION['carrito_temporal']);
        }

        $this->redirect('/carrito');
    }

    /**
     * Incrementa en una unidad la cantidad de un producto en el carrito
     *
     * @param int $producto_id Identificador del producto
     * @return void Redirige al carrito
     */
    public function incrementar($producto_id) {
        if (isset($_SESSION['usuario'])) {
            $this->service->agregarProducto($_SESSION['usuario']['id'], $producto_id, 1);
        }
        $this->redirect('/carrito');
    }

    /**
     * Decrementa en una unidad la cantidad de un producto en el carrito
     *
     * @param int $producto_id Identificador del producto
     * @return void Redirige al carrito
     */
    public function decrementar($producto_id) {
        if (isset($_SESSION['usuario'])) {
            $this->service->borrarUno($_SESSION['usuario']['id'], $producto_id);
        }
        $this->redirect('/carrito');
    }
}
