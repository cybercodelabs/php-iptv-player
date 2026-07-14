<?php
$username = \App\Infrastructure\Session\Session::username();
$appName = \App\Infrastructure\Config\Config::appName();
?>
<header class="app-header">
    <div class="container app-header__inner">
        <a class="app-brand" href="<?= e(url('home')) ?>">
            <img class="app-brand__logo" src="<?= e(asset('img/favicon.ico')) ?>" alt="" width="32" height="32">
            <span><?= e($appName) ?></span>
        </a>

        <div class="app-header__actions">
            <button
                type="button"
                class="app-header__icon"
                id="openSearchModal"
                title="Buscar"
                aria-label="Buscar películas y series"
                aria-haspopup="dialog"
                aria-controls="searchModal"
                data-catalog-url="<?= e(url('api/search/catalog')) ?>"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
                </svg>
            </button>
            <a
                class="app-header__icon"
                href="<?= e(url('profile')) ?>"
                title="<?= e($username ?? 'Cuenta') ?>"
                aria-label="Mi cuenta"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </a>
        </div>
    </div>
</header>
