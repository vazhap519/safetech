#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
PROJECT_DIR="${SAFETECH_PROJECT_DIR:-${SCRIPT_DIR}}"
BACKEND_DIR="${SAFETECH_BACKEND_DIR:-${PROJECT_DIR}/back}"
FRONTEND_DIR="${SAFETECH_FRONTEND_DIR:-${PROJECT_DIR}/frontend}"
BACKEND_ENV="${BACKEND_DIR}/.env"
FRONTEND_ENV="${FRONTEND_DIR}/.env.production"

fail() {
    printf 'AI configuration failed: %s\n' "$*" >&2
    exit 1
}

[[ "${EUID}" -eq 0 ]] || fail "run with sudo/root privileges"
[[ -f "${BACKEND_ENV}" ]] || fail "backend environment file not found: ${BACKEND_ENV}"
[[ -f "${FRONTEND_ENV}" ]] || fail "frontend environment file not found: ${FRONTEND_ENV}"
[[ -f "${BACKEND_DIR}/artisan" ]] || fail "Laravel application not found: ${BACKEND_DIR}"

set_env_var() {
    local file="$1"
    local key="$2"
    local value="$3"
    local tmp
    local replaced=0
    local line

    tmp="$(mktemp "${file}.tmp.XXXXXX")"
    chown --reference="${file}" "${tmp}"
    chmod --reference="${file}" "${tmp}"

    while IFS= read -r line || [[ -n "${line}" ]]; do
        if [[ "${line}" == "${key}="* ]]; then
            printf '%s=%s\n' "${key}" "${value}" >> "${tmp}"
            replaced=1
        else
            printf '%s\n' "${line}" >> "${tmp}"
        fi
    done < "${file}"

    if [[ "${replaced}" -eq 0 ]]; then
        printf '\n%s=%s\n' "${key}" "${value}" >> "${tmp}"
    fi

    mv -f "${tmp}" "${file}"
}

printf '%s' 'Paste a NEW OpenAI API key (input is hidden): '
IFS= read -r -s OPENAI_KEY
printf '\n'

[[ "${OPENAI_KEY}" == sk-* ]] || fail "the key does not look like an OpenAI API key"
[[ "${#OPENAI_KEY}" -ge 40 ]] || fail "the key is unexpectedly short"

set_env_var "${BACKEND_ENV}" "AI_ASSISTANT_ENABLED" "true"
set_env_var "${BACKEND_ENV}" "OPENAI_API_KEY" "${OPENAI_KEY}"
set_env_var "${BACKEND_ENV}" "OPENAI_MODEL" "gpt-5.6"
set_env_var "${FRONTEND_ENV}" "NEXT_PUBLIC_AI_ASSISTANT_ENABLED" "true"

unset OPENAI_KEY

php "${BACKEND_DIR}/artisan" config:clear
php "${BACKEND_DIR}/artisan" optimize

printf '%s\n' 'SafeTech AI assistant configuration is enabled.'
printf '%s\n' 'The API key was written only to the server-side Laravel .env and was not added to Git.'
printf '%s\n' 'Run the normal production deployment now: sudo bash deploy.sh'
