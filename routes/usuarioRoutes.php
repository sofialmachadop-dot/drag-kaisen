<?php
declare(strict_types=1);

/** RUTAS: relacionan verbo HTTP + URL con un método del controlador. */
$metodo = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '';
$pos    = strpos($path, '/api/');
$ruta   = $pos === false ? '' : substr($path, $pos + 5);
$ruta   = preg_replace('#^index\.php/?#', '', $ruta);
$partes = array_values(array_filter(explode('/', trim($ruta, '/'))));

$c = new UsuarioController();

if ($partes === ['usuarios']) {
    if ($metodo === 'POST') { $c->crear(); }
    if ($metodo === 'GET')  { $c->listar(); }
    $c->metodoNoPermitido();
}

if (count($partes) === 2 && $partes[0] === 'usuarios' && ctype_digit($partes[1])) {
    $id = (int)$partes[1];
    if ($metodo === 'GET')    { $c->obtener($id); }
    if ($metodo === 'DELETE') { $c->eliminar($id); }
    $c->metodoNoPermitido();
}

$c->noEncontrado();
