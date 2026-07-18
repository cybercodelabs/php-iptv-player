<?php

declare(strict_types=1);

namespace App\Features\Player\Presentation;

use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use RuntimeException;

/**
 * Proxy de reproducción de episodio: exige sesión y redirige al stream Xtream.
 */
final class SeriesStreamController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $seriesId = isset($_GET['series']) ? trim((string) $_GET['series']) : '';
        $episodeId = isset($_GET['episode']) ? trim((string) $_GET['episode']) : '';

        if ($seriesId === '' || $episodeId === '' || !preg_match('/^\d+$/', $seriesId) || !preg_match('/^\d+$/', $episodeId)) {
            http_response_code(400);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Episodio no válido.';
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
            $raw = $client->getSeriesInfo($username, $password, $seriesId);
            $episodes = isset($raw['episodes']) && is_array($raw['episodes']) ? $raw['episodes'] : [];
            $extension = 'mp4';

            foreach ($episodes as $eps) {
                if (!is_array($eps)) {
                    continue;
                }
                foreach ($eps as $ep) {
                    if (!is_array($ep)) {
                        continue;
                    }
                    if ((string) ($ep['id'] ?? '') !== $episodeId) {
                        continue;
                    }
                    $extension = strtolower(trim((string) ($ep['container_extension'] ?? 'mp4')));
                    if ($extension === '') {
                        $extension = 'mp4';
                    }
                    break 2;
                }
            }

            $target = $client->streamUrl('series', $username, $password, $episodeId, $extension);
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
