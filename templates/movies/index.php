<?php
/**
 * @var list<array{id: string, name: string}> $categories
 * @var list<array{
 *   id: string,
 *   title: string,
 *   year: string|null,
 *   rating: string|null,
 *   meta: string,
 *   href: string,
 *   image: string|null,
 *   type: string
 * }> $movies
 * @var string $activeCategory
 * @var int $page
 * @var int $totalPages
 * @var int $totalMovies
 * @var int $pageSize
 * @var string|null $catalogError
 */

$categories = $categories ?? [];
$movies = $movies ?? [];
$activeCategory = $activeCategory ?? 'all';
$page = max(1, (int) ($page ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$totalMovies = (int) ($totalMovies ?? 0);
$pageSize = (int) ($pageSize ?? 20);
$catalogError = $catalogError ?? null;
$categoryCount = count($categories);
$shownFrom = $totalMovies === 0 ? 0 : (($page - 1) * $pageSize) + 1;
$shownTo = min($page * $pageSize, $totalMovies);

$activeLabel = 'Todas';
if ($activeCategory !== 'all') {
    foreach ($categories as $category) {
        if ($category['id'] === $activeCategory) {
            $activeLabel = $category['name'];
            break;
        }
    }
}

$moviesUrl = static function (string $categoryId, int $pageNum = 1): string {
    $params = [];
    if ($categoryId !== 'all') {
        $params['category'] = $categoryId;
    }
    if ($pageNum > 1) {
        $params['page'] = (string) $pageNum;
    }

    $base = url('movies');
    if ($params === []) {
        return $base;
    }

    return $base . '?' . http_build_query($params);
};
?>

<section class="vod-page">
    <div class="vod-ambient" aria-hidden="true">
        <span class="vod-ambient__orb vod-ambient__orb--a"></span>
        <span class="vod-ambient__orb vod-ambient__orb--b"></span>
        <span class="vod-ambient__noise"></span>
    </div>

    <div class="container vod-page__inner">
        <header class="vod-hero">
            <div class="vod-hero__text">
                <p class="vod-hero__kicker">Biblioteca VOD</p>
                <h1 class="vod-hero__title">Películas</h1>
                <p class="vod-hero__lead">
                    <?= e((string) $totalMovies) ?> títulos
                    <?php if ($categoryCount > 0): ?>
                        · <?= e((string) $categoryCount) ?> géneros
                    <?php endif; ?>
                </p>
            </div>
        </header>

        <?php if ($catalogError !== null): ?>
            <div class="vod-empty" role="alert">
                <strong>No se pudo cargar el catálogo.</strong>
                <?= e(\App\Infrastructure\Config\Config::get('APP_DEBUG') === 'true' ? $catalogError : 'Intenta recargar en unos segundos.') ?>
            </div>
        <?php else: ?>
            <div class="vod-filters" data-vod-categories>
                <div class="vod-filters__bar">
                    <p class="vod-filters__label">Género</p>
                    <nav class="vod-filters__nav" aria-label="Géneros de películas">
                        <a
                            class="vod-chip<?= $activeCategory === 'all' ? ' is-active' : '' ?>"
                            href="<?= e($moviesUrl('all')) ?>"
                            aria-current="<?= $activeCategory === 'all' ? 'page' : 'false' ?>"
                        >Todas</a>
                        <?php foreach ($categories as $category): ?>
                            <a
                                class="vod-chip<?= $activeCategory === $category['id'] ? ' is-active' : '' ?>"
                                href="<?= e($moviesUrl($category['id'])) ?>"
                                aria-current="<?= $activeCategory === $category['id'] ? 'page' : 'false' ?>"
                            ><?= e($category['name']) ?></a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </div>

            <div class="vod-board">
                <div class="vod-board__toolbar">
                    <div class="vod-board__heading">
                        <h2 class="vod-board__title"><?= e($activeLabel) ?></h2>
                        <p class="vod-board__range">
                            <?php if ($totalMovies === 0): ?>
                                Sin resultados
                            <?php else: ?>
                                <?= e((string) $shownFrom) ?>–<?= e((string) $shownTo) ?>
                                de <?= e((string) $totalMovies) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php if ($totalPages > 1): ?>
                        <p class="vod-board__page">
                            Página <?= e((string) $page) ?> / <?= e((string) $totalPages) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if ($movies === []): ?>
                    <p class="vod-empty">No hay películas en este género.</p>
                <?php else: ?>
                    <div class="vod-grid" role="list">
                        <?php foreach ($movies as $index => $movie): ?>
                            <div class="vod-grid__cell" role="listitem" style="--i: <?= (int) $index ?>">
                                <?php
                                $item = $movie;
                                $big = false;
                                require templates_path('partials/catalog-card.php');
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <nav class="vod-pager" aria-label="Paginación de películas">
                            <?php if ($page > 1): ?>
                                <a class="vod-pager__btn" href="<?= e($moviesUrl($activeCategory, $page - 1)) ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
                                    </svg>
                                    Anterior
                                </a>
                            <?php else: ?>
                                <span class="vod-pager__btn is-disabled" aria-disabled="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
                                    </svg>
                                    Anterior
                                </span>
                            <?php endif; ?>

                            <div class="vod-pager__pages" aria-hidden="true">
                                <?php
                                $window = 2;
                                $start = max(1, $page - $window);
                                $end = min($totalPages, $page + $window);
                                for ($p = $start; $p <= $end; $p++):
                                ?>
                                    <a
                                        class="vod-pager__num<?= $p === $page ? ' is-active' : '' ?>"
                                        href="<?= e($moviesUrl($activeCategory, $p)) ?>"
                                        <?= $p === $page ? 'aria-current="page"' : '' ?>
                                    ><?= e((string) $p) ?></a>
                                <?php endfor; ?>
                            </div>

                            <?php if ($page < $totalPages): ?>
                                <a class="vod-pager__btn vod-pager__btn--next" href="<?= e($moviesUrl($activeCategory, $page + 1)) ?>">
                                    Siguiente
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                                    </svg>
                                </a>
                            <?php else: ?>
                                <span class="vod-pager__btn vod-pager__btn--next is-disabled" aria-disabled="true">
                                    Siguiente
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
                                    </svg>
                                </span>
                            <?php endif; ?>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
