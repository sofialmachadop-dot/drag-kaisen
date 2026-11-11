<?php
/**
 * CONFIGURACIÓN: lee las variables del archivo .env (que NO se sube a GitHub).
 * Si no existe el .env, se usan valores locales por defecto (XAMPP/WAMP).
 */
$env = @parse_ini_file(__DIR__ . '/../.env', false, INI_SCANNER_RAW);
if ($env === false) {
    error_log('[Drag&Kaisen] No se pudo leer el archivo .env');
    $env = [];
}
define('DB_HOST', $env['DB_HOST'] ?? 'localhost');
define('DB_NAME', $env['DB_NAME'] ?? 'drag_kaisen');
define('DB_USER', $env['DB_USER'] ?? 'root');
define('DB_PASS', $env['DB_PASS'] ?? '');
define('API_KEY', $env['API_KEY'] ?? '');
