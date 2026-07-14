<?php

declare(strict_types=1);

namespace App\Shared\Layout;

use App\Infrastructure\Config\Config;
use RuntimeException;

/**
 * Renderizado de plantillas PHP.
 */
final class View
{
    public static function render(string $template, array $data = [], string $layout = 'layouts/main'): void
    {
        $content = self::capture($template, $data);

        $layoutData = array_merge($data, [
            'content' => $content,
            'appName' => Config::appName(),
        ]);

        echo self::capture($layout, $layoutData);
    }

    public static function renderPartial(string $template, array $data = []): void
    {
        echo self::capture($template, $data);
    }

    private static function capture(string $template, array $data): string
    {
        $path = templates_path($template . '.php');

        if (!is_file($path)) {
            throw new RuntimeException("Plantilla no encontrada: {$template}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $path;

        return (string) ob_get_clean();
    }
}
