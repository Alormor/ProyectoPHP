<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Cargar configuración (define constantes de BD)
require_once __DIR__ . '/../config/config.php';

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurar zona horaria
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Cargar rutas
require_once __DIR__ . '/../config/routes.php';

// Ejecutar router
Router::dispatch();

