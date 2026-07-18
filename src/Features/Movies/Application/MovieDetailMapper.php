<?php

declare(strict_types=1);

namespace App\Features\Movies\Application;

/**
 * Normaliza la respuesta get_vod_info para la UI de detalle.
 */
final class MovieDetailMapper
{
    /**
     * @param array<string, mixed> $raw
     * @return array{
     *   id: string,
     *   title: string,
     *   plot: string|null,
     *   cast: string|null,
     *   director: string|null,
     *   country: string|null,
     *   categoryId: string|null,
     *   genre: string|null,
     *   genres: list<string>,
     *   year: string|null,
     *   rating: string|null,
     *   duration: string|null,
     *   poster: string|null,
     *   backdrop: string|null,
     *   extension: string,
     *   youtubeTrailer: string|null
     * }|null
     */
    public function fromVodInfo(array $raw, string $fallbackId): ?array
    {
        $info = isset($raw['info']) && is_array($raw['info']) ? $raw['info'] : [];
        $movie = isset($raw['movie_data']) && is_array($raw['movie_data']) ? $raw['movie_data'] : [];

        $id = $movie['stream_id'] ?? $fallbackId;
        if ($id === null || $id === '') {
            return null;
        }

        // Sin movie_data ni info útil = título inexistente / error del panel
        if ($movie === [] && $info === []) {
            return null;
        }

        $title = trim((string) ($movie['name'] ?? $info['name'] ?? 'Película'));
        $title = preg_replace('/\s*\(\d{4}\)\s*$/', '', $title) ?? $title;

        $genreRaw = trim((string) ($info['genre'] ?? ''));
        $genres = $this->splitList($genreRaw);

        $extension = strtolower(trim((string) ($movie['container_extension'] ?? 'mp4')));
        if ($extension === '') {
            $extension = 'mp4';
        }

        return [
            'id' => (string) $id,
            'title' => $title !== '' ? $title : 'Película',
            'plot' => $this->nullableString($info['plot'] ?? $info['description'] ?? null),
            'cast' => $this->nullableString($info['cast'] ?? $info['actors'] ?? null),
            'director' => $this->nullableString($info['director'] ?? null),
            'country' => $this->nullableString($info['country'] ?? null),
            'categoryId' => $this->categoryIdFrom($movie),
            'genre' => $genreRaw !== '' ? $genreRaw : null,
            'genres' => $genres,
            'year' => $this->yearFrom($info, $title),
            'rating' => $this->ratingFrom($info),
            'duration' => $this->durationFrom($info),
            'poster' => $this->image((string) ($info['movie_image'] ?? $info['cover_big'] ?? '')),
            'backdrop' => $this->backdropFrom($info),
            'extension' => $extension,
            'youtubeTrailer' => $this->youtubeId($info['youtube_trailer'] ?? null),
        ];
    }

    /**
     * @param array<string, mixed> $movie
     */
    private function categoryIdFrom(array $movie): ?string
    {
        $id = $movie['category_id'] ?? null;
        if ($id === null || $id === '') {
            return null;
        }

        return (string) $id;
    }

    /**
     * @param array<string, mixed> $info
     */
    private function yearFrom(array $info, string $title): ?string
    {
        foreach (['releasedate', 'releaseDate', 'release_date'] as $key) {
            if (!empty($info[$key]) && preg_match('/(\d{4})/', (string) $info[$key], $m)) {
                return $m[1];
            }
        }

        if (!empty($info['year'])) {
            return (string) $info['year'];
        }

        if (preg_match('/\((\d{4})\)\s*$/', $title, $m)) {
            return $m[1];
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
        if ($duration !== '') {
            return $duration;
        }

        if (!empty($info['duration_secs']) && is_numeric($info['duration_secs'])) {
            $secs = (int) $info['duration_secs'];
            $h = intdiv($secs, 3600);
            $m = intdiv($secs % 3600, 60);
            if ($h > 0) {
                return sprintf('%dh %02dm', $h, $m);
            }

            return sprintf('%d min', max(1, $m));
        }

        return null;
    }

    /**
     * @param array<string, mixed> $info
     */
    private function backdropFrom(array $info): ?string
    {
        $backdrop = $info['backdrop_path'] ?? null;
        if (is_array($backdrop) && $backdrop !== []) {
            $first = (string) reset($backdrop);

            return $this->image($first);
        }

        if (is_string($backdrop)) {
            return $this->image($backdrop);
        }

        return $this->image((string) ($info['movie_image'] ?? ''));
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
