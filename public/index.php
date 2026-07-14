<?php

declare(strict_types=1);

/**
 * Front controller — document root debe apuntar a /public.
 */

require dirname(__DIR__) . '/src/bootstrap.php';

/** @var \App\Infrastructure\Http\Router $router */
$router = require dirname(__DIR__) . '/routes/web.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

// Quita el prefijo del subdirectorio si APP_URL incluye path (p. ej. /php-iptv-player/public)
$appUrl = (string) \App\Infrastructure\Config\Config::get('APP_URL', '');
$basePath = parse_url($appUrl, PHP_URL_PATH) ?: '';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';

if ($basePath !== '' && $basePath !== '/' && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath)) ?: '/';
}

$query = parse_url($uri, PHP_URL_QUERY);
$dispatchUri = $path . ($query ? '?' . $query : '');

$router->dispatch($method, $dispatchUri);
