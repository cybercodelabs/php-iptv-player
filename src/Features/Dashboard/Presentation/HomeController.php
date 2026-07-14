<?php

declare(strict_types=1);

namespace App\Features\Dashboard\Presentation;

use App\Features\Dashboard\Application\GetHomeCatalog;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Dashboard principal — layout inspirado en PLAYGO home.php.
 */
final class HomeController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $auth = Session::auth();
        $username = (string) ($auth['username'] ?? '');
        $password = (string) ($auth['password'] ?? '');

        $catalog = (new GetHomeCatalog(
            new XtreamClient(Config::xtreamHost())
        ))->execute($username, $password);

        View::render('dashboard/home', [
            'title' => 'Inicio',
            'username' => Session::username(),
            'styles' => [
                'css/home/hero.css',
                'css/home/cards.css',
                'css/home/content.css',
            ],
            'scripts' => [
                'js/home/tabs.js',
                'js/home/rails.js',
            ],
            'popular' => $catalog['popular'],
            'movies' => $catalog['movies'],
            'series' => $catalog['series'],
            'catalogError' => $catalog['error'],
        ]);
    }
}
