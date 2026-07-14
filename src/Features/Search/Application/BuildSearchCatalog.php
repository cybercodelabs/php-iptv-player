<?php

declare(strict_types=1);

namespace App\Features\Search\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Índice de películas y series para el buscador (con metadatos de UI).
 */
final class BuildSearchCatalog
{
    public function __construct(
        private readonly XtreamClient $client,
    ) {
    }

    /**
     * @return array{
     *   movies: list<array<string, mixed>>,
     *   series: list<array<string, mixed>>,
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
     * @return list<array<string, mixed>>
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
                'year' => $this->yearFromRow($row),
                'rating' => $this->ratingFromRow($row),
                'duration' => $this->durationFromRow($row),
                'genre' => $this->genreFromRow($row),
            ];
        }

        return $out;
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
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
                'year' => $this->yearFromRow($row),
                'rating' => $this->ratingFromRow($row),
                'duration' => $this->durationFromRow($row),
                'genre' => $this->genreFromRow($row),
            ];
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function yearFromRow(array $row): ?string
    {
        if (!empty($row['year']) && preg_match('/^\d{4}/', (string) $row['year'], $m)) {
            return $m[0];
        }

        foreach (['releaseDate', 'releasedate', 'release_date'] as $key) {
            if (!empty($row[$key]) && preg_match('/^\d{4}/', (string) $row[$key], $m)) {
                return $m[0];
            }
        }

        if (!empty($row['name']) && preg_match('/\((\d{4})\)\s*$/', (string) $row['name'], $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function ratingFromRow(array $row): ?string
    {
        foreach (['rating_5based', 'rating'] as $key) {
            if (!isset($row[$key]) || $row[$key] === '' || $row[$key] === null) {
                continue;
            }

            if (is_numeric($row[$key])) {
                $value = (float) $row[$key];
                if ($value <= 0) {
                    continue;
                }

                return number_format($value, 1);
            }

            $text = trim((string) $row[$key]);
            if ($text !== '' && $text !== '0' && $text !== '0.0') {
                return $text;
            }
        }

        return null;
    }

    /**
     * Duración si el listado la trae (no todos los paneles la incluyen).
     *
     * @param array<string, mixed> $row
     */
    private function durationFromRow(array $row): ?string
    {
        $raw = $row['duration'] ?? $row['episode_run_time'] ?? $row['runtime'] ?? null;
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_numeric($raw)) {
            $minutes = (int) $raw;
            if ($minutes <= 0) {
                return null;
            }
            if ($minutes >= 60) {
                $h = intdiv($minutes, 60);
                $m = $minutes % 60;

                return $m > 0 ? "{$h}h {$m}m" : "{$h}h";
            }

            return $minutes . ' min';
        }

        $text = trim((string) $raw);
        if ($text === '' || $text === '0' || $text === '00:00') {
            return null;
        }

        // Formatos HH:MM:SS o MM:SS
        if (preg_match('/^(\d+):(\d{2})(?::(\d{2}))?$/', $text, $m)) {
            $hours = (int) $m[1];
            $mins = (int) $m[2];
            if (isset($m[3])) {
                // HH:MM:SS
                if ($hours > 0) {
                    return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
                }

                return $mins . ' min';
            }

            // MM:SS → minutos aproximados
            return $hours > 0 ? $hours . ' min' : null;
        }

        if (preg_match('/^\d+\s*(min|m|h)/i', $text)) {
            return $text;
        }

        return $text;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function genreFromRow(array $row): ?string
    {
        $raw = $row['genre'] ?? $row['genres'] ?? null;
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_array($raw)) {
            $parts = array_values(array_filter(array_map('strval', $raw)));
            $raw = implode(', ', $parts);
        }

        $text = trim((string) $raw);
        if ($text === '') {
            return null;
        }

        // Solo el primer género para no saturar la fila
        $first = preg_split('/\s*[,\/|]\s*/', $text)[0] ?? $text;
        $first = trim((string) $first);

        return $first !== '' ? $first : null;
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
