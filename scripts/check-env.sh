#!/bin/sh
set -eu

repo_dir=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
env_file=${1:-"$repo_dir/.env"}

if [ ! -f "$env_file" ]; then
    echo "Missing environment file: $env_file" >&2
    echo "Create it with: cp .env.example .env" >&2
    exit 1
fi

required_keys='DB_NAME DB_USER DB_PASSWORD DB_HOST ALLOW_ORIGIN_URL'
obsolete_keys='POSTGRES_DB POSTGRES_USER POSTGRES_PASSWORD'
missing_keys=''
empty_keys=''
duplicate_keys=''
found_obsolete=''

for key in $required_keys; do
    count=$(awk -F= -v key="$key" '$1 == key { count++ } END { print count + 0 }' "$env_file")
    if [ "$count" -eq 0 ]; then
        missing_keys="$missing_keys $key"
    elif [ "$count" -gt 1 ]; then
        duplicate_keys="$duplicate_keys $key"
    elif ! awk -F= -v key="$key" '$1 == key { value = substr($0, index($0, "=") + 1); exit(value == "" ? 1 : 0) }' "$env_file"; then
        empty_keys="$empty_keys $key"
    fi
done

for key in $obsolete_keys; do
    if awk -F= -v key="$key" '$1 == key { found=1 } END { exit(found ? 0 : 1) }' "$env_file"; then
        found_obsolete="$found_obsolete $key"
    fi
done

if [ -n "$missing_keys$empty_keys$duplicate_keys$found_obsolete" ]; then
    echo "Environment preflight failed for $env_file (values are not shown)." >&2
    [ -z "$missing_keys" ] || echo "Missing keys:$missing_keys" >&2
    [ -z "$empty_keys" ] || echo "Empty keys:$empty_keys" >&2
    [ -z "$duplicate_keys" ] || echo "Duplicate keys:$duplicate_keys" >&2
    [ -z "$found_obsolete" ] || echo "Obsolete keys:$found_obsolete" >&2
    echo "Update the file to match .env.example; use DB_NAME, DB_USER, and DB_PASSWORD instead of POSTGRES_* keys." >&2
    exit 1
fi

echo "Environment preflight passed: required keys are present and non-empty."
