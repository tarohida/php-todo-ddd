#!/bin/sh
set -eu

case "${TEST_DB_NAME:-}" in
  *_test) ;;
  *)
    echo "TEST_DB_NAME must end in _test" >&2
    exit 1
    ;;
esac

if [ "$TEST_DB_NAME" = "$POSTGRES_DB" ]; then
  echo "TEST_DB_NAME must differ from POSTGRES_DB" >&2
  exit 1
fi

database_exists=$(psql --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" \
  --tuples-only --no-align --set test_db_name="$TEST_DB_NAME" <<'SQL'
SELECT 1 FROM pg_database WHERE datname = :'test_db_name';
SQL
)

if [ "$database_exists" = "1" ]; then
  marker=$(psql --username "$POSTGRES_USER" --dbname "$TEST_DB_NAME" \
    --tuples-only --no-align --command="SELECT marker FROM test_database_marker WHERE marker = 'php-todo-ddd-integration-test'" \
    2>/dev/null || true)
  if [ "$marker" != "php-todo-ddd-integration-test" ]; then
    echo "Existing TEST_DB_NAME is missing the dedicated test marker; refusing to bless it." >&2
    exit 1
  fi
  exit 0
fi

psql --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" \
  --set test_db_name="$TEST_DB_NAME" <<'SQL'
SELECT format('CREATE DATABASE %I', :'test_db_name')\gexec
SQL
psql --username "$POSTGRES_USER" --dbname "$TEST_DB_NAME" <<'SQL'
CREATE TABLE test_database_marker (
    marker varchar(100) PRIMARY KEY
);
INSERT INTO test_database_marker (marker)
VALUES ('php-todo-ddd-integration-test')
SQL
