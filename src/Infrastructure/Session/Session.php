<?php

declare(strict_types=1);

namespace App\Infrastructure\Session;

/**
 * Gestión de sesión PHP con cookies seguras.
 */
final class Session
{
    private const AUTH_KEY = 'auth';

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function flash(string $key, mixed $value = null): mixed
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }

        $data = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);

        return $data;
    }

    public static function isAuthenticated(): bool
    {
        $auth = self::get(self::AUTH_KEY);

        return is_array($auth) && !empty($auth['username']);
    }

    /**
     * @param array{username: string, password: string, user_info?: array<string, mixed>, server_info?: array<string, mixed>} $payload
     */
    public static function login(array $payload): void
    {
        self::set(self::AUTH_KEY, $payload);
    }

    public static function logout(): void
    {
        unset($_SESSION[self::AUTH_KEY]);
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
        }

        session_destroy();
    }

    /**
     * @return array{username: string, password: string, user_info?: array<string, mixed>, server_info?: array<string, mixed>}|null
     */
    public static function auth(): ?array
    {
        $auth = self::get(self::AUTH_KEY);

        return is_array($auth) ? $auth : null;
    }

    public static function username(): ?string
    {
        $auth = self::auth();

        return $auth['username'] ?? null;
    }
}
