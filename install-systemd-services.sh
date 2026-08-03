#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="${SAFETECH_PROJECT_DIR:-${SCRIPT_DIR}}"
FRONTEND_UNIT_SOURCE="${PROJECT_DIR}/frontend/deploy/systemd/safetech-frontend.service"
QUEUE_UNIT_SOURCE="${PROJECT_DIR}/frontend/deploy/systemd/safetech-queue.service"
SYSTEMD_DIR="/etc/systemd/system"

fail() {
    printf 'Systemd installation failed: %s\n' "$*" >&2
    exit 1
}

[[ "${EUID}" -eq 0 ]] || fail "run this script with sudo/root privileges"
[[ -f "${FRONTEND_UNIT_SOURCE}" ]] || fail "missing frontend unit: ${FRONTEND_UNIT_SOURCE}"
[[ -f "${QUEUE_UNIT_SOURCE}" ]] || fail "missing queue unit: ${QUEUE_UNIT_SOURCE}"
[[ -s "${PROJECT_DIR}/frontend/.next/BUILD_ID" ]] || fail "frontend production build is missing"
[[ -f "${PROJECT_DIR}/back/artisan" ]] || fail "Laravel backend is missing"

install -o root -g root -m 0644 \
    "${FRONTEND_UNIT_SOURCE}" \
    "${SYSTEMD_DIR}/safetech-frontend.service"
install -o root -g root -m 0644 \
    "${QUEUE_UNIT_SOURCE}" \
    "${SYSTEMD_DIR}/safetech-queue.service"

systemctl daemon-reload
systemctl enable safetech-frontend.service safetech-queue.service
systemctl restart safetech-frontend.service
systemctl restart safetech-queue.service

systemctl is-active --quiet safetech-frontend.service \
    || fail "safetech-frontend.service did not become active"
systemctl is-active --quiet safetech-queue.service \
    || fail "safetech-queue.service did not become active"

for attempt in $(seq 1 30); do
    status="$(curl --silent --output /tmp/safetech-systemd-home.html \
        --write-out '%{http_code}' --max-time 10 \
        http://127.0.0.1:3000/ || true)"

    if [[ "${status}" == "200" ]]; then
        printf 'SafeTech systemd services installed successfully. Frontend HTTP 200.\n'
        exit 0
    fi

    sleep 2
done

systemctl status safetech-frontend.service --no-pager -l >&2 || true
journalctl -u safetech-frontend.service --no-pager -n 150 >&2 || true
printf '%s\n' '--- Frontend response body ---' >&2
head -c 5000 /tmp/safetech-systemd-home.html >&2 || true
printf '\n' >&2
fail "frontend did not return HTTP 200"
