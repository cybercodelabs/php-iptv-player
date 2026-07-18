<?php
/**
 * @var array<string, mixed>|null $serie
 * @var string|null $detailError
 */

$serie = $serie ?? null;
$detailError = $detailError ?? null;
$backdrop = is_array($serie) ? ($serie['backdrop'] ?? $serie['poster'] ?? null) : null;
$poster = is_array($serie) ? ($serie['poster'] ?? null) : null;
$seasons = is_array($serie) ? ($serie['seasons'] ?? []) : [];
?>

<section class="movie-details serie-details">
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
        <?php if ($detailError !== null || $serie === null): ?>
            <div class="movie-details__empty" role="alert">
                <strong>No se pudo abrir la serie.</strong>
                <?= e(
                    \App\Infrastructure\Config\Config::get('APP_DEBUG') === 'true' && $detailError
                        ? $detailError
                        : 'Comprueba el enlace o vuelve al catálogo.'
                ) ?>
            </div>
        <?php else: ?>
            <header class="movie-details__head">
                <h1 class="movie-details__title"><?= e((string) $serie['title']) ?></h1>
                <?php if (!empty($serie['genres'])): ?>
                    <ul class="movie-details__genres">
                        <?php foreach ($serie['genres'] as $genre): ?>
                            <li><?= e((string) $genre) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </header>

            <div class="movie-details__card">
                <div class="movie-details__poster">
                    <?php if ($poster): ?>
                        <img
                            src="<?= e((string) $poster) ?>"
                            alt="<?= e((string) $serie['title']) ?>"
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
                        <?php if (!empty($serie['year'])): ?>
                            <span><?= e((string) $serie['year']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($serie['rating'])): ?>
                            <span class="movie-details__star" aria-hidden="true">★</span>
                            <span><?= e((string) $serie['rating']) ?></span>
                        <?php endif; ?>
                    </p>

                    <ul class="movie-details__meta">
                        <li>
                            <strong>Temporadas:</strong> <?= e((string) ($serie['seasonCount'] ?? 0)) ?>
                            · <strong>Capítulos:</strong> <?= e((string) ($serie['episodeCount'] ?? 0)) ?>
                        </li>
                        <?php if (!empty($serie['director'])): ?>
                            <li><strong>Dirección:</strong> <?= e((string) $serie['director']) ?></li>
                        <?php endif; ?>
                        <?php if (!empty($serie['cast'])): ?>
                            <li><strong>Reparto:</strong> <?= e((string) $serie['cast']) ?></li>
                        <?php endif; ?>
                    </ul>

                    <?php if (!empty($serie['plot'])): ?>
                        <p class="movie-details__plot"><?= e((string) $serie['plot']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($serie['youtubeTrailer'])): ?>
                        <div class="movie-details__actions">
                            <button
                                type="button"
                                class="movie-details__btn movie-details__btn--trailer"
                                data-trailer-open
                                data-trailer-id="<?= e((string) $serie['youtubeTrailer']) ?>"
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

            <?php if ($seasons !== []): ?>
                <section class="serie-episodes" data-serie-seasons>
                    <div class="serie-episodes__toolbar">
                        <div class="serie-episodes__tools-left">
                            <label class="serie-episodes__label" for="serie-season-select">Temporada</label>
                            <select class="serie-episodes__select" id="serie-season-select" data-season-select>
                                <?php foreach ($seasons as $index => $season): ?>
                                    <option
                                        value="<?= e((string) $season['number']) ?>"
                                        <?= $index === 0 ? 'selected' : '' ?>
                                    >
                                        <?= e((string) $season['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button
                            type="button"
                            class="serie-episodes__view-btn"
                            data-view-toggle
                            data-view="grid"
                            title="Cambiar vista"
                        >
                            <svg class="icon-grid" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/>
                            </svg>
                            <svg class="icon-list" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/>
                            </svg>
                            <span data-view-label>Cuadrícula</span>
                        </button>
                    </div>

                    <?php foreach ($seasons as $index => $season): ?>
                        <div
                            class="serie-episodes__panel<?= $index === 0 ? ' is-active' : '' ?>"
                            data-season-panel="<?= e((string) $season['number']) ?>"
                            <?= $index === 0 ? '' : 'hidden' ?>
                        >
                            <div class="serie-episodes__grid" data-view="grid" data-episodes-view>
                                <?php foreach ($season['episodes'] as $ep): ?>
                                    <?php
                                    $epNum = (!empty($ep['number']) && $ep['number'] !== '0')
                                        ? 'E' . str_pad((string) $ep['number'], 2, '0', STR_PAD_LEFT)
                                        : '';
                                    $seasonPad = str_pad((string) $season['number'], 2, '0', STR_PAD_LEFT);
                                    $epPad = (!empty($ep['number']) && $ep['number'] !== '0')
                                        ? str_pad((string) $ep['number'], 2, '0', STR_PAD_LEFT)
                                        : '00';
                                    $cardTitle = trim(
                                        ($epNum !== '' ? $epNum . ' - ' : '')
                                        . (string) $serie['title']
                                        . ' - S' . $seasonPad . 'E' . $epPad
                                        . ' - ' . (string) $ep['title']
                                    );
                                    ?>
                                    <article class="ep-card">
                                        <a class="ep-card__media" href="<?= e((string) $ep['href']) ?>">
                                            <?php if (!empty($ep['image'])): ?>
                                                <img
                                                    src="<?= e((string) $ep['image']) ?>"
                                                    alt=""
                                                    loading="lazy"
                                                    decoding="async"
                                                    onerror="this.classList.add('is-broken'); this.nextElementSibling?.classList.add('is-visible');"
                                                >
                                                <span class="ep-card__fallback" aria-hidden="true"></span>
                                            <?php else: ?>
                                                <span class="ep-card__fallback is-visible" aria-hidden="true"></span>
                                            <?php endif; ?>
                                        </a>
                                        <div class="ep-card__body">
                                            <h3 class="ep-card__title">
                                                <a href="<?= e((string) $ep['href']) ?>"><?= e($cardTitle) ?></a>
                                            </h3>
                                            <?php if (!empty($ep['plot'])): ?>
                                                <p class="ep-card__plot"><?= e((string) $ep['plot']) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($ep['duration'])): ?>
                                                <p class="ep-card__meta"><?= e((string) $ep['duration']) ?></p>
                                            <?php endif; ?>
                                            <a class="ep-card__cta" href="<?= e((string) $ep['href']) ?>">
                                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-2 14.5v-9l7 4.5-7 4.5z"/>
                                                </svg>
                                                Ver episodio
                                            </a>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
