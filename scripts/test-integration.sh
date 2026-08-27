#!/bin/sh
set -eu

repository_root=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
cd "$repository_root"

run_id=$$
export COMPOSE_PROJECT_NAME="php-todo-ddd-integration-$run_id"
export DB_NAME="todo_integration_admin_$run_id"
export TEST_DB_NAME="todo_integration_${run_id}_test"
export DB_USER="todo_integration_$run_id"
export DB_PASSWORD="integration-local-only-$run_id"
export DB_HOST=db
export ALLOW_ORIGIN_URL="http://integration-frontend.test:$run_id"
export POSTGRES_VOLUME_NAME="php-todo-ddd-integration-$run_id-postgres"

compose() {
  docker compose -f docker-compose.yml -f docker-compose.integration.yml "$@"
}

cleanup() {
  compose down --volumes --remove-orphans >/dev/null 2>&1 || true
}
trap cleanup EXIT HUP INT TERM

compose up -d db
compose run --rm php php scripts/prepare-test-database.php
compose run --rm php vendor/bin/phinx migrate -e testing
compose up -d php web
compose run --rm php composer test

compose stop web php
compose run --rm php vendor/bin/phinx rollback -e testing -t 0
compose run --rm php vendor/bin/phinx migrate -e testing
compose up -d php web
compose run --rm php vendor/bin/phpunit --testsuite Integration

dev_tasks_table=$(compose exec -T db psql -U "$DB_USER" -d "$DB_NAME" -Atc \
  "select count(*) from information_schema.tables where table_schema='public' and table_name='tasks'")
if [ "$dev_tasks_table" != "0" ]; then
  echo "Development database was unexpectedly migrated or mutated." >&2
  exit 1
fi
