<?php
$username = \App\Infrastructure\Session\Session::username();
?>
<header class="app-header">
    <nav class="navbar container">
        <a class="navbar-brand" href="<?= e(url('home')) ?>"><?= e(\App\Infrastructure\Config\Config::appName()) ?></a>
        <ul class="navbar-nav">
            <li><a class="nav-link" href="<?= e(url('home')) ?>">Inicio</a></li>
            <li><a class="nav-link" href="<?= e(url('channels')) ?>">TV en vivo</a></li>
            <li><a class="nav-link" href="<?= e(url('movies')) ?>">Películas</a></li>
            <li><a class="nav-link" href="<?= e(url('series')) ?>">Series</a></li>
            <li><a class="nav-link" href="<?= e(url('profile')) ?>"><?= e($username ?? 'Cuenta') ?></a></li>
            <li><a class="nav-link" href="<?= e(url('logout')) ?>">Salir</a></li>
        </ul>
    </nav>
</header>
