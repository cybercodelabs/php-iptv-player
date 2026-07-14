<?php

declare(strict_types=1);

namespace App\Features\Auth\Presentation;

use App\Infrastructure\Http\Response;
use App\Infrastructure\Session\Session;

/**
 * Cierre de sesión.
 */
final class LogoutController
{
    public function __invoke(): void
    {
        Session::logout();
        Response::redirect('login');
    }
}
