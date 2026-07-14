<?php

declare(strict_types=1);

namespace App\Features\Live\Presentation;

use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/** Detalle / reproducción de un canal (esqueleto). */
final class ChannelController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        View::render('live/channel', [
            'title' => 'Canal',
            'streamId' => $_GET['stream'] ?? null,
        ]);
    }
}
