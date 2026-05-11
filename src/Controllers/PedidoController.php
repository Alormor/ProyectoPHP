<?php

namespace Controllers;

use Core\Controller;
use Services\CarritoService;
use Services\MailService;
use Repositories\ProductoRepository;
use Repositories\PedidoRepository;
use Core\BaseDatos;

/**
 * PedidoController - Controlador para gestionar pedidos y checkout
 *
 * @package Controllers
 * @uses Controller
 * @uses CarritoService
 * @uses PedidoRepository
 */
class PedidoController extends Controller
{
    /**
     * Inicia el proceso de checkout del carrito
     *
     * @return void Redirige a confirmar dirección
     */
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

    /**
     * Muestra el formulario para confirmar dirección de envío
     *
     * @return string Vista del formulario de dirección
     */
    public function confirmarDireccion()
    {
        if (!isset($_SESSION['usuario'])) {
            $this->redirect('/login');
        }

        $usuario_id = $_SESSION['usuario']['id'];
        $usuarioRepository = new \Repositories\UsuarioRepository(BaseDatos::getInstancia());
        $usuario = $usuarioRepository->find($usuario_id);

        return $this->view('pedidos/confirmar-direccion', [
            'title' => 'Confirmar Dirección',
            'showHeader' => true,
            'showFooter' => true,
            'usuario' => $usuario
        ]);
    }

    /**
     * Guarda la dirección de envío en la sesión
     *
     * @return void Redirige a pago
     */
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

    /**
     * Muestra el formulario de pago
     *
     * @return string Vista del formulario de pago
     */
    public function mostrarPago()
    {
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['pedido_temporal'])) {
            $this->redirect('/carrito');
        }

        $usuario_id = $_SESSION['usuario']['id'];
        $carritoService = new CarritoService();
        $items = $carritoService->obtenerCarrito($usuario_id);

        // Calculamos el total real
        $subtotal = 0;
        $productoRepo = new ProductoRepository();
        foreach ($items as $item) {
            $p = $productoRepo->find($item['producto_id']);
            $precio = $p['precio_oferta'] ?? $p['precio'];
            $subtotal += $precio * $item['cantidad'];
        }

        $totalFinal = $subtotal;

        return $this->view('pedidos/pago', [
            'title' => 'Finalizar Pago',
            'total' => $totalFinal,
            'direccion' => $_SESSION['pedido_temporal'],
            'showHeader' => true,
            'showFooter' => true
        ]);
    }

    /**
     * Lista los pedidos del usuario autenticado
     *
     * @return string Vista de pedidos del usuario
     */
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


}