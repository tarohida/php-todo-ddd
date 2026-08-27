<?php
declare(strict_types=1);

namespace App\Infrastructure\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskTitle;

final class TaskRowMapper
{
    public function map(array $row): Task
    {
        return new Task(
            TaskId::createFromMixedTypeValue($row['id'] ?? null),
            TaskTitle::createFromMixedTypeValue($row['title'] ?? null),
        );
    }
}
