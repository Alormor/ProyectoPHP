<?php
require_once '../vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Iniciar sesión
session_start();

// Configurar zona horaria
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

include_once __DIR__ . '/../Views/layout/header.php';
// Aquí va la lógica principal de tu aplicación
echo 'Bienvenido a tu aplicación PHP';
?>
