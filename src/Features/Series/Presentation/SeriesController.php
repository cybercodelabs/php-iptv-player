<?php

declare(strict_types=1);

namespace App\Features\Series\Presentation;

use App\Features\Series\Application\GetSeriesCatalog;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Catálogo de series — mismo diseño que películas (géneros, grid 5×4, paginación).
 */
final class SeriesController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $auth = Session::auth();
        $username = (string) ($auth['username'] ?? '');
        $password = (string) ($auth['password'] ?? '');
        $category = isset($_GET['category']) ? (string) $_GET['category'] : null;
        $page = isset($_GET['page']) && ctype_digit((string) $_GET['page'])
            ? (int) $_GET['page']
            : 1;

        $catalog = (new GetSeriesCatalog(
            new XtreamClient(Config::xtreamHost())
        ))->execute($username, $password, $category, $page);

        View::render('series/index', [
            'title' => 'Series',
            'styles' => [
                'css/home/cards.css',
                'css/movies/hero.css',
                'css/movies/categories.css',
                'css/movies/grid.css',
                'css/movies/pagination.css',
            ],
            'scripts' => [
                'js/movies/categories.js',
            ],
            'categories' => $catalog['categories'],
            'series' => $catalog['series'],
            'activeCategory' => $catalog['activeCategory'],
            'page' => $catalog['page'],
            'totalPages' => $catalog['totalPages'],
            'totalSeries' => $catalog['totalSeries'],
            'pageSize' => GetSeriesCatalog::PAGE_SIZE,
            'catalogError' => $catalog['error'],
        ]);
    }
}
