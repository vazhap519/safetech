#!/usr/bin/env bash

set -Eeuo pipefail

SOURCE_DIR="${SAFETECH_SOURCE_DIR:-/var/www/safetech-source}"
API_DIR="${SAFETECH_API_DIR:-/var/www/safetech-api}"
NEXT_DIR="${SAFETECH_NEXT_DIR:-/var/www/safetech-next}"

for required_file in \
    "${SOURCE_DIR}/back/artisan" \
    "${SOURCE_DIR}/frontend/package.json" \
    "${API_DIR}/.env" \
    "${NEXT_DIR}/.env.production"; do
    if [[ ! -f "${required_file}" ]]; then
        echo "Missing required file: ${required_file}" >&2
        exit 1
    fi
done

git -C "${SOURCE_DIR}" pull --ff-only origin main

rsync -a --delete \
    --exclude='.env' \
    --exclude='vendor/' \
    --exclude='storage/' \
    --exclude='bootstrap/cache/' \
    "${SOURCE_DIR}/back/" "${API_DIR}/"

rsync -a --delete \
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
php "${API_DIR}/artisan" db:seed --force
php "${API_DIR}/artisan" storage:link --force
php "${API_DIR}/artisan" optimize

npm --prefix "${NEXT_DIR}" ci
npm --prefix "${NEXT_DIR}" run check

systemctl restart safetech-frontend
if systemctl list-unit-files safetech-queue.service >/dev/null 2>&1; then
    systemctl restart safetech-queue
fi

curl --fail --silent --show-error "https://api.safetech.ge/api/health" >/dev/null
curl --fail --silent --show-error "https://api.safetech.ge/api/service-calculator/profiles?locale=ka" >/dev/null
curl --fail --silent --show-error "https://safetech.ge/service-calculator" >/dev/null

echo "SafeTech deployment completed successfully."
