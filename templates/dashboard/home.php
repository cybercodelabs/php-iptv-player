<?php
/**
 * @var string|null $username
 * @var list<array<string, mixed>> $popular
 * @var list<array<string, mixed>> $movies
 * @var list<array<string, mixed>> $series
 * @var list<array<string, mixed>> $premieres
 * @var list<array<string, mixed>> $recent
 * @var string|null $catalogError
 */

$popular = $popular ?? [];
$movies = $movies ?? [];
$series = $series ?? [];
$premieres = $premieres ?? [];
$recent = $recent ?? [];
$catalogError = $catalogError ?? null;

$renderGrid = static function (array $items, string $emptyMessage): void {
    if ($items === []) {
        echo '<p class="home-empty">' . e($emptyMessage) . '</p>';
        return;
    }
    echo '<div class="home-grid">';
    foreach ($items as $item) {
        $big = false;
        require templates_path('partials/catalog-card.php');
    }
    echo '</div>';
};
?>

<section class="home-hero">
    <div class="home-hero__glow" aria-hidden="true"></div>
    <div class="container home-hero__inner">
        <div class="home-hero__copy">
            <p class="home-hero__eyebrow">Inicio</p>
            <h1 class="home-hero__title">Hola, <span><?= e($username ?? 'usuario') ?></span></h1>
            <p class="home-hero__subtitle">
                Lo más reciente de tu catálogo IPTV: canales, películas y series desde Xtream.
            </p>
        </div>

        <?php if (!empty($catalogError)): ?>
            <div class="home-empty" role="alert">
                <strong>No se pudo cargar el catálogo.</strong>
                <?= e(\App\Infrastructure\Config\Config::get('APP_DEBUG') === 'true' ? $catalogError : 'Intenta recargar en unos segundos.') ?>
            </div>
        <?php endif; ?>

        <nav class="home-shortcuts" aria-label="Accesos rápidos">
            <a class="home-shortcut" href="<?= e(url('channels')) ?>">
                <span class="home-shortcut__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 6h8a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                    </svg>
                </span>
                <span class="home-shortcut__body">
                    <span class="home-shortcut__title">TV en vivo</span>
                    <span class="home-shortcut__text">Categorías y canales</span>
                </span>
            </a>
            <a class="home-shortcut" href="<?= e(url('movies')) ?>">
                <span class="home-shortcut__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z"/>
                    </svg>
                </span>
                <span class="home-shortcut__body">
                    <span class="home-shortcut__title">Películas</span>
                    <span class="home-shortcut__text">Catálogo VOD</span>
                </span>
            </a>
            <a class="home-shortcut" href="<?= e(url('series')) ?>">
                <span class="home-shortcut__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </span>
                <span class="home-shortcut__body">
                    <span class="home-shortcut__title">Series</span>
                    <span class="home-shortcut__text">Temporadas y episodios</span>
                </span>
            </a>
            <a class="home-shortcut" href="<?= e(url('profile')) ?>">
                <span class="home-shortcut__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <span class="home-shortcut__body">
                    <span class="home-shortcut__title">Mi cuenta</span>
                    <span class="home-shortcut__text">Suscripción</span>
                </span>
            </a>
        </nav>
    </div>
</section>

<section class="home-popular">
    <div class="container" data-rail-wrap>
        <div class="home-popular__head">
            <h2 class="home-popular__title">Populares <b>ahora</b></h2>
            <?php if ($popular !== []): ?>
                <div class="home-popular__nav">
                    <button type="button" class="home-popular__nav-btn" data-rail-prev aria-label="Anterior">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button type="button" class="home-popular__nav-btn" data-rail-next aria-label="Siguiente">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($popular === []): ?>
            <p class="home-empty">Aún no hay títulos populares para mostrar.</p>
        <?php else: ?>
            <div class="home-popular__rail" data-rail>
                <div class="home-popular__track" data-rail-track role="list">
                    <?php foreach ($popular as $item): ?>
                        <?php
                        $big = true;
                        require templates_path('partials/catalog-card.php');
                        ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="home-content" data-home-tabs>
    <div class="container">
        <h2 class="home-content__title">Recomendaciones</h2>

        <div class="home-content__tabs" role="tablist" aria-label="Recomendaciones">
            <button type="button" class="home-content__tab is-active" role="tab" aria-selected="true" data-tab="movies">Películas</button>
            <button type="button" class="home-content__tab" role="tab" aria-selected="false" data-tab="series">Series</button>
            <button type="button" class="home-content__tab" role="tab" aria-selected="false" data-tab="premieres">Estrenos</button>
            <button type="button" class="home-content__tab" role="tab" aria-selected="false" data-tab="recent">Recién agregados</button>
        </div>

        <div class="home-content__panel" data-panel="movies" role="tabpanel">
            <?php $renderGrid($movies, 'No se encontraron películas en el catálogo.'); ?>
        </div>
        <div class="home-content__panel" data-panel="series" role="tabpanel" hidden>
            <?php $renderGrid($series, 'No se encontraron series en el catálogo.'); ?>
        </div>
        <div class="home-content__panel" data-panel="premieres" role="tabpanel" hidden>
            <?php $renderGrid($premieres, 'No hay estrenos para mostrar.'); ?>
        </div>
        <div class="home-content__panel" data-panel="recent" role="tabpanel" hidden>
            <?php $renderGrid($recent, 'No hay títulos recién agregados.'); ?>
        </div>
    </div>
</section>
