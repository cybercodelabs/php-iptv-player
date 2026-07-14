<?php

declare(strict_types=1);

namespace App\Features\Dashboard\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Obtiene el catálogo del home desde Xtream (layout tipo PLAYGO).
 */
final class GetHomeCatalog
{
    private const POPULAR_LIMIT = 12;
    private const GRID_LIMIT = 16;
    private const SOURCE_SLICE = 120;

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
     *   premieres: list<array<string, mixed>>,
     *   recent: list<array<string, mixed>>,
     *   error: string|null
     * }
     */
    public function execute(string $username, string $password): array
    {
        $empty = [
            'popular' => [],
            'movies' => [],
            'series' => [],
            'premieres' => [],
            'recent' => [],
            'error' => null,
        ];

        try {
            $moviesRaw = array_reverse($this->tail($this->client->getVodStreams($username, $password), self::SOURCE_SLICE));
            $seriesRaw = array_reverse($this->tail($this->client->getSeries($username, $password), self::SOURCE_SLICE));

            $moviesMapped = $this->mapper->mapMany(
                $moviesRaw,
                fn (array $row) => $this->mapper->fromMovie($row),
                40
            );
            $seriesMapped = $this->mapper->mapMany(
                $seriesRaw,
                fn (array $row) => $this->mapper->fromSeries($row),
                40
            );

            $currentYear = (string) date('Y');
            $premieres = array_values(array_filter(
                $moviesMapped,
                static fn (array $card): bool => ($card['year'] ?? null) === $currentYear
            ));
            $premieres = array_slice($premieres !== [] ? $premieres : $moviesMapped, 0, self::GRID_LIMIT);

            $recent = [];
            $max = max(count($moviesMapped), count($seriesMapped));
            for ($i = 0; $i < $max && count($recent) < self::GRID_LIMIT; $i++) {
                if (isset($moviesMapped[$i])) {
                    $recent[] = $moviesMapped[$i];
                }
                if (count($recent) >= self::GRID_LIMIT) {
                    break;
                }
                if (isset($seriesMapped[$i])) {
                    $recent[] = $seriesMapped[$i];
                }
            }

            return [
                'popular' => $this->mapper->mixPopular($moviesMapped, $seriesMapped, self::POPULAR_LIMIT),
                'movies' => array_slice($moviesMapped, 0, self::GRID_LIMIT),
                'series' => array_slice($seriesMapped, 0, self::GRID_LIMIT),
                'premieres' => $premieres,
                'recent' => $recent,
                'error' => null,
            ];
        } catch (Throwable $e) {
            $empty['error'] = $e->getMessage();

            return $empty;
        }
    }

    /**
     * @template T
     * @param list<T> $items
     * @return list<T>
     */
    private function tail(array $items, int $limit): array
    {
        if (count($items) <= $limit) {
            return $items;
        }

        return array_values(array_slice($items, -$limit));
    }
}
