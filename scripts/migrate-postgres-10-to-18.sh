#!/bin/sh
set -eu

usage() {
    printf '%s\n' \
        'Usage: LEGACY_POSTGRES_VOLUME=<existing-volume> scripts/migrate-postgres-10-to-18.sh [options]' \
        '' \
        '  --reuse-dump       Validate and reuse var/backups/postgres-10.dump.' \
        '  --recreate-target  Delete and recreate POSTGRES_VOLUME_NAME if the target is non-empty.' \
        '  --help             Show this help.'
}

reuse_dump=false
recreate_target=false
for option in "$@"; do
    case "$option" in
        --reuse-dump) reuse_dump=true ;;
        --recreate-target) recreate_target=true ;;
        --help) usage; exit 0 ;;
        *) echo "Unknown option: $option" >&2; usage >&2; exit 2 ;;
    esac
done

repo_dir=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
cd "$repo_dir"

if [ ! -f .env ]; then
    echo "Missing .env; copy .env.example and set local credentials first." >&2
    exit 1
fi
if [ -z "${LEGACY_POSTGRES_VOLUME:-}" ]; then
    echo "LEGACY_POSTGRES_VOLUME is required; select an existing PostgreSQL 10 volume explicitly." >&2
    exit 1
fi
configured_target=$(sed -n 's/^POSTGRES_VOLUME_NAME=//p' .env | tail -n 1)
target_volume=${POSTGRES_VOLUME_NAME:-${configured_target:-php-todo-ddd-postgres-data-v18}}
case "$target_volume" in
    ''|*[!A-Za-z0-9_.-]*) echo "POSTGRES_VOLUME_NAME contains unsupported characters." >&2; exit 1 ;;
esac
if [ "$LEGACY_POSTGRES_VOLUME" = "$target_volume" ]; then
    echo "Legacy and target volume names must differ." >&2
    exit 1
fi
if ! docker volume inspect "$LEGACY_POSTGRES_VOLUME" >/dev/null 2>&1; then
    echo "Legacy volume does not exist: $LEGACY_POSTGRES_VOLUME" >&2
    exit 1
fi
if ! docker run --rm --mount "type=volume,src=$LEGACY_POSTGRES_VOLUME,dst=/legacy,readonly" postgres:10.23-bullseye \
    sh -c 'test -s /legacy/PG_VERSION && test -d /legacy/base && test -d /legacy/global && grep -qx "10" /legacy/PG_VERSION'; then
    echo "Legacy volume is not a recognizable PostgreSQL 10 data directory." >&2
    exit 1
fi

backup_file="$repo_dir/var/backups/postgres-10.dump"
mkdir -p "$repo_dir/var/backups"
if [ -e "$backup_file" ]; then
    if [ "$reuse_dump" != true ]; then
        echo "Backup already exists. Use --reuse-dump to validate and reuse it, or move it aside to generate a new dump." >&2
        exit 1
    fi
    docker run --rm --entrypoint pg_restore -v "$repo_dir/var/backups:/backup:ro" postgres:18.6 \
        --list /backup/postgres-10.dump >/dev/null
else
    if [ "$reuse_dump" = true ]; then
        echo "--reuse-dump was requested but the backup does not exist: $backup_file" >&2
        exit 1
    fi
    cleanup_legacy() {
        LEGACY_POSTGRES_VOLUME="$LEGACY_POSTGRES_VOLUME" docker compose -f docker-compose.migration.yml down >/dev/null 2>&1 || true
    }
    trap cleanup_legacy EXIT INT TERM
    LEGACY_POSTGRES_VOLUME="$LEGACY_POSTGRES_VOLUME" docker compose -f docker-compose.migration.yml \
        up --abort-on-container-exit --exit-code-from dump dump
    test -s "$backup_file"
    docker run --rm --entrypoint pg_restore -v "$repo_dir/var/backups:/backup:ro" postgres:18.6 \
        --list /backup/postgres-10.dump >/dev/null
    cleanup_legacy
    trap - EXIT INT TERM
fi

docker compose up -d db

resolve_compose_target_volume() {
    db_container=$(docker compose ps -q db)
    if [ -z "$db_container" ]; then
        echo "Could not resolve the Compose db container." >&2
        exit 1
    fi
    docker inspect --format '{{range .Mounts}}{{if eq .Destination "/var/lib/postgresql"}}{{.Name}}{{end}}{{end}}' "$db_container"
}

resolved_target=$(resolve_compose_target_volume)
if [ "$resolved_target" != "$target_volume" ]; then
    echo "Refusing to modify target: Compose resolved '$resolved_target', expected '$target_volume'." >&2
    exit 1
fi

if [ "$recreate_target" = true ]; then
    # Reset even a partial restore containing only schemas or non-table objects.
    docker compose stop db
    docker compose rm -f db
    if docker volume inspect "$target_volume" >/dev/null 2>&1; then
        docker volume rm "$target_volume"
    fi
    docker compose up -d db
    resolved_target=$(resolve_compose_target_volume)
    if [ "$resolved_target" != "$target_volume" ]; then
        echo "Recreated Compose target '$resolved_target' does not match expected '$target_volume'." >&2
        exit 1
    fi
fi

docker compose exec -T db sh -c 'until psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" -c "SELECT 1" >/dev/null 2>&1; do sleep 1; done'
object_count=$(docker compose exec -T db sh -c \
    'psql --username="$POSTGRES_USER" --dbname="$POSTGRES_DB" --tuples-only --no-align --command="$1"' sh \
    "SELECT count(*) FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace WHERE n.nspname NOT IN ('pg_catalog', 'information_schema') AND n.nspname !~ '^pg_toast';")
if [ "$object_count" -ne 0 ]; then
    if [ "$recreate_target" != true ]; then
        echo "Target database contains user objects. Re-run with --reuse-dump --recreate-target only if replacing it is intended." >&2
        exit 1
    fi
    echo "Recreated target database unexpectedly contains user objects." >&2
    exit 1
fi

docker compose exec -T db sh -c \
    'pg_restore --username="$POSTGRES_USER" --dbname="$POSTGRES_DB" --no-owner --no-privileges --exit-on-error' \
    < "$backup_file"

echo "Restored the validated PostgreSQL 10 dump into PostgreSQL 18."
echo "Keep $backup_file and the untouched legacy volume until application and row-count checks pass."
