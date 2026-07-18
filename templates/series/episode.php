<?php
/**
 * @var array<string, mixed>|null $serie
 * @var array<string, mixed>|null $episode
 * @var string|null $seasonNumber
 * @var string|null $prevHref
 * @var string|null $nextHref
 * @var string|null $playUrl
 * @var string|null $detailError
 */

$serie = $serie ?? null;
$episode = $episode ?? null;
$seasonNumber = $seasonNumber ?? null;
$prevHref = $prevHref ?? null;
$nextHref = $nextHref ?? null;
$playUrl = $playUrl ?? null;
$detailError = $detailError ?? null;

$backdrop = null;
$poster = null;
if (is_array($serie)) {
    $backdrop = $serie['backdrop'] ?? $serie['poster'] ?? null;
    $poster = $serie['poster'] ?? null;
}
if (is_array($episode) && !empty($episode['image'])) {
    $backdrop = $episode['image'];
}

$serieHref = is_array($serie)
    ? url('serie') . '?series=' . rawurlencode((string) $serie['id'])
    : url('series');

$heading = 'Episodio';
if (is_array($serie) && is_array($episode)) {
    $epNum = (string) ($episode['number'] ?? '');
    $heading = (string) $serie['title']
        . ' · T' . ($seasonNumber ?? '?')
        . 'E' . ($epNum !== '' && $epNum !== '0' ? $epNum : '?')
        . ' — ' . (string) ($episode['title'] ?? 'Episodio');
}
?>

<section class="movie-details episode-page">
    <?php if ($backdrop): ?>
        <div
            class="movie-details__bg"
            style="background-image: url('<?= e((string) $backdrop) ?>')"
            aria-hidden="true"
        ></div>
    <?php else: ?>
        <div class="movie-details__bg movie-details__bg--empty" aria-hidden="true"></div>
    <?php endif; ?>

    <div class="container movie-details__inner">
        <?php if ($detailError !== null || $serie === null || $episode === null): ?>
            <div class="movie-details__empty" role="alert">
                <strong>No se pudo abrir el episodio.</strong>
                <?= e(
                    \App\Infrastructure\Config\Config::get('APP_DEBUG') === 'true' && $detailError
                        ? $detailError
                        : 'Comprueba el enlace o vuelve a la serie.'
                ) ?>
            </div>
        <?php else: ?>
            <a class="episode-page__back" href="<?= e($serieHref) ?>">← Volver a la serie</a>

            <header class="movie-details__head">
                <h1 class="movie-details__title episode-page__title"><?= e($heading) ?></h1>
            </header>

            <div class="movie-details__card episode-page__card">
                <div class="movie-details__poster">
                    <?php
                    $epPoster = $episode['image'] ?? $poster;
                    ?>
                    <?php if ($epPoster): ?>
                        <img
                            src="<?= e((string) $epPoster) ?>"
                            alt=""
                            loading="eager"
                            decoding="async"
                            onerror="this.classList.add('is-broken'); this.nextElementSibling?.classList.add('is-visible');"
                        >
                        <span class="movie-details__poster-fallback" aria-hidden="true"></span>
                    <?php else: ?>
                        <span class="movie-details__poster-fallback is-visible" aria-hidden="true"></span>
                    <?php endif; ?>
                </div>

                <div class="movie-details__content">
                    <ul class="movie-details__meta">
                        <?php if (!empty($episode['duration'])): ?>
                            <li><strong>Duración:</strong> <?= e((string) $episode['duration']) ?></li>
                        <?php endif; ?>
                        <?php if (!empty($serie['director'])): ?>
                            <li><strong>Dirección:</strong> <?= e((string) $serie['director']) ?></li>
                        <?php endif; ?>
                        <?php if (!empty($serie['cast'])): ?>
                            <li><strong>Reparto:</strong> <?= e((string) $serie['cast']) ?></li>
                        <?php endif; ?>
                    </ul>

                    <?php
                    $plot = $episode['plot'] ?? $serie['plot'] ?? null;
                    ?>
                    <?php if ($plot): ?>
                        <p class="movie-details__plot"><?= e((string) $plot) ?></p>
                    <?php endif; ?>

                    <div class="episode-page__nav">
                        <?php if ($prevHref): ?>
                            <a class="episode-page__nav-btn" href="<?= e($prevHref) ?>">← Anterior</a>
                        <?php else: ?>
                            <span class="episode-page__nav-btn is-disabled">← Anterior</span>
                        <?php endif; ?>

                        <?php if ($nextHref): ?>
                            <a class="episode-page__nav-btn episode-page__nav-btn--next" href="<?= e($nextHref) ?>">Siguiente →</a>
                        <?php else: ?>
                            <span class="episode-page__nav-btn episode-page__nav-btn--next is-disabled">Siguiente →</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($playUrl): ?>
                <section class="movie-player" id="movie-player" aria-label="Reproductor">
                    <video
                        id="plyr-video"
                        playsinline
                        controls
                        width="100%"
                        height="450"
                        <?php if ($epPoster ?? $backdrop): ?>
                            poster="<?= e((string) ($epPoster ?? $backdrop)) ?>"
                        <?php endif; ?>
                        data-vod-player
                        data-src="<?= e($playUrl) ?>"
                    >
                        Tu navegador no soporta la reproducción de vídeo.
                    </video>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
