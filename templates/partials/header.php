<?php
$username = \App\Infrastructure\Session\Session::username();
$appName = \App\Infrastructure\Config\Config::appName();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isActive = static function (string $needle) use ($path): bool {
    return str_contains($path, $needle);
};
?>
<header class="app-header">
    <div class="container app-header__inner">
        <a class="app-brand" href="<?= e(url('home')) ?>">
            <img class="app-brand__logo" src="<?= e(asset('img/favicon.ico')) ?>" alt="" width="32" height="32">
            <span><?= e($appName) ?></span>
        </a>

        <button
            class="app-icon-btn app-nav-toggle"
            type="button"
            id="navToggle"
            aria-controls="appNav"
            aria-expanded="false"
            aria-label="Abrir menú"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
            </svg>
        </button>

        <ul class="app-nav" id="appNav">
            <li><a class="app-nav__link<?= $isActive('/home') ? ' is-active' : '' ?>" href="<?= e(url('home')) ?>">Inicio</a></li>
            <li><a class="app-nav__link<?= $isActive('/channel') ? ' is-active' : '' ?>" href="<?= e(url('channels')) ?>">TV en vivo</a></li>
            <li><a class="app-nav__link<?= $isActive('/movie') ? ' is-active' : '' ?>" href="<?= e(url('movies')) ?>">Películas</a></li>
            <li><a class="app-nav__link<?= $isActive('/serie') || $isActive('/episode') ? ' is-active' : '' ?>" href="<?= e(url('series')) ?>">Series</a></li>
        </ul>

        <div class="app-header__actions">
            <a class="app-icon-btn" href="<?= e(url('profile')) ?>" title="<?= e($username ?? 'Cuenta') ?>" aria-label="Perfil">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </a>
            <a class="app-icon-btn" href="<?= e(url('logout')) ?>" title="Salir" aria-label="Cerrar sesión">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </a>
        </div>
    </div>
</header>
