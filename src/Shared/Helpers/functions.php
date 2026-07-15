<?php

declare(strict_types=1);

/**
 * Helpers globales de la aplicación.
 */

use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;

function base_path(string $path = ''): string
{
    // helpers → Shared → src → raíz del proyecto
    $root = dirname(__DIR__, 3);
    $root = dirname(__DIR__, 3);

    return $path === '' ? $root : $root . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
}

function templates_path(string $path = ''): string
{
    return base_path('templates' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR) : ''));
}

function public_path(string $path = ''): string
{
    return base_path('public' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR) : ''));
}

function asset(string $path): string
{
    $base = rtrim((string) Config::get('APP_URL', ''), '/');

    return $base . '/assets/' . ltrim($path, '/');
}

function url(string $path = ''): string
{
    $base = rtrim((string) Config::get('APP_URL', ''), '/');

    if ($path === '' || $path === '/') {
        return $base . '/';
    }

    return $base . '/' . ltrim($path, '/');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Segmento de ruta actual (ej. home, channels, movies) relativo a APP_URL.
 */
function current_route(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $appUrl = (string) Config::get('APP_URL', '');
    $basePath = parse_url($appUrl, PHP_URL_PATH) ?: '';

    if ($basePath !== '' && $basePath !== '/' && str_starts_with($path, $basePath)) {
        $path = substr($path, strlen($basePath)) ?: '/';
    }

    $segment = trim($path, '/');
    if ($segment === '') {
        return 'home';
    }

    return explode('/', $segment)[0];
}

/**
 * Clase CSS si la ruta actual coincide con alguno de los segmentos.
 */
function nav_active(string ...$routes): string
{
    $current = current_route();

    return in_array($current, $routes, true) ? ' is-active' : '';
}

function redirect_if_guest(): void
{
    if (!Session::isAuthenticated()) {
        \App\Infrastructure\Http\Response::redirect('login');
    }
}

function redirect_if_authenticated(): void
{
    if (Session::isAuthenticated()) {
        \App\Infrastructure\Http\Response::redirect('home');
    }
}
