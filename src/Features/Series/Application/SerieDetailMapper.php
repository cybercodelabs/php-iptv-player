<?php

declare(strict_types=1);

namespace App\Features\Series\Application;

/**
 * Normaliza get_series_info para la ficha de serie.
 */
final class SerieDetailMapper
{
    /**
     * @param array<string, mixed> $raw
     * @return array{
     *   id: string,
     *   title: string,
     *   plot: string|null,
     *   cast: string|null,
     *   director: string|null,
     *   genres: list<string>,
     *   year: string|null,
     *   rating: string|null,
     *   poster: string|null,
     *   backdrop: string|null,
     *   youtubeTrailer: string|null,
     *   seasonCount: int,
     *   episodeCount: int,
     *   seasons: list<array{
     *     number: string,
     *     label: string,
     *     episodes: list<array{
     *       id: string,
     *       number: string,
     *       title: string,
     *       plot: string|null,
     *       duration: string|null,
     *       image: string|null,
     *       extension: string,
     *       href: string
     *     }>
     *   }>
     * }|null
     */
    public function fromSeriesInfo(array $raw, string $seriesId): ?array
    {
        $info = isset($raw['info']) && is_array($raw['info']) ? $raw['info'] : [];
        $episodesRaw = isset($raw['episodes']) && is_array($raw['episodes']) ? $raw['episodes'] : [];

        if ($info === [] && $episodesRaw === []) {
            return null;
        }

        $title = trim((string) ($info['name'] ?? $info['title'] ?? 'Serie'));
        $title = preg_replace('/\s*\(\d{4}\)\s*$/', '', $title) ?? $title;

        $genreRaw = trim((string) ($info['genre'] ?? ''));
        $seasons = $this->mapSeasons($episodesRaw, $seriesId, (string) ($info['cover'] ?? ''));
        $episodeCount = 0;
        foreach ($seasons as $season) {
            $episodeCount += count($season['episodes']);
        }

        return [
            'id' => $seriesId,
            'title' => $title !== '' ? $title : 'Serie',
            'plot' => $this->nullableString($info['plot'] ?? $info['description'] ?? null),
            'cast' => $this->nullableString($info['cast'] ?? $info['actors'] ?? null),
            'director' => $this->nullableString($info['director'] ?? null),
            'genres' => $this->splitList($genreRaw),
            'year' => $this->yearFrom($info),
            'rating' => $this->ratingFrom($info),
            'poster' => $this->image((string) ($info['cover'] ?? $info['movie_image'] ?? '')),
            'backdrop' => $this->backdropFrom($info),
            'youtubeTrailer' => $this->youtubeId($info['youtube_trailer'] ?? null),
            'seasonCount' => count($seasons),
            'episodeCount' => $episodeCount,
            'seasons' => $seasons,
        ];
    }

    /**
     * @param array<string|int, mixed> $episodesRaw
     * @return list<array{
     *   number: string,
     *   label: string,
     *   episodes: list<array{
     *     id: string,
     *     number: string,
     *     title: string,
     *     plot: string|null,
     *     duration: string|null,
     *     image: string|null,
     *     extension: string,
     *     href: string
     *   }>
     * }>
     */
    private function mapSeasons(array $episodesRaw, string $seriesId, string $fallbackCover): array
    {
        $seasons = [];

        foreach ($episodesRaw as $seasonKey => $eps) {
            if (!is_array($eps)) {
                continue;
            }

            $seasonNumber = (string) $seasonKey;
            $mapped = [];

            foreach ($eps as $ep) {
                if (!is_array($ep)) {
                    continue;
                }
                $item = $this->mapEpisode($ep, $seriesId, $seasonNumber, $fallbackCover);
                if ($item !== null) {
                    $mapped[] = $item;
                }
            }

            if ($mapped === []) {
                continue;
            }

            usort($mapped, static fn (array $a, array $b): int => ((int) $a['number']) <=> ((int) $b['number']));

            $seasons[] = [
                'number' => $seasonNumber,
                'label' => 'Temporada ' . $seasonNumber,
                'episodes' => $mapped,
            ];
        }

        usort($seasons, static fn (array $a, array $b): int => ((int) $a['number']) <=> ((int) $b['number']));

        return $seasons;
    }

    /**
     * @param array<string, mixed> $ep
     * @return array{
     *   id: string,
     *   number: string,
     *   title: string,
     *   plot: string|null,
     *   duration: string|null,
     *   image: string|null,
     *   extension: string,
     *   href: string
     * }|null
     */
    private function mapEpisode(array $ep, string $seriesId, string $seasonNumber, string $fallbackCover): ?array
    {
        $id = $ep['id'] ?? null;
        if ($id === null || $id === '') {
            return null;
        }

        $info = isset($ep['info']) && is_array($ep['info']) ? $ep['info'] : [];
        $num = (string) ($ep['episode_num'] ?? $ep['episode'] ?? '');
        $title = trim((string) ($ep['title'] ?? 'Episodio'));
        if ($title === '') {
            $title = 'Episodio' . ($num !== '' ? ' ' . $num : '');
        }

        $extension = strtolower(trim((string) ($ep['container_extension'] ?? 'mp4')));
        if ($extension === '') {
            $extension = 'mp4';
        }

        $image = $this->image((string) ($info['movie_image'] ?? $info['cover'] ?? ''));
        if ($image === null) {
            $image = $this->image($fallbackCover);
        }

        return [
            'id' => (string) $id,
            'number' => $num !== '' ? $num : '0',
            'title' => $this->cleanEpisodeTitle($title),
            'plot' => $this->nullableString($info['plot'] ?? null),
            'duration' => $this->durationFrom($info),
            'image' => $image,
            'extension' => $extension,
            'href' => url('episode') . '?series=' . rawurlencode($seriesId)
                . '&episode=' . rawurlencode((string) $id)
                . '&season=' . rawurlencode($seasonNumber),
        ];
    }

    private function cleanEpisodeTitle(string $title): string
    {
        $title = trim($title);
        if (mb_strlen($title) > 48) {
            return mb_substr($title, 0, 47) . '…';
        }

        return $title;
    }

    /**
     * @param array<string, mixed> $info
     */
    private function yearFrom(array $info): ?string
    {
        foreach (['releaseDate', 'releasedate', 'release_date'] as $key) {
            if (!empty($info[$key]) && preg_match('/(\d{4})/', (string) $info[$key], $m)) {
                return $m[1];
            }
        }

        if (!empty($info['year'])) {
            return (string) $info['year'];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $info
     */
    private function ratingFrom(array $info): ?string
    {
        foreach (['rating', 'rating_5based'] as $key) {
            if (!isset($info[$key]) || $info[$key] === '' || $info[$key] === null) {
                continue;
            }

            return is_numeric($info[$key])
                ? number_format((float) $info[$key], 1)
                : (string) $info[$key];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $info
     */
    private function durationFrom(array $info): ?string
    {
        $duration = trim((string) ($info['duration'] ?? ''));
        if ($duration === '' || in_array($duration, ['0', '00:00', '00:00:00'], true)) {
            return null;
        }

        return $duration;
    }

    /**
     * @param array<string, mixed> $info
     */
    private function backdropFrom(array $info): ?string
    {
        $backdrop = $info['backdrop_path'] ?? null;
        if (is_array($backdrop) && $backdrop !== []) {
            return $this->image((string) reset($backdrop));
        }
        if (is_string($backdrop)) {
            return $this->image($backdrop);
        }

        return $this->image((string) ($info['cover'] ?? ''));
    }

    private function youtubeId(mixed $value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $raw)) {
            return $raw;
        }
        if (preg_match('/(?:v=|youtu\.be\/|embed\/)([A-Za-z0-9_-]{11})/', $raw, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function splitList(string $value): array
    {
        if ($value === '') {
            return [];
        }

        $parts = preg_split('/[,|\/]/', $value) ?: [];
        $out = [];
        foreach ($parts as $part) {
            $item = trim($part);
            if ($item !== '') {
                $out[] = $item;
            }
        }

        return $out;
    }

    private function nullableString(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : null;
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
