<?php

declare(strict_types=1);

namespace App\Features\Player;

/**
 * Abstracción del reproductor multimedia (esqueleto).
 * Live: HLS.js · VOD/episodios: Plyr / HTML5.
 */
final class PlayerService
{
    public const MODE_LIVE = 'live';
    public const MODE_VOD = 'vod';

    /**
     * @return array{mode: string, stream_url: string, poster?: string}
     */
    public function build(string $mode, string $streamUrl, ?string $poster = null): array
    {
        return [
            'mode' => $mode,
            'stream_url' => $streamUrl,
            'poster' => $poster,
        ];
    }
}
