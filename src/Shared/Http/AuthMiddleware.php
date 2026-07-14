<?php

declare(strict_types=1);

namespace App\Shared\Http;

use App\Infrastructure\Session\Session;
use App\Infrastructure\Http\Response;

/**
 * Middleware de autenticación para rutas protegidas.
 */
final class AuthMiddleware
{
    public static function requireAuth(): void
    {
        if (!Session::isAuthenticated()) {
            Response::redirect('login');
        }
    }

    public static function guestOnly(): void
    {
        if (Session::isAuthenticated()) {
            Response::redirect('home');
        }
    }
}
