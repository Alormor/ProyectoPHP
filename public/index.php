<?php
require_once '../vendor/autoload.php';

use Core\Application;

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Definir BASE_URL
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost/ProyectoPHP');

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurar zona horaria
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Crear y ejecutar aplicación
$app = new Application();
$app->run();

