#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
API_URL="${SAFETECH_API_URL:-https://api.safetech.ge}"
REMOTE="${SAFETECH_REMOTE:-origin}"
BRANCH="${SAFETECH_BRANCH:-main}"
DEPLOY_SCRIPT="${SCRIPT_DIR}/scripts/deploy-production.sh"
PATCHED_DEPLOY="$(mktemp /tmp/safetech-deploy-production.XXXXXX.sh)"
DEPLOY_STATE_DIR="${SAFETECH_DEPLOY_STATE_DIR:-/var/lib/safetech}"
FRONTEND_RELEASE_MARKER="${DEPLOY_STATE_DIR}/frontend-release-commit"
SKIP_FRONTEND_BUILD=0
TARGET_SHA="${1:-${SAFETECH_TARGET_SHA:-}}"

cleanup_patched_deploy() {
    rm -f "${PATCHED_DEPLOY}"
}
trap cleanup_patched_deploy EXIT INT TERM

if [[ -n "${TARGET_SHA}" && ! "${TARGET_SHA}" =~ ^[0-9a-f]{40}$ ]]; then
    printf 'Invalid deployment SHA: %s\n' "${TARGET_SHA}" >&2
    exit 1
fi

bash "${SCRIPT_DIR}/scripts/repair-backend-runtime.sh"

# Fetch first so we can compare the next release against the last frontend that
# was actually deployed successfully. Comparing only the current checkout to
# origin/main is not sufficient after a failed frontend build because Git may
# already be at the new commit while the live .next release is still old.
git -C "${SCRIPT_DIR}" fetch --prune "${REMOTE}"
TARGET_REF="${REMOTE}/${BRANCH}"

if [[ -n "${TARGET_SHA}" ]]; then
    git -C "${SCRIPT_DIR}" cat-file -e "${TARGET_SHA}^{commit}" 2>/dev/null || {
        printf 'Requested deployment commit was not fetched: %s\n' "${TARGET_SHA}" >&2
        exit 1
    }
    git -C "${SCRIPT_DIR}" merge-base --is-ancestor "${TARGET_SHA}" "${REMOTE}/${BRANCH}" || {
        printf 'Requested deployment commit is not contained in %s/%s: %s\n' \
            "${REMOTE}" "${BRANCH}" "${TARGET_SHA}" >&2
        exit 1
    }
    TARGET_REF="${TARGET_SHA}"
    printf 'Pinned deployment target: %s\n' "${TARGET_SHA}"
fi

install -d -o root -g root -m 0755 "${DEPLOY_STATE_DIR}"

if [[ -s "${FRONTEND_RELEASE_MARKER}" ]]; then
    LAST_FRONTEND_RELEASE="$(tr -d '[:space:]' < "${FRONTEND_RELEASE_MARKER}")"

    if git -C "${SCRIPT_DIR}" cat-file -e "${LAST_FRONTEND_RELEASE}^{commit}" 2>/dev/null; then
        if git -C "${SCRIPT_DIR}" diff --quiet \
            "${LAST_FRONTEND_RELEASE}" "${TARGET_REF}" -- frontend/; then
            SKIP_FRONTEND_BUILD=1
            printf 'No frontend changes since deployed release %s; skipping Next.js rebuild.\n' \
                "${LAST_FRONTEND_RELEASE:0:12}"
        else
            printf 'Frontend changes detected since deployed release %s; Next.js rebuild required.\n' \
                "${LAST_FRONTEND_RELEASE:0:12}"
        fi
    else
        printf 'Frontend release marker is not a valid local commit; rebuilding frontend safely.\n'
    fi
else
    printf 'No frontend release marker exists yet; rebuilding frontend safely.\n'
fi

# Production deploy already performs a strict TypeScript check and a full Next.js
# production build. ESLint has repeatedly stalled on this small production host,
# so lint remains a CI/development concern. For backend-only releases, also omit
# the expensive isolated Next.js build and activation block entirely. Public
# frontend smoke checks still run against the currently active release.
#
# Next.js 16 uses Turbopack for `next build` by default. The base deployment
# script still forces `--webpack`, which is substantially slower on this small
# VPS. Rewrite only that invocation in the temporary deploy copy so production
# builds use the supported Next.js 16 default bundler without modifying the
# tracked base script.
awk -v skip_frontend="${SKIP_FRONTEND_BUILD}" '
    /log "Linting staged frontend"/ { skipping_lint = 1; next }
    /log "Type-checking staged frontend"/ { skipping_lint = 0 }

    skip_frontend == "1" && /log "Preparing isolated frontend build"/ {
        skipping_frontend = 1
        next
    }
    skip_frontend == "1" && /log "Restarting the queue worker"/ {
        skipping_frontend = 0
    }

    !skipping_lint && !skipping_frontend {
        gsub(/run build -- --webpack/, "run build")
        gsub(/Building staged frontend with Webpack/, "Building staged frontend with Turbopack")
        print
    }
' "${DEPLOY_SCRIPT}" > "${PATCHED_DEPLOY}"
chmod 0700 "${PATCHED_DEPLOY}"

SAFETECH_PROJECT_DIR="${SAFETECH_PROJECT_DIR:-${SCRIPT_DIR}}" \
SAFETECH_TARGET_SHA="${TARGET_SHA}" \
    bash "${PATCHED_DEPLOY}"

# Reaching this point means the production deploy and all of its smoke checks
# succeeded. Advancing the marker after backend-only releases is safe because we
# only skip when there were no frontend changes between the previous marker and
# the target release.
DEPLOYED_COMMIT="$(git -C "${SCRIPT_DIR}" rev-parse HEAD)"

if [[ -n "${TARGET_SHA}" && "${DEPLOYED_COMMIT}" != "${TARGET_SHA}" ]]; then
    printf 'Deployment finished on unexpected commit %s (expected %s).\n' \
        "${DEPLOYED_COMMIT}" "${TARGET_SHA}" >&2
    exit 1
fi

printf '%s\n' "${DEPLOYED_COMMIT}" > "${FRONTEND_RELEASE_MARKER}"
chmod 0644 "${FRONTEND_RELEASE_MARKER}"
printf 'Frontend release marker updated to %s.\n' "${DEPLOYED_COMMIT:0:12}"

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
