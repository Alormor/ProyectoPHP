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
     * @param array $config Configuración SMTP a usar
     * @return PHPMailer Instancia configurada de PHPMailer
     */
    private function crearMailer(array $config)
    {
        $mail = new PHPMailer(true);

        // Configuracion del servidor
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['secure'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['port'];
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $mail->setFrom($config['from'], $config['from_name']);

        return $mail;
    }

    /**
     * Devuelve las configuraciones SMTP disponibles para el envío doble.
     *
     * @return array
     */
    private function obtenerConfiguraciones(): array
    {
        return [
            'mailtrap' => [
                'host' => $_ENV['MAILTRAP_HOST'] ?? '',
                'port' => (int) ($_ENV['MAILTRAP_PORT'] ?? 587),
                'secure' => $_ENV['MAILTRAP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS,
                'username' => $_ENV['MAILTRAP_USER'] ?? '',
                'password' => $_ENV['MAILTRAP_PASS'] ?? '',
                'from' => $_ENV['MAILTRAP_FROM'] ?? ($_ENV['MAILTRAP_USER'] ?? ''),
                'from_name' => $_ENV['MAILTRAP_FROM_NAME'] ?? 'Proyecto PHP',
            ],
            'gmail' => [
                'host' => $_ENV['SMTP_HOST'] ?? '',
                'port' => (int) ($_ENV['SMTP_PORT'] ?? 587),
                'secure' => $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS,
                'username' => $_ENV['SMTP_USER'] ?? '',
                'password' => $_ENV['SMTP_PASS'] ?? '',
                'from' => $_ENV['SMTP_FROM'] ?? ($_ENV['SMTP_USER'] ?? ''),
                'from_name' => $_ENV['SMTP_FROM_NAME'] ?? 'Proyecto PHP',
            ],
        ];
    }

    /**
     * Envía un correo usando una configuración SMTP concreta.
     *
     * @param array $config
     * @param string $emailDestino
     * @param string $subject
     * @param string $body
     * @param string $altBody
     * @param string|null $pdfContent
     * @param string|null $pdfFileName
     * @return bool
     */
    private function enviarConConfiguracion(array $config, string $emailDestino, string $subject, string $body, string $altBody, ?string $pdfContent = null, ?string $pdfFileName = null): bool
    {
        if (empty($config['host']) || empty($config['username']) || empty($config['password']) || empty($config['from'])) {
            return false;
        }

        try {
            $mail = $this->crearMailer($config);
            $mail->addAddress($emailDestino);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altBody;

            if ($pdfContent && $pdfFileName) {
                $mail->addStringAttachment($pdfContent, $pdfFileName, 'base64', 'application/pdf');
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Error enviando correo: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envía el mismo mensaje a Mailtrap y al correo real del usuario.
     *
     * @param string $emailDestino
     * @param string $subject
     * @param string $body
     * @param string $altBody
     * @param string|null $pdfContent
     * @param string|null $pdfFileName
     * @return bool True si el envío real por Gmail funciona
     */
    private function enviarDuplicado(string $emailDestino, string $subject, string $body, string $altBody, ?string $pdfContent = null, ?string $pdfFileName = null): bool
    {
        $configuraciones = $this->obtenerConfiguraciones();

        $this->enviarConConfiguracion(
            $configuraciones['mailtrap'],
            $emailDestino,
            $subject,
            $body,
            $altBody,
            $pdfContent,
            $pdfFileName
        );

        return $this->enviarConConfiguracion(
            $configuraciones['gmail'],
            $emailDestino,
            $subject,
            $body,
            $altBody,
            $pdfContent,
            $pdfFileName
        );
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
        $enlace = $_ENV['APP_URL'] . "/confirmar-cuenta?token=" . $token;
        $body = "¡Hola! Haz click aquí para confirmar tu cuenta: <a href='{$enlace}'>Confirmar Cuenta</a>";
        $altBody = "¡Hola! Para confirmar tu cuenta copia este enlace: {$enlace}";

        return $this->enviarDuplicado($emailDestino, 'Finaliza tu registro', $body, $altBody);
    }

    public function enviarEmailReset($emailDestino, $token) {
        $enlace = $_ENV['APP_URL'] . "/resetPassword?token=" . $token;
        $body = "¡Hola! Haz click aquí para restablecer tu contraseña: <a href='{$enlace}'>Restablecer Contraseña</a>";
        $altBody = "¡Hola! Para restablecer tu contraseña copia este enlace: {$enlace}";

        return $this->enviarDuplicado($emailDestino, 'Solicitud de restablecimiento de contraseña', $body, $altBody);
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

        $body = "
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
        $altBody = "Pedido #$numeroPedido confirmado. Total: €" . number_format($costeTotal, 2);
        $pdfFileName = $pdfContent ? "Pedido_$numeroPedido.pdf" : null;

        return $this->enviarDuplicado($emailDestino, "Confirmación de tu pedido #$numeroPedido", $body, $altBody, $pdfContent, $pdfFileName);
    }

    /**
     * Envía correo notificando que la contraseña ha sido cambiada
     *
     * @param string $emailDestino
     * @return bool
     */
    public function enviarCorreoCambioPassword($emailDestino)
    {
        $body = "Hola,\n\nTe confirmamos que tu contraseña ha sido actualizada correctamente. Si no has realizado este cambio, contacta con soporte inmediatamente.";
        $altBody = "Tu contraseña ha sido actualizada correctamente. Si no has realizado este cambio, contacta con soporte.";

        return $this->enviarDuplicado($emailDestino, 'Tu contraseña ha sido cambiada', $body, $altBody);
    }
}
