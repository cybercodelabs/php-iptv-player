<?php

declare(strict_types=1);

namespace App\Features\Dashboard\Application;

/**
 * Normaliza filas Xtream a tarjetas de UI para el home.
 */
final class CatalogCardMapper
{
    private const TITLE_MAX = 40;

    /**
     * @param array<string, mixed> $row
     * @return array{title: string, meta: string, year: string|null, rating: string|null, href: string, image: string|null, type: string}|null
     */
    public function fromMovie(array $row): ?array
    {
        $id = $row['stream_id'] ?? null;
        if ($id === null || $id === '') {
            return null;
        }

        $name = $this->cleanTitle((string) ($row['name'] ?? 'Película'));
        $year = $this->yearFromRow($row);
        $rating = $this->ratingFromRow($row, 'rating_5based');

        return [
            'type' => 'movie',
            'title' => $name,
            'year' => $year,
            'rating' => $rating,
            'meta' => $this->metaLine($year, $rating),
            'href' => url('movie') . '?stream=' . rawurlencode((string) $id),
            'image' => $this->image((string) ($row['stream_icon'] ?? '')),
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @return array{title: string, meta: string, year: string|null, rating: string|null, href: string, image: string|null, type: string}|null
     */
    public function fromSeries(array $row): ?array
    {
        $id = $row['series_id'] ?? null;
        if ($id === null || $id === '') {
            return null;
        }

        $name = $this->cleanTitle((string) ($row['name'] ?? 'Serie'));
        $year = $this->yearFromRow($row);
        if ($year === null && !empty($row['releaseDate'])) {
            $year = substr((string) $row['releaseDate'], 0, 4);
        }
        $rating = $this->ratingFromRow($row, 'rating');
        if ($rating === null) {
            $rating = $this->ratingFromRow($row, 'rating_5based');
        }

        return [
            'type' => 'serie',
            'title' => $name,
            'year' => $year,
            'rating' => $rating,
            'meta' => $this->metaLine($year, $rating),
            'href' => url('serie') . '?series=' . rawurlencode((string) $id),
            'image' => $this->image((string) ($row['cover'] ?? '')),
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @return array{title: string, meta: string, year: string|null, rating: string|null, href: string, image: string|null, type: string}|null
     */
    public function fromLive(array $row): ?array
    {
        $id = $row['stream_id'] ?? null;
        if ($id === null || $id === '') {
            return null;
        }

        $name = $this->cleanTitle((string) ($row['name'] ?? 'Canal'));

        return [
            'type' => 'live',
            'title' => $name,
            'year' => null,
            'rating' => null,
            'meta' => 'En vivo',
            'href' => url('channel') . '?stream=' . rawurlencode((string) $id),
            'image' => $this->image((string) ($row['stream_icon'] ?? '')),
        ];
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @param callable(array<string, mixed>): (?array{title: string, meta: string, href: string, image: string|null, type: string}) $mapper
     * @return list<array{title: string, meta: string, href: string, image: string|null, type: string}>
     */
    public function mapMany(array $rows, callable $mapper, int $limit): array
    {
        $cards = [];

        foreach ($rows as $row) {
            $card = $mapper($row);
            if ($card === null) {
                continue;
            }
            $cards[] = $card;
            if (count($cards) >= $limit) {
                break;
            }
        }

        return $cards;
    }

    /**
     * Mezcla películas y series para el riel “Populares”.
     *
     * @param list<array{title: string, meta: string, href: string, image: string|null, type: string}> $movies
     * @param list<array{title: string, meta: string, href: string, image: string|null, type: string}> $series
     * @return list<array{title: string, meta: string, href: string, image: string|null, type: string}>
     */
    public function mixPopular(array $movies, array $series, int $limit): array
    {
        $pool = array_merge($movies, $series);
        if ($pool === []) {
            return [];
        }

        shuffle($pool);

        return array_slice($pool, 0, $limit);
    }

    private function cleanTitle(string $name): string
    {
        $name = trim(preg_replace('/\s*\(\d{4}\)\s*$/', '', $name) ?? $name);
        if (mb_strlen($name) > self::TITLE_MAX) {
            return mb_substr($name, 0, self::TITLE_MAX - 1) . '…';
        }

        return $name;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function yearFromRow(array $row): ?string
    {
        if (!empty($row['year'])) {
            return (string) $row['year'];
        }

        if (!empty($row['releaseDate']) && preg_match('/^\d{4}/', (string) $row['releaseDate'], $m)) {
            return $m[0];
        }

        if (!empty($row['name']) && preg_match('/\((\d{4})\)\s*$/', (string) $row['name'], $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function ratingFromRow(array $row, string $key): ?string
    {
        if (!isset($row[$key]) || $row[$key] === '' || $row[$key] === null) {
            return null;
        }

        $value = is_numeric($row[$key]) ? number_format((float) $row[$key], 1) : (string) $row[$key];

        return $value;
    }

    private function metaLine(?string $year, ?string $rating): string
    {
        $parts = array_filter([$year, $rating !== null ? '★ ' . $rating : null]);

        return $parts !== [] ? implode(' · ', $parts) : 'Catálogo';
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
