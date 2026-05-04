<?php

namespace Controllers;

use Core\Controller;
use Core\BaseDatos;
use Repositories\PedidoRepository;
use Repositories\ProductoRepository;
use Services\CarritoService;
use Services\MailService;
use GuzzleHttp\Client;

/**
 * PagoController - Controlador para gestionar pagos con PayPal
 *
 * @package Controllers
 * @uses Controller
 * @uses PedidoRepository
 * @uses CarritoService
 */
class PagoController extends Controller
{
    private $clientId;
    private $secret;
    private $baseUrl;
    private $httpClient;

    /**
     * Constructor de PagoController
     */
    public function __construct()
    {
        $this->clientId = $_ENV['PAYPAL_CLIENT_ID'] ?? 'TU_CLIENT_ID';
        $this->secret = $_ENV['PAYPAL_SECRET'] ?? 'TU_SECRET_KEY';
        $this->baseUrl = "https://api-m.sandbox.paypal.com";

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 30.0,
        ]);
    }

    /**
     * Obtiene un token de acceso desde PayPal
     *
     * @return string Token de acceso de PayPal
     * @throws \Exception Si hay error en la autenticación
     */
    private function getAccessToken()
    {
        try {
            $response = $this->httpClient->request('POST', '/v1/oauth2/token', [
                'auth' => [$this->clientId, $this->secret],
                'form_params' => ['grant_type' => 'client_credentials']
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['access_token'])) {
                throw new \Exception("No se recibió el token de acceso de PayPal.");
            }

            return $data['access_token'];

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            error_log("Error de autenticación PayPal: " . $errorBody);
            throw new \Exception("Error de autenticación con PayPal. Revisa ClientID y Secret.");
        } catch (\Exception $e) {
            error_log("Error general en getAccessToken: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crea una orden de pago en PayPal
     *
     * @return void Devuelve JSON con ID de orden o error
     */
    public function crearOrden()
    {
        if (!isset($_SESSION['usuario'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $direccionTmp = $_SESSION['pedido_temporal'] ?? null;
        if (!$direccionTmp) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Faltan datos de envío en la sesión']);
            exit;
        }

        $usuario_id = $_SESSION['usuario']['id'];
        $carritoService = new \Services\CarritoService();
        $items = $carritoService->obtenerCarrito($usuario_id);

        if (empty($items)) {
            http_response_code(400);x
            header('Content-Type: application/json');
            echo json_encode(['error' => 'El carrito está vacío']);
            exit;
        }

        $subtotal = 0.0;
        $productoRepo = new \Repositories\ProductoRepository();

        foreach ($items as $item) {
            $p = $productoRepo->find($item['producto_id']);
            if ($p) {
                $precio = $p['precio_oferta'] ?? $p['precio'];
                $subtotal += (float)$precio * (int)$item['cantidad'];
            }
        }

        $total = $subtotal * 1.21;

        try {
            $accessToken = $this->getAccessToken();
        $response = $this->httpClient->request('POST', '/v2/checkout/orders', [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type'  => 'application/json'
            ],
            'json' => [
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" => number_format($total, 2, '.', '') 
                    ]
                ]]
            ]
        ]);

        header('Content-Type: application/json');
        echo $response->getBody();
        exit; 

    } catch (\Exception $e) {
        error_log("Error PayPal crearOrden: " . $e->getMessage()); 
        
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error de comunicación con PayPal']);
        exit;
    }
}

    public function capturarPago($orderId)
{
    try {
        $accessToken = $this->getAccessToken();
        
        $response = $this->httpClient->request('POST', "/v2/checkout/orders/$orderId/capture", [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type'  => 'application/json'
            ]
        ]);

        $result = json_decode($response->getBody(), true);

        if (isset($result['status']) && $result['status'] === 'COMPLETED') {
            $this->finalizarProcesoPedido($result);
            
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'COMPLETED',
                'id' => $result['id'] // ID de PayPal
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'FAILED',
                'details' => $result
            ]);
        }

    } catch (\Exception $e) {
        error_log("Error en capturarPago: " . $e->getMessage());

        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error interno al procesar el pago']);
    }
    
    //exit para evitar que el router añada HTML extra
    exit; 
}
private function finalizarProcesoPedido($detallesPaypal)
{
    $usuario = $_SESSION['usuario'];
    $usuario_id = $usuario['id'];
    
    $dir = $_SESSION['pedido_temporal'] ?? null;
    if (!$dir) {
        error_log("Error: No hay dirección en la sesión al finalizar el pedido.");
        return false;
    }

    $carritoService = new \Services\CarritoService();
    $items = $carritoService->obtenerCarrito($usuario_id);

    $productoRepository = new \Repositories\ProductoRepository();
    $subtotal = 0.0;
    foreach ($items as &$item) {
        $producto = $productoRepository->find($item['producto_id']);
        $item['precio'] = $producto['precio_oferta'] ?? $producto['precio'];
        $item['nombre'] = $producto['nombre'];
        $subtotal += $item['precio'] * $item['cantidad'];
    }

    $impuestos = $subtotal * 0.21;
    $total = $subtotal + $impuestos;

    $pedido = new \Models\Pedido();
    $pedido->setUsuarioId($usuario_id);
    $pedido->setProvincia($dir['provincia']);
    $pedido->setLocalidad($dir['localidad']);
    $pedido->setDireccion($dir['direccion']);
    $pedido->setSubtotal($subtotal);
    $pedido->setImpuestos($impuestos);
    $pedido->setCosteTotal($total);
    $pedido->setEstado('confirmado');

    $pedidoRepository = new \Repositories\PedidoRepository(\Core\BaseDatos::getInstancia());
    $exito = $pedidoRepository->create($pedido);

    if ($exito) {
        foreach ($items as $item) {
            $productoRepository->decrementarStock($item['producto_id'], $item['cantidad']);
        }

        try {
            $numeroPedido = 'PED-' . date('YmdHis') . rand(100, 999);
            $htmlPedido = $this->generarPdfPedido($items, $usuario, $dir['provincia'], $dir['localidad'], $dir['direccion'], $subtotal, $impuestos, $total);

            $mailService = new MailService();
            $mailService->enviarCorreoPedido(
                $usuario['email'],
                $numeroPedido,
                $items,
                $usuario,
                $dir['provincia'],
                $dir['localidad'],
                $dir['direccion'],
                $subtotal,
                $impuestos,
                $total,
                $htmlPedido
            );
        } catch (\Exception $e) {
            error_log("Error enviando email: " . $e->getMessage());
        }

        $carritoService->vaciarCarrito($usuario_id);
        unset($_SESSION['pedido_temporal']);

        return true;
    }

    return false;
}
    private function generarPdfPedido(array $items, array $usuario, string $provincia, string $localidad, string $direccion, float $subtotal, float $impuestos, float $coste_total): string
    {
        $orderNumber = 'PED-' . date('YmdHis') . rand(100, 999);
        $fecha = date('d/m/Y H:i:s');

        $htmlItems = '';
        foreach ($items as $item) {
            $sub = $item['precio'] * $item['cantidad'];
            $htmlItems .= '<tr>
                <td style="padding: 10px; border-bottom: 1px solid #ecf0f1;">' . htmlspecialchars($item['nombre']) . '</td>
                <td style="padding: 10px; border-bottom: 1px solid #ecf0f1; text-align: center;">' . intval($item['cantidad']) . '</td>
                <td style="padding: 10px; border-bottom: 1px solid #ecf0f1; text-align: right;">€' . number_format($item['precio'], 2) . '</td>
                <td style="padding: 10px; border-bottom: 1px solid #ecf0f1; text-align: right;">€' . number_format($sub, 2) . '</td>
            </tr>';
        }

        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; background-color: #f5f5f5; }
                .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #2c3e50; padding-bottom: 20px; }
                .header h1 { margin: 0; color: #2c3e50; font-size: 28px; }
                .header p { margin: 5px 0; color: #7f8c8d; }
                .section { margin-bottom: 25px; }
                .section h3 { background-color: #34495e; color: white; padding: 12px; margin: 0 0 15px 0; border-radius: 3px; }
                .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
                .info-block { }
                .info-row { display: flex; padding: 8px 0; border-bottom: 1px solid #ecf0f1; }
                .info-label { font-weight: bold; color: #2c3e50; min-width: 120px; }
                .info-value { color: #555; flex: 1; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th { background-color: #34495e; color: white; padding: 12px; text-align: left; font-weight: bold; }
                td { padding: 12px; border-bottom: 1px solid #ecf0f1; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .totals { text-align: right; margin-top: 20px; padding-top: 20px; border-top: 2px solid #2c3e50; }
                .total-row { display: flex; justify-content: flex-end; gap: 20px; margin: 10px 0; font-size: 14px; }
                .total-label { font-weight: bold; min-width: 160px; }
                .total-value { min-width: 100px; text-align: right; }
                .grand-total { font-size: 18px; color: white; background-color: #27ae60; padding: 12px; border-radius: 3px; margin-top: 15px; }
                .grand-total .total-label { color: white; }
                .grand-total .total-value { color: white; font-size: 20px; font-weight: bold; }
                .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #bdc3c7; font-size: 11px; color: #7f8c8d; text-align: center; line-height: 1.6; }
                .footer strong { color: #2c3e50; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✓ PEDIDO CONFIRMADO</h1>
                    <p>Tienda Online Cubos</p>
                </div>

                <div class="section">
                    <h3>Información del Pedido</h3>
                    <div class="info-grid">
                        <div class="info-block">
                            <div class="info-row">
                                <span class="info-label">Número:</span>
                                <span class="info-value">' . htmlspecialchars($orderNumber) . '</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Fecha:</span>
                                <span class="info-value">' . htmlspecialchars($fecha) . '</span>
                            </div>
                        </div>
                        <div class="info-block">
                            <div class="info-row">
                                <span class="info-label">Estado:</span>
                                <span class="info-value" style="color: #27ae60; font-weight: bold;">CONFIRMADO</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3>Datos del Cliente</h3>
                    <div class="info-grid">
                        <div class="info-block">
                            <div class="info-row">
                                <span class="info-label">Nombre:</span>
                                <span class="info-value">' . htmlspecialchars(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? '')) . '</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Email:</span>
                                <span class="info-value">' . htmlspecialchars($usuario['email'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3>Dirección de Envío</h3>
                    <div class="info-block">
                        <div class="info-row">
                            <span class="info-label">Dirección:</span>
                            <span class="info-value">' . htmlspecialchars($direccion) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Localidad:</span>
                            <span class="info-value">' . htmlspecialchars($localidad) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Provincia:</span>
                            <span class="info-value">' . htmlspecialchars($provincia) . '</span>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3>Detalles del Pedido</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="text-align: center; width: 80px;">Cantidad</th>
                                <th style="text-align: right; width: 100px;">Precio Unit.</th>
                                <th style="text-align: right; width: 100px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $htmlItems . '
                        </tbody>
                    </table>
                </div>

                <div class="totals">
                    <div class="total-row">
                        <span class="total-label">Subtotal:</span>
                        <span class="total-value">€' . number_format($subtotal, 2) . '</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Impuestos (21% IVA):</span>
                        <span class="total-value">€' . number_format($impuestos, 2) . '</span>
                    </div>
                    <div class="total-row grand-total">
                        <span class="total-label">TOTAL DEL PEDIDO:</span>
                        <span class="total-value">€' . number_format($coste_total, 2) . '</span>
                    </div>
                </div>

                <div class="footer">
                    <p><strong>¡Gracias por tu compra en Tienda Online Cubos!</strong></p>
                    <p>Tu pedido ha sido confirmado y será procesado en breve.</p>
                    <p>Av. de Francisco Ayala, 18014 Granada | Teléfono: +34 123 456 789</p>
                    <p>Email: no-reply@cubos3.com</p>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }
}