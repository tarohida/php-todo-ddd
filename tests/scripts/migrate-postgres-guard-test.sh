#!/bin/sh
set -eu

repo_dir=$(CDPATH= cd -- "$(dirname -- "$0")/../.." && pwd)
script="$repo_dir/scripts/migrate-postgres-10-to-18.sh"
tmp_dir=$(mktemp -d)
trap 'rm -rf "$tmp_dir"' EXIT INT TERM
cp "$repo_dir/.env.example" "$tmp_dir/.env"
mkdir -p "$tmp_dir/scripts"
cp "$script" "$tmp_dir/scripts/"

assert_fails_with() {
    expected=$1
    shift
    if output=$(cd "$tmp_dir" && "$@" 2>&1); then
        echo "Expected command to fail: $*" >&2
        exit 1
    fi
    case "$output" in
        *"$expected"*) ;;
        *) echo "Expected '$expected', got: $output" >&2; exit 1 ;;
    esac
}

assert_fails_with "LEGACY_POSTGRES_VOLUME is required" "$tmp_dir/scripts/migrate-postgres-10-to-18.sh"

mkdir -p "$tmp_dir/bin"
cat > "$tmp_dir/bin/docker" <<'EOF'
#!/bin/sh
if [ "$1 $2" = "volume inspect" ]; then
    exit 1
fi
exit 99
EOF
chmod +x "$tmp_dir/bin/docker"
assert_fails_with "Legacy volume does not exist: definitely-missing" env \
    PATH="$tmp_dir/bin:$PATH" LEGACY_POSTGRES_VOLUME=definitely-missing \
    "$tmp_dir/scripts/migrate-postgres-10-to-18.sh"

assert_fails_with "Legacy and target volume names must differ" env \
    LEGACY_POSTGRES_VOLUME=php-todo-ddd-postgres-data-v18 \
    "$tmp_dir/scripts/migrate-postgres-10-to-18.sh"

cat > "$tmp_dir/bin/docker" <<'EOF'
#!/bin/sh
case "$1 $2" in
    "volume inspect"|"run --rm") exit 0 ;;
esac
exit 99
EOF
chmod +x "$tmp_dir/bin/docker"
mkdir -p "$tmp_dir/var/backups"
: > "$tmp_dir/var/backups/postgres-10.dump"
assert_fails_with "Backup already exists" env PATH="$tmp_dir/bin:$PATH" \
    LEGACY_POSTGRES_VOLUME=verified-legacy "$tmp_dir/scripts/migrate-postgres-10-to-18.sh"
rm "$tmp_dir/var/backups/postgres-10.dump"
assert_fails_with "--reuse-dump was requested but the backup does not exist" env PATH="$tmp_dir/bin:$PATH" \
    LEGACY_POSTGRES_VOLUME=verified-legacy "$tmp_dir/scripts/migrate-postgres-10-to-18.sh" --reuse-dump

# --recreate-target must reset the verified Compose volume even when the
# database reports zero tables (a failed restore may leave non-table objects).
printf 'validated dump\n' > "$tmp_dir/var/backups/postgres-10.dump"
docker_log="$tmp_dir/docker.log"
cat > "$tmp_dir/bin/docker" <<'EOF'
#!/bin/sh
printf '%s\n' "$*" >> "$DOCKER_LOG"
case "$1 $2" in
    "volume inspect"|"run --rm") exit 0 ;;
    "inspect --format") printf '%s\n' "${MOCK_RESOLVED_VOLUME:-php-todo-ddd-postgres-data-v18}"; exit 0 ;;
    "volume rm") exit 0 ;;
    "compose up"|"compose stop"|"compose rm") exit 0 ;;
    "compose ps") printf '%s\n' mock-db-container; exit 0 ;;
    "compose exec")
        case "$*" in
            *"SELECT count(*) FROM pg_catalog.pg_class"*) printf '0\n' ;;
        esac
        exit 0
        ;;
esac
exit 99
EOF
chmod +x "$tmp_dir/bin/docker"
env PATH="$tmp_dir/bin:$PATH" DOCKER_LOG="$docker_log" \
    LEGACY_POSTGRES_VOLUME=verified-legacy \
    "$tmp_dir/scripts/migrate-postgres-10-to-18.sh" --reuse-dump --recreate-target >/dev/null
if ! grep -qx 'volume rm php-todo-ddd-postgres-data-v18' "$docker_log"; then
    echo "Expected --recreate-target to remove the verified target volume." >&2
    exit 1
fi
expected_sql="SELECT count(*) FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace WHERE n.nspname NOT IN ('pg_catalog', 'information_schema') AND n.nspname !~ '^pg_toast';"
if ! grep -Fq -- "$expected_sql" "$docker_log"; then
    echo "Expected the object-count query to retain all SQL quotes." >&2
    exit 1
fi

# A resolved mount mismatch must fail before any volume is removed.
: > "$docker_log"
assert_fails_with "Compose resolved 'unexpected-volume', expected 'php-todo-ddd-postgres-data-v18'" env \
    PATH="$tmp_dir/bin:$PATH" DOCKER_LOG="$docker_log" MOCK_RESOLVED_VOLUME=unexpected-volume \
    LEGACY_POSTGRES_VOLUME=verified-legacy \
    "$tmp_dir/scripts/migrate-postgres-10-to-18.sh" --reuse-dump --recreate-target
if grep -q '^volume rm ' "$docker_log"; then
    echo "Volume removal occurred despite a resolved target mismatch." >&2
    exit 1
fi

echo "Migration guard tests passed."
