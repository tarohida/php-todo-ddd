<?php
declare(strict_types=1);

use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$testDatabase = $_ENV['TEST_DB_NAME'] ?? '';
$developmentDatabase = $_ENV['DEV_DB_NAME'] ?? ($_ENV['DB_NAME'] ?? '');
if (!str_ends_with($testDatabase, '_test') || $testDatabase === $developmentDatabase) {
    fwrite(STDERR, "TEST_DB_NAME must end in _test and differ from DB_NAME.\n");
    exit(1);
}

$pdo = new PDO(
    sprintf('pgsql:host=%s;port=5432;dbname=%s', $_ENV['DB_HOST'], $developmentDatabase),
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);
$statement = $pdo->prepare('select 1 from pg_database where datname = :name');
$statement->execute(['name' => $testDatabase]);
$databaseExists = $statement->fetchColumn() !== false;
if (!$databaseExists) {
    $quotedDatabase = '"' . str_replace('"', '""', $testDatabase) . '"';
    $pdo->exec("create database {$quotedDatabase}");
}

$testPdo = new PDO(
    sprintf('pgsql:host=%s;port=5432;dbname=%s', $_ENV['DB_HOST'], $testDatabase),
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);
if (!$databaseExists) {
    $testPdo->exec(<<<'SQL'
create table test_database_marker (
    marker varchar(100) primary key
)
SQL);
    $marker = $testPdo->prepare('insert into test_database_marker (marker) values (:marker)');
    $marker->execute(['marker' => 'php-todo-ddd-integration-test']);
    exit(0);
}

try {
    $marker = $testPdo->prepare('select marker from test_database_marker where marker = :marker');
    $marker->execute(['marker' => 'php-todo-ddd-integration-test']);
} catch (PDOException) {
    fwrite(STDERR, "Existing TEST_DB_NAME is missing the dedicated test marker; refusing to bless it.\n");
    exit(1);
}
if ($marker->fetchColumn() !== 'php-todo-ddd-integration-test') {
    fwrite(STDERR, "Existing TEST_DB_NAME is missing the dedicated test marker; refusing to bless it.\n");
    exit(1);
}
