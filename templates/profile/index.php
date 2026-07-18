<?php
/**
 * @var array{
 *   username: string,
 *   status: string,
 *   statusLabel: string,
 *   isActive: bool,
 *   expDate: string|null,
 *   createdAt: string|null,
 *   maxConnections: string,
 *   activeConnections: string,
 *   isTrial: bool,
 *   formats: list<string>,
 *   serverTimezone: string|null,
 *   serverUrl: string|null
 * } $profile
 */

$profile = $profile ?? [
    'username' => '',
    'status' => 'unknown',
    'statusLabel' => 'Estado desconocido',
    'isActive' => false,
    'expDate' => null,
    'createdAt' => null,
    'maxConnections' => '—',
    'activeConnections' => '—',
    'isTrial' => false,
    'formats' => [],
    'serverTimezone' => null,
    'serverUrl' => null,
];

$initial = mb_strtoupper(mb_substr((string) $profile['username'], 0, 1));
if ($initial === '') {
    $initial = '?';
}
?>

<section class="profile-page">
    <div class="container profile-page__inner">
        <header class="profile-page__title">
            <h1>Perfil</h1>
        </header>

        <section class="profile-hero">
            <div class="profile-hero__avatar" aria-hidden="true">
                <span><?= e($initial) ?></span>
            </div>

            <div class="profile-hero__info">
                <h2 class="profile-hero__welcome">
                    ¡Bienvenido, <?= e((string) $profile['username']) ?>!
                </h2>
                <p class="profile-hero__user">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 21a8 8 0 10-16 0"/>
                        <circle cx="12" cy="8" r="4"/>
                    </svg>
                    <?= e((string) $profile['username']) ?>
                </p>
                <?php if ($profile['createdAt']): ?>
                    <p class="profile-hero__meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path stroke-linecap="round" d="M3 10h18M8 3v4M16 3v4"/>
                        </svg>
                        Miembro desde: <?= e((string) $profile['createdAt']) ?>
                    </p>
                <?php endif; ?>
                <span class="profile-hero__status<?= $profile['isActive'] ? ' is-active' : ' is-inactive' ?>">
                    <?= e((string) $profile['statusLabel']) ?>
                </span>
            </div>

            <a class="profile-hero__logout" href="<?= e(url('logout')) ?>">
                Cerrar sesión
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3M10 7l5 5-5 5"/>
                    <path stroke-linecap="round" d="M21 4v16"/>
                </svg>
            </a>
        </section>

        <h2 class="profile-section__title">Información de cuenta</h2>

        <div class="profile-grid">
            <article class="profile-card">
                <h3 class="profile-card__title">Datos de acceso</h3>
                <dl class="profile-card__list">
                    <div class="profile-card__row">
                        <dt>Usuario</dt>
                        <dd><?= e((string) $profile['username']) ?></dd>
                    </div>
                    <div class="profile-card__row">
                        <dt>Clave</dt>
                        <dd>••••••</dd>
                    </div>
                    <div class="profile-card__row">
                        <dt>Tipo</dt>
                        <dd><?= $profile['isTrial'] ? 'Prueba' : 'Suscripción' ?></dd>
                    </div>
                    <div class="profile-card__row">
                        <dt>Estado</dt>
                        <dd><?= e((string) $profile['statusLabel']) ?></dd>
                    </div>
                </dl>
            </article>

            <article class="profile-card">
                <h3 class="profile-card__title">Suscripción</h3>
                <dl class="profile-card__list">
                    <div class="profile-card__row">
                        <dt>Expiración</dt>
                        <dd><?= e((string) ($profile['expDate'] ?? 'Sin fecha')) ?></dd>
                    </div>
                    <div class="profile-card__row">
                        <dt>Alta</dt>
                        <dd><?= e((string) ($profile['createdAt'] ?? '—')) ?></dd>
                    </div>
                    <div class="profile-card__row">
                        <dt>Conexiones</dt>
                        <dd>
                            <?= e((string) $profile['activeConnections']) ?>
                            /
                            <?= e((string) $profile['maxConnections']) ?>
                        </dd>
                    </div>
                    <div class="profile-card__row">
                        <dt>Formatos</dt>
                        <dd>
                            <?php if ($profile['formats'] === []): ?>
                                —
                            <?php else: ?>
                                <?= e(implode(' · ', $profile['formats'])) ?>
                            <?php endif; ?>
                        </dd>
                    </div>
                </dl>
            </article>

            <article class="profile-card">
                <h3 class="profile-card__title">Servidor</h3>
                <dl class="profile-card__list">
                    <div class="profile-card__row">
                        <dt>Zona horaria</dt>
                        <dd><?= e((string) ($profile['serverTimezone'] ?? '—')) ?></dd>
                    </div>
                    <div class="profile-card__row">
                        <dt>URL</dt>
                        <dd class="profile-card__mono"><?= e((string) ($profile['serverUrl'] ?? '—')) ?></dd>
                    </div>
                    <div class="profile-card__row">
                        <dt>Accesos rápidos</dt>
                        <dd class="profile-card__links">
                            <a href="<?= e(url('movies')) ?>">Películas</a>
                            <a href="<?= e(url('series')) ?>">Series</a>
                            <a href="<?= e(url('channels')) ?>">TV en vivo</a>
                            <a href="<?= e(url('backgrounds')) ?>">Fondos</a>
                        </dd>
                    </div>
                </dl>
            </article>
        </div>
    </div>
</section>
