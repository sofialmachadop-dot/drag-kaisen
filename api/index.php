<?php
declare(strict_types=1);

/** server.js de la API: punto de entrada. Carga config, controlador y rutas. */
require_once __DIR__ . '/../includes/auth.php';   // carga config/ y la conexión
require_once __DIR__ . '/../controllers/UsuarioController.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    require __DIR__ . '/../routes/usuarioRoutes.php';
} catch (Throwable $e) {
    error_log('[Drag&Kaisen] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error del servidor']);
}
