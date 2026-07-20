# Despliegue en Ubuntu + Nginx

Guía paso a paso para ejecutar **PHP IPTV Player** en un servidor Ubuntu con Nginx y PHP-FPM.

> [!NOTE]
> Probado en Ubuntu **22.04 / 24.04 LTS**. Ajusta los nombres de paquetes si usas otra versión.

> [!WARNING]
> Esta aplicación es un **cliente / reproductor web**. No aloja ni redistribuye contenido IPTV. Configura solo servidores y credenciales para los que tengas autorización. Ver [DISCLAIMER.md](../DISCLAIMER.md).

English version: [ubuntu-nginx.md](ubuntu-nginx.md)

---

## 1. Qué necesitas

- Un VPS o servidor dedicado con IP pública
- Ubuntu 22.04 o 24.04 LTS
- Un dominio (o subdominio) apuntando al servidor (para HTTPS)
- Acceso SSH con `sudo`
- La URL de tu servidor compatible con Xtream (`XTREAM_HOST`)

Estructura sugerida en el servidor:

```text
/var/www/php-iptv-player/     # raíz de la aplicación
└── public/                   # document root de Nginx
```

---

## 2. Actualizar el sistema

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl git unzip ca-certificates software-properties-common
```

---

## 3. Instalar Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable --now nginx
```

Comprobar:

```bash
sudo systemctl status nginx
```

---

## 4. Instalar PHP 8.3 (FPM) y extensiones

Ubuntu 24.04 trae PHP 8.3; en 22.04 puedes usar los paquetes 8.1+ por defecto o [ondrej/php](https://launchpad.net/~ondrej/+archive/ubuntu/php).

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

Confirma la versión (debe ser **8.1+**):

```bash
php -v
```

Localiza el socket de FPM (el nombre depende de la versión de PHP):

```bash
ls /run/php/
# p. ej. php8.3-fpm.sock  o  php8.1-fpm.sock
```

Habilita e inicia FPM:

```bash
# Sustituye 8.3 por tu versión si hace falta
sudo systemctl enable --now php8.3-fpm
```

---

## 5. Instalar Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

---

## 6. Desplegar la aplicación

### 6.1 Crear el directorio de la app

```bash
sudo mkdir -p /var/www
sudo chown "$USER":www-data /var/www
```

### 6.2 Clonar el repositorio

```bash
cd /var/www
git clone https://github.com/cybercodelabs/php-iptv-player.git
cd php-iptv-player
```

O sube un archivo de release y extráelo en `/var/www/php-iptv-player`.

### 6.3 Instalar dependencias PHP (producción)

```bash
composer install --no-dev --optimize-autoloader
```

### 6.4 Archivo de entorno

```bash
cp .env.example .env
nano .env
```

Ejemplo de producción:

```env
APP_NAME="IPTV Player"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://iptv.example.com

XTREAM_HOST=http://tu-servidor-xtream:puerto

APP_LOCALE=es
HOME_BACKGROUND=1
```

> [!IMPORTANT]
> - En producción pon `APP_DEBUG=false`.
> - `APP_URL` debe ser la **URL pública** que abren los usuarios (esquema + host, sin barra final). Si la app está en la raíz del dominio, **no** añadas `/public`.
> - Nunca subas `.env` al repositorio. Mantén `XTREAM_HOST` y secretos solo en el servidor.

### 6.5 Permisos

```bash
sudo chown -R www-data:www-data /var/www/php-iptv-player
sudo find /var/www/php-iptv-player -type d -exec chmod 755 {} \;
sudo find /var/www/php-iptv-player -type f -exec chmod 644 {} \;

# Runtime escribible (si usas storage más adelante)
sudo mkdir -p /var/www/php-iptv-player/storage
sudo chown -R www-data:www-data /var/www/php-iptv-player/storage
sudo chmod -R 775 /var/www/php-iptv-player/storage

# Proteger .env
sudo chmod 640 /var/www/php-iptv-player/.env
sudo chown www-data:www-data /var/www/php-iptv-player/.env
```

---

## 7. Configurar Nginx

Copia el ejemplo de sitio de este repositorio (o créalo a mano):

```bash
sudo cp /var/www/php-iptv-player/deploy/nginx/php-iptv-player.conf.example \
  /etc/nginx/sites-available/php-iptv-player
```

Edita el archivo:

```bash
sudo nano /etc/nginx/sites-available/php-iptv-player
```

Sustituye:

- `iptv.example.com` → tu dominio
- `php8.3-fpm.sock` → el socket PHP-FPM del paso 4

Activa el sitio y desactiva el default si hace falta:

```bash
sudo ln -sf /etc/nginx/sites-available/php-iptv-player /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

> [!TIP]
> Config de ejemplo: [`nginx/php-iptv-player.conf.example`](nginx/php-iptv-player.conf.example).  
> Línea crítica: `root /var/www/php-iptv-player/public;`

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

## 9. HTTPS con Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d iptv.example.com
```

Sigue las indicaciones. Certbot ajustará Nginx y programará la renovación.

Probar renovación:

```bash
sudo certbot renew --dry-run
```

Cuando el SSL funcione, confirma el `.env`:

```env
APP_URL=https://iptv.example.com
APP_ENV=production
APP_DEBUG=false
```

Luego recarga PHP-FPM:

```bash
sudo systemctl reload php8.3-fpm
```

---

## 10. Verificar el despliegue

1. Abre `https://iptv.example.com` — deberías ver el login.
2. Comprueba que carguen assets estáticos (favicon, CSS).
3. Inicia sesión con una cuenta Xtream válida contra tu `XTREAM_HOST`.
4. Prueba rápida: Home, Live, Películas, Series, Perfil.

Logs útiles:

```bash
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
# PHP-FPM (la ruta puede variar)
sudo tail -f /var/log/php8.3-fpm.log
```

---

## 11. Actualizaciones (despliegues posteriores)

```bash
cd /var/www/php-iptv-player
sudo -u www-data git pull
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx
```

Si cambiaste la config de Nginx:

```bash
sudo nginx -t && sudo systemctl reload nginx
```

---

## 12. Checklist de endurecimiento

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Solo HTTPS (redirección HTTP → HTTPS con Certbot)
- [ ] Document root solo en `public/`
- [ ] `.env` no legible por todos (`640`, dueño `www-data`)
- [ ] `/vendor`, `/src`, `/.env` no expuestos como estáticos (el `root` en `public` lo evita)
- [ ] Firewall: solo SSH + HTTP/HTTPS
- [ ] Mantén Ubuntu, Nginx y PHP actualizados (`apt upgrade`)

Opcional (recomendado en VPS público):

```nginx
# Dentro del bloque server (ya está en el ejemplo)
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

---

## Solución de problemas

| Síntoma | Qué revisar |
|---------|-------------|
| 404 en todas las rutas | `try_files` / front controller `index.php`; `root` debe ser `.../public` |
| Página en blanco / 500 | `APP_DEBUG=true` temporalmente, logs de PHP-FPM, `composer install` |
| CSS/JS 404 | `APP_URL` con esquema/host incorrectos; limpia caché del navegador |
| Login no llega al IPTV | Salida del servidor hacia `XTREAM_HOST`; firewall; TLS/`verify` |
| Permission denied | Propietario `www-data`, `.env` legible por el usuario FPM |
| Versión de PHP incorrecta | `php -v` y el socket `phpX.Y-fpm` coincidente en Nginx |

---

## Archivos relacionados

- Ejemplo Nginx: [`nginx/php-iptv-player.conf.example`](nginx/php-iptv-player.conf.example)
- Plantilla de entorno: [`.env.example`](../.env.example)
- Licencia / legal: [`LICENSE`](../LICENSE), [`DISCLAIMER.md`](../DISCLAIMER.md)
