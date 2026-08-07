#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
API_URL="${SAFETECH_API_URL:-https://api.safetech.ge}"

bash "${SCRIPT_DIR}/scripts/repair-backend-runtime.sh"
bash "${SCRIPT_DIR}/scripts/deploy-production.sh" "$@"
bash "${SCRIPT_DIR}/scripts/repair-backend-runtime.sh"

admin_status="$(curl --silent --show-error --location --output /tmp/safetech-admin-login.html \
    --write-out '%{http_code}' --max-time 20 "${API_URL%/}/admin/login" || true)"

if [[ "${admin_status}" != "200" ]]; then
    printf 'Filament admin login smoke test failed with HTTP %s.\n' "${admin_status:-unreachable}" >&2
    head -c 5000 /tmp/safetech-admin-login.html >&2 || true
    printf '\n' >&2
    exit 1
fi

rm -f /tmp/safetech-admin-login.html
printf 'Filament admin login smoke test passed.\n'
