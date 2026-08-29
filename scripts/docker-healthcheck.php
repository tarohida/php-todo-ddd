<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = new PDO(
        sprintf('pgsql:host=%s;port=5432;dbname=%s', getenv('DB_HOST'), getenv('DB_NAME')),
        (string) getenv('DB_USER'),
        (string) getenv('DB_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
    );
    $statement = $pdo->prepare('SELECT 1');
    exit($statement->execute() ? 0 : 1);
} catch (Throwable) {
    exit(1);
}
