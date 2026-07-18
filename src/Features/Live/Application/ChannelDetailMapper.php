<?php

declare(strict_types=1);

namespace App\Features\Live\Application;

/**
 * Normaliza canal live y entradas EPG para /channel.
 */
final class ChannelDetailMapper
{
    /**
     * @param array<string, mixed> $row
     * @param array<string, string> $categoryNames id => name
     * @return array{
     *   id: string,
     *   name: string,
     *   logo: string|null,
     *   categoryId: string|null,
     *   categoryName: string|null
     * }|null
     */
    public function fromStream(array $row, array $categoryNames = []): ?array
    {
        $id = $row['stream_id'] ?? null;
        if ($id === null || $id === '') {
            return null;
        }

        $categoryId = $row['category_id'] ?? null;
        $categoryKey = $categoryId !== null && $categoryId !== '' ? (string) $categoryId : null;
        $categoryName = $categoryKey !== null ? ($categoryNames[$categoryKey] ?? null) : null;

        if ($categoryName === null) {
            $fromRow = trim((string) ($row['category_name'] ?? ''));
            $categoryName = $fromRow !== '' ? $fromRow : null;
        }

        return [
            'id' => (string) $id,
            'name' => trim((string) ($row['name'] ?? 'Canal')) ?: 'Canal',
            'logo' => $this->image((string) ($row['stream_icon'] ?? '')),
            'categoryId' => $categoryKey,
            'categoryName' => $categoryName,
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @return array{title: string, description: string|null, start: string|null, end: string|null}|null
     */
    public function fromEpg(array $row): ?array
    {
        $title = $this->decodeEpgText((string) ($row['title'] ?? ''));
        if ($title === '') {
            return null;
        }

        $description = $this->decodeEpgText((string) ($row['description'] ?? ''));

        return [
            'title' => $title,
            'description' => $description !== '' ? $description : null,
            'start' => $this->formatEpgTime($row['start'] ?? $row['start_timestamp'] ?? null),
            'end' => $this->formatEpgTime($row['end'] ?? $row['stop_timestamp'] ?? null),
        ];
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array{title: string, description: string|null, start: string|null, end: string|null}>
     */
    public function mapEpg(array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            $item = $this->fromEpg($row);
            if ($item !== null) {
                $out[] = $item;
            }
        }

        return $out;
    }

    private function decodeEpgText(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $decoded = base64_decode($value, true);
        if ($decoded !== false && $decoded !== '' && preg_match('//u', $decoded)) {
            // Muchos paneles envían title/description en base64
            return trim($decoded);
        }

        return $value;
    }

    private function formatEpgTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value) && (int) $value > 0) {
            return date('H:i', (int) $value);
        }

        $text = trim((string) $value);
        $parsed = strtotime($text);

        return $parsed ? date('H:i', $parsed) : $text;
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
