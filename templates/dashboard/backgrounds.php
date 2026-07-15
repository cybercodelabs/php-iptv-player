<?php
/**
 * @var string $currentBackground
 * @var list<array{id: string, name: string, hint: string}> $backgrounds
 */

$currentBackground = $currentBackground ?? '1';
$backgrounds = $backgrounds ?? [];
?>

<section class="bg-gallery">
    <div class="container bg-gallery__inner">
        <header class="bg-gallery__intro">
            <p class="bg-gallery__eyebrow">Configuración</p>
            <h1 class="bg-gallery__title">Fondos de inicio</h1>
            <p class="bg-gallery__lead">
                Elige un número y ponlo en <code>.env</code> como
                <code>HOME_BACKGROUND=<?= e($currentBackground) ?></code>.
                Activo ahora: <strong><?= e($currentBackground) ?></strong>.
            </p>
        </header>

        <ul class="bg-gallery__grid">
            <?php foreach ($backgrounds as $bg): ?>
                <?php
                $id = (string) $bg['id'];
                $isActive = $id === $currentBackground;
                ?>
                <li class="bg-gallery__item<?= $isActive ? ' is-active' : '' ?>">
                    <div class="bg-gallery__swatch" aria-hidden="true">
                        <div class="app-atmosphere app-atmosphere--<?= e($id) ?>"></div>
                    </div>
                    <div class="bg-gallery__meta">
                        <span class="bg-gallery__id"><?= e($id) ?></span>
                        <div class="bg-gallery__copy">
                            <h2 class="bg-gallery__name"><?= e($bg['name']) ?></h2>
                            <p class="bg-gallery__hint"><?= e($bg['hint']) ?></p>
                            <?php if ($isActive): ?>
                                <p class="bg-gallery__badge">En uso</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="bg-gallery__env"><code>HOME_BACKGROUND=<?= e($id) ?></code></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
