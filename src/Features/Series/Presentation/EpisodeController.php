<?php

declare(strict_types=1);

namespace App\Features\Series\Presentation;

use App\Features\Series\Application\GetEpisodeDetail;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Reproducción de un episodio de serie.
 */
final class EpisodeController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $auth = Session::auth();
        $username = (string) ($auth['username'] ?? '');
        $password = (string) ($auth['password'] ?? '');
        $seriesId = isset($_GET['series']) ? (string) $_GET['series'] : null;
        $episodeId = isset($_GET['episode']) ? (string) $_GET['episode'] : null;

        $detail = (new GetEpisodeDetail(
            new XtreamClient(Config::xtreamHost())
        ))->execute($username, $password, $seriesId, $episodeId);

        $serie = $detail['serie'];
        $episode = $detail['episode'];
        $seasonNumber = $detail['seasonNumber'];

        $title = 'Episodio';
        if (is_array($serie) && is_array($episode)) {
            $epNum = (string) ($episode['number'] ?? '');
            $title = (string) ($serie['title'] ?? 'Serie')
                . ' · T' . ($seasonNumber ?? '?')
                . 'E' . ($epNum !== '' ? $epNum : '?');
        }

        View::render('series/episode', [
            'title' => $title,
            'showAtmosphere' => false,
            'cdnStyles' => [
                'https://cdn.plyr.io/3.7.8/plyr.css',
            ],
            'cdnScripts' => [
                'https://cdn.plyr.io/3.7.8/plyr.polyfilled.js',
            ],
            'styles' => [
                'css/movies/detail.css',
                'css/player/vod.css',
                'css/series/episode.css',
            ],
            'scripts' => [
                'js/player/vod.js',
            ],
            'serie' => $serie,
            'episode' => $episode,
            'seasonNumber' => $seasonNumber,
            'prevHref' => $detail['prevHref'],
            'nextHref' => $detail['nextHref'],
            'playUrl' => $detail['playUrl'],
            'detailError' => $detail['error'],
        ]);
    }
}
