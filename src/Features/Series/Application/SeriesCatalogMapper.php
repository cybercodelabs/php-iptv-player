<?php

declare(strict_types=1);

namespace App\Features\Series\Application;

/**
 * Normaliza categorías y series para la UI.
 */
final class SeriesCatalogMapper
{
    private const TITLE_MAX = 42;

    /**
     * @param array<string, mixed> $row
     * @return array{id: string, name: string}|null
     */
    public function fromCategory(array $row): ?array
    {
        $id = $row['category_id'] ?? null;
        $name = trim((string) ($row['category_name'] ?? ''));

        if ($id === null || $id === '' || $name === '') {
            return null;
        }

        return [
            'id' => (string) $id,
            'name' => $name,
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @return array{
     *   id: string,
     *   title: string,
     *   year: string|null,
     *   rating: string|null,
     *   meta: string,
     *   href: string,
     *   image: string|null,
     *   type: string
     * }|null
     */
    public function fromSeries(array $row): ?array
    {
        $id = $row['series_id'] ?? null;
        if ($id === null || $id === '') {
            return null;
        }

        $year = $this->yearFromRow($row);
        $rating = $this->ratingFromRow($row);

        return [
            'id' => (string) $id,
            'type' => 'serie',
            'title' => $this->cleanTitle((string) ($row['name'] ?? 'Serie')),
            'year' => $year,
            'rating' => $rating,
            'meta' => $this->metaLine($year, $rating),
            'href' => url('serie') . '?series=' . rawurlencode((string) $id),
            'image' => $this->image((string) ($row['cover'] ?? '')),
        ];
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array{id: string, name: string}>
     */
    public function mapCategories(array $rows): array
    {
        $out = [];

        foreach ($rows as $row) {
            $item = $this->fromCategory($row);
            if ($item !== null) {
                $out[] = $item;
            }
        }

        return $out;
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array{
     *   id: string,
     *   title: string,
     *   year: string|null,
     *   rating: string|null,
     *   meta: string,
     *   href: string,
     *   image: string|null,
     *   type: string
     * }>
     */
    public function mapSeries(array $rows): array
    {
        $out = [];

        foreach ($rows as $row) {
            $item = $this->fromSeries($row);
            if ($item !== null) {
                $out[] = $item;
            }
        }

        return $out;
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
    private function ratingFromRow(array $row): ?string
    {
        foreach (['rating', 'rating_5based'] as $key) {
            if (!isset($row[$key]) || $row[$key] === '' || $row[$key] === null) {
                continue;
            }

            return is_numeric($row[$key])
                ? number_format((float) $row[$key], 1)
                : (string) $row[$key];
        }

        return null;
    }

    private function metaLine(?string $year, ?string $rating): string
    {
        $parts = array_filter([$year, $rating !== null ? '★ ' . $rating : null]);

        return $parts !== [] ? implode(' · ', $parts) : 'Serie';
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
