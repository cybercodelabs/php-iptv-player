<?php

declare(strict_types=1);

namespace App\Features\Player\Presentation;

use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;

/**
 * Proxy de reproducción live: exige sesión y redirige al HLS Xtream
 * (evita incrustar user/pass en el HTML).
 */
final class LiveStreamController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $streamId = isset($_GET['stream']) ? trim((string) $_GET['stream']) : '';
        if ($streamId === '' || !preg_match('/^\d+$/', $streamId)) {
            http_response_code(400);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Stream no válido.';
            exit;
        }

        $auth = Session::auth();
        $username = (string) ($auth['username'] ?? '');
        $password = (string) ($auth['password'] ?? '');

        if ($username === '' || $password === '') {
            http_response_code(401);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Sesión no válida.';
            exit;
        }

        $client = new XtreamClient(Config::xtreamHost());
        $target = $client->streamUrl('live', $username, $password, $streamId, 'm3u8');
        header('Location: ' . $target, true, 302);
        exit;
    }
}
