#!/usr/bin/env bash

set -Eeuo pipefail

SOURCE_DIR="${SAFETECH_SOURCE_DIR:-/var/www/safetech-source}"
API_DIR="${SAFETECH_API_DIR:-/var/www/safetech-api}"
NEXT_DIR="${SAFETECH_NEXT_DIR:-/var/www/safetech-next}"
WEB_USER="${SAFETECH_WEB_USER:-www-data}"
WEB_GROUP="${SAFETECH_WEB_GROUP:-www-data}"
SYSTEMD_DIR="${SAFETECH_SYSTEMD_DIR:-/etc/systemd/system}"
NGINX_CONFIG_SOURCE="${SOURCE_DIR}/frontend/deploy/nginx/safetech.example.conf"
NGINX_SITE_AVAILABLE="${SAFETECH_NGINX_SITE_AVAILABLE:-/etc/nginx/sites-available/safetech.conf}"
NGINX_SITE_ENABLED="${SAFETECH_NGINX_SITE_ENABLED:-/etc/nginx/sites-enabled/safetech.conf}"
FRONTEND_SERVICE="safetech-frontend.service"
QUEUE_SERVICE="safetech-queue.service"

if [[ "${EUID}" -ne 0 ]]; then
    echo "Run this deployment script with sudo/root privileges." >&2
    exit 1
fi

required_commands=(git rsync php composer node npm systemctl curl install nginx ln awk grep tr tail dirname)

for command_name in "${required_commands[@]}"; do
    if ! command -v "${command_name}" >/dev/null 2>&1; then
        echo "Missing required command: ${command_name}" >&2
        exit 1
    fi
done

for required_file in \
    "${SOURCE_DIR}/.git/HEAD" \
    "${SOURCE_DIR}/back/artisan" \
    "${SOURCE_DIR}/frontend/package.json" \
    "${NGINX_CONFIG_SOURCE}" \
    "${SOURCE_DIR}/frontend/deploy/systemd/${FRONTEND_SERVICE}" \
    "${SOURCE_DIR}/frontend/deploy/systemd/${QUEUE_SERVICE}" \
    "${API_DIR}/.env" \
    "${NEXT_DIR}/.env.production"; do
    if [[ ! -f "${required_file}" ]]; then
        echo "Missing required file: ${required_file}" >&2
        exit 1
    fi
done

if [[ -n "$(git -C "${SOURCE_DIR}" status --porcelain)" ]]; then
    echo "Deployment source has uncommitted changes: ${SOURCE_DIR}" >&2
    exit 1
fi

git -C "${SOURCE_DIR}" fetch --prune origin
git -C "${SOURCE_DIR}" checkout main
git -C "${SOURCE_DIR}" pull --ff-only origin main

install -o root -g root -m 0644 \
    "${SOURCE_DIR}/frontend/deploy/systemd/${FRONTEND_SERVICE}" \
    "${SYSTEMD_DIR}/${FRONTEND_SERVICE}"
install -o root -g root -m 0644 \
    "${SOURCE_DIR}/frontend/deploy/systemd/${QUEUE_SERVICE}" \
    "${SYSTEMD_DIR}/${QUEUE_SERVICE}"
systemctl daemon-reload
systemctl enable "${FRONTEND_SERVICE}" "${QUEUE_SERVICE}" >/dev/null

mkdir -p \
    "${API_DIR}/storage/app/public" \
    "${API_DIR}/storage/framework/cache" \
    "${API_DIR}/storage/framework/sessions" \
    "${API_DIR}/storage/framework/views" \
    "${API_DIR}/storage/logs" \
    "${API_DIR}/bootstrap/cache" \
    "${NEXT_DIR}"

install -d -o "${WEB_USER}" -g "${WEB_GROUP}" -m 0750 \
    /var/cache/nginx/safetech

install -d -o root -g root -m 0755 \
    "$(dirname "${NGINX_SITE_AVAILABLE}")" \
    "$(dirname "${NGINX_SITE_ENABLED}")" \
    /var/www/_letsencrypt
install -o root -g root -m 0644 \
    "${NGINX_CONFIG_SOURCE}" \
    "${NGINX_SITE_AVAILABLE}"
ln -sfn "${NGINX_SITE_AVAILABLE}" "${NGINX_SITE_ENABLED}"
nginx -t

rsync -a --delete-after \
    --exclude='.env' \
    --exclude='vendor/' \
    --exclude='storage/' \
    --exclude='bootstrap/cache/' \
    "${SOURCE_DIR}/back/" "${API_DIR}/"

rsync -a --delete-after \
    --exclude='.env.production' \
    --exclude='node_modules/' \
    --exclude='.next/' \
    "${SOURCE_DIR}/frontend/" "${NEXT_DIR}/"

composer --working-dir="${API_DIR}" install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

php "${API_DIR}/artisan" config:clear
php "${API_DIR}/artisan" route:clear
php "${API_DIR}/artisan" view:clear
php "${API_DIR}/artisan" migrate --force
php "${API_DIR}/artisan" cache:clear
php "${API_DIR}/artisan" storage:link --force
php "${API_DIR}/artisan" optimize
php "${API_DIR}/artisan" queue:restart

npm --prefix "${NEXT_DIR}" ci
npm --prefix "${NEXT_DIR}" run check
npm --prefix "${NEXT_DIR}" prune --omit=dev

chown -R "${WEB_USER}:${WEB_GROUP}" \
    "${API_DIR}/storage" \
    "${API_DIR}/bootstrap/cache" \
    "${NEXT_DIR}/.next"

systemctl restart "${FRONTEND_SERVICE}"
systemctl restart "${QUEUE_SERVICE}"
systemctl reload nginx

curl --fail --silent --show-error --retry 10 --retry-connrefused --retry-delay 2 \
    "https://api.safetech.ge/api/health" >/dev/null
curl --fail --silent --show-error --retry 5 --retry-delay 2 \
    "https://api.safetech.ge/api/service-calculator/profiles?locale=ka" >/dev/null
curl --fail --silent --show-error --retry 10 --retry-connrefused --retry-delay 2 \
    "https://safetech.ge/service-calculator" >/dev/null
curl --fail --silent --show-error --retry 5 --retry-delay 2 \
    "https://safetech.ge/sitemap.xml" >/dev/null

warm_paths=(
    "/"
    "/about"
    "/contact"
    "/services"
    "/service-calculator"
    "/projects"
    "/blog"
    "/en"
    "/en/about"
    "/en/contact"
    "/en/services"
    "/en/service-calculator"
    "/en/projects"
    "/en/blog"
    "/ru"
    "/ru/about"
    "/ru/contact"
    "/ru/services"
    "/ru/service-calculator"
    "/ru/projects"
    "/ru/blog"
)

for path in "${warm_paths[@]}"; do
    page_html="$(curl --fail --silent --show-error --compressed --retry 3 --retry-delay 1 \
        -H "Accept: text/html" \
        "https://safetech.ge${path}")"

    if ! grep -Eq '<meta name="description" content="[^"]{20,}"' <<< "${page_html}"; then
        echo "Missing or too-short meta description: https://safetech.ge${path}" >&2
        exit 1
    fi
done

llms_content="$(curl --fail --silent --show-error --retry 3 --retry-delay 1 \
    "https://safetech.ge/llms.txt")"

if [[ "${llms_content}" != "# SafeTech"* ]]; then
    echo "Invalid llms.txt response." >&2
    exit 1
fi

frontend_http_version="$(curl --fail --silent --show-error --http2 \
    -o /dev/null -w '%{http_version}' \
    -H "Accept: text/html" \
    "https://safetech.ge/")"

if [[ ! "${frontend_http_version}" =~ ^(2|2\.0|3)$ ]]; then
    echo "Modern HTTP negotiation failed; received HTTP/${frontend_http_version}." >&2
    exit 1
fi

frontend_cache_status="$(curl --fail --silent --show-error --compressed \
    -D - -o /dev/null \
    -H "Accept: text/html" \
    "https://safetech.ge/" \
    | awk 'tolower($1) == "x-safetech-cache:" { print toupper($2) }' \
    | tr -d '\r' \
    | tail -n 1)"

if [[ ! "${frontend_cache_status}" =~ ^(HIT|STALE|UPDATING|REVALIDATED)$ ]]; then
    echo "Frontend HTML microcache is not active; status: ${frontend_cache_status:-missing}." >&2
    exit 1
fi

echo "SafeTech deployment completed: $(git -C "${SOURCE_DIR}" rev-parse --short HEAD)"
