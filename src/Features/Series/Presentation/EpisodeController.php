<?php

declare(strict_types=1);

namespace App\Features\Series\Presentation;

use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/** Reproducción de episodio (esqueleto). */
final class EpisodeController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        View::render('series/episode', [
            'title' => 'Episodio',
            'episodeId' => $_GET['episode'] ?? null,
        ]);
    }
}
