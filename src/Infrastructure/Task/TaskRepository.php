<?php
declare(strict_types=1);

namespace App\Infrastructure\Task;

class TaskRepository
{
    public function __construct(
        private \PDO $pdo
    ) {
    }
}