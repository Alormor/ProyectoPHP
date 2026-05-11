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
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'El carrito está vacío']);
            exit;
        }

        $subtotal = 0.0;
        $productoRepo = new \Repositories\ProductoRepository();
        foreach($items as $item){
            if($item['cantidad'] <= 0){
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Cantidad inválida para el producto: ' . $item['nombre']]);
                exit;
            }
        }
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
                    'id' => $result['id']
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

    /**
     * Simula la confirmación del pago para pruebas (salta PayPal)
     * Devuelve JSON con el resultado.
     */
    public function simularPago()
    {
        if (!isset($_SESSION['usuario'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        try {
            $exito = $this->finalizarProcesoPedido(null);

            header('Content-Type: application/json');
            if ($exito) {
                echo json_encode(['status' => 'COMPLETED', 'id' => 'SIMULATED_' . time()]);
            } else {
                echo json_encode(['status' => 'FAILED']);
            }
        } catch (\Exception $e) {
            error_log("Error en simularPago: " . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error interno al simular pago']);
        }

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
        if($item['cantidad'] <= 0){
            return false;
        }
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
            if($productoRepository->obtenerStock($item['producto_id']) <= 0){
                $productoRepository->desactivarProducto($item['producto_id']);
            }
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

        $pdf = new \Fpdf\Fpdf();
        $pdf->AddPage();

        $toPdf = function ($s) {
            if ($s === null) return '';
            if (function_exists('iconv')) {
                $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $s);
                if ($converted !== false) return $converted;
            }
            return utf8_decode($s);
        };

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFillColor(44, 62, 80);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 15, $toPdf('PEDIDO CONFIRMADO'), 0, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(5);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, $toPdf('Tienda Online Cubos'), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(236, 240, 241);
        $pdf->Cell(50, 7, $toPdf('Numero Pedido:'), 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 7, $toPdf($orderNumber), 0, 1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 7, $toPdf('Fecha:'), 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 7, $toPdf($fecha), 0, 1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 7, $toPdf('Estado:'), 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(39, 174, 96);
        $pdf->Cell(0, 7, $toPdf('CONFIRMADO'), 0, 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(52, 73, 94);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 7, $toPdf('DATOS DEL CLIENTE'), 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFont('Arial', '', 9);
        $nombreCompleto = ($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? '');
        $pdf->MultiCell(0, 5, $toPdf('Nombre: ' . substr($nombreCompleto, 0, 50)));
        $pdf->MultiCell(0, 5, $toPdf('Email: ' . ($usuario['email'] ?? '')));
        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(52, 73, 94);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 7, $toPdf('DIRECCION DE ENVIO'), 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(0, 5, $toPdf('Direccion: ' . substr($direccion, 0, 50)));
        $pdf->MultiCell(0, 5, $toPdf('Localidad: ' . substr($localidad, 0, 50)));
        $pdf->MultiCell(0, 5, $toPdf('Provincia: ' . substr($provincia, 0, 50)));
        $pdf->Ln(3);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(52, 73, 94);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(65, 7, $toPdf('Producto'), 1, 0, 'L', true);
        $pdf->Cell(25, 7, $toPdf('Cantidad'), 1, 0, 'C', true);
        $pdf->Cell(30, 7, $toPdf('Precio'), 1, 0, 'R', true);
        $pdf->Cell(40, 7, $toPdf('Subtotal'), 1, 1, 'R', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);

        foreach ($items as $item) {
            $sub = $item['precio'] * $item['cantidad'];
            $nombre = $toPdf(substr($item['nombre'], 0, 40));
            $pdf->Cell(65, 6, $nombre, 1, 0);
            $pdf->Cell(25, 6, intval($item['cantidad']), 1, 0, 'C');
            $pdf->Cell(30, 6, $toPdf('€' . number_format($item['precio'], 2)), 1, 0, 'R');
            $pdf->Cell(40, 6, $toPdf('€' . number_format($sub, 2)), 1, 1, 'R');
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(120, 6, $toPdf('Subtotal:'), 0, 0, 'R');
        $pdf->Cell(40, 6, $toPdf('€' . number_format($subtotal, 2)), 0, 1, 'R');

        $pdf->Cell(120, 6, $toPdf('Impuestos (21% IVA):'), 0, 0, 'R');
        $pdf->Cell(40, 6, $toPdf('€' . number_format($impuestos, 2)), 0, 1, 'R');

        $pdf->SetFillColor(39, 174, 96);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(120, 8, $toPdf('TOTAL DEL PEDIDO:'), 1, 0, 'R', true);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 8, $toPdf('€' . number_format($coste_total, 2)), 1, 1, 'R', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(5);
        $pdf->SetFillColor(236, 240, 241);
        $pdf->MultiCell(0, 4, $toPdf('Gracias por tu compra en Tienda Online Cubos. Tu pedido ha sido confirmado y sera procesado en breve.'), 0, 'C', true);

        $pdfContent = $pdf->Output('S');
        return $pdfContent;
    }
}