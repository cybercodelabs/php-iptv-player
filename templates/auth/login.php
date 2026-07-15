<?php
/** @var string|null $error */
/** @var string|null $errorTitle */
/** @var string $appName */
$year = date('Y');
$errorTitle = $errorTitle ?? 'Ocurrió un error';
?>
<div class="auth-page">
    <div class="overlay"></div>

    <div class="auth-wrapper">
        <main class="auth-card" aria-labelledby="auth-title">
            <header class="card-header">
                <div class="logo-container">
                    <div class="logo-wordmark" aria-label="<?= e($appName) ?>">
                        <img
                            class="logo-favicon"
                            src="<?= e(asset('img/favicon.ico')) ?>"
                            alt="<?= e($appName) ?>"
                            width="64"
                            height="64"
                        />
                        <span class="logo-text"><?= e($appName) ?></span>
                    </div>
                </div>
                <h2 id="auth-title">Bienvenido de vuelta</h2>
                <p class="card-subtitle">Inicia sesión para continuar</p>
            </header>

            <?php if (!empty($error)): ?>
                <?php
                $showCredentialHint = !in_array((string) $errorTitle, ['Sin conexión', 'Cuenta no disponible', 'Suscripción vencida'], true);
                ?>
                <div class="login-alert" role="alert" aria-live="assertive">
                    <div class="login-alert__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div class="login-alert__body">
                        <p class="login-alert__title"><?= e($errorTitle) ?></p>
                        <p class="login-alert__message"><?= e($error) ?></p>
                        <?php if ($showCredentialHint): ?>
                            <p class="login-alert__hint">Revisa usuario y contraseña e inténtalo de nuevo.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(url('login')) ?>" id="loginForm" novalidate class="form<?= !empty($error) ? ' is-error' : '' ?>" autocomplete="on">
                <div class="field">
                    <label class="label" for="username">Usuario</label>
                    <div class="control has-icon">
                        <span class="control-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            class="input"
                            placeholder="Ingresa tu usuario"
                            required
                            autocomplete="username"
                            autofocus
                            aria-required="true"
                            autocapitalize="off"
                            autocorrect="off"
                            spellcheck="false"
                        />
                    </div>
                </div>

                <div class="field">
                    <label class="label" for="password">Contraseña</label>
                    <div class="control has-icon has-action">
                        <span class="control-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="input"
                            placeholder="Ingresa tu contraseña"
                            required
                            autocomplete="current-password"
                            aria-required="true"
                            autocapitalize="off"
                            autocorrect="off"
                            spellcheck="false"
                        />
                        <button type="button" class="control-action" id="togglePassword" aria-label="Mostrar u ocultar contraseña">
                            <svg class="icon-eye icon-eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="icon-eye icon-eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-row">
                    <a class="link-muted" href="#" id="forgotLink">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" id="submitBtn" class="button button-primary" disabled>
                    <span>Iniciar sesión</span>
                    <svg class="button-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>

                <p class="secure-note" aria-live="polite">
                    <svg class="secure-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Powered by <a href="https://cybercodelabs.com.pe/" target="_blank" rel="noopener noreferrer"><strong>CyberCode Labs</strong></a></span>
                </p>
            </form>

            <footer class="auth-footer">
                <p>© <?= e($year) ?> <?= e($appName) ?>. Todos los derechos reservados.</p>
            </footer>
        </main>
    </div>
</div>

<div class="modal" id="forgotModal" role="dialog" aria-modal="true" aria-labelledby="forgotTitle" aria-hidden="true">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-dialog" tabindex="-1">
        <div class="modal-surface">
            <button class="modal-close" type="button" aria-label="Cerrar" data-modal-close>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <header class="modal-header">
                <h3 id="forgotTitle">Recuperación de acceso</h3>
                <p class="modal-header-text">Si olvidó su contraseña, comuníquese con soporte para restablecer su acceso a <?= e($appName) ?>.</p>
            </header>
            <div class="modal-body">
                <ul class="modal-list">
                    <li>Asegúrese de que su cuenta esté habilitada.</li>
                    <li>Solicite el restablecimiento indicando su usuario.</li>
                </ul>
            </div>
            <footer class="modal-footer">
                <button type="button" class="button button-secondary" data-modal-close>Cerrar</button>
            </footer>
        </div>
    </div>
</div>
