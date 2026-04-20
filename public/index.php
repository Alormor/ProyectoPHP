<?php
require_once '../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Iniciar sesión
session_start();

// Configurar zona horaria
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

include_once __DIR__ . '/../src/Views/layout/header.php';

echo 'Bienvenido a tu aplicación PHP';
?>
