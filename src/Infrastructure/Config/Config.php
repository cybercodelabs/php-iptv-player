<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use Dotenv\Dotenv;
use RuntimeException;

/**
 * Carga y acceso a variables de configuración (.env).
 */
final class Config
{
    /** @var array<string, string> */
    private static array $values = [];

    private static bool $loaded = false;

    public static function load(string $rootPath): void
    {
        if (self::$loaded) {
            return;
        }

        $envFile = $rootPath . DIRECTORY_SEPARATOR . '.env';
        if (is_file($envFile)) {
            Dotenv::createImmutable($rootPath)->safeLoad();
        }

        self::$values = [
            'APP_NAME' => $_ENV['APP_NAME'] ?? 'PHP IPTV Player',
            'APP_ENV' => $_ENV['APP_ENV'] ?? 'local',
            'APP_DEBUG' => $_ENV['APP_DEBUG'] ?? 'false',
            'APP_URL' => rtrim($_ENV['APP_URL'] ?? '', '/'),
            'APP_LOCALE' => $_ENV['APP_LOCALE'] ?? 'es',
            'APP_TIMEZONE' => $_ENV['APP_TIMEZONE'] ?? 'UTC',
            'XTREAM_HOST' => rtrim($_ENV['XTREAM_HOST'] ?? '', '/'),
            // Fondo de /home: 1..7 (ver hero.css)
            'HOME_BACKGROUND' => $_ENV['HOME_BACKGROUND'] ?? '1',
        ];

        self::$loaded = true;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        if (!self::$loaded) {
            throw new RuntimeException('Config no ha sido cargada. Llama a Config::load() primero.');
        }

        return self::$values[$key] ?? $default;
    }

    /**
     * Variante de fondo global (1–7): home, login y resto de layouts con atmósfera.
     */
    public static function homeBackground(): string
    {
        $value = trim((string) self::get('HOME_BACKGROUND', '1'));
        $allowed = ['1', '2', '3', '4', '5', '6', '7'];

        return in_array($value, $allowed, true) ? $value : '1';
    }

    public static function appName(): string
    {
        return (string) self::get('APP_NAME', 'PHP IPTV Player');
    }

    public static function xtreamHost(): string
    {
        return (string) self::get('XTREAM_HOST', '');
    }
}
