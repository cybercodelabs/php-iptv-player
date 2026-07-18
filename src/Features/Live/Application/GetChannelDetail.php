<?php

declare(strict_types=1);

namespace App\Features\Live\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Carga detalle de canal en vivo para /channel?stream=.
 */
final class GetChannelDetail
{
    private const RELATED_LIMIT = 12;
    private const EPG_LIMIT = 5;

    public function __construct(
        private readonly XtreamClient $client,
        private readonly ChannelDetailMapper $mapper = new ChannelDetailMapper(),
        private readonly LiveCatalogMapper $catalogMapper = new LiveCatalogMapper(),
    ) {
    }

    /**
     * @return array{
     *   channel: array<string, mixed>|null,
     *   playUrl: string|null,
     *   epg: list<array<string, mixed>>,
     *   related: list<array<string, mixed>>,
     *   error: string|null
     * }
     */
    public function execute(string $username, string $password, ?string $streamId): array
    {
        $id = $this->normalizeId($streamId);
        if ($id === null) {
            return [
                'channel' => null,
                'playUrl' => null,
                'epg' => [],
                'related' => [],
                'error' => 'Identificador de canal no válido.',
            ];
        }

        try {
            $categoryNames = $this->categoryMap($username, $password);
            $streams = $this->client->getLiveStreams($username, $password, null);
            $raw = $this->findStream($streams, $id);

            if ($raw === null) {
                return [
                    'channel' => null,
                    'playUrl' => null,
                    'epg' => [],
                    'related' => [],
                    'error' => 'No se encontró el canal.',
                ];
            }

            $channel = $this->mapper->fromStream($raw, $categoryNames);
            if ($channel === null) {
                return [
                    'channel' => null,
                    'playUrl' => null,
                    'epg' => [],
                    'related' => [],
                    'error' => 'No se encontró el canal.',
                ];
            }

            $epg = $this->loadEpg($username, $password, $id);
            $related = $this->relatedChannels($streams, $id, $channel['categoryId'] ?? null);

            return [
                'channel' => $channel,
                'playUrl' => url('stream/live') . '?stream=' . rawurlencode($id),
                'epg' => $epg,
                'related' => $related,
                'error' => null,
            ];
        } catch (Throwable $e) {
            return [
                'channel' => null,
                'playUrl' => null,
                'epg' => [],
                'related' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @return array<string, string>
     */
    private function categoryMap(string $username, string $password): array
    {
        try {
            $rows = $this->client->getLiveCategories($username, $password);
        } catch (Throwable) {
            return [];
        }

        $map = [];
        foreach ($this->catalogMapper->mapCategories($rows) as $category) {
            $map[$category['id']] = $category['name'];
        }

        return $map;
    }

    /**
     * @param list<array<string, mixed>> $streams
     * @return array<string, mixed>|null
     */
    private function findStream(array $streams, string $id): ?array
    {
        foreach ($streams as $row) {
            if ((string) ($row['stream_id'] ?? '') === $id) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @return list<array{title: string, description: string|null, start: string|null, end: string|null}>
     */
    private function loadEpg(string $username, string $password, string $id): array
    {
        try {
            $rows = $this->client->getShortEpg($username, $password, $id, self::EPG_LIMIT);
        } catch (Throwable) {
            return [];
        }

        return $this->mapper->mapEpg($rows);
    }

    /**
     * Canales relacionados: misma categoría primero; si faltan, aleatorios del resto.
     *
     * @param list<array<string, mixed>> $streams
     * @return list<array{id: string, name: string, logo: string|null, categoryId: string|null, href: string}>
     */
    private function relatedChannels(array $streams, string $currentId, ?string $categoryId): array
    {
        $same = [];
        $other = [];

        foreach ($streams as $row) {
            $item = $this->catalogMapper->fromChannel($row);
            if ($item === null || $item['id'] === $currentId) {
                continue;
            }

            if ($categoryId !== null && $item['categoryId'] === $categoryId) {
                $same[] = $item;
            } else {
                $other[] = $item;
            }
        }

        shuffle($same);
        shuffle($other);

        $out = [];
        foreach (array_merge($same, $other) as $item) {
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
