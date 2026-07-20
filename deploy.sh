#!/usr/bin/env bash

set -Eeuo pipefail

SOURCE_DIR="${SAFETECH_SOURCE_DIR:-/var/www/safetech-source}"
API_DIR="${SAFETECH_API_DIR:-/var/www/safetech-api}"
NEXT_DIR="${SAFETECH_NEXT_DIR:-/var/www/safetech-next}"
WEB_USER="${SAFETECH_WEB_USER:-www-data}"
WEB_GROUP="${SAFETECH_WEB_GROUP:-www-data}"
SYSTEMD_DIR="${SAFETECH_SYSTEMD_DIR:-/etc/systemd/system}"
FRONTEND_SERVICE="safetech-frontend.service"
QUEUE_SERVICE="safetech-queue.service"

if [[ "${EUID}" -ne 0 ]]; then
    echo "Run this deployment script with sudo/root privileges." >&2
    exit 1
fi

required_commands=(git rsync php composer node npm systemctl curl install)

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

curl --fail --silent --show-error --retry 10 --retry-connrefused --retry-delay 2 \
    "https://api.safetech.ge/api/health" >/dev/null
curl --fail --silent --show-error --retry 5 --retry-delay 2 \
    "https://api.safetech.ge/api/service-calculator/profiles?locale=ka" >/dev/null
curl --fail --silent --show-error --retry 10 --retry-connrefused --retry-delay 2 \
    "https://safetech.ge/service-calculator" >/dev/null
curl --fail --silent --show-error --retry 5 --retry-delay 2 \
    "https://safetech.ge/sitemap.xml" >/dev/null

echo "SafeTech deployment completed: $(git -C "${SOURCE_DIR}" rev-parse --short HEAD)"
