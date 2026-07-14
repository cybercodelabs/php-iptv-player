<?php

declare(strict_types=1);

namespace App\Features\Movies\Presentation;

use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/** Detalle de película (esqueleto). */
final class MovieController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        View::render('movies/show', [
            'title' => 'Película',
            'streamId' => $_GET['stream'] ?? null,
        ]);
    }
}
