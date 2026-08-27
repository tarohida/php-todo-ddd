# Runtime version decisions

Checked on 2026-08-26 against primary project documentation.

| Component | Pinned version | Decision and source |
| --- | --- | --- |
| PHP | 8.5.9 FPM (Bookworm) | Latest stable patch in the actively supported 8.5 branch. [PHP support policy](https://www.php.net/supported-versions.php), [official image tags](https://hub.docker.com/_/php/tags?name=8.5-fpm-bookworm) |
| PostgreSQL | 18.6 | Current patch of the latest supported major. Major upgrades require dump/reload or `pg_upgrade`. [Versioning policy](https://www.postgresql.org/support/versioning/), [official image](https://hub.docker.com/_/postgres/) |
| Composer | 2.10.2 | Current stable release, pinned in both the build and tool service. [Composer releases](https://github.com/composer/composer/releases/tag/2.10.2), [official image](https://hub.docker.com/_/composer/) |
| nginx | 1.28.3 Alpine | Current stable branch rather than mainline. [Official image tags](https://hub.docker.com/_/nginx/) |
| Xdebug | 3.5.3 | Current stable release with PHP 8.5 support. [Xdebug downloads](https://xdebug.org/download), [compatibility](https://xdebug.org/docs/compat) |
| Docker Compose | Compose Specification | The obsolete top-level `version` key remains removed. [Compose specification](https://docs.docker.com/compose/compose-file/) |

Exact patch tags make builds reviewable and repeatable. Updating a pin is an explicit maintenance change; it must be followed by image build, dependency install, migration, PHPUnit, and HTTP checks.

PHP dependencies are constrained to their current stable major/minor lines and resolved exactly by `composer.lock`: [Phinx](https://packagist.org/packages/robmorgan/phinx), [Slim](https://packagist.org/packages/slim/slim), [Slim PSR-7](https://packagist.org/packages/slim/psr7), [Monolog](https://packagist.org/packages/monolog/monolog), [PHP-DI](https://packagist.org/packages/php-di/php-di), [phpdotenv](https://packagist.org/packages/vlucas/phpdotenv), [PHPUnit](https://packagist.org/packages/phpunit/phpunit), and [Guzzle](https://packagist.org/packages/guzzlehttp/guzzle). `composer outdated --direct --strict` and `composer audit` are the maintenance checks.

## PostgreSQL storage migration

PostgreSQL 10 data directories cannot be mounted by PostgreSQL 18. The main Compose file therefore uses a new `postgres_data_v18` volume and mounts it at `/var/lib/postgresql`, the location required by the official PostgreSQL 18 image. The legacy volume name is deliberately not assumed: inspect the existing volume with `docker volume ls` and `docker volume inspect <name>`, then pass that exact name as `LEGACY_POSTGRES_VOLUME`. `docker-compose.migration.yml` mounts only that explicitly selected volume into PostgreSQL 10.23 and writes a custom-format logical dump using the PostgreSQL 18 client.

Run `LEGACY_POSTGRES_VOLUME=<inspected-name> scripts/migrate-postgres-10-to-18.sh` once before starting the complete stack. The script validates that the selected data directory reports PostgreSQL major 10, refuses to overwrite a dump, keeps the old volume untouched, and retains `var/backups/postgres-10.dump` for rollback. `--recreate-target` always deletes and recreates only the Compose-resolved target volume after verifying its exact name. Verify application behavior and row counts before manually removing either backup or old volume.
