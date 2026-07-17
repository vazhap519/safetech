# SafeTech Deployment Checklist

## Your Server Layout

- Frontend: `/var/www/safetech-next`
- Backend API: `/var/www/safetech-api`

Do not deploy the frontend and backend into one shared root. Your current split layout is correct and should stay that way.

## Critical Fixes

1. If `https://api.safetech.ge` shows the default Laravel page, the API vhost is still pointed at the wrong directory.
2. The API domain must point to `/var/www/safetech-api/public`, never to `/var/www/safetech-api`.
3. The public site should run through Nginx with `listen 443 ssl http2;`, otherwise Lighthouse will keep reporting `http/1.1`.
4. Use the sample configs in [deploy/nginx/safetech.example.conf](/F:/safetech/frontend/deploy/nginx/safetech.example.conf) and [deploy/systemd](/F:/safetech/frontend/deploy/systemd).

## Frontend

1. Upload the contents of local `frontend/` into `/var/www/safetech-next`.
2. Copy [`.env.production.example`](/F:/safetech/frontend/.env.production.example) to `/var/www/safetech-next/.env.production`.
3. Set:

```env
BACKEND_API_URL=https://api.safetech.ge/api
NEXT_PUBLIC_API_URL=https://api.safetech.ge/api
NEXT_PUBLIC_SITE_URL=https://safetech.ge
NODE_ENV=production
```

4. Only switch `BACKEND_API_URL` to a private/internal URL if you have explicitly exposed Laravel on an internal hostname or local reverse proxy.
5. Install and build:

```bash
cd /var/www/safetech-next
npm ci
npm run build
```

6. Install the frontend service:

```bash
sudo cp /var/www/safetech-next/deploy/systemd/safetech-frontend.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now safetech-frontend
sudo systemctl restart safetech-frontend
```

## Backend

1. Upload the contents of local `back/` into `/var/www/safetech-api`.
2. Copy [`.env.production.example`](/F:/safetech/back/.env.production.example) to `/var/www/safetech-api/.env`.
3. Set:

```env
APP_URL=https://api.safetech.ge
APP_ENV=production
APP_DEBUG=false
FILESYSTEM_DISK=public
LEADS_NOTIFICATION_EMAIL=safetechgeorgia@gmail.com
```

4. Configure PostgreSQL credentials in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

5. Configure SMTP so contact forms really send mail.
6. Run:

```bash
cd /var/www/safetech-api
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

7. If queue-based jobs are enabled, install the queue worker:

```bash
sudo cp /var/www/safetech-next/deploy/systemd/safetech-queue.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now safetech-queue
sudo systemctl restart safetech-queue
```

## Nginx

1. Copy [deploy/nginx/safetech.example.conf](/F:/safetech/frontend/deploy/nginx/safetech.example.conf) to your Nginx config directory, for example `/etc/nginx/sites-available/safetech.conf`.
2. Canonical public domain is `https://safetech.ge`.
3. The sample config also redirects these aliases to the canonical domain:
   - `http://safetech.ge`
   - `https://saftech.ge`
   - `http://saftech.ge`
   - `https://www.safetech.ge`
   - `http://www.safetech.ge`
   - `https://www.saftech.ge`
   - `http://www.saftech.ge`
4. For those redirects to work correctly, point DNS `A`/`AAAA` records for every alias you actually want to support.
5. Confirm these two paths are exactly correct:
   - Frontend static alias: `/var/www/safetech-next/.next/static/`
   - API root: `/var/www/safetech-api/public`
6. Create certificates:

```bash
sudo certbot --nginx -d safetech.ge -d www.safetech.ge -d saftech.ge -d www.saftech.ge
sudo certbot --nginx -d api.safetech.ge
```

7. If Certbot creates a different lineage path than `/etc/letsencrypt/live/safetech.ge/`, update the certificate paths inside the Nginx file before reloading.
8. Update the PHP-FPM socket path if your server differs.
9. Enable and reload:

```bash
sudo mkdir -p /var/www/_letsencrypt
sudo cp /var/www/safetech-next/deploy/nginx/safetech.example.conf /etc/nginx/sites-available/safetech.conf
sudo ln -sfn /etc/nginx/sites-available/safetech.conf /etc/nginx/sites-enabled/safetech.conf
sudo nginx -t
sudo systemctl reload nginx
```

## Post-Deploy Checks

1. Open `https://safetech.ge/`, `https://safetech.ge/en`, `https://safetech.ge/services`, `https://safetech.ge/projects`, and `https://safetech.ge/contact`.
2. Open `https://www.safetech.ge/`, `https://saftech.ge/`, and `https://www.saftech.ge/` and confirm every one of them redirects to `https://safetech.ge/...`.
3. Open `https://api.safetech.ge/api/services`.
4. Confirm `https://api.safetech.ge` no longer shows the default Laravel welcome page.
5. Submit every form and confirm the request is saved and delivered to `safetechgeorgia@gmail.com`.
6. Confirm the service switcher in contact forms changes fields dynamically after you add services from admin.
7. Confirm the language switcher changes the URL to `/en` and `/ru`.
8. Confirm `https://safetech.ge/sitemap.xml` contains alternate language entries.
9. Confirm `https://safetech.ge/robots.txt` loads.
10. In browser DevTools, confirm the public site negotiates `h2` or `h3`.
