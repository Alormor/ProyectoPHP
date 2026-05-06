<?php

namespace Request;

/**
 * Request - Clase base para manejo de solicitudes HTTP
 *
 * @package Request
 */
class Request
{
    /**
     * Obtiene un parámetro GET o todos los parámetros GET
     *
     * @param string|null $key Clave del parámetro (null para todos)
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor del parámetro o array de parámetros
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Obtiene un parámetro POST o todos los parámetros POST
     *
     * @param string|null $key Clave del parámetro (null para todos)
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor del parámetro o array de parámetros
     */
    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Obtiene todos los parámetros GET y POST combinados
     *
     * @return array Array con todos los parámetros de la solicitud
     */
    public function all()
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Obtiene el método HTTP de la solicitud
     *
     * @return string GET, POST, PUT, DELETE, etc.
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Obtiene la URI de la solicitud
     *
     * @return string URI de la solicitud actual
     */
    public function uri()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}
