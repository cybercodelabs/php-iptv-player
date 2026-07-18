<?php

declare(strict_types=1);

namespace App\Features\Series\Presentation;

use App\Features\Series\Application\GetSerieDetail;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Ficha de serie: info + temporadas/episodios.
 */
final class SerieController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $auth = Session::auth();
        $username = (string) ($auth['username'] ?? '');
        $password = (string) ($auth['password'] ?? '');
        $seriesId = isset($_GET['series']) ? (string) $_GET['series'] : null;

        $detail = (new GetSerieDetail(
            new XtreamClient(Config::xtreamHost())
        ))->execute($username, $password, $seriesId);

        $serie = $detail['serie'];
        $title = is_array($serie) ? (string) ($serie['title'] ?? 'Serie') : 'Serie';

        View::render('series/show', [
            'title' => $title,
            'showAtmosphere' => false,
            'styles' => [
                'css/movies/detail.css',
                'css/movies/trailer.css',
                'css/series/detail.css',
                'css/series/episodes.css',
            ],
            'scripts' => [
                'js/movies/trailer-modal.js',
                'js/series/seasons.js',
            ],
            'serie' => $serie,
            'detailError' => $detail['error'],
        ]);
    }
}
