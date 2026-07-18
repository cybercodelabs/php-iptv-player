<?php

declare(strict_types=1);

namespace App\Features\Movies\Presentation;

use App\Features\Movies\Application\GetMoviesCatalog;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Catálogo de películas — géneros, grid 5×4 y paginación.
 */
final class MoviesController
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

        $catalog = (new GetMoviesCatalog(
            new XtreamClient(Config::xtreamHost())
        ))->execute($username, $password, $category, $page);

        View::render('movies/index', [
            'title' => 'Películas',
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
            'movies' => $catalog['movies'],
            'activeCategory' => $catalog['activeCategory'],
            'page' => $catalog['page'],
            'totalPages' => $catalog['totalPages'],
            'totalMovies' => $catalog['totalMovies'],
            'pageSize' => GetMoviesCatalog::PAGE_SIZE,
            'catalogError' => $catalog['error'],
        ]);
    }
}
