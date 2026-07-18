<?php

declare(strict_types=1);

namespace App\Features\Live\Presentation;

use App\Features\Live\Application\GetChannelDetail;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Xtream\XtreamClient;
use App\Shared\Http\AuthMiddleware;
use App\Shared\Layout\View;

/**
 * Detalle y reproducción de un canal en vivo.
 */
final class ChannelController
{
    public function __invoke(): void
    {
        AuthMiddleware::requireAuth();

        $auth = Session::auth();
        $username = (string) ($auth['username'] ?? '');
        $password = (string) ($auth['password'] ?? '');
        $streamId = isset($_GET['stream']) ? (string) $_GET['stream'] : null;

        $detail = (new GetChannelDetail(
            new XtreamClient(Config::xtreamHost())
        ))->execute($username, $password, $streamId);

        $channel = $detail['channel'];
        $title = is_array($channel) ? (string) ($channel['name'] ?? 'Canal') : 'Canal';

        View::render('live/channel', [
            'title' => $title,
            'styles' => [
                'css/live/cards.css',
                'css/live/channel.css',
                'css/player/live.css',
            ],
            'scripts' => [
                'vendor/hls/hls.min.js',
                'js/player/live.js',
            ],
            'channel' => $channel,
            'playUrl' => $detail['playUrl'],
            'epg' => $detail['epg'],
            'related' => $detail['related'],
            'detailError' => $detail['error'],
        ]);
    }
}
