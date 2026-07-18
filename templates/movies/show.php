<?php
/**
 * @var array{
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
 * }|null $movie
 * @var string|null $playUrl
 * @var list<array<string, mixed>> $related
 * @var string|null $detailError
 */

$movie = $movie ?? null;
$playUrl = $playUrl ?? null;
$related = $related ?? [];
$detailError = $detailError ?? null;
$backdrop = $movie['backdrop'] ?? $movie['poster'] ?? null;
$poster = $movie['poster'] ?? null;
?>

<section class="movie-details">
    <?php if ($backdrop): ?>
        <div
            class="movie-details__bg"
            style="background-image: url('<?= e($backdrop) ?>')"
            aria-hidden="true"
        ></div>
    <?php else: ?>
        <div class="movie-details__bg movie-details__bg--empty" aria-hidden="true"></div>
    <?php endif; ?>

    <div class="container movie-details__inner">
        <?php if ($detailError !== null || $movie === null): ?>
            <div class="movie-details__empty" role="alert">
                <strong>No se pudo abrir la película.</strong>
                <?= e(
                    \App\Infrastructure\Config\Config::get('APP_DEBUG') === 'true' && $detailError
                        ? $detailError
                        : 'Comprueba el enlace o vuelve al catálogo.'
                ) ?>
            </div>
        <?php else: ?>
            <header class="movie-details__head">
                <h1 class="movie-details__title"><?= e($movie['title']) ?></h1>
                <?php if ($movie['genres'] !== []): ?>
                    <ul class="movie-details__genres">
                        <?php foreach ($movie['genres'] as $genre): ?>
                            <li><?= e($genre) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </header>

            <div class="movie-details__card">
                <div class="movie-details__poster">
                    <?php if ($poster): ?>
                        <img
                            src="<?= e($poster) ?>"
                            alt="<?= e($movie['title']) ?>"
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
                    <p class="movie-details__rate">
                        <?php if ($movie['year']): ?>
                            <span><?= e($movie['year']) ?></span>
                        <?php endif; ?>
                        <?php if ($movie['rating']): ?>
                            <span class="movie-details__star" aria-hidden="true">★</span>
                            <span><?= e($movie['rating']) ?></span>
                        <?php endif; ?>
                    </p>

                    <ul class="movie-details__meta">
                        <?php if ($movie['duration']): ?>
                            <li><strong>Duración:</strong> <?= e($movie['duration']) ?></li>
                        <?php endif; ?>
                        <?php if ($movie['country']): ?>
                            <li><strong>País:</strong> <?= e($movie['country']) ?></li>
                        <?php endif; ?>
                        <?php if ($movie['director']): ?>
                            <li><strong>Dirección:</strong> <?= e($movie['director']) ?></li>
                        <?php endif; ?>
                        <?php if ($movie['cast']): ?>
                            <li><strong>Reparto:</strong> <?= e($movie['cast']) ?></li>
                        <?php endif; ?>
                    </ul>

                    <?php if ($movie['plot']): ?>
                        <p class="movie-details__plot"><?= e($movie['plot']) ?></p>
                    <?php endif; ?>

                    <?php if ($movie['youtubeTrailer']): ?>
                        <div class="movie-details__actions">
                            <button
                                type="button"
                                class="movie-details__btn movie-details__btn--trailer"
                                data-trailer-open
                                data-trailer-id="<?= e($movie['youtubeTrailer']) ?>"
                            >
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M23.5 6.2a3 3 0 00-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 00.5 6.2 31.5 31.5 0 000 12a31.5 31.5 0 00.5 5.8 3 3 0 002.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 002.1-2.1A31.5 31.5 0 0024 12a31.5 31.5 0 00-.5-5.8zM9.8 15.5v-7l6.2 3.5-6.2 3.5z"/>
                                </svg>
                                Tráiler
                            </button>
                        </div>
                    <?php endif; ?>
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
                        <?php if ($backdrop || $poster): ?>
                            poster="<?= e($backdrop ?: $poster) ?>"
                        <?php endif; ?>
                        data-vod-player
                        data-src="<?= e($playUrl) ?>"
                    >
                        Tu navegador no soporta la reproducción de vídeo.
                    </video>
                </section>
            <?php endif; ?>

            <?php if ($related !== []): ?>
                <section class="movie-related">
                    <h2 class="movie-related__title">Usuarios también vieron</h2>
                    <div class="movie-related__grid">
                        <?php foreach ($related as $item): ?>
                            <?php
                            $big = false;
                            require templates_path('partials/catalog-card.php');
                            ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<div
    class="trailer-modal"
    data-trailer-modal
    hidden
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-label="Tráiler"
>
    <div class="trailer-modal__backdrop" data-trailer-backdrop></div>
    <div class="trailer-modal__dialog" role="document">
        <button type="button" class="trailer-modal__close" data-trailer-close aria-label="Cerrar tráiler">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>
        <div class="trailer-modal__media">
            <iframe
                data-trailer-iframe
                title="Tráiler"
                allow="autoplay; encrypted-media; picture-in-picture"
                allowfullscreen
            ></iframe>
        </div>
    </div>
</div>
