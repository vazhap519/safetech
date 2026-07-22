# SafeTech production deployment

The repository contains two applications and is deployed into three directories:

- Git source: `/var/www/safetech-source`
- Laravel API: `/var/www/safetech-api`
- Next.js frontend: `/var/www/safetech-next`

The API virtual host must use `/var/www/safetech-api/public` as its document root. Never expose the Laravel project root.

## 1. Server requirements

- Ubuntu/Debian server with Nginx
- PHP 8.2 or newer with FPM, PostgreSQL, GD, DOM/XML, cURL, Mbstring and Zip extensions
- Composer 2
- PostgreSQL
- Node.js 20.9 or newer and npm
- Git, rsync and Certbot

The supplied systemd and Nginx examples assume PHP 8.3 and system Node/npm paths. Adjust their executable or socket paths when your server differs.

## 2. DNS and source clone

Point the required DNS records to the server before requesting TLS certificates:

- `safetech.ge`
- `www.safetech.ge`
- `api.safetech.ge`
- Optional typo redirect: `saftech.ge` and `www.saftech.ge`

On the server, clone the repository and create both deployment targets:

```bash
sudo -i
git clone https://github.com/vazhap519/safetech.git /var/www/safetech-source
install -d -m 0755 /var/www/safetech-api /var/www/safetech-next

rsync -a /var/www/safetech-source/back/ /var/www/safetech-api/
rsync -a /var/www/safetech-source/frontend/ /var/www/safetech-next/

cp /var/www/safetech-api/.env.production.example /var/www/safetech-api/.env
cp /var/www/safetech-next/.env.production.example /var/www/safetech-next/.env.production
```

The repository is public. For a future private repository, use an SSH deploy key and the SSH clone URL instead.

## 3. PostgreSQL

Create the role and database once. Replace the example password with a long, unique password:

```bash
sudo -u postgres psql
```

```sql
CREATE USER safetech WITH ENCRYPTED PASSWORD 'replace_with_a_strong_password';
CREATE DATABASE safetech OWNER safetech ENCODING 'UTF8';
\q
```

Edit `/var/www/safetech-api/.env` before running any migration:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.safetech.ge
APP_TIMEZONE=Asia/Tbilisi
SEED_DEMO_CONTENT=false

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=safetech
DB_USERNAME=safetech
DB_PASSWORD=replace_with_the_same_password
DB_SSLMODE=prefer

FRONTEND_URL=https://safetech.ge
FRONTEND_URLS=https://safetech.ge,https://www.safetech.ge
TRUSTED_PROXIES=127.0.0.1,::1
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true

ADMIN_NAME="SafeTech Admin"
ADMIN_EMAIL=safetechgeorgia@gmail.com
ADMIN_PASSWORD=replace_with_a_unique_12_plus_character_password
```

Also configure Gmail SMTP with an app password, keep `LEADS_NOTIFICATION_EMAIL=safetechgeorgia@gmail.com`, configure CRM credentials when used, and use the same strong `REVALIDATE_SECRET` in both applications. `APP_KEY` is generated in the initial-install step. The deployment provisions the configured administrator when missing, promotes an existing account with the same email, and never overwrites a password changed later in Filament.

`SQLSTATE ... fe_sendauth: no password supplied` means `DB_PASSWORD` is still blank or Laravel is using cached configuration. Correct `.env`, then run `php artisan config:clear` before retrying.

## 4. Frontend environment

Edit `/var/www/safetech-next/.env.production`:

```env
BACKEND_API_URL=https://api.safetech.ge/api
NEXT_PUBLIC_API_URL=https://api.safetech.ge/api
NEXT_PUBLIC_SITE_URL=https://safetech.ge
REVALIDATE_SECRET=use_the_same_secret_as_laravel
GEO_BLOCK_ENABLED=false
GEO_ALLOWED_COUNTRIES=GE
GEO_BLOCK_UNKNOWN_COUNTRY=false
NODE_ENV=production
```

Analytics, Pixel and webmaster verification IDs can be managed in Filament. Environment IDs remain optional fallbacks.

Keep geographic blocking disabled for an SEO-facing public site. Google, Bing and Yandex crawlers may originate outside Georgia. If blocking is a strict business requirement, enable it only after the country header comes from a trusted CDN and the origin is protected from direct traffic.

## 5. Initial application install

Run the seed during the first installation to create the admin account and system defaults. Production keeps `SEED_DEMO_CONTENT=false`, so bundled example services and projects are not inserted. Existing admin-managed values are preserved.

```bash
cd /var/www/safetech-api
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan key:generate
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link --force
php artisan optimize

install -d -o www-data -g www-data \
  storage/app/public storage/framework/cache storage/framework/sessions \
  storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

cd /var/www/safetech-next
npm ci
npm run check
npm prune --omit=dev
install -d -o root -g root -m 0755 /var/www/safetech-static
rsync -a --checksum .next/static/ /var/www/safetech-static/
chown -R root:root /var/www/safetech-static
chown -R www-data:www-data .next
```

If an older installation already contains the bundled examples, preview and remove only those exact demo slugs with:

```bash
cd /var/www/safetech-api
php artisan cms:remove-demo-content
```

## 6. systemd services

Install the supplied service units:

```bash
cp /var/www/safetech-next/deploy/systemd/safetech-frontend.service /etc/systemd/system/
cp /var/www/safetech-next/deploy/systemd/safetech-queue.service /etc/systemd/system/
systemctl daemon-reload
systemctl enable --now safetech-frontend safetech-queue
systemctl status safetech-frontend safetech-queue --no-pager
```

See [safetech-frontend.service](deploy/systemd/safetech-frontend.service) and [safetech-queue.service](deploy/systemd/safetech-queue.service). If Node was installed through NVM, replace `/usr/bin/npm` in the frontend unit with the absolute path returned by `command -v npm`.

## 7. TLS and Nginx

The full sample config references certificate files, so issue certificates before enabling it on a fresh server. With ports 80 and 443 free:

```bash
systemctl stop nginx
certbot certonly --standalone \
  -d safetech.ge -d www.safetech.ge -d api.safetech.ge
systemctl start nginx
```

Add optional typo aliases to both the certificate and Nginx `server_name` list only after their DNS records are active.

The deployment script installs [safetech.example.conf](deploy/nginx/safetech.example.conf) automatically. For the first installation or manual recovery, use:

```bash
mkdir -p /var/www/_letsencrypt
install -d -o root -g root -m 0755 /var/www/safetech-static
install -d -o www-data -g www-data -m 0750 /var/cache/nginx/safetech
cp -a /etc/nginx/sites-available/safetech.conf \
  /etc/nginx/sites-available/safetech.conf.backup-$(date +%F-%H%M) 2>/dev/null || true
cp /var/www/safetech-next/deploy/nginx/safetech.example.conf \
  /etc/nginx/sites-available/safetech.conf
ln -sfn /etc/nginx/sites-available/safetech.conf \
  /etc/nginx/sites-enabled/safetech.conf
nginx -t
systemctl reload nginx
```

Confirm that the certificate paths and PHP-FPM socket in the config exist before reloading. The config enables HTTP/2 with the current Nginx syntax and keeps a one-minute public HTML microcache; frontend `/api/` routes and React Server Component requests are never cached. Versioned Next.js assets are retained in `/var/www/safetech-static` for 30 days so open tabs keep working across releases. Missing shared assets fall back to the active Next.js process, which prevents an incomplete manual static sync from rendering an unstyled page. Configure Cloudflare real-IP handling and restrict direct origin access before uncommenting any country-header forwarding lines.

Verify protocol negotiation and the warmed HTML cache:

```bash
openssl s_client -connect safetech.ge:443 -servername safetech.ge -alpn h2 \
  </dev/null 2>/dev/null | grep 'ALPN protocol: h2'
curl -sS -o /dev/null -H 'Accept: text/html' https://safetech.ge/ru/contact
curl -sS -D - -o /dev/null -H 'Accept: text/html' https://safetech.ge/ru/contact \
  | grep -Ei 'HTTP/|cache-control|x-safetech-cache|content-encoding'
```

## 8. Future Git deployments

Do not edit files inside `/var/www/safetech-source`. Commit and push changes to GitHub, then run:

```bash
sudo bash /var/www/safetech-source/deploy.sh
```

The script refuses a dirty source tree, fast-forwards `main`, preserves both production environment files and Laravel storage, installs dependencies, runs migrations, syncs production-safe system defaults without demo catalog content, builds/tests the frontend, installs and validates the Nginx site, restarts services, warms localized pages, verifies metadata, HTTP/2, HTML caching, `llms.txt`, the live API and calculator, then loads every URL emitted by the sitemap and fails the release if any URL is malformed or does not return HTTP 200.

Before schema-changing deployments, back up PostgreSQL and uploaded media:

```bash
sudo -u postgres pg_dump -Fc safetech > /root/safetech-$(date +%F-%H%M).dump
tar -C /var/www/safetech-api -czf /root/safetech-media-$(date +%F-%H%M).tar.gz storage/app/public
```

## 9. Post-deploy checks

```bash
curl -I https://safetech.ge/
curl -I https://safetech.ge/en/services
curl -I https://safetech.ge/ru/projects
curl -I https://safetech.ge/service-calculator
curl -I https://safetech.ge/robots.txt
curl -I https://safetech.ge/llms.txt
curl -I https://safetech.ge/sitemap.xml
curl -I https://api.safetech.ge/api/health
curl -I https://api.safetech.ge/api/services
```

Confirm that every stylesheet and script referenced by the homepage returns HTTP 200. A `404` under `/_next/static/` means the active HTML and static release do not match:

```bash
curl -fsS https://safetech.ge/ \
  | grep -Eo '/_next/static/[^"[:space:]]+\.(css|js)' \
  | sort -u \
  | while read -r asset; do curl -fsS -o /dev/null "https://safetech.ge${asset}" || exit 1; done
```

Then verify login at `https://api.safetech.ge/admin`, submit each public form, confirm queued email delivery, open all sitemap children, and validate a service, project, blog post and category in Google Rich Results Test and Search Console URL Inspection.
