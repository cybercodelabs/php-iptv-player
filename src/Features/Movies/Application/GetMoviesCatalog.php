<?php

declare(strict_types=1);

namespace App\Features\Movies\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Catálogo de películas (categorías + VOD paginado) desde Xtream.
 */
final class GetMoviesCatalog
{
    /** Vista fija: 5 columnas × 4 filas. */
    public const PAGE_SIZE = 20;

    public function __construct(
        private readonly XtreamClient $client,
        private readonly MoviesCatalogMapper $mapper = new MoviesCatalogMapper(),
    ) {
    }

    /**
     * @return array{
     *   categories: list<array{id: string, name: string}>,
     *   movies: list<array{
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
     *   totalMovies: int,
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
            'movies' => [],
            'activeCategory' => $active,
            'page' => 1,
            'totalPages' => 1,
            'totalMovies' => 0,
            'error' => null,
        ];

        try {
            $categoriesRaw = $this->client->getVodCategories($username, $password);
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
                        'movies' => [],
                        'activeCategory' => $active,
                        'page' => 1,
                        'totalPages' => 1,
                        'totalMovies' => 0,
                        'error' => null,
                    ];
                }

                $categoryId = (int) $active;
            }

            $streamsRaw = $this->client->getVodStreams($username, $password, $categoryId);
            $allMovies = $this->mapper->mapMovies($streamsRaw);
            $totalMovies = count($allMovies);
            $totalPages = max(1, (int) ceil($totalMovies / self::PAGE_SIZE));
            $currentPage = min($currentPage, $totalPages);

            $offset = ($currentPage - 1) * self::PAGE_SIZE;
            $movies = array_slice($allMovies, $offset, self::PAGE_SIZE);

            return [
                'categories' => $categories,
                'movies' => $movies,
                'activeCategory' => $active,
                'page' => $currentPage,
                'totalPages' => $totalPages,
                'totalMovies' => $totalMovies,
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
