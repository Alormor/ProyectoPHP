<?php

namespace Request;

class AdminRequest extends Request
{
    public function verificarPermisosAdmin()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            $_SESSION['errors'] = ['No tienes permisos para realizar esta acción.'];
            return false;
        }
        return true;
    }

    public function prepararDatosVista($title, $message, $data = [])
    {
        return array_merge([
            'title' => $title,
            'message' => $message,
            'showHeader' => true,
            'showFooter' => true
        ], $data);
    }

    public function guardarErroresYRedirigir($errors, $formData, $redirectUrl)
    {
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $formData;
            return $redirectUrl;
        }
        return null;
    }

    public function guardarExito($mensaje)
    {
        $_SESSION['success'] = $mensaje;
        unset($_SESSION['form_data']);
    }

    public function guardarError($mensaje)
    {
        $_SESSION['errors'] = [$mensaje];
    }
}

?>
