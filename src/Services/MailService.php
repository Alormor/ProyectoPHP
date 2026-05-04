<?php

namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * MailService - Servicio para envío de correos electrónicos
 *
 * @package Services
 * @uses PHPMailer\PHPMailer\PHPMailer
 * @uses PHPMailer\PHPMailer\SMTP
 * @uses PHPMailer\PHPMailer\Exception
 */
class MailService
{
    /**
     * Configura una instancia de PHPMailer con los parámetros SMTP
     *
     * @return PHPMailer Instancia configurada de PHPMailer
     */
    private function crearMailer()
    {
        $mail = new PHPMailer(true);

        // Configuracion del servidor
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['SMTP_PORT'];
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);

        return $mail;
    }

    /**
     * Envía correo de confirmación de cuenta al usuario
     *
     * @param string $emailDestino Email del usuario
     * @param string $token Token de confirmación
     * @return bool True si se envía correctamente, false en caso contrario
     */
    public function enviarCorreoConfirmacion($emailDestino, $token)
    {
        try {
            $mail = $this->crearMailer();
            $mail->addAddress($emailDestino);

            $enlace = $_ENV['APP_URL'] . "/confirmar-cuenta?token=" . $token;

            $mail->isHTML(true);
            $mail->Subject = 'Finaliza tu registro';
            $mail->Body    = "Hola! Haz click aquí para confirmar tu cuenta: <a href='{$enlace}'>Confirmar Cuenta</a>";
            $mail->AltBody = "Hola! Para confirmar tu cuenta copia este enlace: {$enlace}";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Envía correo de confirmación de pedido al usuario
     *
     * @param string $emailDestino Email del usuario
     * @param int $numeroPedido Número del pedido
     * @param array $items Array de productos del pedido
     * @param array $usuario Array con datos del usuario
     * @param string $provincia Provincia de envío
     * @param string $localidad Localidad de envío
     * @param string $direccion Dirección de envío
     * @param float $subtotal Subtotal del pedido
     * @param float $impuestos Impuestos del pedido
     * @param float $costeTotal Coste total del pedido
     * @param string|null $pdfContent Contenido PDF del pedido (opcional)
     * @return bool True si se envía correctamente
     */
    public function enviarCorreoPedido($emailDestino, $numeroPedido, $items, $usuario, $provincia, $localidad, $direccion, $subtotal, $impuestos, $costeTotal, $pdfContent = null)
    {
        try {
            $mail = $this->crearMailer();
            $mail->addAddress($emailDestino);

            $mail->isHTML(true);
            $mail->Subject = "Confirmación de tu pedido #$numeroPedido";

            $htmlItems = '<tr>';
            foreach ($items as $item) {
                $subtotalItem = $item['precio'] * $item['cantidad'];
                $htmlItems .= "<tr>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd;'>{$item['nombre']}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: center;'>{$item['cantidad']}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>€{$item['precio']}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #ddd; text-align: right;'>€" . number_format($subtotalItem, 2) . "</td>
                </tr>";
            }

            $mail->Body = "
            <html>
            <body style='font-family: Arial, sans-serif; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #2c3e50;'>¡Tu pedido ha sido confirmado!</h2>
                    <p>Hola <strong>{$usuario['nombre']}</strong>,</p>
                    <p>Tu pedido <strong>#$numeroPedido</strong> ha sido procesado correctamente.</p>

                    <h3 style='color: #34495e; margin-top: 20px;'>Detalles del Pedido</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <thead>
                            <tr style='background-color: #ecf0f1;'>
                                <th style='padding: 10px; text-align: left; border-bottom: 2px solid #bdc3c7;'>Producto</th>
                                <th style='padding: 10px; text-align: center; border-bottom: 2px solid #bdc3c7;'>Cantidad</th>
                                <th style='padding: 10px; text-align: right; border-bottom: 2px solid #bdc3c7;'>Precio</th>
                                <th style='padding: 10px; text-align: right; border-bottom: 2px solid #bdc3c7;'>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            $htmlItems
                        </tbody>
                    </table>

                    <div style='margin-top: 20px; text-align: right;'>
                        <p><strong>Subtotal:</strong> €" . number_format($subtotal, 2) . "</p>
                        <p><strong>Impuestos (21% IVA):</strong> €" . number_format($impuestos, 2) . "</p>
                        <p style='font-size: 18px; color: #27ae60;'><strong>Total: €" . number_format($costeTotal, 2) . "</strong></p>
                    </div>

                    <h3 style='color: #34495e; margin-top: 20px;'>Dirección de Envío</h3>
                    <p>
                        $direccion<br>
                        $localidad<br>
                        $provincia
                    </p>

                    <p style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #bdc3c7; font-size: 12px; color: #7f8c8d;'>
                        Gracias por tu compra. Si tienes alguna pregunta, no dudes en contactarnos.
                    </p>
                </div>
            </body>
            </html>";

            $mail->AltBody = "Pedido #$numeroPedido confirmado. Total: €" . number_format($costeTotal, 2);

            if ($pdfContent) {
                $pdfFileName = "Pedido_$numeroPedido.pdf";
                $mail->addStringAttachment($pdfContent, $pdfFileName, 'base64', 'application/pdf');
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error enviando correo de pedido: " . $e->getMessage());
            return false;
        }
    }
}
