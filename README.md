<p align="center">
  <img src="docs/screenshots/favicon.webp" alt="PHP IPTV Player" width="96" />
</p>

<h1 align="center">PHP IPTV Player</h1>

<p align="center">
  Lightweight PHP IPTV web client — compatible with the Xtream UI API.
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

## Description

**PHP IPTV Player** is a PHP IPTV web client in a **base version**, for authenticating users and consuming live TV, movies, and series from Xtream UI–compatible servers. No database and no complex infrastructure at this stage.

> [!WARNING]
> It is a **web player / client**. It does not host or redistribute IPTV content: the catalog and streams are provided by the external server you configure (`XTREAM_HOST`).

See [DISCLAIMER.md](DISCLAIMER.md) for more details.

## Preview

<p align="center">
  <img src="docs/screenshots/screenshot_01.webp" alt="Preview — home / catalog" width="800" />
</p>

<p align="center">
  <img src="docs/screenshots/screenshot_02.webp" alt="Preview — playback" width="800" />
</p>

## Architecture

Organized by features (`auth`, `live`, `movies`, `series`, `profile`, `player`) with presentation / domain / infrastructure layers.

```text
public/          # Front controller and public assets
src/Features/    # Business modules
src/Shared/      # Layout, helpers, middleware
src/Infrastructure/  # Config, session, Xtream client
templates/       # PHP views
routes/          # Web routes
```

## Requirements

- PHP 8.1+
- Extensions: `curl`, `json`, `mbstring`, `session`
- Composer 2.x
- Apache (`mod_rewrite`) or equivalent PHP server

## Modules

| Module | Status |
|--------|--------|
| Auth (login/logout + session) | Functional |
| Shared layout | Functional |
| Dashboard / Home | Functional |
| Live TV (`/channels`, `/channel`) | Functional |
| Movies / Series | Functional |
| Search | Functional |
| Profile | Functional |
| Player (VOD + HLS live) | Functional |

## Security

- Credentials stored in a **PHP session** (HttpOnly), not in clear-text cookies.
- Secrets only in `.env` (not committed).

## License

Released under the **MIT** license. See [LICENSE](LICENSE).

Copyright (c) 2026 CyberCode Labs.
