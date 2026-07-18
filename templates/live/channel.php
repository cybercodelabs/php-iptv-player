<?php
/**
 * @var array{
 *   id: string,
 *   name: string,
 *   logo: string|null,
 *   categoryId: string|null,
 *   categoryName: string|null
 * }|null $channel
 * @var string|null $playUrl
 * @var list<array{title: string, description: string|null, start: string|null, end: string|null}> $epg
 * @var list<array{id: string, name: string, logo: string|null, categoryId: string|null, href: string}> $related
 * @var string|null $detailError
 */

$channel = $channel ?? null;
$playUrl = $playUrl ?? null;
$epg = $epg ?? [];
$related = $related ?? [];
$detailError = $detailError ?? null;
?>

<section class="channel-page">
    <div class="container channel-page__inner">
        <?php if ($detailError !== null || $channel === null): ?>
            <div class="channel-page__empty" role="alert">
                <strong>No se pudo abrir el canal.</strong>
                <?= e(
                    \App\Infrastructure\Config\Config::get('APP_DEBUG') === 'true' && $detailError
                        ? $detailError
                        : 'Comprueba el enlace o vuelve a la guía.'
                ) ?>
                <p>
                    <a class="channel-page__back" href="<?= e(url('channels')) ?>">← Volver a TV en vivo</a>
                </p>
            </div>
        <?php else: ?>
            <a class="channel-page__back" href="<?= e(url('channels')) ?>">← Volver a TV en vivo</a>

            <header class="channel-page__head">
                <div class="channel-page__logo">
                    <?php if (!empty($channel['logo'])): ?>
                        <img
                            src="<?= e((string) $channel['logo']) ?>"
                            alt=""
                            loading="eager"
                            decoding="async"
                            onerror="this.classList.add('is-broken'); this.nextElementSibling?.classList.add('is-visible');"
                        >
                        <span class="channel-page__logo-fallback" aria-hidden="true"></span>
                    <?php else: ?>
                        <span class="channel-page__logo-fallback is-visible" aria-hidden="true"></span>
                    <?php endif; ?>
                </div>

                <div class="channel-page__info">
                    <div class="channel-page__topline">
                        <span class="channel-page__live">
                            <span class="channel-page__live-dot" aria-hidden="true"></span>
                            En vivo
                        </span>
                        <?php if (!empty($channel['categoryName'])): ?>
                            <span class="channel-page__category"><?= e((string) $channel['categoryName']) ?></span>
                        <?php endif; ?>
                    </div>
                    <h1 class="channel-page__title"><?= e((string) $channel['name']) ?></h1>
                </div>
            </header>

            <?php if ($playUrl): ?>
                <section class="live-player" id="live-player" aria-label="Reproductor en vivo">
                    <video
                        id="live-video"
                        class="live-player__video"
                        playsinline
                        controls
                        autoplay
                        muted
                        <?php if (!empty($channel['logo'])): ?>
                            poster="<?= e((string) $channel['logo']) ?>"
                        <?php endif; ?>
                        data-live-player
                        data-src="<?= e($playUrl) ?>"
                    >
                        Tu navegador no soporta la reproducción de vídeo.
                    </video>
                    <p class="live-player__hint" data-live-hint hidden></p>
                </section>
            <?php endif; ?>

            <?php if ($epg !== []): ?>
                <section class="channel-epg" aria-label="Programación">
                    <h2 class="channel-epg__title">Ahora en antena</h2>
                    <ol class="channel-epg__list">
                        <?php foreach ($epg as $index => $item): ?>
                            <li class="channel-epg__item<?= $index === 0 ? ' is-now' : '' ?>">
                                <div class="channel-epg__time">
                                    <?php
                                    $timeLabel = '';
                                    if (!empty($item['start']) && !empty($item['end'])) {
                                        $timeLabel = (string) $item['start'] . ' – ' . (string) $item['end'];
                                    } elseif (!empty($item['start'])) {
                                        $timeLabel = (string) $item['start'];
                                    } elseif (!empty($item['end'])) {
                                        $timeLabel = (string) $item['end'];
                                    } else {
                                        $timeLabel = '—';
                                    }
                                    ?>
                                    <?= e($timeLabel) ?>
                                </div>
                                <div class="channel-epg__body">
                                    <p class="channel-epg__name"><?= e((string) $item['title']) ?></p>
                                    <?php if (!empty($item['description'])): ?>
                                        <p class="channel-epg__desc"><?= e((string) $item['description']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </section>
            <?php endif; ?>

            <?php if ($related !== []): ?>
                <section class="channel-related" aria-label="Más canales">
                    <h2 class="channel-related__title">También en vivo</h2>
                    <div class="tv-mosaic channel-related__grid" role="list">
                        <?php foreach ($related as $index => $item): ?>
                            <article class="tv-channel" role="listitem" style="--i: <?= (int) ($index % 12) ?>">
                                <a class="tv-channel__hit" href="<?= e($item['href']) ?>">
                                    <span class="tv-channel__screen">
                                        <?php if (!empty($item['logo'])): ?>
                                            <img
                                                class="tv-channel__logo"
                                                src="<?= e((string) $item['logo']) ?>"
                                                alt=""
                                                loading="lazy"
                                                decoding="async"
                                                onerror="this.classList.add('is-broken'); this.nextElementSibling?.classList.add('is-visible');"
                                            >
                                            <span class="tv-channel__fallback" aria-hidden="true"></span>
                                        <?php else: ?>
                                            <span class="tv-channel__fallback is-visible" aria-hidden="true"></span>
                                        <?php endif; ?>
                                        <span class="tv-channel__play" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                                        </span>
                                    </span>
                                    <span class="tv-channel__caption">
                                        <span class="tv-channel__name"><?= e((string) $item['name']) ?></span>
                                        <span class="tv-channel__watch">Ver ahora</span>
                                    </span>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
