<?php

declare(strict_types=1);

namespace App\Features\Movies\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Carga el detalle de una película VOD para la página /movie.
 */
final class GetMovieDetail
{
    private const RELATED_LIMIT = 12;

    public function __construct(
        private readonly XtreamClient $client,
        private readonly MovieDetailMapper $mapper = new MovieDetailMapper(),
        private readonly MoviesCatalogMapper $catalogMapper = new MoviesCatalogMapper(),
    ) {
    }

    /**
     * @return array{
     *   movie: array<string, mixed>|null,
     *   playUrl: string|null,
     *   related: list<array<string, mixed>>,
     *   error: string|null
     * }
     */
    public function execute(string $username, string $password, ?string $streamId): array
    {
        $id = $this->normalizeId($streamId);
        if ($id === null) {
            return [
                'movie' => null,
                'playUrl' => null,
                'related' => [],
                'error' => 'Identificador de película no válido.',
            ];
        }

        try {
            $raw = $this->client->getVodInfo($username, $password, $id);
            $movie = $this->mapper->fromVodInfo($raw, $id);

            if ($movie === null) {
                return [
                    'movie' => null,
                    'playUrl' => null,
                    'related' => [],
                    'error' => 'No se encontró la película.',
                ];
            }

            $playUrl = url('stream/vod') . '?stream=' . rawurlencode($movie['id']);
            $related = $this->relatedMovies($username, $password, $movie['id']);

            return [
                'movie' => $movie,
                'playUrl' => $playUrl,
                'related' => $related,
                'error' => null,
            ];
        } catch (Throwable $e) {
            return [
                'movie' => null,
                'playUrl' => null,
                'related' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Recomendaciones totalmente aleatorias del catálogo VOD.
     *
     * @return list<array<string, mixed>>
     */
    private function relatedMovies(string $username, string $password, string $currentId): array
    {
        try {
            $rows = $this->client->getVodStreams($username, $password, null);
        } catch (Throwable) {
            return [];
        }

        if ($rows === []) {
            return [];
        }

        shuffle($rows);

        $out = [];
        foreach ($rows as $row) {
            $item = $this->catalogMapper->fromMovie($row);
            if ($item === null || $item['id'] === $currentId) {
                continue;
            }
            $out[] = $item;
            if (count($out) >= self::RELATED_LIMIT) {
                break;
            }
        }

        return $out;
    }

    private function normalizeId(?string $streamId): ?string
    {
        $value = trim((string) $streamId);
        if ($value === '' || !preg_match('/^\d+$/', $value)) {
            return null;
        }

        return $value;
    }
}
