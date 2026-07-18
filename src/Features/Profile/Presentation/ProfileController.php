<?php

declare(strict_types=1);

namespace App\Features\Profile\Presentation;

use App\Features\Profile\Application\ProfileMapper;
use App\Infrastructure\Session\Session;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Perfil / información de cuenta Xtream.
 */
final class ProfileController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $auth = Session::auth() ?? [];
        $username = Session::username() ?? (string) ($auth['username'] ?? '');
        $userInfo = is_array($auth['user_info'] ?? null) ? $auth['user_info'] : [];
        $serverInfo = is_array($auth['server_info'] ?? null) ? $auth['server_info'] : [];

        $profile = (new ProfileMapper())->map($username, $userInfo, $serverInfo);

        View::render('profile/index', [
            'title' => 'Mi cuenta',
            'styles' => [
                'css/profile/page.css',
            ],
            'profile' => $profile,
        ]);
    }
}
