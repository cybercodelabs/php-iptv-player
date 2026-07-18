<?php

declare(strict_types=1);

namespace App\Features\Player\Presentation;

use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use RuntimeException;

/**
 * Proxy de reproducción VOD: exige sesión y redirige al stream Xtream
 * (evita incrustar user/pass en el HTML).
 */
final class VodStreamController
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

        try {
            $client = new XtreamClient(Config::xtreamHost());
            $raw = $client->getVodInfo($username, $password, $streamId);
            $movie = isset($raw['movie_data']) && is_array($raw['movie_data']) ? $raw['movie_data'] : [];
            $extension = strtolower(trim((string) ($movie['container_extension'] ?? 'mp4')));
            if ($extension === '') {
                $extension = 'mp4';
            }

            $target = $client->streamUrl('movie', $username, $password, $streamId, $extension);
            header('Location: ' . $target, true, 302);
            exit;
        } catch (RuntimeException $e) {
            http_response_code(503);
            header('Content-Type: text/plain; charset=UTF-8');
            echo Config::get('APP_DEBUG') === 'true' ? $e->getMessage() : 'Stream no disponible.';
            exit;
        }
    }
}
