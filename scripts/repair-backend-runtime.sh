#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
PROJECT_DIR="${SAFETECH_PROJECT_DIR:-${SCRIPT_DIR}}"
BACKEND_DIR="${SAFETECH_BACKEND_DIR:-${PROJECT_DIR}/back}"
WEB_USER="${SAFETECH_WEB_USER:-www-data}"
WEB_GROUP="${SAFETECH_WEB_GROUP:-www-data}"

fail() {
    printf 'Backend runtime repair failed: %s\n' "$*" >&2
    exit 1
}

[[ "${EUID}" -eq 0 ]] || fail "run with sudo/root privileges"
[[ -f "${BACKEND_DIR}/artisan" ]] || fail "Laravel application not found: ${BACKEND_DIR}"

runtime_dirs=(
    "${BACKEND_DIR}/storage/framework/cache/data"
    "${BACKEND_DIR}/storage/framework/sessions"
    "${BACKEND_DIR}/storage/framework/testing"
    "${BACKEND_DIR}/storage/framework/views"
    "${BACKEND_DIR}/storage/logs"
    "${BACKEND_DIR}/bootstrap/cache"
)

for runtime_dir in "${runtime_dirs[@]}"; do
    install -d -o "${WEB_USER}" -g "${WEB_GROUP}" -m 0775 "${runtime_dir}"
done

chown -R "${WEB_USER}:${WEB_GROUP}" \
    "${BACKEND_DIR}/storage" \
    "${BACKEND_DIR}/bootstrap/cache"

find "${BACKEND_DIR}/storage" "${BACKEND_DIR}/bootstrap/cache" \
    -type d -exec chmod 0775 {} +
find "${BACKEND_DIR}/storage" "${BACKEND_DIR}/bootstrap/cache" \
    -type f -exec chmod 0664 {} +

for runtime_dir in "${runtime_dirs[@]}"; do
    runuser -u "${WEB_USER}" -- test -w "${runtime_dir}" \
        || fail "${WEB_USER} cannot write to ${runtime_dir}"
done

printf 'Laravel runtime permissions verified for %s:%s.\n' "${WEB_USER}" "${WEB_GROUP}"
