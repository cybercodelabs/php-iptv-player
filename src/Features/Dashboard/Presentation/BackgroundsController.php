<?php

declare(strict_types=1);

namespace App\Features\Dashboard\Presentation;

use App\Infrastructure\Config\Config;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Galería de fondos de /home para elegir HOME_BACKGROUND en .env.
 */
final class BackgroundsController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        View::render('dashboard/backgrounds', [
            'title' => 'Fondos',
            'username' => \App\Infrastructure\Session\Session::username(),
            'styles' => [
                'css/home/backgrounds-preview.css',
            ],
            'currentBackground' => Config::homeBackground(),
            'backgrounds' => [
                ['id' => '1', 'name' => 'Líneas diagonales', 'hint' => 'Patrón geométrico clásico'],
                ['id' => '2', 'name' => 'Abstracto', 'hint' => 'Motivo irregular a color'],
                ['id' => '3', 'name' => 'Blob orgánico', 'hint' => 'Formas suaves rellenas'],
                ['id' => '4', 'name' => 'Marcos', 'hint' => 'Circuitos y marcos'],
                ['id' => '5', 'name' => 'Hexágonos', 'hint' => 'Celosía hexagonal'],
                ['id' => '6', 'name' => 'Arcos', 'hint' => 'Arcos entrelazados'],
                ['id' => '7', 'name' => 'Hex rotados', 'hint' => 'Hexágonos a escala rotada'],
            ],
        ]);
    }
}
