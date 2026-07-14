<?php

declare(strict_types=1);

namespace App\Features\Profile\Presentation;

use App\Infrastructure\Session\Session;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/** Perfil / información de cuenta (esqueleto). */
final class ProfileController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $auth = Session::auth() ?? [];

        View::render('profile/index', [
            'title' => 'Mi cuenta',
            'username' => Session::username(),
            'userInfo' => $auth['user_info'] ?? [],
        ]);
    }
}
