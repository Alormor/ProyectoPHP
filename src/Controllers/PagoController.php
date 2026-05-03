<?php

namespace Controllers;

use Core\Controller;
use Core\BaseDatos;
use Repositories\PedidoRepository;
use Repositories\ProductoRepository;
use Services\CarritoService;
use Services\MailService;
use GuzzleHttp\Client;

class PagoController extends Controller
{
    private $clientId;
    private $secret;
    private $baseUrl;
    private $httpClient;

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
        $lines = [];
        $lines[] = 'Pedido realizado con exito';
        $lines[] = 'Numero de pedido: ' . $orderNumber;
        $lines[] = 'Cliente: ' . ($usuario['nombre'] ?? '');
        $lines[] = 'Email: ' . ($usuario['email'] ?? '');
        $lines[] = 'Provincia: ' . $provincia;
        $lines[] = 'Localidad: ' . $localidad;
        $lines[] = 'Direccion: ' . $direccion;
        $lines[] = 'Fecha: ' . date('Y-m-d H:i:s');
        $lines[] = '';
        $lines[] = 'Detalles del pedido:';
        $lines[] = '------------------------------------------------------------';
        $lines[] = 'Producto | Cantidad | Precio | Subtotal';
        $lines[] = '------------------------------------------------------------';

        foreach ($items as $item) {
            $sub = $item['precio'] * $item['cantidad'];
            $lines[] = sprintf(
                '%s | %d | %.2fEUR | %.2fEUR',
                $item['nombre'],
                $item['cantidad'],
                $item['precio'],
                $sub
            );
        }

        $lines[] = '------------------------------------------------------------';
        $lines[] = 'Subtotal: ' . number_format($subtotal, 2) . '€';
        $lines[] = 'Impuestos (21% IVA): ' . number_format($impuestos, 2) . '€';
        $lines[] = 'Total del pedido: ' . number_format($coste_total, 2) . '€';
        $lines[] = 'Gracias por tu compra.';
        $lines[] = '';
        $lines[] = 'Tienda Online Cubos';
        $lines[] = 'Av. de Francisco Ayala, 18014 Granada';
        $lines[] = 'Telefono: +34 123 456 789 | Email: no-reply@cubos3.com';

        $content = "BT\r\n/F1 12 Tf\r\n";
        foreach ($lines as $index => $line) {
            $y = 770 - ($index * 18);
            $content .= sprintf("1 0 0 1 50 %d Tm\r\n(%s) Tj\r\n", $y, $this->escapePdfString($line));
        }
        $content .= "ET";

        $header = "%PDF-1.4\r\n%\xE2\xE3\xCF\xD3\r\n";
        $objects = [];
        $objects[] = "1 0 obj\r\n<< /Type /Catalog /Pages 2 0 R >>\r\nendobj\r\n";
        $objects[] = "2 0 obj\r\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\r\nendobj\r\n";
        $objects[] = "3 0 obj\r\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>\r\nendobj\r\n";
        $objects[] = "4 0 obj\r\n<< /Length " . strlen($content) . " >>\r\nstream\r\n" . $content . "\r\nendstream\r\nendobj\r\n";
        $objects[] = "5 0 obj\r\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\r\nendobj\r\n";

        $pdf = $header;
        $offsets = [0];
        $cursor = strlen($pdf);

        foreach ($objects as $object) {
            $offsets[] = $cursor;
            $pdf .= $object;
            $cursor += strlen($object);
        }

        $startxref = strlen($pdf);
        $xref = "xref\r\n0 " . count($offsets) . "\r\n";
        $xref .= sprintf("%010d 65535 f\r\n", 0);
        foreach (array_slice($offsets, 1) as $offset) {
            $xref .= sprintf("%010d 00000 n\r\n", $offset);
        }

        $pdf .= $xref;
        $pdf .= "trailer\r\n<< /Size " . count($offsets) . " /Root 1 0 R >>\r\n";
        $pdf .= "startxref\r\n" . $startxref . "\r\n";
        $pdf .= "%%EOF\r\n";

        return $pdf;
    }
}