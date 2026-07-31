#!/usr/bin/env bash

set -Eeuo pipefail

# This project is deployed in place:
#   /var/www/safetech/back
#   /var/www/safetech/frontend
#
# Every default can be overridden without editing this file.
SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="${SAFETECH_PROJECT_DIR:-${SCRIPT_DIR}}"
BACKEND_DIR="${SAFETECH_BACKEND_DIR:-${PROJECT_DIR}/back}"
FRONTEND_DIR="${SAFETECH_FRONTEND_DIR:-${PROJECT_DIR}/frontend}"
STATIC_DIR="${SAFETECH_STATIC_DIR:-/var/www/safetech-static}"
BRANCH="${SAFETECH_BRANCH:-main}"
REMOTE="${SAFETECH_REMOTE:-origin}"
SITE_URL="${SAFETECH_SITE_URL:-https://safetech.ge}"
API_URL="${SAFETECH_API_URL:-https://api.safetech.ge}"
WEB_USER="${SAFETECH_WEB_USER:-www-data}"
WEB_GROUP="${SAFETECH_WEB_GROUP:-www-data}"
FRONTEND_SERVICE="${SAFETECH_FRONTEND_SERVICE:-safetech-frontend.service}"
QUEUE_SERVICE="${SAFETECH_QUEUE_SERVICE:-safetech-queue.service}"
NGINX_CACHE_DIR="${SAFETECH_NGINX_CACHE_DIR:-/var/cache/nginx/safetech}"

log() {
    printf '\n==> %s\n' "$*"
}

fail() {
    printf 'Deployment failed: %s\n' "$*" >&2
    exit 1
}

service_exists() {
    systemctl cat "$1" >/dev/null 2>&1
}

restart_service_if_present() {
    local service_name="$1"

    if service_exists "${service_name}"; then
        systemctl restart "${service_name}"
        systemctl is-active --quiet "${service_name}" \
            || fail "service did not become active: ${service_name}"
    else
        printf 'Skipping missing systemd service: %s\n' "${service_name}"
    fi
}

clear_nginx_cache() {
    if [[ ! -d "${NGINX_CACHE_DIR}" ]]; then
        return
    fi

    case "${NGINX_CACHE_DIR}" in
        /var/cache/nginx/*)
            find "${NGINX_CACHE_DIR}" -mindepth 1 -delete
            ;;
        *)
            fail "refusing to clear unexpected Nginx cache path: ${NGINX_CACHE_DIR}"
            ;;
    esac
}

if [[ "${EUID}" -ne 0 ]]; then
    fail "run this script with sudo/root privileges"
fi

required_commands=(git php composer node npm systemctl curl rsync find grep sort install chown timeout nice)
for command_name in "${required_commands[@]}"; do
    command -v "${command_name}" >/dev/null 2>&1 \
        || fail "missing required command: ${command_name}"
done

[[ "${PROJECT_DIR}" == /* && "${PROJECT_DIR}" != "/" ]] \
    || fail "SAFETECH_PROJECT_DIR must be an absolute non-root path"
[[ "${STATIC_DIR}" == /* && "${STATIC_DIR}" != "/" ]] \
    || fail "SAFETECH_STATIC_DIR must be an absolute non-root path"
[[ -d "${PROJECT_DIR}/.git" ]] || fail "Git checkout not found: ${PROJECT_DIR}"
[[ -f "${BACKEND_DIR}/artisan" ]] || fail "Laravel application not found: ${BACKEND_DIR}"
[[ -f "${BACKEND_DIR}/.env" ]] || fail "Backend production .env not found: ${BACKEND_DIR}/.env"
[[ -f "${FRONTEND_DIR}/package.json" ]] || fail "Next.js application not found: ${FRONTEND_DIR}"
[[ -f "${FRONTEND_DIR}/package-lock.json" ]] || fail "Frontend package-lock.json not found"
[[ -f "${FRONTEND_DIR}/.env.production" ]] \
    || fail "Frontend production .env not found: ${FRONTEND_DIR}/.env.production"

if [[ -n "$(git -C "${PROJECT_DIR}" status --porcelain)" ]]; then
    git -C "${PROJECT_DIR}" status --short >&2
    fail "the production checkout contains uncommitted changes"
fi

log "Updating ${REMOTE}/${BRANCH}"
git -C "${PROJECT_DIR}" fetch --prune "${REMOTE}"
git -C "${PROJECT_DIR}" checkout "${BRANCH}"
git -C "${PROJECT_DIR}" pull --ff-only "${REMOTE}" "${BRANCH}"

log "Installing and validating backend dependencies"
composer --working-dir="${BACKEND_DIR}" install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader
composer --working-dir="${BACKEND_DIR}" audit --locked --no-dev --no-interaction

log "Updating Laravel"
php "${BACKEND_DIR}/artisan" config:clear
php "${BACKEND_DIR}/artisan" route:clear
php "${BACKEND_DIR}/artisan" view:clear
php "${BACKEND_DIR}/artisan" cms:production-check
php "${BACKEND_DIR}/artisan" migrate --force
php "${BACKEND_DIR}/artisan" db:seed --class=AdminUserSeeder --force
php "${BACKEND_DIR}/artisan" db:seed --class=SystemContentSeeder --force
php "${BACKEND_DIR}/artisan" cms:remove-demo-content --force
php "${BACKEND_DIR}/artisan" cache:clear
php "${BACKEND_DIR}/artisan" storage:link --force
php "${BACKEND_DIR}/artisan" optimize
php "${BACKEND_DIR}/artisan" queue:restart

log "Cleaning previous frontend build"
rm -rf "${FRONTEND_DIR}/.next"
rm -rf "/tmp/safetech-eslint-cache"
rm -f "${FRONTEND_DIR}/.eslintcache"
mkdir -p "/tmp/safetech-eslint-cache"

log "Installing frontend dependencies"
npm --prefix "${FRONTEND_DIR}" ci
npm --prefix "${FRONTEND_DIR}" audit --omit=dev --audit-level=high

log "Linting frontend"
(
    cd "${FRONTEND_DIR}"

    timeout 20m ./node_modules/.bin/eslint . \
        --ignore-pattern '.next/**' \
        --ignore-pattern 'node_modules/**' \
        --ignore-pattern 'out/**' \
        --ignore-pattern 'dist/**' \
        --ignore-pattern 'coverage/**' \
        --cache \
        --cache-location '/tmp/safetech-eslint-cache/.eslintcache'
)

log "Type-checking frontend"
timeout 20m npm --prefix "${FRONTEND_DIR}" run typecheck

log "Building frontend with Webpack"
NEXT_TELEMETRY_DISABLED=1 \
NODE_OPTIONS="${NODE_OPTIONS:---max-old-space-size=1536}" \
nice -n 10 npm --prefix "${FRONTEND_DIR}" run build -- --webpack

npm --prefix "${FRONTEND_DIR}" prune \
    --omit=dev \
    --no-package-lock \
    --no-audit \
    --no-fund

[[ -s "${FRONTEND_DIR}/.next/BUILD_ID" ]] \
    || fail "Next.js production build did not create .next/BUILD_ID"
[[ -d "${FRONTEND_DIR}/.next/static" ]] \
    || fail "Next.js production build did not create .next/static"

log "Synchronizing Next.js static assets"
install -d -o root -g root -m 0755 "${STATIC_DIR}"
rsync -a --checksum "${FRONTEND_DIR}/.next/static/" "${STATIC_DIR}/"
find "${STATIC_DIR}" -type f -mtime +30 -delete
find "${STATIC_DIR}" -mindepth 1 -type d -empty -delete

chown -R "${WEB_USER}:${WEB_GROUP}" \
    "${BACKEND_DIR}/storage" \
    "${BACKEND_DIR}/bootstrap/cache" \
    "${FRONTEND_DIR}/.next"

log "Restarting application services"
restart_service_if_present "${FRONTEND_SERVICE}"
restart_service_if_present "${QUEUE_SERVICE}"

if command -v nginx >/dev/null 2>&1; then
    nginx -t
    clear_nginx_cache
    systemctl reload nginx
fi

log "Running smoke checks"
health_json="$(curl --fail --silent --show-error --retry 10 \
    --retry-connrefused --retry-delay 2 "${API_URL%/}/api/health")"
calculator_json="$(curl --fail --silent --show-error --retry 5 \
    --retry-delay 2 "${API_URL%/}/api/service-calculator/profiles?locale=ka")"

for api_payload in "${health_json}" "${calculator_json}"; do
    php -r '
        json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);
    ' <<< "${api_payload}" || fail "a required production API returned invalid JSON"
done

curl --fail --silent --show-error --retry 10 \
    --retry-connrefused --retry-delay 2 \
    "${SITE_URL%/}/service-calculator" >/dev/null

home_html="$(curl --fail --silent --show-error --compressed \
    -H 'Accept: text/html' "${SITE_URL%/}/")"
static_asset_paths="$(grep -Eo '/_next/static/[^"[:space:]]+\.(js|css)' \
    <<< "${home_html}" | sort -u || true)"

[[ -n "${static_asset_paths}" ]] \
    || fail "homepage does not reference any built CSS or JavaScript assets"

live_css_count=0
live_js_count=0

while IFS= read -r static_asset_path; do
    [[ -z "${static_asset_path}" ]] && continue

    static_asset_type="$(curl --fail --silent --show-error --compressed \
        -o /dev/null -w '%{content_type}' \
        "${SITE_URL%/}${static_asset_path}")"

    case "${static_asset_path}" in
        *.css)
            [[ "${static_asset_type}" == text/css* ]] \
                || fail "invalid CSS response (${static_asset_type}): ${static_asset_path}"
            live_css_count=$((live_css_count + 1))
            ;;
        *.js)
            [[ "${static_asset_type}" == *javascript* ]] \
                || fail "invalid JavaScript response (${static_asset_type}): ${static_asset_path}"
            live_js_count=$((live_js_count + 1))
            ;;
    esac
done <<< "${static_asset_paths}"

[[ "${live_css_count}" -gt 0 && "${live_js_count}" -gt 0 ]] \
    || fail "homepage is missing live CSS or JavaScript assets"

log "Deployment completed successfully"
git -C "${PROJECT_DIR}" log -1 --oneline
