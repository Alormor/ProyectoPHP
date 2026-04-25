<?php

namespace Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    // Esta función privada configura el servidor (lo que siempre es igual)
    private function crearMailer()
    {
        $mail = new PHPMailer(true);

        // Configuracion del servidor
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
        $mail->isSMTP();                                            
        $mail->Host       = $_ENV['SMTP_HOST'];                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = $_ENV['SMTP_USER'];                     
        $mail->Password   = $_ENV['SMTP_PASS'];                     
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
        $mail->Port       = $_ENV['SMTP_PORT'];                     
        
        // Recipients
        $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);            
        
        return $mail;
    }

    public function enviarCorreoConfirmacion($emailDestino, $token)
    {
        try {
            $mail = $this->crearMailer();
            $mail->addAddress($emailDestino); 

            // Contenido dinámico
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
}

