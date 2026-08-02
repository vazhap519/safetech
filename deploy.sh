#!/usr/bin/env bash

set -Eeuo pipefail

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
PHP_FPM_SERVICE="${SAFETECH_PHP_FPM_SERVICE:-php8.3-fpm.service}"
NGINX_CACHE_DIR="${SAFETECH_NGINX_CACHE_DIR:-/var/cache/nginx/safetech}"
FRONTEND_HOST="${SAFETECH_FRONTEND_HOST:-127.0.0.1}"
FRONTEND_PORT="${SAFETECH_FRONTEND_PORT:-3000}"
FRONTEND_READY_URL="${SAFETECH_FRONTEND_READY_URL:-http://${FRONTEND_HOST}:${FRONTEND_PORT}/}"
FRONTEND_READY_ATTEMPTS="${SAFETECH_FRONTEND_READY_ATTEMPTS:-30}"
FRONTEND_READY_DELAY="${SAFETECH_FRONTEND_READY_DELAY:-2}"
DEPLOY_LOCK_FILE="${SAFETECH_DEPLOY_LOCK_FILE:-/var/lock/safetech-deploy.lock}"

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

frontend_port_is_listening() {
    ss -H -ltn "sport = :${FRONTEND_PORT}" | grep -q .
}

show_frontend_diagnostics() {
    systemctl status "${FRONTEND_SERVICE}" --no-pager -l >&2 || true
    journalctl -u "${FRONTEND_SERVICE}" --no-pager -n 100 >&2 || true
    ss -ltnp "sport = :${FRONTEND_PORT}" >&2 || true
}

wait_for_http_200() {
    local url="$1"
    local label="$2"
    local attempt
    local status

    for ((attempt = 1; attempt <= FRONTEND_READY_ATTEMPTS; attempt++)); do
        status="$(curl --silent --location --output /dev/null \
            --write-out '%{http_code}' --max-time 10 "${url}" 2>/dev/null || true)"

        if [[ "${status}" == "200" ]]; then
            return 0
        fi

        printf 'Waiting for %s (%s), attempt %d/%d, HTTP=%s\n' \
            "${label}" "${url}" "${attempt}" "${FRONTEND_READY_ATTEMPTS}" \
            "${status:-unreachable}"
        sleep "${FRONTEND_READY_DELAY}"
    done

    fail "${label} did not return HTTP 200: ${url}"
}

release_frontend_port() {
    local attempt

    if ! frontend_port_is_listening; then
        return
    fi

    printf 'Port %s is still occupied after stopping %s; terminating the stale listener.\n' \
        "${FRONTEND_PORT}" "${FRONTEND_SERVICE}"

    fuser -k "${FRONTEND_PORT}/tcp" >/dev/null 2>&1 || true

    for ((attempt = 1; attempt <= 10; attempt++)); do
        if ! frontend_port_is_listening; then
            return
        fi

        sleep 1
    done

    ss -ltnp "sport = :${FRONTEND_PORT}" >&2 || true
    fail "frontend port ${FRONTEND_PORT} is still occupied"
}

restart_frontend_service() {
    if ! service_exists "${FRONTEND_SERVICE}"; then
        fail "missing required systemd service: ${FRONTEND_SERVICE}"
    fi

    systemctl stop "${FRONTEND_SERVICE}"
    release_frontend_port
    systemctl reset-failed "${FRONTEND_SERVICE}" || true
    systemctl start "${FRONTEND_SERVICE}"

    wait_for_http_200 "${FRONTEND_READY_URL}" "local Next.js readiness"

    sleep 5

    if ! systemctl is-active --quiet "${FRONTEND_SERVICE}"; then
        show_frontend_diagnostics
        fail "frontend service did not remain active: ${FRONTEND_SERVICE}"
    fi

    if ! frontend_port_is_listening; then
        show_frontend_diagnostics
        fail "frontend service is active but port ${FRONTEND_PORT} is not listening"
    fi

    if ! curl --fail --silent --show-error --location --max-time 10 \
        "${FRONTEND_READY_URL}" >/dev/null; then
        show_frontend_diagnostics
        fail "frontend failed the post-start stability check"
    fi
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

required_commands=(git php composer node npm systemctl curl rsync find grep sort install chown chmod timeout nice flock ss fuser sleep journalctl runuser)
for command_name in "${required_commands[@]}"; do
    command -v "${command_name}" >/dev/null 2>&1 \
        || fail "missing required command: ${command_name}"
done

[[ "${FRONTEND_PORT}" =~ ^[0-9]+$ ]] \
    || fail "SAFETECH_FRONTEND_PORT must be numeric"
[[ "${FRONTEND_READY_ATTEMPTS}" =~ ^[1-9][0-9]*$ ]] \
    || fail "SAFETECH_FRONTEND_READY_ATTEMPTS must be a positive integer"
[[ "${FRONTEND_READY_DELAY}" =~ ^[1-9][0-9]*$ ]] \
    || fail "SAFETECH_FRONTEND_READY_DELAY must be a positive integer"
[[ "${DEPLOY_LOCK_FILE}" == /* ]] \
    || fail "SAFETECH_DEPLOY_LOCK_FILE must be an absolute path"

install -d -o root -g root -m 0755 "$(dirname -- "${DEPLOY_LOCK_FILE}")"
exec 9>"${DEPLOY_LOCK_FILE}"
flock -n 9 || fail "another SafeTech deployment is already running"

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
[[ -f "${FRONTEND_DIR}/scripts/seo-smoke.mjs" ]] \
    || fail "Frontend SEO smoke test not found"

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
php "${BACKEND_DIR}/artisan" db:seed --class=SeoPageSeeder --force
php "${BACKEND_DIR}/artisan" cms:remove-demo-content --force
php "${BACKEND_DIR}/artisan" cache:clear
php "${BACKEND_DIR}/artisan" storage:link --force
php "${BACKEND_DIR}/artisan" optimize
php "${BACKEND_DIR}/artisan" queue:restart
restart_service_if_present "${PHP_FPM_SERVICE}"

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

log "Synchronizing and verifying Next.js static assets"
install -d -o root -g root -m 0755 "${STATIC_DIR}"
rsync -a --checksum "${FRONTEND_DIR}/.next/static/" "${STATIC_DIR}/"

static_sync_diff="$(rsync -a --checksum --dry-run --itemize-changes \
    "${FRONTEND_DIR}/.next/static/" "${STATIC_DIR}/")"
[[ -z "${static_sync_diff}" ]] || {
    printf '%s\n' "${static_sync_diff}" >&2
    fail "Next.js static asset synchronization is incomplete"
}

chown -R root:root "${STATIC_DIR}"
find "${STATIC_DIR}" -type d -exec chmod 0755 {} +
find "${STATIC_DIR}" -type f -exec chmod 0644 {} +
find "${STATIC_DIR}" -type f -mtime +30 -delete
find "${STATIC_DIR}" -mindepth 1 -type d -empty -delete

chown -R "${WEB_USER}:${WEB_GROUP}" \
    "${BACKEND_DIR}/storage" \
    "${BACKEND_DIR}/bootstrap/cache" \
    "${FRONTEND_DIR}/.next"

log "Restarting application services"
restart_frontend_service
restart_service_if_present "${QUEUE_SERVICE}"

if command -v nginx >/dev/null 2>&1; then
    nginx -t
    clear_nginx_cache
    systemctl reload nginx
fi

log "Running smoke checks"
wait_for_http_200 "${API_URL%/}/api/health" "Laravel API health"
wait_for_http_200 "${SITE_URL%/}/" "public homepage"

log "Testing the public Filament/Livewire upload path"
runuser -u "${WEB_USER}" -- php "${BACKEND_DIR}/artisan" cms:upload-smoke \
    --check-nginx-runtime \
    --http-base-url="${API_URL%/}"

health_json="$(curl --fail --silent --show-error --location \
    --max-time 20 "${API_URL%/}/api/health")"
services_json="$(curl --fail --silent --show-error --location \
    --max-time 20 "${API_URL%/}/api/services")"
projects_json="$(curl --fail --silent --show-error --location \
    --max-time 20 "${API_URL%/}/api/projects")"

for api_payload in "${health_json}" "${services_json}" "${projects_json}"; do
    php -r '
        json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);
    ' <<< "${api_payload}" || fail "a required production API returned invalid JSON"
done

for page_path in / /about /services /projects /contact; do
    wait_for_http_200 "${SITE_URL%/}${page_path}" "public page ${page_path}"
done

build_id="$(<"${FRONTEND_DIR}/.next/BUILD_ID")"
home_html="$(curl --fail --silent --show-error --compressed --location \
    --max-time 20 \
    -H 'Accept: text/html' \
    -H 'Cache-Control: no-cache' \
    "${SITE_URL%/}/?deploy_check=${build_id}")"
static_asset_paths="$(grep -Eo '/_next/static/[^"[:space:]]+\.(js|css)' \
    <<< "${home_html}" | sort -u || true)"

[[ -n "${static_asset_paths}" ]] \
    || fail "homepage does not reference any built CSS or JavaScript assets"

live_css_count=0
live_js_count=0

while IFS= read -r static_asset_path; do
    [[ -z "${static_asset_path}" ]] && continue

    static_asset_type="$(curl --fail --silent --show-error --compressed \
        --location --retry 5 --retry-all-errors --retry-delay 2 --max-time 20 \
        -H 'Cache-Control: no-cache' \
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

log "Running Google SEO smoke checks"
SEO_BASE_URL="${SITE_URL%/}" \
NEXT_PUBLIC_SITE_URL="${SITE_URL%/}" \
node "${FRONTEND_DIR}/scripts/seo-smoke.mjs"

log "Deployment completed successfully"
git -C "${PROJECT_DIR}" log -1 --oneline