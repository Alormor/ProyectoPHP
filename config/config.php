<?php

// Todas las configuraciones se cargan desde el archivo .env
// Las constantes se definen dinámicamente usando los valores del .env

// define('APP_NAME', $_ENV['APP_NAME'] ?? 'Tienda Online');
// define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
// define('APP_DEBUG', $_ENV['APP_DEBUG'] ?? false);
// define('APP_TIMEZONE', $_ENV['APP_TIMEZONE'] ?? 'UTC');

// BASE_URL cargado desde .env
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost');

// Configuración de Base de Datos cargada desde .env
// define('DB_SERVIDOR', $_ENV['DB_HOST'] ?? 'localhost');
// define('DB_USUARIO', $_ENV['DB_USER'] ?? 'root');
// define('DB_PASS', $_ENV['DB_PASS'] ?? '');
// define('DB_DATABASE', $_ENV['DB_NAME'] ?? 'tienda');
// define('DB_CHARSET', 'utf8mb4');
// define('DB_PORT', $_ENV['DB_PORT'] ?? 3306);

// Configuración de Sesión cargada desde .env
// define('SESSION_NAME', $_ENV['SESSION_NAME'] ?? 'PHPSESSID');
// define('SESSION_LIFETIME', $_ENV['SESSION_LIFETIME'] ?? 3600);

// Configurar nombre y tiempo de vida de sesión
// ini_set('session.name', SESSION_NAME);
// ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
