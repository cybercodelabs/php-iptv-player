<?php

declare(strict_types=1);

namespace App\Features\Movies\Presentation;

use App\Features\Movies\Application\GetMovieDetail;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Detalle y reproducción de una película VOD (ficha + reproductor).
 */
final class MovieController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $auth = Session::auth();
        $username = (string) ($auth['username'] ?? '');
        $password = (string) ($auth['password'] ?? '');
        $streamId = isset($_GET['stream']) ? (string) $_GET['stream'] : null;

        $detail = (new GetMovieDetail(
            new XtreamClient(Config::xtreamHost())
        ))->execute($username, $password, $streamId);

        $movie = $detail['movie'];
        $title = is_array($movie) ? (string) ($movie['title'] ?? 'Película') : 'Película';

        View::render('movies/show', [
            'title' => $title,
            'showAtmosphere' => false,
            'cdnStyles' => [
                'https://cdn.plyr.io/3.7.8/plyr.css',
            ],
            'cdnScripts' => [
                'https://cdn.plyr.io/3.7.8/plyr.polyfilled.js',
            ],
            'styles' => [
                'css/home/cards.css',
                'css/movies/detail.css',
                'css/movies/trailer.css',
                'css/player/vod.css',
            ],
            'scripts' => [
                'js/player/vod.js',
                'js/movies/trailer-modal.js',
            ],
            'movie' => $movie,
            'playUrl' => $detail['playUrl'],
            'related' => $detail['related'],
            'detailError' => $detail['error'],
        ]);
    }
}
