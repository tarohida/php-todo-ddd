#!/bin/sh
set -eu

repository_root=$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)
cd "$repository_root"

mode=${1:-run}
case "$mode" in
  run|--cleanup-only) ;;
  *) echo "Usage: $0 [--cleanup-only]" >&2; exit 2 ;;
esac

run_id=${INTEGRATION_RUN_ID:-$$}
case "$run_id" in
  ''|*[!0-9_]*)
    echo "INTEGRATION_RUN_ID must contain only digits and underscores." >&2
    exit 2
    ;;
esac
if [ "${#run_id}" -gt 24 ]; then
  echo "INTEGRATION_RUN_ID must be at most 24 characters." >&2
  exit 2
fi

export COMPOSE_PROJECT_NAME="php-todo-ddd-integration-$run_id"
export DB_NAME="todo_integration_admin_$run_id"
export TEST_DB_NAME="todo_integration_${run_id}_test"
export DB_USER="todo_integration_$run_id"
export DB_PASSWORD="integration-local-only-$run_id"
export DB_HOST=db
export ALLOW_ORIGIN_URL="http://integration-frontend.test"
export POSTGRES_VOLUME_NAME="php-todo-ddd-integration-$run_id-postgres"

compose() {
  docker compose -f docker-compose.yml -f docker-compose.integration.yml "$@"
}

reset_test_tasks() {
  marker=$(compose exec -T db psql -U "$DB_USER" -d "$TEST_DB_NAME" -Atc \
    "select marker from test_database_marker where marker='php-todo-ddd-integration-test'")
  if [ "$marker" != "php-todo-ddd-integration-test" ]; then
    echo "Dedicated test database marker is missing; refusing to reset tasks." >&2
    exit 1
  fi
  compose exec -T db psql -U "$DB_USER" -d "$TEST_DB_NAME" -v ON_ERROR_STOP=1 \
    -c "truncate table tasks restart identity" >/dev/null
}

run_exclusive_database_test() {
  echo "== Exclusive database HTTP test: pre-reset =="
  reset_test_tasks
  compose run --rm php composer test:integration:exclusive-database
  echo "== Exclusive database HTTP test: post-reset =="
  reset_test_tasks
}

cleanup() {
  compose down --volumes --remove-orphans --rmi local
}

handle_exit() {
  body_status=$?
  trap - EXIT

  if cleanup; then
    cleanup_status=0
  else
    cleanup_status=$?
  fi

  if [ "$cleanup_status" -ne 0 ]; then
    if [ "$body_status" -ne 0 ]; then
      echo "Cleanup failed with status $cleanup_status; preserving prior status $body_status." >&2
    else
      echo "Cleanup failed with status $cleanup_status." >&2
    fi
  fi

  if [ "$body_status" -ne 0 ]; then
    exit "$body_status"
  fi
  exit "$cleanup_status"
}

if [ "$mode" = "--cleanup-only" ]; then
  if cleanup; then
    exit 0
  else
    cleanup_status=$?
    echo "Cleanup failed with status $cleanup_status." >&2
    exit "$cleanup_status"
  fi
fi

trap handle_exit EXIT
trap 'exit 129' HUP
trap 'exit 130' INT
trap 'exit 143' TERM

compose build php
compose run --rm --no-deps php composer install --prefer-dist --no-progress --no-interaction
compose up -d db
compose run --rm php php scripts/prepare-test-database.php
compose run --rm php vendor/bin/phinx migrate -e testing
compose up -d php web
compose run --rm php composer test
run_exclusive_database_test

compose stop web php
compose run --rm php vendor/bin/phinx rollback -e testing -t 0
compose run --rm php vendor/bin/phinx migrate -e testing
compose up -d php web
compose run --rm php vendor/bin/phpunit --testsuite Integration --exclude-group exclusive-database
run_exclusive_database_test

dev_tasks_table=$(compose exec -T db psql -U "$DB_USER" -d "$DB_NAME" -Atc \
  "select count(*) from information_schema.tables where table_schema='public' and table_name='tasks'")
if [ "$dev_tasks_table" != "0" ]; then
  echo "Development database was unexpectedly migrated or mutated." >&2
  exit 1
fi
