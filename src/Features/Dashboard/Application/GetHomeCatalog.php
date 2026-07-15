<?php

declare(strict_types=1);

namespace App\Features\Dashboard\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Obtiene el catálogo del home desde Xtream.
 */
final class GetHomeCatalog
{
    private const POPULAR_LIMIT = 12;
    private const GRID_LIMIT = 16;
    private const POOL_LIMIT = 40;

    public function __construct(
        private readonly XtreamClient $client,
        private readonly CatalogCardMapper $mapper = new CatalogCardMapper(),
    ) {
    }

    /**
     * @return array{
     *   popular: list<array<string, mixed>>,
     *   movies: list<array<string, mixed>>,
     *   series: list<array<string, mixed>>,
     *   error: string|null
     * }
     */
    public function execute(string $username, string $password): array
    {
        $empty = [
            'popular' => [],
            'movies' => [],
            'series' => [],
            'error' => null,
        ];

        try {
            // Mezclar el catálogo y tomar una muestra aleatoria
            $moviesRaw = $this->client->getVodStreams($username, $password);
            $seriesRaw = $this->client->getSeries($username, $password);
            shuffle($moviesRaw);
            shuffle($seriesRaw);

            $moviesMapped = $this->mapper->mapMany(
                $moviesRaw,
                fn (array $row) => $this->mapper->fromMovie($row),
                self::POOL_LIMIT
            );
            $seriesMapped = $this->mapper->mapMany(
                $seriesRaw,
                fn (array $row) => $this->mapper->fromSeries($row),
                self::POOL_LIMIT
            );

            return [
                'popular' => $this->mapper->mixPopular($moviesMapped, $seriesMapped, self::POPULAR_LIMIT),
                'movies' => array_slice($moviesMapped, 0, self::GRID_LIMIT),
                'series' => array_slice($seriesMapped, 0, self::GRID_LIMIT),
                'error' => null,
            ];
        } catch (Throwable $e) {
            $empty['error'] = $e->getMessage();

            return $empty;
        }
    }
}
