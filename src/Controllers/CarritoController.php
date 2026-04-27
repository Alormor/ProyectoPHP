<?php

namespace Controllers;

use Core\Controller;
use Services\CarritoService;

class CarritoController extends Controller
{
    private CarritoService $service;

    public function __construct() {
        $this->service = new CarritoService();
    }

    // Mostrar el carrito de compras
    public function index() {
        if (!isset($_SESSION['usuario'])) {
            $this->redirect('/login');
        }

        $usuario_id = $_SESSION['usuario']['id'];
        $items = $this->service->obtenerCarrito($usuario_id);

        return $this->view('carrito/index', [
            'title' => 'Mi Carrito de Compras',
            'items' => $items,
            'showHeader' => true, 
            'showFooter' => true 
        ]);
    }

    // Agregar un producto al carrito de un usuario
    public function agregar() {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['errors'] = ['Debes iniciar sesión para añadir productos al carrito'];
            $this->redirect('/login');
        }

        $producto_id = $_POST['producto_id'] ?? null;
        if ($producto_id) {
            $usuario_id = $_SESSION['usuario']['id'];
            $this->service->agregarProducto($usuario_id, $producto_id);
            $_SESSION['success'] = 'Producto añadido al carrito';
        }

        // Redireccionamos al producto con un fragmento para que el navegador se desplace a ese elemento
        $this->redirect('/productos#prod-' . $producto_id);
    }

    // Eliminar un producto del carrito de un usuario
    public function eliminar($producto_id) {
        if (isset($_SESSION['usuario'])) {
            $this->service->eliminarProducto($_SESSION['usuario']['id'], $producto_id);
        }
        $this->redirect('/carrito');
    }

    // Vaciar el carrito de un usuario
    public function vaciar() {
        if (isset($_SESSION['usuario'])) {
            $this->service->vaciarCarrito($_SESSION['usuario']['id']);
        }
        $this->redirect('/carrito');
    }


    // Incrementar la cantidad de un producto en el carrito
    public function incrementar($producto_id) {
        if (isset($_SESSION['usuario'])) {
            $this->service->agregarProducto($_SESSION['usuario']['id'], $producto_id, 1);
        }
        $this->redirect('/carrito');
    }

    // Elimina una unidad de un producto del carrito
    public function decrementar($producto_id) {
        if (isset($_SESSION['usuario'])) {
            $this->service->borrarUno($_SESSION['usuario']['id'], $producto_id);
        }
        $this->redirect('/carrito');
    }
}
