<?php
declare(strict_types=1);

use App\Infrastructure\Task\TaskRepository;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$pdo = new PDO(
    sprintf('pgsql:host=%s;port=5432;dbname=%s', $_ENV['DB_HOST'], $_ENV['TEST_DB_NAME']),
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);
echo (new TaskRepository($pdo))->createTaskId()->id();
