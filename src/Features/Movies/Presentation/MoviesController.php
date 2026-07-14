<?php

declare(strict_types=1);

namespace App\Features\Movies\Presentation;

use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/** Catálogo de películas (esqueleto). */
final class MoviesController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        View::render('movies/index', [
            'title' => 'Películas',
        ]);
    }
}
