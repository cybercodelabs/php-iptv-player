<?php

declare(strict_types=1);

namespace App\Features\Live\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Catálogo de TV en vivo (categorías + canales) desde Xtream.
 */
final class GetLiveCatalog
{
    public function __construct(
        private readonly XtreamClient $client,
        private readonly LiveCatalogMapper $mapper = new LiveCatalogMapper(),
    ) {
    }

    /**
     * @return array{
     *   categories: list<array{id: string, name: string}>,
     *   channels: list<array{id: string, name: string, logo: string|null, categoryId: string|null, href: string}>,
     *   activeCategory: string,
     *   error: string|null
     * }
     */
    public function execute(string $username, string $password, ?string $categoryFilter = null): array
    {
        $active = $this->normalizeCategory($categoryFilter);

        $empty = [
            'categories' => [],
            'channels' => [],
            'activeCategory' => $active,
            'error' => null,
        ];

        try {
            $categoriesRaw = $this->client->getLiveCategories($username, $password);
            $categories = $this->mapper->mapCategories($categoriesRaw);

            $categoryId = null;
            if ($active !== 'all') {
                // Solo pedir a Xtream si la categoría existe; si no, filtramos a vacío local
                $known = false;
                foreach ($categories as $category) {
                    if ($category['id'] === $active) {
                        $known = true;
                        break;
                    }
                }

                if ($known) {
                    $categoryId = (int) $active;
                } else {
                    return [
                        'categories' => $categories,
                        'channels' => [],
                        'activeCategory' => $active,
                        'error' => null,
                    ];
                }
            }

            $streamsRaw = $this->client->getLiveStreams($username, $password, $categoryId);
            $channels = $this->mapper->mapChannels($streamsRaw);

            return [
                'categories' => $categories,
                'channels' => $channels,
                'activeCategory' => $active,
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

        // Solo IDs numéricos (evita inyección rara en query)
        if (!preg_match('/^\d+$/', $value)) {
            return 'all';
        }

        return $value;
    }
}
