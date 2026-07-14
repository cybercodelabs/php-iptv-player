<?php
/**
 * Partial de tarjeta de catálogo (estilo PLAYGO).
 *
 * @var array{title: string, year?: string|null, rating?: string|null, meta?: string, href: string, image: string|null, type: string} $item
 * @var bool $big
 */
$big = $big ?? false;
$year = $item['year'] ?? null;
$rating = $item['rating'] ?? null;
$meta = $item['meta'] ?? '';
$hasImage = !empty($item['image']);
?>
<article class="catalog-card<?= $big ? ' catalog-card--big' : '' ?>">
    <a class="catalog-card__cover" href="<?= e($item['href']) ?>">
        <?php if ($hasImage): ?>
            <img
                class="catalog-card__img"
                src="<?= e($item['image']) ?>"
                alt="<?= e($item['title']) ?>"
                loading="lazy"
                decoding="async"
                onerror="this.classList.add('is-broken'); this.nextElementSibling?.classList.add('is-visible');"
            >
            <span class="catalog-card__fallback" aria-hidden="true"></span>
        <?php else: ?>
            <span class="catalog-card__fallback is-visible" aria-hidden="true"></span>
        <?php endif; ?>
        <span class="catalog-card__play" aria-hidden="true">
            <span class="catalog-card__play-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
            </span>
        </span>
    </a>
    <div class="catalog-card__content">
        <h3 class="catalog-card__title">
            <a href="<?= e($item['href']) ?>"><?= e($item['title']) ?></a>
        </h3>
        <span class="catalog-card__rate">
            <?php if ($year !== null || $rating !== null): ?>
                <?= e($year ?? 'N/A') ?>
                <?php if ($rating !== null): ?>
                    &nbsp;<span class="catalog-card__star" aria-hidden="true">★</span> <?= e($rating) ?>
                <?php endif; ?>
            <?php else: ?>
                <?= e($meta !== '' ? $meta : 'Catálogo') ?>
            <?php endif; ?>
        </span>
    </div>
</article>
