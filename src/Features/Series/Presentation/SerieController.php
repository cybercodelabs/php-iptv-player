<?php

declare(strict_types=1);

namespace App\Features\Series\Presentation;

use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/** Detalle de serie (esqueleto). */
final class SerieController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        View::render('series/show', [
            'title' => 'Serie',
            'seriesId' => $_GET['series'] ?? null,
        ]);
    }
}
