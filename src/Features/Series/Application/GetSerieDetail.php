<?php

declare(strict_types=1);

namespace App\Features\Series\Application;

use App\Infrastructure\Xtream\XtreamClient;
use Throwable;

/**
 * Carga ficha de serie + temporadas/episodios.
 */
final class GetSerieDetail
{
    public function __construct(
        private readonly XtreamClient $client,
        private readonly SerieDetailMapper $mapper = new SerieDetailMapper(),
    ) {
    }

    /**
     * @return array{serie: array<string, mixed>|null, error: string|null}
     */
    public function execute(string $username, string $password, ?string $seriesId): array
    {
        $id = $this->normalizeId($seriesId);
        if ($id === null) {
            return [
                'serie' => null,
                'error' => 'Identificador de serie no válido.',
            ];
        }

        try {
            $raw = $this->client->getSeriesInfo($username, $password, $id);
            $serie = $this->mapper->fromSeriesInfo($raw, $id);

            if ($serie === null) {
                return [
                    'serie' => null,
                    'error' => 'No se encontró la serie.',
                ];
            }

            return [
                'serie' => $serie,
                'error' => null,
            ];
        } catch (Throwable $e) {
            return [
                'serie' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function normalizeId(?string $seriesId): ?string
    {
        $value = trim((string) $seriesId);
        if ($value === '' || !preg_match('/^\d+$/', $value)) {
            return null;
        }

        return $value;
    }
}
