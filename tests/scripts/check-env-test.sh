#!/bin/sh
set -eu

repo_dir=$(CDPATH= cd -- "$(dirname -- "$0")/../.." && pwd)
script="$repo_dir/scripts/check-env.sh"
tmp_dir=$(mktemp -d)
trap 'rm -rf "$tmp_dir"' EXIT INT TERM

cp "$repo_dir/.env.example" "$tmp_dir/valid.env"
"$script" "$tmp_dir/valid.env" >/dev/null

# A fresh checkout must resolve one set of DB_* values consistently for both
# the application and PostgreSQL. Parse the rendered model without printing it,
# because it contains the test password.
docker compose --env-file "$tmp_dir/valid.env" -f "$repo_dir/docker-compose.yml" config --format json \
    | php -r '
        $config = json_decode(stream_get_contents(STDIN), true, flags: JSON_THROW_ON_ERROR);
        $php = $config["services"]["php"]["environment"];
        $db = $config["services"]["db"]["environment"];
        $pairs = ["DB_NAME" => "POSTGRES_DB", "DB_USER" => "POSTGRES_USER", "DB_PASSWORD" => "POSTGRES_PASSWORD"];
        foreach ($pairs as $appKey => $postgresKey) {
            if (($php[$appKey] ?? null) !== ($db[$postgresKey] ?? null)) {
                fwrite(STDERR, "Compose DB mapping mismatch for {$appKey}.\n");
                exit(1);
            }
        }
        if (($php["DB_HOST"] ?? null) !== "db") {
            fwrite(STDERR, "Compose DB_HOST must address the db service.\n");
            exit(1);
        }
    '

# The legacy format used by this repository contains only these five keys.
# Optional runtime settings must continue to receive safe Compose defaults.
sed -n '/^DB_NAME=/p;/^DB_USER=/p;/^DB_PASSWORD=/p;/^DB_HOST=/p;/^ALLOW_ORIGIN_URL=/p' \
    "$tmp_dir/valid.env" > "$tmp_dir/legacy.env"
"$script" "$tmp_dir/legacy.env" >/dev/null
docker compose --env-file "$tmp_dir/legacy.env" -f "$repo_dir/docker-compose.yml" config --quiet

assert_fails_with() {
    expected=$1
    env_file=$2
    if output=$("$script" "$env_file" 2>&1); then
        echo "Expected preflight to fail for $env_file" >&2
        exit 1
    fi
    case "$output" in
        *"$expected"*) ;;
        *) echo "Expected '$expected', got: $output" >&2; exit 1 ;;
    esac
}

sed '/^DB_PASSWORD=/d' "$tmp_dir/valid.env" > "$tmp_dir/missing.env"
assert_fails_with "Missing keys: DB_PASSWORD" "$tmp_dir/missing.env"

sed 's/^DB_USER=.*/DB_USER=/' "$tmp_dir/valid.env" > "$tmp_dir/empty.env"
assert_fails_with "Empty keys: DB_USER" "$tmp_dir/empty.env"

cp "$tmp_dir/valid.env" "$tmp_dir/duplicate.env"
printf '%s\n' 'DB_HOST=duplicate.example' >> "$tmp_dir/duplicate.env"
assert_fails_with "Duplicate keys: DB_HOST" "$tmp_dir/duplicate.env"

cp "$tmp_dir/valid.env" "$tmp_dir/obsolete.env"
printf '%s\n' 'POSTGRES_USER=obsolete' >> "$tmp_dir/obsolete.env"
assert_fails_with "Obsolete keys: POSTGRES_USER" "$tmp_dir/obsolete.env"

assert_fails_with "Missing environment file" "$tmp_dir/absent.env"

echo "Environment preflight tests passed."
