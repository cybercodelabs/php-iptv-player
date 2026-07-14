<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

/**
 * Respuestas HTTP y redirecciones.
 */
final class Response
{
    public static function redirect(string $path): never
    {
        $base = rtrim((string) (\App\Infrastructure\Config\Config::get('APP_URL') ?? ''), '/');
        $location = str_starts_with($path, 'http') ? $path : $base . '/' . ltrim($path, '/');

        header('Location: ' . $location);
        exit;
    }

    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
