# Deploy on Ubuntu + Nginx

Step-by-step guide to run **PHP IPTV Player** on an Ubuntu server with Nginx and PHP-FPM.

> [!NOTE]
> Tested against Ubuntu **22.04 / 24.04 LTS**. Adjust package names if you use another release.

> [!WARNING]
> This app is a **web client / player**. It does not host or redistribute IPTV content. Configure only servers and credentials you are authorized to use. See [DISCLAIMER.md](../DISCLAIMER.md).

Versión en español: [ubuntu-nginx-ESP.md](ubuntu-nginx-ESP.md)

---

## 1. What you need

- A VPS or dedicated server with a public IP
- Ubuntu 22.04 or 24.04 LTS
- A domain (or subdomain) pointing to the server (for HTTPS)
- SSH access with `sudo`
- Your Xtream-compatible server URL (`XTREAM_HOST`)

Suggested layout on the server:

```text
/var/www/php-iptv-player/     # application root
└── public/                   # Nginx document root
```

---

## 2. Update the system

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl git unzip ca-certificates software-properties-common
```

---

## 3. Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable --now nginx
```

Check:

```bash
sudo systemctl status nginx
```

---

## 4. Install PHP 8.3 (FPM) and extensions

Ubuntu 24.04 ships PHP 8.3; on 22.04 you can use the default 8.1+ packages or [ondrej/php](https://launchpad.net/~ondrej/+archive/ubuntu/php).

```bash
sudo apt install -y \
  php-fpm \
  php-cli \
  php-curl \
  php-mbstring \
  php-xml \
  php-zip \
  php-intl
```

Confirm version (must be **8.1+**):

```bash
php -v
```

Locate the FPM socket (name depends on the PHP version):

```bash
ls /run/php/
# e.g. php8.3-fpm.sock  or  php8.1-fpm.sock
```

Enable and start FPM:

```bash
# Replace 8.3 with your version if needed
sudo systemctl enable --now php8.3-fpm
```

---

## 5. Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

---

## 6. Deploy the application

### 6.1 Create the app user directory

```bash
sudo mkdir -p /var/www
sudo chown "$USER":www-data /var/www
```

### 6.2 Clone the repository

```bash
cd /var/www
git clone https://github.com/cybercodelabs/php-iptv-player.git
cd php-iptv-player
```

Or upload a release archive and extract it into `/var/www/php-iptv-player`.

### 6.3 Install PHP dependencies (production)

```bash
composer install --no-dev --optimize-autoloader
```

### 6.4 Environment file

```bash
cp .env.example .env
nano .env
```

Production example:

```env
APP_NAME="IPTV Player"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://iptv.example.com

XTREAM_HOST=http://your-xtream-host:port

APP_LOCALE=es
HOME_BACKGROUND=1
```

> [!IMPORTANT]
> - Set `APP_DEBUG=false` in production.
> - `APP_URL` must be the **public URL** users open in the browser (scheme + host, no trailing slash). If the app is at the domain root, do **not** append `/public`.
> - Never commit `.env`. Keep `XTREAM_HOST` and any secrets only on the server.

### 6.5 Permissions

```bash
sudo chown -R www-data:www-data /var/www/php-iptv-player
sudo find /var/www/php-iptv-player -type d -exec chmod 755 {} \;
sudo find /var/www/php-iptv-player -type f -exec chmod 644 {} \;

# Writable runtime (if you use storage later)
sudo mkdir -p /var/www/php-iptv-player/storage
sudo chown -R www-data:www-data /var/www/php-iptv-player/storage
sudo chmod -R 775 /var/www/php-iptv-player/storage

# Protect .env
sudo chmod 640 /var/www/php-iptv-player/.env
sudo chown www-data:www-data /var/www/php-iptv-player/.env
```

---

## 7. Configure Nginx

Copy the example site config from this repo (or create it manually):

```bash
sudo cp /var/www/php-iptv-player/deploy/nginx/php-iptv-player.conf.example \
  /etc/nginx/sites-available/php-iptv-player
```

Edit the file:

```bash
sudo nano /etc/nginx/sites-available/php-iptv-player
```

Replace:

- `iptv.example.com` → your domain
- `php8.3-fpm.sock` → your PHP-FPM socket from step 4

Enable the site and disable the default if needed:

```bash
sudo ln -sf /etc/nginx/sites-available/php-iptv-player /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

> [!TIP]
> Example config: [`nginx/php-iptv-player.conf.example`](nginx/php-iptv-player.conf.example).  
> Critical line: `root /var/www/php-iptv-player/public;`

---

## 8. Firewall (UFW)

```bash
sudo apt install -y ufw
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status
```

---

## 9. HTTPS with Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d iptv.example.com
```

Follow the prompts. Certbot will adjust the Nginx config and set up renewal.

Test renewal:

```bash
sudo certbot renew --dry-run
```

After SSL works, confirm `.env`:

```env
APP_URL=https://iptv.example.com
APP_ENV=production
APP_DEBUG=false
```

Then reload PHP-FPM (so env is re-read if cached by workers — usually each request loads `.env`):

```bash
sudo systemctl reload php8.3-fpm
```

---

## 10. Verify the deployment

1. Open `https://iptv.example.com` — you should see the login page.
2. Confirm static assets load (favicon, CSS).
3. Sign in with a valid Xtream account against your `XTREAM_HOST`.
4. Smoke-test: Home, Live, Movies, Series, Profile.

Useful logs:

```bash
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
# PHP-FPM (path may vary)
sudo tail -f /var/log/php8.3-fpm.log
```

---

## 11. Updates (later deploys)

```bash
cd /var/www/php-iptv-player
sudo -u www-data git pull
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx
```

If you changed Nginx config:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

---

## 12. Hardening checklist

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS only (HTTP → HTTPS redirect via Certbot)
- [ ] Document root is `public/` only
- [ ] `.env` not world-readable (`640`, owned by `www-data`)
- [ ] `/vendor`, `/src`, `/.env` not exposed as static downloadable trees (Nginx `root` under `public` handles this)
- [ ] Firewall: only SSH + HTTP/HTTPS
- [ ] Keep Ubuntu, Nginx, and PHP updated (`apt upgrade`)

Optional (recommended on public VPS):

```nginx
# Inside the server block (already in the example)
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

---

## Troubleshooting

| Symptom | What to check |
|---------|----------------|
| 404 on every route | `try_files` / `index.php` front controller; `root` must be `.../public` |
| Blank page / 500 | `APP_DEBUG=true` temporarily, PHP-FPM logs, `composer install` |
| CSS/JS 404 | `APP_URL` wrong scheme/host; clear browser cache |
| Login cannot reach IPTV | Server outbound access to `XTREAM_HOST`; firewall; TLS/`verify` issues |
| Permission denied | Ownership `www-data`, `.env` readable by FPM user |
| Wrong PHP version | `php -v` and matching `phpX.Y-fpm` socket in Nginx |

---

## Related files

- Example Nginx site: [`nginx/php-iptv-player.conf.example`](nginx/php-iptv-player.conf.example)
- Environment template: [`.env.example`](../.env.example)
- License / legal: [`LICENSE`](../LICENSE), [`DISCLAIMER.md`](../DISCLAIMER.md)
