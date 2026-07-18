<?php

declare(strict_types=1);

namespace App\Features\Series\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Catálogo de series (categorías + listado paginado) desde Xtream.
 */
final class GetSeriesCatalog
{
    /** Vista fija: 5 columnas × 4 filas (mismo diseño que películas). */
    public const PAGE_SIZE = 20;

    public function __construct(
        private readonly XtreamClient $client,
        private readonly SeriesCatalogMapper $mapper = new SeriesCatalogMapper(),
    ) {
    }

    /**
     * @return array{
     *   categories: list<array{id: string, name: string}>,
     *   series: list<array{
     *     id: string,
     *     title: string,
     *     year: string|null,
     *     rating: string|null,
     *     meta: string,
     *     href: string,
     *     image: string|null,
     *     type: string
     *   }>,
     *   activeCategory: string,
     *   page: int,
     *   totalPages: int,
     *   totalSeries: int,
     *   error: string|null
     * }
     */
    public function execute(
        string $username,
        string $password,
        ?string $categoryFilter = null,
        ?int $page = null,
    ): array {
        $active = $this->normalizeCategory($categoryFilter);
        $currentPage = $this->normalizePage($page);

        $empty = [
            'categories' => [],
            'series' => [],
            'activeCategory' => $active,
            'page' => 1,
            'totalPages' => 1,
            'totalSeries' => 0,
            'error' => null,
        ];

        try {
            $categoriesRaw = $this->client->getSeriesCategories($username, $password);
            $categories = $this->mapper->mapCategories($categoriesRaw);

            $categoryId = null;
            if ($active !== 'all') {
                $known = false;
                foreach ($categories as $category) {
                    if ($category['id'] === $active) {
                        $known = true;
                        break;
                    }
                }

                if (!$known) {
                    return [
                        'categories' => $categories,
                        'series' => [],
                        'activeCategory' => $active,
                        'page' => 1,
                        'totalPages' => 1,
                        'totalSeries' => 0,
                        'error' => null,
                    ];
                }

                $categoryId = (int) $active;
            }

            $seriesRaw = $this->client->getSeries($username, $password, $categoryId);
            $allSeries = $this->mapper->mapSeries($seriesRaw);
            $totalSeries = count($allSeries);
            $totalPages = max(1, (int) ceil($totalSeries / self::PAGE_SIZE));
            $currentPage = min($currentPage, $totalPages);

            $offset = ($currentPage - 1) * self::PAGE_SIZE;
            $series = array_slice($allSeries, $offset, self::PAGE_SIZE);

            return [
                'categories' => $categories,
                'series' => $series,
                'activeCategory' => $active,
                'page' => $currentPage,
                'totalPages' => $totalPages,
                'totalSeries' => $totalSeries,
                'error' => null,
            ];
        } catch (Throwable $e) {
            $empty['error'] = $e->getMessage();

            return $empty;
        }
    }

    private function normalizeCategory(?string $categoryFilter): string
    {
        $value = trim((string) $categoryFilter);
        if ($value === '' || strcasecmp($value, 'all') === 0) {
            return 'all';
        }

        if (!preg_match('/^\d+$/', $value)) {
            return 'all';
        }

        return $value;
    }

    private function normalizePage(?int $page): int
    {
        if ($page === null || $page < 1) {
            return 1;
        }

        return $page;
    }
}
