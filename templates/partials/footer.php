<?php
$appName = \App\Infrastructure\Config\Config::appName();
$year = date('Y');
?>
<footer class="app-footer">
    <div class="container app-footer__inner">
        <div class="app-footer__row">
            <div class="app-footer__brand">
                <a class="app-footer__logo" href="<?= e(url('home')) ?>">
                    <img
                        class="app-footer__logo-img"
                        src="<?= e(asset('img/favicon.ico')) ?>"
                        alt=""
                        width="40"
                        height="40"
                    >
                    <span class="app-footer__logo-text"><?= e($appName) ?></span>
                </a>
                <p class="app-footer__tagline">
                    Streaming simple. Experiencia clara.
                </p>
            </div>

            <div class="app-footer__actions">
                <a
                    class="app-footer__cta"
                    href="https://cybercodelabs.com.pe/"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Contacto
                </a>
                <div class="app-footer__social" aria-label="Redes sociales">
                    <a
                        class="app-footer__social-link"
                        href="https://cybercodelabs.com.pe/"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Sitio web"
                        title="Sitio web"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"/>
                            <path stroke-linecap="round" d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/>
                        </svg>
                    </a>
                    <a
                        class="app-footer__social-link"
                        href="https://github.com/cybercodelabs"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="GitHub"
                        title="GitHub"
                    >
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2C6.48 2 2 6.58 2 12.26c0 4.52 2.87 8.35 6.84 9.7.5.1.68-.22.68-.48v-1.7c-2.78.62-3.37-1.37-3.37-1.37-.46-1.2-1.12-1.52-1.12-1.52-.92-.64.07-.63.07-.63 1.02.07 1.56 1.07 1.56 1.07.9 1.58 2.36 1.12 2.94.86.09-.67.35-1.12.64-1.38-2.22-.26-4.55-1.14-4.55-5.07 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.3.1-2.7 0 0 .84-.27 2.75 1.05A9.3 9.3 0 0112 6.84c.85 0 1.7.12 2.5.34 1.9-1.32 2.74-1.05 2.74-1.05.55 1.4.2 2.44.1 2.7.64.72 1.03 1.63 1.03 2.75 0 3.94-2.34 4.8-4.57 5.06.36.32.68.94.68 1.9v2.82c0 .26.18.58.69.48A10.03 10.03 0 0022 12.26C22 6.58 17.52 2 12 2z"/>
                        </svg>
                    </a>
                    <a
                        class="app-footer__social-link"
                        href="https://www.linkedin.com/company/cybercodelabs"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="LinkedIn"
                        title="LinkedIn"
                    >
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M6.94 8.5H3.56V20h3.38V8.5zM5.25 4A1.97 1.97 0 105.24 8a1.97 1.97 0 00.01-4zM20.44 20h-3.37v-5.6c0-1.34-.02-3.05-1.86-3.05-1.86 0-2.15 1.45-2.15 2.95V20H9.7V8.5h3.24v1.57h.05c.45-.85 1.55-1.75 3.2-1.75 3.42 0 4.05 2.25 4.05 5.18V20z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="app-footer__meta">
            <p class="app-footer__copy">
                Copyright © <?= e($year) ?> · Powered by
                <a href="https://cybercodelabs.com.pe/" target="_blank" rel="noopener noreferrer">CyberCode Labs</a>
            </p>
        </div>
    </div>
</footer>
