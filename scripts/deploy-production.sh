#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
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
FRONTEND_HOST="${SAFETECH_FRONTEND_HOST:-127.0.0.1}"
FRONTEND_PORT="${SAFETECH_FRONTEND_PORT:-3000}"
FRONTEND_READY_URL="${SAFETECH_FRONTEND_READY_URL:-http://${FRONTEND_HOST}:${FRONTEND_PORT}/}"
FRONTEND_READY_ATTEMPTS="${SAFETECH_FRONTEND_READY_ATTEMPTS:-45}"
FRONTEND_READY_DELAY="${SAFETECH_FRONTEND_READY_DELAY:-2}"
DEPLOY_LOCK_FILE="${SAFETECH_DEPLOY_LOCK_FILE:-/var/lock/safetech-deploy.lock}"
NGINX_CACHE_DIR="${SAFETECH_NGINX_CACHE_DIR:-/var/cache/nginx/safetech}"
NGINX_CONFIG_SOURCE="${SAFETECH_NGINX_CONFIG_SOURCE:-${FRONTEND_DIR}/deploy/nginx/safetech.example.conf}"
NGINX_CONFIG_TARGET="${SAFETECH_NGINX_CONFIG_TARGET:-/etc/nginx/sites-available/safetech.conf}"
NGINX_CONFIG_LINK="${SAFETECH_NGINX_CONFIG_LINK:-/etc/nginx/sites-enabled/safetech.conf}"
FRONTEND_UNIT_SOURCE="${SAFETECH_FRONTEND_UNIT_SOURCE:-${FRONTEND_DIR}/deploy/systemd/safetech-frontend.service}"
QUEUE_UNIT_SOURCE="${SAFETECH_QUEUE_UNIT_SOURCE:-${FRONTEND_DIR}/deploy/systemd/safetech-queue.service}"
SYSTEMD_DIR="${SAFETECH_SYSTEMD_DIR:-/etc/systemd/system}"
STAGE_ROOT="${SAFETECH_STAGE_ROOT:-${PROJECT_DIR}/.deploy}"
FRONTEND_STAGE_DIR="${STAGE_ROOT}/frontend-stage"
FRONTEND_ROLLBACK_DIR="${STAGE_ROOT}/frontend-rollback"
FRONTEND_SWAPPED=0
DEPLOY_SUCCESS=0

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
    printf '%s\n' '--- systemd status ---' >&2
    systemctl status "${FRONTEND_SERVICE}" --no-pager -l >&2 || true
    printf '%s\n' '--- frontend journal ---' >&2
    journalctl -u "${FRONTEND_SERVICE}" --no-pager -n 200 >&2 || true
    printf '%s\n' '--- port listener ---' >&2
    ss -ltnp "sport = :${FRONTEND_PORT}" >&2 || true
    printf '%s\n' '--- local response ---' >&2
    curl --silent --show-error --location --max-time 15 \
        -D - "${FRONTEND_READY_URL}" >&2 || true
}

wait_for_http_200() {
    local url="$1"
    local label="$2"
    local attempt
    local status
    local body_file="/tmp/safetech-readiness-body.$$.txt"

    for ((attempt = 1; attempt <= FRONTEND_READY_ATTEMPTS; attempt++)); do
        status="$(curl --silent --location --output "${body_file}" \
            --write-out '%{http_code}' --max-time 15 "${url}" 2>/dev/null || true)"

        if [[ "${status}" == "200" ]]; then
            rm -f "${body_file}"
            return 0
        fi

        printf 'Waiting for %s (%s), attempt %d/%d, HTTP=%s\n' \
            "${label}" "${url}" "${attempt}" "${FRONTEND_READY_ATTEMPTS}" \
            "${status:-unreachable}"
        sleep "${FRONTEND_READY_DELAY}"
    done

    printf '%s\n' "--- ${label} response body ---" >&2
    head -c 10000 "${body_file}" >&2 || true
    printf '\n' >&2
    rm -f "${body_file}"
    return 1
}

release_frontend_port() {
    local attempt

    if ! frontend_port_is_listening; then
        return 0
    fi

    printf 'Port %s is still occupied after stopping %s; terminating the stale listener.\n' \
        "${FRONTEND_PORT}" "${FRONTEND_SERVICE}"
    fuser -k "${FRONTEND_PORT}/tcp" >/dev/null 2>&1 || true

    for ((attempt = 1; attempt <= 15; attempt++)); do
        if ! frontend_port_is_listening; then
            return 0
        fi
        sleep 1
    done

    ss -ltnp "sport = :${FRONTEND_PORT}" >&2 || true
    return 1
}

restore_frontend() {
    if [[ "${FRONTEND_SWAPPED}" -ne 1 ]]; then
        return 0
    fi

    printf '%s\n' 'Restoring the previous frontend release...' >&2
    systemctl stop "${FRONTEND_SERVICE}" >/dev/null 2>&1 || true
    fuser -k "${FRONTEND_PORT}/tcp" >/dev/null 2>&1 || true

    rm -rf "${FRONTEND_DIR}/.next" "${FRONTEND_DIR}/node_modules"

    if [[ -d "${FRONTEND_ROLLBACK_DIR}/.next" ]]; then
        mv "${FRONTEND_ROLLBACK_DIR}/.next" "${FRONTEND_DIR}/.next"
    fi
    if [[ -d "${FRONTEND_ROLLBACK_DIR}/node_modules" ]]; then
        mv "${FRONTEND_ROLLBACK_DIR}/node_modules" "${FRONTEND_DIR}/node_modules"
    fi

    FRONTEND_SWAPPED=0

    if [[ -s "${FRONTEND_DIR}/.next/BUILD_ID" && -d "${FRONTEND_DIR}/node_modules" ]]; then
        chown -R "${WEB_USER}:${WEB_GROUP}" "${FRONTEND_DIR}/.next"
        systemctl start "${FRONTEND_SERVICE}" >/dev/null 2>&1 || true
        wait_for_http_200 "${FRONTEND_READY_URL}" "restored frontend" || true
    fi
}

cleanup() {
    local exit_code=$?
    trap - EXIT ERR INT TERM

    if [[ "${DEPLOY_SUCCESS}" -ne 1 ]]; then
        restore_frontend || true
    fi

    rm -rf "${FRONTEND_STAGE_DIR}"
    if [[ "${DEPLOY_SUCCESS}" -eq 1 ]]; then
        rm -rf "${FRONTEND_ROLLBACK_DIR}"
    fi

    exit "${exit_code}"
}

on_error() {
    local exit_code=$?
    local line_number="${1:-unknown}"
    printf 'Deployment error at line %s (exit %s).\n' "${line_number}" "${exit_code}" >&2
    show_frontend_diagnostics
    return "${exit_code}"
}

trap 'on_error ${LINENO}' ERR
trap cleanup EXIT INT TERM

render_path_file() {
    local source_file="$1"
    local destination_file="$2"

    sed \
        -e "s|/var/www/safetech/frontend|${FRONTEND_DIR}|g" \
        -e "s|/var/www/safetech/back|${BACKEND_DIR}|g" \
        -e "s|/var/www/safetech-api|${BACKEND_DIR}|g" \
        -e "s|/var/www/safetech-static|${STATIC_DIR}|g" \
        "${source_file}" > "${destination_file}"
}

install_systemd_units() {
    local frontend_tmp
    local queue_tmp

    [[ -f "${FRONTEND_UNIT_SOURCE}" ]] || fail "missing frontend unit: ${FRONTEND_UNIT_SOURCE}"
    [[ -f "${QUEUE_UNIT_SOURCE}" ]] || fail "missing queue unit: ${QUEUE_UNIT_SOURCE}"

    frontend_tmp="$(mktemp)"
    queue_tmp="$(mktemp)"
    render_path_file "${FRONTEND_UNIT_SOURCE}" "${frontend_tmp}"
    render_path_file "${QUEUE_UNIT_SOURCE}" "${queue_tmp}"

    install -o root -g root -m 0644 "${frontend_tmp}" "${SYSTEMD_DIR}/${FRONTEND_SERVICE}"
    install -o root -g root -m 0644 "${queue_tmp}" "${SYSTEMD_DIR}/${QUEUE_SERVICE}"
    rm -f "${frontend_tmp}" "${queue_tmp}"

    systemctl daemon-reload
    systemctl enable "${FRONTEND_SERVICE}" "${QUEUE_SERVICE}" >/dev/null

    [[ "$(systemctl show "${FRONTEND_SERVICE}" -p WorkingDirectory --value)" == "${FRONTEND_DIR}" ]] \
        || fail "frontend systemd WorkingDirectory does not match ${FRONTEND_DIR}"
    [[ "$(systemctl show "${QUEUE_SERVICE}" -p WorkingDirectory --value)" == "${BACKEND_DIR}" ]] \
        || fail "queue systemd WorkingDirectory does not match ${BACKEND_DIR}"
}

install_nginx_config() {
    local rendered_config
    local backup_config=""

    command -v nginx >/dev/null 2>&1 || return 0
    [[ -f "${NGINX_CONFIG_SOURCE}" ]] || fail "missing Nginx config: ${NGINX_CONFIG_SOURCE}"

    rendered_config="$(mktemp)"
    render_path_file "${NGINX_CONFIG_SOURCE}" "${rendered_config}"

    if [[ -f "${NGINX_CONFIG_TARGET}" ]]; then
        backup_config="${NGINX_CONFIG_TARGET}.pre-deploy"
        cp -a "${NGINX_CONFIG_TARGET}" "${backup_config}"
    fi

    install -o root -g root -m 0644 "${rendered_config}" "${NGINX_CONFIG_TARGET}"
    rm -f "${rendered_config}"
    ln -sfn "${NGINX_CONFIG_TARGET}" "${NGINX_CONFIG_LINK}"

    if ! nginx -t; then
        if [[ -n "${backup_config}" && -f "${backup_config}" ]]; then
            mv -f "${backup_config}" "${NGINX_CONFIG_TARGET}"
        else
            rm -f "${NGINX_CONFIG_TARGET}"
        fi
        nginx -t || true
        fail "new Nginx configuration is invalid; previous configuration restored"
    fi

    rm -f "${backup_config}"
}

clear_nginx_cache() {
    [[ -d "${NGINX_CACHE_DIR}" ]] || return 0

    case "${NGINX_CACHE_DIR}" in
        /var/cache/nginx/*)
            find "${NGINX_CACHE_DIR}" -mindepth 1 -delete
            ;;
        *)
            fail "refusing to clear unexpected Nginx cache path: ${NGINX_CACHE_DIR}"
            ;;
    esac
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

[[ "${EUID}" -eq 0 ]] || fail "run this script with sudo/root privileges"

required_commands=(git php composer node npm systemctl curl rsync find grep sort install chown chmod timeout nice flock ss fuser sleep journalctl runuser sed mktemp cp mv rm head nginx)
for command_name in "${required_commands[@]}"; do
    command -v "${command_name}" >/dev/null 2>&1 \
        || fail "missing required command: ${command_name}"
done

[[ "${FRONTEND_PORT}" =~ ^[0-9]+$ ]] || fail "SAFETECH_FRONTEND_PORT must be numeric"
[[ "${FRONTEND_READY_ATTEMPTS}" =~ ^[1-9][0-9]*$ ]] || fail "readiness attempts must be positive"
[[ "${FRONTEND_READY_DELAY}" =~ ^[1-9][0-9]*$ ]] || fail "readiness delay must be positive"
[[ "${PROJECT_DIR}" == /* && "${PROJECT_DIR}" != "/" ]] || fail "project path must be absolute"
[[ "${STATIC_DIR}" == /* && "${STATIC_DIR}" != "/" ]] || fail "static path must be absolute"
[[ "${STAGE_ROOT}" == "${PROJECT_DIR}"/* ]] || fail "stage path must be inside the project directory"

install -d -o root -g root -m 0755 "$(dirname -- "${DEPLOY_LOCK_FILE}")"
exec 9>"${DEPLOY_LOCK_FILE}"
flock -n 9 || fail "another SafeTech deployment is already running"

[[ -d "${PROJECT_DIR}/.git" ]] || fail "Git checkout not found: ${PROJECT_DIR}"
[[ -f "${BACKEND_DIR}/artisan" ]] || fail "Laravel application not found: ${BACKEND_DIR}"
[[ -f "${BACKEND_DIR}/.env" ]] || fail "Backend .env not found: ${BACKEND_DIR}/.env"
[[ -f "${FRONTEND_DIR}/package.json" ]] || fail "Next.js application not found: ${FRONTEND_DIR}"
[[ -f "${FRONTEND_DIR}/package-lock.json" ]] || fail "frontend package-lock.json not found"
[[ -f "${FRONTEND_DIR}/.env.production" ]] || fail "frontend .env.production not found"

if [[ -n "$(git -C "${PROJECT_DIR}" status --porcelain)" ]]; then
    git -C "${PROJECT_DIR}" status --short >&2
    fail "the production checkout contains uncommitted changes"
fi

log "Updating ${REMOTE}/${BRANCH}"
git -C "${PROJECT_DIR}" fetch --prune "${REMOTE}"
git -C "${PROJECT_DIR}" checkout "${BRANCH}"
git -C "${PROJECT_DIR}" pull --ff-only "${REMOTE}" "${BRANCH}"

log "Installing current systemd service definitions"
install_systemd_units

log "Installing and validating backend dependencies"
composer --working-dir="${BACKEND_DIR}" install \
    --no-dev --no-interaction --prefer-dist --optimize-autoloader
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

log "Preparing isolated frontend build"
rm -rf "${FRONTEND_STAGE_DIR}" "${FRONTEND_ROLLBACK_DIR}"
install -d -o root -g root -m 0755 "${FRONTEND_STAGE_DIR}" "${FRONTEND_ROLLBACK_DIR}"
rsync -a --delete \
    --exclude='.next' \
    --exclude='node_modules' \
    --exclude='.eslintcache' \
    --exclude='.deploy' \
    "${FRONTEND_DIR}/" "${FRONTEND_STAGE_DIR}/"

log "Installing staged frontend dependencies"
npm --prefix "${FRONTEND_STAGE_DIR}" ci --no-audit --no-fund
npm --prefix "${FRONTEND_STAGE_DIR}" audit --omit=dev --audit-level=high

log "Linting staged frontend"
(
    cd "${FRONTEND_STAGE_DIR}"
    timeout 20m ./node_modules/.bin/eslint . \
        --ignore-pattern '.next/**' \
        --ignore-pattern 'node_modules/**' \
        --ignore-pattern 'out/**' \
        --ignore-pattern 'dist/**' \
        --ignore-pattern 'coverage/**'
)

log "Type-checking staged frontend"
timeout 20m npm --prefix "${FRONTEND_STAGE_DIR}" run typecheck

log "Building staged frontend with Webpack"
NEXT_TELEMETRY_DISABLED=1 \
NODE_OPTIONS="${NODE_OPTIONS:---max-old-space-size=1536}" \
nice -n 10 npm --prefix "${FRONTEND_STAGE_DIR}" run build -- --webpack

npm --prefix "${FRONTEND_STAGE_DIR}" prune \
    --omit=dev --no-package-lock --no-audit --no-fund

[[ -s "${FRONTEND_STAGE_DIR}/.next/BUILD_ID" ]] \
    || fail "staged Next.js build did not create .next/BUILD_ID"
[[ -d "${FRONTEND_STAGE_DIR}/.next/static" ]] \
    || fail "staged Next.js build did not create .next/static"
[[ -d "${FRONTEND_STAGE_DIR}/node_modules/next" ]] \
    || fail "staged production dependencies do not include Next.js"

log "Synchronizing immutable Next.js static assets"
install -d -o root -g root -m 0755 "${STATIC_DIR}"
rsync -a --checksum "${FRONTEND_STAGE_DIR}/.next/static/" "${STATIC_DIR}/"
static_sync_diff="$(rsync -a --checksum --dry-run --itemize-changes \
    "${FRONTEND_STAGE_DIR}/.next/static/" "${STATIC_DIR}/")"
[[ -z "${static_sync_diff}" ]] || {
    printf '%s\n' "${static_sync_diff}" >&2
    fail "Next.js static asset synchronization is incomplete"
}
chown -R root:root "${STATIC_DIR}"
find "${STATIC_DIR}" -type d -exec chmod 0755 {} +
find "${STATIC_DIR}" -type f -exec chmod 0644 {} +
find "${STATIC_DIR}" -type f -mtime +30 -delete
find "${STATIC_DIR}" -mindepth 1 -type d -empty -delete

log "Atomically activating the staged frontend"
systemctl stop "${FRONTEND_SERVICE}"
release_frontend_port || fail "frontend port ${FRONTEND_PORT} is still occupied"

if [[ -d "${FRONTEND_DIR}/.next" ]]; then
    mv "${FRONTEND_DIR}/.next" "${FRONTEND_ROLLBACK_DIR}/.next"
fi
if [[ -d "${FRONTEND_DIR}/node_modules" ]]; then
    mv "${FRONTEND_DIR}/node_modules" "${FRONTEND_ROLLBACK_DIR}/node_modules"
fi

mv "${FRONTEND_STAGE_DIR}/.next" "${FRONTEND_DIR}/.next"
mv "${FRONTEND_STAGE_DIR}/node_modules" "${FRONTEND_DIR}/node_modules"
FRONTEND_SWAPPED=1

chown -R "${WEB_USER}:${WEB_GROUP}" "${FRONTEND_DIR}/.next"
find "${FRONTEND_DIR}/node_modules" -type d -exec chmod 0755 {} +
find "${FRONTEND_DIR}/node_modules" -type f -exec chmod u+rw,go+r {} +

systemctl reset-failed "${FRONTEND_SERVICE}" || true
systemctl start "${FRONTEND_SERVICE}"

if ! wait_for_http_200 "${FRONTEND_READY_URL}" "local Next.js readiness"; then
    show_frontend_diagnostics
    fail "new frontend release did not return HTTP 200"
fi

sleep 5
systemctl is-active --quiet "${FRONTEND_SERVICE}" \
    || fail "frontend service did not remain active"
frontend_port_is_listening || fail "frontend service is active but port ${FRONTEND_PORT} is not listening"

log "Restarting the queue worker"
restart_service_if_present "${QUEUE_SERVICE}"

log "Installing and validating Nginx configuration"
install_nginx_config
clear_nginx_cache
systemctl reload nginx

log "Running production smoke checks"
wait_for_http_200 "${API_URL%/}/api/health" "Laravel API health" \
    || fail "Laravel API health check failed"
wait_for_http_200 "${SITE_URL%/}/?deploy_check=$(<"${FRONTEND_DIR}/.next/BUILD_ID")" "public homepage" \
    || fail "public homepage check failed"

runuser -u "${WEB_USER}" -- php "${BACKEND_DIR}/artisan" cms:upload-smoke \
    --check-nginx-runtime --http-base-url="${API_URL%/}"

for page_path in / /about /services /projects /contact; do
    wait_for_http_200 "${SITE_URL%/}${page_path}" "public page ${page_path}" \
        || fail "public page failed: ${page_path}"
done

health_json="$(curl --fail --silent --show-error --location --max-time 20 \
    "${API_URL%/}/api/health")"
services_json="$(curl --fail --silent --show-error --location --max-time 20 \
    "${API_URL%/}/api/services")"
projects_json="$(curl --fail --silent --show-error --location --max-time 20 \
    "${API_URL%/}/api/projects")"
for api_payload in "${health_json}" "${services_json}" "${projects_json}"; do
    php -r 'json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);' \
        <<< "${api_payload}" || fail "a required production API returned invalid JSON"
done

home_html="$(curl --fail --silent --show-error --compressed --location \
    --max-time 20 -H 'Accept: text/html' -H 'Cache-Control: no-cache' \
    "${SITE_URL%/}/?asset_check=$(<"${FRONTEND_DIR}/.next/BUILD_ID")")"
static_asset_paths="$(grep -Eo '/_next/static/[^"[:space:]]+\.(js|css)' \
    <<< "${home_html}" | sort -u || true)"
[[ -n "${static_asset_paths}" ]] || fail "homepage does not reference CSS or JavaScript assets"

while IFS= read -r static_asset_path; do
    [[ -z "${static_asset_path}" ]] && continue
    curl --fail --silent --show-error --compressed --location \
        --retry 5 --retry-all-errors --retry-delay 2 --max-time 20 \
        -H 'Cache-Control: no-cache' -o /dev/null \
        "${SITE_URL%/}${static_asset_path}" \
        || fail "static asset is unavailable: ${static_asset_path}"
done <<< "${static_asset_paths}"

log "Running Google SEO smoke checks"
SEO_BASE_URL="${SITE_URL%/}" \
NEXT_PUBLIC_SITE_URL="${SITE_URL%/}" \
node "${FRONTEND_DIR}/scripts/seo-smoke.mjs"

FRONTEND_SWAPPED=0
DEPLOY_SUCCESS=1
rm -rf "${FRONTEND_ROLLBACK_DIR}" "${FRONTEND_STAGE_DIR}"

log "Deployment completed successfully"
git -C "${PROJECT_DIR}" log -1 --oneline
