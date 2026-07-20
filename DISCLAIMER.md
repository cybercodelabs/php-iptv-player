# Disclaimer

**PHP IPTV Player** is a web IPTV client developed by [CyberCode Labs](https://cybercodelabs.com.pe/), published as an **early-stage base version**.

> [!IMPORTANT]
> The current scope is deliberately limited. This stage does not yet include all capabilities planned for later product iterations.

## What this software is

It is a **web client / player**. Its purpose is to:

- provide an interface to sign in against an Xtream UI–compatible server that **you** configure;
- list and play in the browser the streams that external server delivers to your account.

## What this software is not

> [!CAUTION]
> This project is **not** an IPTV service, does **not** host audiovisual content, and does **not** redistribute channels, movies, or series.

Specifically, CyberCode Labs / this repository:

- does **not** provide playlists, catalogs, or content URLs;
- does **not** operate or sell IPTV subscriptions;
- does **not** store or retransmit media on its own servers;
- does **not** act as an intermediary for distributing third-party content.

Media is obtained **directly** from the Xtream server (or another compatible panel) specified by the user in the configuration (`XTREAM_HOST` and their account credentials).

## Technical scope (early stage)

- Base version: login, home, live TV, movies, series, profile, search, and in-browser playback.
- Compatible with servers that implement the Xtream UI API.
- Simple design: no local database and no complex infrastructure at this stage.

## Out of current scope

More advanced features (admin panel, advanced user management, caching, proprietary API, mobile apps, etc.) are **not** part of this base version and may be addressed later.

## User responsibility

Use of this client is the **sole responsibility** of whoever deploys or runs it. You must:

- use only providers and content for which you have a **legitimate right** of access;
- comply with the IPTV provider’s terms of service and the laws applicable in your jurisdiction;
- not use this software for piracy, unauthorized redistribution, or copyright infringement.

CyberCode Labs does not control, audit, or assume responsibility for content offered by third-party servers that the user chooses to connect to.

## License and warranty

The software is provided “as is” under the [MIT](LICENSE) license. CyberCode Labs does not guarantee availability, ongoing support, or fitness for any production environment at this early stage.
