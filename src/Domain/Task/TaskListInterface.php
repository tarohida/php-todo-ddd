<?php
declare(strict_types=1);

namespace App\Domain\Task;

use Iterator;

interface TaskListInterface extends Iterator
{
    public function __construct(array $task_array);
}