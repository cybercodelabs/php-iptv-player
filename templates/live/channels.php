<?php
/**
 * @var list<array{id: string, name: string}> $categories
 * @var list<array{id: string, name: string, logo: string|null, categoryId: string|null, href: string}> $channels
 * @var string $activeCategory
 * @var string|null $catalogError
 */

$categories = $categories ?? [];
$channels = $channels ?? [];
$activeCategory = $activeCategory ?? 'all';
$catalogError = $catalogError ?? null;
$channelCount = count($channels);
$categoryCount = count($categories);

$activeLabel = 'Todos los canales';
if ($activeCategory !== 'all') {
    foreach ($categories as $category) {
        if ($category['id'] === $activeCategory) {
            $activeLabel = $category['name'];
            break;
        }
    }
}

$categoryUrl = static function (string $id): string {
    if ($id === 'all') {
        return url('channels');
    }

    return url('channels') . '?category=' . rawurlencode($id);
};
?>

<section class="tv-page">
    <div class="tv-stage" aria-hidden="true">
        <div class="tv-stage__glow tv-stage__glow--a"></div>
        <div class="tv-stage__glow tv-stage__glow--b"></div>
        <div class="tv-stage__grid"></div>
    </div>

    <div class="container tv-page__inner">
        <header class="tv-masthead">
            <div class="tv-masthead__copy">
                <div class="tv-masthead__topline">
                    <span class="tv-live-pill">
                        <span class="tv-live-pill__dot" aria-hidden="true"></span>
                        En vivo
                    </span>
                    <span class="tv-masthead__meta">
                        <?= e((string) $channelCount) ?> canales
                        <?php if ($categoryCount > 0): ?>
                            · <?= e((string) $categoryCount) ?> categorías
                        <?php endif; ?>
                    </span>
                </div>
                <h1 class="tv-masthead__title">Guía de canales</h1>
                <p class="tv-masthead__subtitle">
                    Explora por categoría y entra al instante.
                </p>
            </div>
            <div class="tv-masthead__signal" aria-hidden="true">
                <span class="tv-masthead__bars">
                    <i></i><i></i><i></i><i></i><i></i>
                </span>
                <span class="tv-masthead__signal-label">Señal IPTV</span>
            </div>
        </header>

        <?php if ($catalogError !== null): ?>
            <div class="tv-empty" role="alert">
                <strong>No se pudo cargar el catálogo en vivo.</strong>
                <?= e(\App\Infrastructure\Config\Config::get('APP_DEBUG') === 'true' ? $catalogError : 'Intenta recargar en unos segundos.') ?>
            </div>
        <?php else: ?>
            <div class="tv-shell">
                <aside class="tv-rail" data-live-categories>
                    <p class="tv-rail__label">Categorías</p>
                    <nav class="tv-rail__nav" aria-label="Categorías de TV">
                        <a
                            class="tv-rail__item<?= $activeCategory === 'all' ? ' is-active' : '' ?>"
                            href="<?= e($categoryUrl('all')) ?>"
                            aria-current="<?= $activeCategory === 'all' ? 'page' : 'false' ?>"
                        >
                            <span class="tv-rail__name">Todos</span>
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a
                                class="tv-rail__item<?= $activeCategory === $category['id'] ? ' is-active' : '' ?>"
                                href="<?= e($categoryUrl($category['id'])) ?>"
                                aria-current="<?= $activeCategory === $category['id'] ? 'page' : 'false' ?>"
                            >
                                <span class="tv-rail__name"><?= e($category['name']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </aside>

                <div class="tv-main">
                    <div class="tv-main__head">
                        <h2 class="tv-main__title"><?= e($activeLabel) ?></h2>
                        <p class="tv-main__count"><?= e((string) $channelCount) ?> disponibles</p>
                    </div>

                    <?php if ($channels === []): ?>
                        <p class="tv-empty">No hay canales para mostrar en esta categoría.</p>
                    <?php else: ?>
                        <div class="tv-mosaic" role="list">
                            <?php foreach ($channels as $index => $channel): ?>
                                <article class="tv-channel" role="listitem" style="--i: <?= (int) ($index % 12) ?>">
                                    <a class="tv-channel__hit" href="<?= e($channel['href']) ?>">
                                        <span class="tv-channel__screen">
                                            <?php if (!empty($channel['logo'])): ?>
                                                <img
                                                    class="tv-channel__logo"
                                                    src="<?= e($channel['logo']) ?>"
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
                                            <span class="tv-channel__name"><?= e($channel['name']) ?></span>
                                            <span class="tv-channel__watch">Ver ahora</span>
                                        </span>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
