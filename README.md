<p align="center">
  <img src="public/assets/img/favicon.png" alt="PHP IPTV Player" width="96" />
</p>

<h1 align="center">PHP IPTV Player</h1>

<p align="center">
  Cliente web IPTV ligero en PHP — implementación de referencia compatible con la API de Xtream UI.
</p>

<p align="center">
  <a href="https://github.com/cybercodelabs/php-iptv-player"><img src="https://img.shields.io/badge/repo-cybercodelabs%2Fphp--iptv--player-181717?logo=github" alt="GitHub" /></a>
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/Composer-Guzzle%20%7C%20Dotenv-885630?logo=composer&logoColor=white" alt="Composer" />
  <img src="https://img.shields.io/badge/API-Xtream%20UI-e50914" alt="Xtream UI" />
</p>

<p align="center">
  <a href="https://hits.sh/github.com/cybercodelabs/php-iptv-player/">
    <img src="https://hits.sh/github.com/cybercodelabs/php-iptv-player.svg?style=flat-square&label=Visitors&color=e50914" alt="Visitors" />
  </a>
</p>

---

## Descripción

**PHP IPTV Player** es una demo / referencia para autenticar usuarios y consumir live TV, películas y series desde servidores compatibles con Xtream UI, sin base de datos ni arquitecturas complejas.

> [!NOTE]
> Versión inicial (demo). No pretende reemplazar una solución empresarial de producción.

> [!IMPORTANT]
> El document root del servidor debe apuntar a la carpeta `public/`.

## Arquitectura

Organización por features (`auth`, `live`, `movies`, `series`, `profile`, `player`) con capas presentation / dominio / infraestructura. Detalle en `.cursor/rules/architecture.mdc`.

```text
public/          # Front controller y assets
src/Features/    # Módulos de negocio
src/Shared/      # Layout, helpers, middleware
src/Infrastructure/  # Config, sesión, cliente Xtream
templates/       # Vistas PHP
routes/          # Rutas web
```

## Requisitos

- PHP 8.1+
- Extensiones: `curl`, `json`, `mbstring`, `session`
- Composer 2.x
- Servidor Apache (mod_rewrite) o `php -S` para desarrollo

## Instalación

```bash
git clone https://github.com/cybercodelabs/php-iptv-player.git
cd php-iptv-player
composer install
cp .env.example .env
```

Edita `.env`:

```env
APP_URL=http://localhost/php-iptv-player/public
XTREAM_HOST=http://tu-servidor-xtream:puerto
```

> [!TIP]
> Con el servidor PHP embebido: `composer run serve` y abre `http://localhost:8080` (ajusta `APP_URL` a `http://localhost:8080`).

### XAMPP / Apache

1. Coloca el proyecto bajo `htdocs`.
2. Apunta el virtual host o usa `http://localhost/php-iptv-player/public`.
3. Si usas subdirectorio, descomenta `RewriteBase` en `public/.htaccess`.

## Estado del esqueleto

| Módulo | Estado |
|--------|--------|
| Auth (login/logout + sesión) | Funcional (requiere `XTREAM_HOST`) |
| Layout compartido | Funcional |
| Dashboard / Live / Movies / Series / Profile | Placeholders |
| Player | Stub |

## Seguridad

- Credenciales en **sesión PHP** (HttpOnly), no en cookies en claro.
- Secretos solo en `.env` (no versionado).

## Licencia

MIT — ver repositorio.
