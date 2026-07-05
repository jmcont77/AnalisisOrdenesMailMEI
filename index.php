<?php
require_once __DIR__ . '/controllers/ApiController.php';

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Rutas API — Órdenes
if (preg_match('#^/api/ordenes/?$#', $uri)) {
    (new ApiController())->ordenes();
    exit;
}
if (preg_match('#^/api/orden/(\d+)/?$#', $uri, $m)) {
    $api = new ApiController();
    if ($method === 'GET')  $api->orden((int)$m[1]);
    if ($method === 'POST') $api->actualizarOrden((int)$m[1]);
    exit;
}
if (preg_match('#^/api/adjuntos/(\d+)/?$#', $uri, $m)) {
    (new ApiController())->adjuntos((int)$m[1]);
    exit;
}
if (preg_match('#^/api/adjunto/(\d+)/?$#', $uri, $m)) {
    (new ApiController())->adjunto((int)$m[1]);
    exit;
}

// Rutas API — Configuración / Prompt
if (preg_match('#^/api/config/([a-zA-Z0-9_]+)/?$#', $uri, $m)) {
    $api = new ApiController();
    if ($method === 'GET')  $api->obtenerConfig($m[1]);
    if ($method === 'POST') $api->actualizarConfig($m[1]);
    exit;
}
if (preg_match('#^/api/config-auditoria/([a-zA-Z0-9_]+)/?$#', $uri, $m)) {
    (new ApiController())->auditoriaConfig($m[1]);
    exit;
}
if (preg_match('#^/api/config-auditoria-item/(\d+)/?$#', $uri, $m)) {
    (new ApiController())->auditoriaConfigItem((int)$m[1]);
    exit;
}

// Vista editor de prompt
if (preg_match('#^/prompt/?$#', $uri)) {
    require_once __DIR__ . '/views/editor_prompt.php';
    exit;
}

// Vista principal
require_once __DIR__ . '/views/main.php';
