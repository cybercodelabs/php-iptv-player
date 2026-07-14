<?php

declare(strict_types=1);

namespace App\Features\Series\Presentation;

use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/** Catálogo de series (esqueleto). */
final class SeriesController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        View::render('series/index', [
            'title' => 'Series',
        ]);
    }
}
