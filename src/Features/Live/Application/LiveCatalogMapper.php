<?php

declare(strict_types=1);

namespace App\Features\Live\Application;

/**
 * Normaliza categorías y canales live para la UI.
 */
final class LiveCatalogMapper
{
    private const NAME_MAX = 42;

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
     * @return array{id: string, name: string, logo: string|null, categoryId: string|null, href: string}|null
     */
    public function fromChannel(array $row): ?array
    {
        $id = $row['stream_id'] ?? null;
        if ($id === null || $id === '') {
            return null;
        }

        $categoryId = $row['category_id'] ?? null;

        return [
            'id' => (string) $id,
            'name' => $this->cleanName((string) ($row['name'] ?? 'Canal')),
            'logo' => $this->image((string) ($row['stream_icon'] ?? '')),
            'categoryId' => $categoryId !== null && $categoryId !== '' ? (string) $categoryId : null,
            'href' => url('channel') . '?stream=' . rawurlencode((string) $id),
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
     * @return list<array{id: string, name: string, logo: string|null, categoryId: string|null, href: string}>
     */
    public function mapChannels(array $rows): array
    {
        $out = [];

        foreach ($rows as $row) {
            $item = $this->fromChannel($row);
            if ($item !== null) {
                $out[] = $item;
            }
        }

        return $out;
    }

    private function cleanName(string $name): string
    {
        $name = trim($name);
        if (mb_strlen($name) > self::NAME_MAX) {
            return mb_substr($name, 0, self::NAME_MAX - 1) . '…';
        }

        return $name;
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
