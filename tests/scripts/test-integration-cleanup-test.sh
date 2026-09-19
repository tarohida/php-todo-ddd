#!/bin/sh
set -eu

repo_dir=$(CDPATH= cd -- "$(dirname -- "$0")/../.." && pwd)
integration_script="$repo_dir/scripts/test-integration.sh"
tmp_dir=$(mktemp -d)
trap 'rm -rf "$tmp_dir"' EXIT INT TERM

mock_bin="$tmp_dir/bin"
docker_log="$tmp_dir/docker.log"
mkdir -p "$mock_bin"

cat > "$mock_bin/docker" <<'EOF'
#!/bin/sh
set -eu

printf '%s|%s\n' "${COMPOSE_PROJECT_NAME:-unset}" "$*" >> "$MOCK_DOCKER_LOG"

case "$*" in
  *"select marker from test_database_marker"*)
    printf '%s\n' 'php-todo-ddd-integration-test'
    exit 0
    ;;
  *"information_schema.tables"*)
    printf '%s\n' '0'
    exit 0
    ;;
esac

if [ -n "${MOCK_BODY_FAIL_MATCH:-}" ]; then
  case "$*" in
    *"$MOCK_BODY_FAIL_MATCH"*)
      echo "mock body failure: $*" >&2
      exit "${MOCK_BODY_STATUS:-42}"
      ;;
  esac
fi

case "$*" in
  *" down "*)
    cleanup_status=${MOCK_CLEANUP_STATUS:-0}
    if [ "$cleanup_status" -ne 0 ]; then
      echo "mock cleanup failure: $*" >&2
      exit "$cleanup_status"
    fi
    ;;
esac

exit 0
EOF
chmod +x "$mock_bin/docker"

last_output=

run_case() {
  case_name=$1
  expected_status=$2
  shift 2
  last_output="$tmp_dir/$case_name.out"

  if "$@" >"$last_output" 2>&1; then
    actual_status=0
  else
    actual_status=$?
  fi

  if [ "$actual_status" -ne "$expected_status" ]; then
    echo "$case_name: expected status $expected_status, got $actual_status" >&2
    cat "$last_output" >&2
    exit 1
  fi
}

assert_output_contains() {
  expected=$1
  if ! grep -Fq -- "$expected" "$last_output"; then
    echo "Expected output to contain '$expected':" >&2
    cat "$last_output" >&2
    exit 1
  fi
}

assert_down_count() {
  expected=$1
  actual=$(grep -c '|compose .* down ' "$docker_log" || true)
  if [ "$actual" -ne "$expected" ]; then
    echo "Expected $expected cleanup invocations, got $actual:" >&2
    cat "$docker_log" >&2
    exit 1
  fi
}

common_path="$mock_bin:$PATH"

: > "$docker_log"
run_case successful_run 0 env PATH="$common_path" MOCK_DOCKER_LOG="$docker_log" \
  MOCK_BODY_FAIL_MATCH= MOCK_CLEANUP_STATUS=0 INTEGRATION_RUN_ID=9001 \
  "$integration_script"
assert_down_count 1
expected_cleanup='php-todo-ddd-integration-9001|compose -f docker-compose.yml -f docker-compose.integration.yml down --volumes --remove-orphans --rmi local'
if ! grep -Fxq -- "$expected_cleanup" "$docker_log"; then
  echo "Cleanup was not project-scoped with local-image removal:" >&2
  cat "$docker_log" >&2
  exit 1
fi

: > "$docker_log"
run_case cleanup_only_first 0 env PATH="$common_path" MOCK_DOCKER_LOG="$docker_log" \
  MOCK_CLEANUP_STATUS=0 INTEGRATION_RUN_ID=9002 "$integration_script" --cleanup-only
run_case cleanup_only_second 0 env PATH="$common_path" MOCK_DOCKER_LOG="$docker_log" \
  MOCK_CLEANUP_STATUS=0 INTEGRATION_RUN_ID=9002 "$integration_script" --cleanup-only
assert_down_count 2

: > "$docker_log"
run_case cleanup_only_failure 74 env PATH="$common_path" MOCK_DOCKER_LOG="$docker_log" \
  MOCK_CLEANUP_STATUS=74 INTEGRATION_RUN_ID=9003 "$integration_script" --cleanup-only
assert_output_contains 'mock cleanup failure:'
assert_output_contains 'Cleanup failed with status 74.'

: > "$docker_log"
run_case body_failure 42 env PATH="$common_path" MOCK_DOCKER_LOG="$docker_log" \
  MOCK_BODY_FAIL_MATCH='build php' MOCK_BODY_STATUS=42 MOCK_CLEANUP_STATUS=0 \
  INTEGRATION_RUN_ID=9004 "$integration_script"
assert_output_contains 'mock body failure:'
assert_down_count 1

: > "$docker_log"
run_case successful_body_cleanup_failure 74 env PATH="$common_path" \
  MOCK_DOCKER_LOG="$docker_log" MOCK_BODY_FAIL_MATCH= MOCK_CLEANUP_STATUS=74 \
  INTEGRATION_RUN_ID=9005 "$integration_script"
assert_output_contains 'mock cleanup failure:'
assert_output_contains 'Cleanup failed with status 74.'

: > "$docker_log"
run_case body_and_cleanup_failure 42 env PATH="$common_path" \
  MOCK_DOCKER_LOG="$docker_log" MOCK_BODY_FAIL_MATCH='build php' MOCK_BODY_STATUS=42 \
  MOCK_CLEANUP_STATUS=74 INTEGRATION_RUN_ID=9006 "$integration_script"
assert_output_contains 'mock body failure:'
assert_output_contains 'mock cleanup failure:'
assert_output_contains 'Cleanup failed with status 74; preserving prior status 42.'
assert_down_count 1

echo "Integration cleanup tests passed."
