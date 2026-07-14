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
