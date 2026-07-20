<p align="center">
  <img src="docs/screenshots/favicon.webp" alt="PHP IPTV Player" width="96" />
</p>

<h1 align="center">PHP IPTV Player</h1>

<p align="center">
  Cliente web IPTV ligero en PHP — compatible con la API de Xtream UI.
</p>

<p align="center">
  <a href="https://github.com/cybercodelabs/php-iptv-player"><img src="https://img.shields.io/badge/repo-cybercodelabs%2Fphp--iptv--player-181717?logo=github" alt="GitHub" /></a>
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/Composer-Guzzle%20%7C%20Dotenv-885630?logo=composer&logoColor=white" alt="Composer" />
  <img src="https://img.shields.io/badge/API-Xtream%20UI-e50914" alt="Xtream UI" />
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License MIT" /></a>
</p>

<p align="center">
  <a href="https://hits.sh/github.com/cybercodelabs/php-iptv-player/">
    <img src="https://hits.sh/github.com/cybercodelabs/php-iptv-player.svg?style=flat-square&label=Visitors&color=e50914" alt="Visitors" />
  </a>
</p>

---

## Descripción

**PHP IPTV Player** es un cliente web IPTV en PHP, en **versión base**, para autenticar usuarios y consumir live TV, películas y series desde servidores compatibles con Xtream UI. Sin base de datos ni infra compleja en esta etapa.

> [!WARNING]
> Es un **reproductor web / cliente**. No aloja ni redistribuye contenido IPTV: el catálogo y los streams los entrega el servidor externo que configures (`XTREAM_HOST`).

Más detalle en [DISCLAIMER.md](DISCLAIMER.md).

## Preview

<p align="center">
  <img src="docs/screenshots/screenshot_01.webp" alt="Preview — inicio / catálogo" width="800" />
</p>

<p align="center">
  <img src="docs/screenshots/screenshot_02.webp" alt="Preview — reproducción" width="800" />
</p>

## Arquitectura

Organización por features (`auth`, `live`, `movies`, `series`, `profile`, `player`) con capas presentation / dominio / infraestructura.

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
- Servidor Apache (`mod_rewrite`) o equivalente PHP

## Módulos

| Módulo | Estado |
|--------|--------|
| Auth (login/logout + sesión) | Funcional |
| Layout compartido | Funcional |
| Dashboard / Home | Funcional |
| Live TV (`/channels`, `/channel`) | Funcional |
| Películas / Series | Funcional |
| Búsqueda | Funcional |
| Perfil | Funcional |
| Player (VOD + HLS live) | Funcional |

## Seguridad

- Credenciales en **sesión PHP** (HttpOnly), no en cookies en claro.
- Secretos solo en `.env` (no versionado).

## Licencia

Distribuido bajo la licencia **MIT**. Ver [LICENSE](LICENSE).

Copyright (c) 2026 CyberCode Labs.
