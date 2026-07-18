<?php

declare(strict_types=1);

namespace App\Features\Series\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Resuelve un episodio concreto para la página de reproducción.
 */
final class GetEpisodeDetail
{
    public function __construct(
        private readonly XtreamClient $client,
        private readonly SerieDetailMapper $mapper = new SerieDetailMapper(),
    ) {
    }

    /**
     * @return array{
     *   serie: array<string, mixed>|null,
     *   episode: array<string, mixed>|null,
     *   seasonNumber: string|null,
     *   prevHref: string|null,
     *   nextHref: string|null,
     *   playUrl: string|null,
     *   error: string|null
     * }
     */
    public function execute(
        string $username,
        string $password,
        ?string $seriesId,
        ?string $episodeId,
    ): array {
        $sid = $this->normalizeId($seriesId);
        $eid = $this->normalizeId($episodeId);

        if ($sid === null || $eid === null) {
            return $this->fail('Identificador de episodio no válido.');
        }

        try {
            $raw = $this->client->getSeriesInfo($username, $password, $sid);
            $serie = $this->mapper->fromSeriesInfo($raw, $sid);

            if ($serie === null) {
                return $this->fail('No se encontró la serie.');
            }

            $found = null;
            $seasonNumber = null;
            $flat = [];

            foreach ($serie['seasons'] as $season) {
                foreach ($season['episodes'] as $ep) {
                    $flat[] = [
                        'season' => $season['number'],
                        'episode' => $ep,
                    ];
                    if ($ep['id'] === $eid) {
                        $found = $ep;
                        $seasonNumber = $season['number'];
                    }
                }
            }

            if ($found === null) {
                return $this->fail('No se encontró el episodio.');
            }

            $prevHref = null;
            $nextHref = null;
            foreach ($flat as $index => $row) {
                if ($row['episode']['id'] !== $eid) {
                    continue;
                }
                if ($index > 0) {
                    $prevHref = $flat[$index - 1]['episode']['href'];
                }
                if (isset($flat[$index + 1])) {
                    $nextHref = $flat[$index + 1]['episode']['href'];
                }
                break;
            }

            $playUrl = url('stream/series') . '?series=' . rawurlencode($sid)
                . '&episode=' . rawurlencode($eid);

            return [
                'serie' => $serie,
                'episode' => $found,
                'seasonNumber' => $seasonNumber,
                'prevHref' => $prevHref,
                'nextHref' => $nextHref,
                'playUrl' => $playUrl,
                'error' => null,
            ];
        } catch (Throwable $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * @return array{
     *   serie: null,
     *   episode: null,
     *   seasonNumber: null,
     *   prevHref: null,
     *   nextHref: null,
     *   playUrl: null,
     *   error: string
     * }
     */
    private function fail(string $message): array
    {
        return [
            'serie' => null,
            'episode' => null,
            'seasonNumber' => null,
            'prevHref' => null,
            'nextHref' => null,
            'playUrl' => null,
            'error' => $message,
        ];
    }

    private function normalizeId(?string $value): ?string
    {
        $id = trim((string) $value);
        if ($id === '' || !preg_match('/^\d+$/', $id)) {
            return null;
        }

        return $id;
    }
}
