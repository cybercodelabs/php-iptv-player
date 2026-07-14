<?php

declare(strict_types=1);

namespace App\Features\Search\Presentation;

use App\Features\Search\Application\BuildSearchCatalog;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;

/**
 * API JSON del catálogo para el buscador del header.
 */
final class SearchCatalogController
{
    public function __invoke(): void
    {
        if (!Session::isAuthenticated()) {
            Response::json(['error' => 'No autenticado'], 401);
        }

        $auth = Session::auth();
        $username = (string) ($auth['username'] ?? '');
        $password = (string) ($auth['password'] ?? '');

        $catalog = (new BuildSearchCatalog(
            new XtreamClient(Config::xtreamHost())
        ))->execute($username, $password);

        if ($catalog['error'] !== null) {
            Response::json([
                'movies' => [],
                'series' => [],
                'error' => Config::get('APP_DEBUG') === 'true'
                    ? $catalog['error']
                    : 'No se pudo cargar el catálogo de búsqueda.',
            ], 503);
        }

        Response::json([
            'movies' => $catalog['movies'],
            'series' => $catalog['series'],
        ]);
    }
}
