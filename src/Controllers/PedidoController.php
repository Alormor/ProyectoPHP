<?php

namespace Controllers;

use Core\Controller;
use Services\CarritoService;
use Services\MailService;
use Repositories\ProductoRepository;
use Repositories\PedidoRepository;
use Core\BaseDatos;

class PedidoController extends Controller
{
    public function checkout()
    {
        if (!isset($_SESSION['usuario'])) {
            $this->redirect('/login');
        }

        $usuario_id = $_SESSION['usuario']['id'];
        $carritoService = new CarritoService();
        $items = $carritoService->obtenerCarrito($usuario_id);

        if (empty($items)) {
            $_SESSION['errors'] = ['No hay productos en el carrito.'];
            $this->redirect('/carrito');
        }

        $this->redirect('/pedidos/confirmar-direccion');
    }

    public function confirmarDireccion()
    {
        if (!isset($_SESSION['usuario'])) {
            $this->redirect('/login');
        }

        return $this->view('pedidos/confirmar-direccion', [
            'title' => 'Confirmar Dirección',
            'showHeader' => true,
            'showFooter' => true
        ]);
    }

    public function guardarDireccion()
    {
        if (!isset($_SESSION['usuario'])) {
            $this->redirect('/login');
        }

        $provincia = trim($_POST['provincia'] ?? '');
        $localidad = trim($_POST['localidad'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        if (empty($provincia) || empty($localidad) || empty($direccion)) {
            $_SESSION['errors'] = ['Todos los campos son obligatorios.'];
            $this->redirect('/pedidos/confirmar-direccion');
        }

        $_SESSION['pedido_temporal'] = [
            'provincia' => $provincia,
            'localidad' => $localidad,
            'direccion' => $direccion
        ];

        $this->redirect('/pedidos/pago');
    }

public function mostrarPago()
{
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['pedido_temporal'])) {
        $this->redirect('/carrito');
    }

    $usuario_id = $_SESSION['usuario']['id'];
    $carritoService = new \Services\CarritoService();
    $items = $carritoService->obtenerCarrito($usuario_id);
    
    // Calculamos el total real
    $subtotal = 0;
    $productoRepo = new \Repositories\ProductoRepository();
    foreach ($items as $item) {
        $p = $productoRepo->find($item['producto_id']);
        $precio = $p['precio_oferta'] ?? $p['precio'];
        $subtotal += $precio * $item['cantidad'];
    }
    
    $totalFinal = $subtotal * 1.21; // Aplicando el 21% de IVA

    return $this->view('pedidos/pago', [
        'title' => 'Finalizar Pago',
        'total' => $totalFinal,
        'direccion' => $_SESSION['pedido_temporal'],
        'showHeader' => true,
        'showFooter' => true
    ]);
}

    public function index()
    {
        if (!isset($_SESSION['usuario'])) {
            $this->redirect('/login');
        }
        $usuario_id = $_SESSION['usuario']['id'];
        $pedidoRepository = new PedidoRepository(BaseDatos::getInstancia());
        $pedidos = $pedidoRepository->findByUsuario($usuario_id);

        return $this->view('/pedidos/index', [
            'title' => 'Mis Pedidos',
            'pedidos' => $pedidos,
            'showHeader' => true,
            'showFooter' => true
        ]);
    }

    public function show($id)
    {
        if (!isset($_SESSION['usuario'])) {
            $this->redirect('/login');
        }

        $pedidoRepository = new PedidoRepository(BaseDatos::getInstancia());
        $pedido = $pedidoRepository->find($id);

        if (!$pedido || $pedido['usuario_id'] != $_SESSION['usuario']['id']) {
            $_SESSION['errors'] = ['Pedido no encontrado'];
            $this->redirect('/mis-pedidos');
        }

        return $this->view('pedidos/detalle', [
            'pedido' => $pedido,
            'title' => 'Detalle del pedido'
        ]);
    }
}