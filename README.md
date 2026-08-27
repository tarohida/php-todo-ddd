# php-todo-ddd

todo アプリをひたすら作ってイテレーションを回すという実験をするリポジトリです。バックエンドは DDD の境界を保ち、振る舞いをテストで固定してから最小限の実装を行います。

## Docker 開発環境

このリポジトリだけで Web API、PHP、PostgreSQL を起動できます。フロントエンドとの統合環境はフロントエンドリポジトリ側で管理します。

```bash
test -f .env || cp .env.example .env
scripts/check-env.sh
docker compose build php
docker compose run --rm --no-deps php composer install
docker compose up -d --build
docker compose exec php composer migrate
```

- Web API: http://localhost:8081/tasks
- PostgreSQL はホストへ公開せず、Compose ネットワーク内の `db:5432` で利用します。

## Web API 契約

認証なしのAPIをJSONで提供します。JSON本文がある正常時と異常時の
`Content-Type` は `application/json` です。`DELETE` 成功時は本文がないため、
`Content-Type` も付与しません。

| Method | Path | Request | Success |
| --- | --- | --- | --- |
| `GET` | `/tasks` | なし | `200`、`[{"id":1,"title":"..."}]` |
| `POST` | `/tasks` | `{"title":"..."}` | `201`、`{"task":{"id":1,"title":"..."}}` |
| `DELETE` | `/tasks/{id}` | なし | `204` |

空または不正な `title` と不正なIDは `400`、存在しないIDは `404` を返し、
本文は `{"error":{"status":400,"message":"Bad Request"}}` 形式です。
IDは先頭ゼロのない正の10進整数だけを受理し、小数、指数表記、空白、0、
PHP整数範囲を超える値は不正です。
ブラウザ向けCORSは `ALLOW_ORIGIN_URL` のoriginだけを応答ヘッダーへ設定します。

従来の `POST /tasks/create` は既存クライアント移行用の互換エイリアスとして
当面維持します。フォーム入力も受け付けますが、レスポンスは標準の `POST /tasks`
と同じ `201` JSON契約です。新規実装は `/tasks` とJSON入力を使用してください。
このイテレーションに認証、完了状態、編集、ページングは含みません。

API の公開先は安全のため既定で `127.0.0.1` です。LAN 等から接続する必要がある場合だけ `.env` で `BACKEND_BIND_ADDRESS=0.0.0.0` とし、OS のファイアウォールも設定してください。`BACKEND_PORT` でポートを変更できます。`ALLOW_ORIGIN_URL` にはブラウザで開くフロントエンドのURLを設定します。`.env` はコミットせず、秘密情報を `.env.example` に追加しないでください。

`.env` の `DB_NAME`、`DB_USER`、`DB_PASSWORD` がPHP実行環境とPostgreSQL初期化の唯一の設定元です。従来の `DB_NAME`、`DB_USER`、`DB_PASSWORD`、`DB_HOST`、`ALLOW_ORIGIN_URL` を持つ `.env` はそのまま利用できます。起動前に `scripts/check-env.sh` を実行してください。不足・空・重複キー、または廃止した `POSTGRES_DB`、`POSTGRES_USER`、`POSTGRES_PASSWORD` がある場合、値を表示せずキー名だけを報告します。

既存 `.env` の更新ではファイルを上書きしません。まず退避し、例との差分をキー名だけで確認してから不足キーを手動で追加します。既存の `DB_*` の値は保持し、`POSTGRES_*` があれば同じ値を対応する `DB_*` へ移して `POSTGRES_*` 行を削除します。

```bash
env_backup=$(mktemp /tmp/php-todo-ddd.env.backup.XXXXXX)
chmod 600 "$env_backup"
cp .env "$env_backup"
comm -3 \
  <(sed -n 's/^\([A-Za-z_][A-Za-z0-9_]*\)=.*/\1/p' .env.example | sort) \
  <(sed -n 's/^\([A-Za-z_][A-Za-z0-9_]*\)=.*/\1/p' .env | sort)
scripts/check-env.sh
```

上記の `<(...)` は Bash/Zsh 用です。退避先は `echo "$env_backup"` で確認できます。バックアップも秘密情報として扱い、確認後は安全な場所へ移すか不要なら削除してください。

Xdebug は通常起動では無効です。一時的に使う場合は `XDEBUG_MODE=debug,develop` を設定し、`XDEBUG_TRIGGER=1` をリクエストへ渡します。接続先は既定で `host.docker.internal:9003`、クライアント自動検出は無効です。

## サポート対象の実行環境

バックエンドは PHP 8.5、PostgreSQL 18、Composer 2.10、nginx 1.28 stable、Xdebug 3.5 に固定しています。選定根拠と一次資料は [docs/runtime-versions.md](docs/runtime-versions.md) を参照してください。

### PostgreSQL 10 からの移行

既存の PostgreSQL 10 volume がある場合だけ、完全なスタックを起動する前に論理 dump/restore を実行します。スクリプトはvolume名を推測しません。まず旧コンテナとmountを調べ、`/var/lib/postgresql/data` の `Name` を選びます。

```bash
docker ps -a --filter ancestor=postgres:10 --format '{{.ID}} {{.Names}}'
docker inspect <旧コンテナID> --format '{{range .Mounts}}{{println .Destination .Name .Source}}{{end}}'
docker volume inspect <確認したvolume名>
```

移行を実行します。

```bash
LEGACY_POSTGRES_VOLUME=<確認したvolume名> scripts/migrate-postgres-10-to-18.sh
```

旧volumeが存在し、PostgreSQL data directoryを含むことを読み取り専用mountで検証してからdumpします。旧volumeは変更せず、`var/backups/postgres-10.dump` も自動削除しません。

途中失敗後は原因を直し、既存dumpを検証して再利用します。移行先にテーブルができている場合、そのvolumeだけを明示的に再作成します。この操作は移行先データを削除するため、volume名を再確認してください。

```bash
LEGACY_POSTGRES_VOLUME=<確認したvolume名> scripts/migrate-postgres-10-to-18.sh --reuse-dump
LEGACY_POSTGRES_VOLUME=<確認したvolume名> scripts/migrate-postgres-10-to-18.sh --reuse-dump --recreate-target
```

これは旧環境へ自動的に切り戻す仕組みではなく、**復旧用資産**を保持する手順です。復旧する場合は、(1) APIを停止、(2) `var/backups/postgres-10.dump` と旧volumeを保持、(3) 空の移行先へ上記 `--reuse-dump --recreate-target` で再復元、(4) `composer migrate`、(5) 行数とAPIを確認してからAPIを再開します。dumpを作り直す場合は既存dumpを別名へ移し、旧volumeを明示して通常コマンドを再実行します。検証完了まで旧volumeとdumpを削除しないでください。新規環境ではこの手順は不要です。

## 検証コマンド

```bash
docker compose config
tests/scripts/check-env-test.sh
docker compose exec php composer test
docker compose down
```

DBデータも初期化する場合だけ `docker compose down --volumes` を使用します。
