<?php

declare(strict_types=1);

namespace App\Features\Live\Presentation;

use App\Features\Live\Application\GetLiveCatalog;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Listado de TV en vivo — categorías + grid de canales.
 */
final class ChannelsController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $auth = Session::auth();
        $username = (string) ($auth['username'] ?? '');
        $password = (string) ($auth['password'] ?? '');
        $category = isset($_GET['category']) ? (string) $_GET['category'] : null;

        $catalog = (new GetLiveCatalog(
            new XtreamClient(Config::xtreamHost())
        ))->execute($username, $password, $category);

        View::render('live/channels', [
            'title' => 'TV en vivo',
            'styles' => [
                'css/live/hero.css',
                'css/live/categories.css',
                'css/live/cards.css',
            ],
            'scripts' => [
                'js/live/categories.js',
            ],
            'categories' => $catalog['categories'],
            'channels' => $catalog['channels'],
            'activeCategory' => $catalog['activeCategory'],
            'catalogError' => $catalog['error'],
        ]);
    }
}
