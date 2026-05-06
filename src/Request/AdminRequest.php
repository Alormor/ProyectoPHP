<?php

namespace Request;

/**
 * AdminRequest - Clase para validación y manejo de solicitudes administrativas
 *
 * @package Request
 * @uses Request
 */
class AdminRequest extends Request
{
    /**
     * Verifica si el usuario autenticado tiene permisos de administrador
     *
     * @return bool True si es admin, false en caso contrario
     */
    public function verificarPermisosAdmin()
    {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            return false;
        }
        return true;
    }

    /**
     * Prepara datos para pasar a una vista admin
     *
     * @param string $title Título de la página
     * @param string $message Mensaje descriptivo
     * @param array $data Array con datos adicionales
     * @return array Array con datos preparados para la vista
     */
    public function prepararDatosVista($title, $message, $data = [])
    {
        return array_merge([
            'title' => $title,
            'message' => $message,
            'showHeader' => true,
            'showFooter' => true
        ], $data);
    }

    /**
     * Guarda errores en la sesión y retorna URL para redirección
     *
     * @param array $errors Array de errores a guardar
     * @param array $formData Datos del formulario para repoblar
     * @param string $redirectUrl URL a la que redirigir
     * @return string|null URL de redirección o null si no hay errores
     */
    public function guardarErroresYRedirigir($errors, $formData, $redirectUrl)
    {
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $formData;
            return $redirectUrl;
        }
        return null;
    }

    /**
     * Guarda un mensaje de éxito en la sesión
     *
     * @param string $mensaje Mensaje de éxito a mostrar
     * @return void
     */
    public function guardarExito($mensaje)
    {
        $_SESSION['success'] = $mensaje;
        unset($_SESSION['form_data']);
    }

    /**
     * Guarda un mensaje de error en la sesión
     *
     * @param string $mensaje Mensaje de error a mostrar
     * @return void
     */
    public function guardarError($mensaje)
    {
        $_SESSION['errors'] = [$mensaje];
    }
}

