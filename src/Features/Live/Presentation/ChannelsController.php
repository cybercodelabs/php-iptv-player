<?php

declare(strict_types=1);

namespace App\Features\Live\Presentation;

use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/** Listado de canales en vivo (esqueleto). */
final class ChannelsController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        View::render('live/channels', [
            'title' => 'TV en vivo',
        ]);
    }
}
