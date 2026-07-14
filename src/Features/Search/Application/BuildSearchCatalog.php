<?php

declare(strict_types=1);

namespace App\Features\Search\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Índice ligero de películas y series para el buscador (estilo PLAYGO).
 */
final class BuildSearchCatalog
{
    public function __construct(
        private readonly XtreamClient $client,
    ) {
    }

    /**
     * @return array{
     *   movies: list<array{id: string, title: string, image: string|null, href: string}>,
     *   series: list<array{id: string, title: string, image: string|null, href: string}>,
     *   error: string|null
     * }
     */
    public function execute(string $username, string $password): array
    {
        try {
            $moviesRaw = $this->client->getVodStreams($username, $password);
            $seriesRaw = $this->client->getSeries($username, $password);

            return [
                'movies' => $this->mapMovies($moviesRaw),
                'series' => $this->mapSeries($seriesRaw),
                'error' => null,
            ];
        } catch (Throwable $e) {
            return [
                'movies' => [],
                'series' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array{id: string, title: string, image: string|null, href: string}>
     */
    private function mapMovies(array $rows): array
    {
        $out = [];

        foreach ($rows as $row) {
            $id = $row['stream_id'] ?? null;
            if ($id === null || $id === '') {
                continue;
            }

            $out[] = [
                'id' => (string) $id,
                'title' => $this->cleanTitle((string) ($row['name'] ?? 'Película')),
                'image' => $this->image((string) ($row['stream_icon'] ?? '')),
                'href' => url('movie') . '?stream=' . rawurlencode((string) $id),
            ];
        }

        return $out;
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array{id: string, title: string, image: string|null, href: string}>
     */
    private function mapSeries(array $rows): array
    {
        $out = [];

        foreach ($rows as $row) {
            $id = $row['series_id'] ?? null;
            if ($id === null || $id === '') {
                continue;
            }

            $out[] = [
                'id' => (string) $id,
                'title' => $this->cleanTitle((string) ($row['name'] ?? 'Serie')),
                'image' => $this->image((string) ($row['cover'] ?? '')),
                'href' => url('serie') . '?series=' . rawurlencode((string) $id),
            ];
        }

        return $out;
    }

    private function cleanTitle(string $name): string
    {
        return trim(preg_replace('/\s*\(\d{4}\)\s*$/', '', $name) ?? $name);
    }

    private function image(string $url): ?string
    {
        $url = trim($url);
        if ($url === '' || !preg_match('#^https?://#i', $url)) {
            return null;
        }

        return $url;
    }
}
