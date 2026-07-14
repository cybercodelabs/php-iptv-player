<?php

declare(strict_types=1);

namespace App\Features\Dashboard\Presentation;

use App\Infrastructure\Session\Session;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Dashboard principal (esqueleto).
 */
final class HomeController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        View::render('dashboard/home', [
            'title' => 'Inicio',
            'username' => Session::username(),
        ]);
    }
}
