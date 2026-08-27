<?php
declare(strict_types=1);

namespace App\Application\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskTitle;

class CreateTask
{
    public function __construct(private TaskRepositoryInterface $repository) {}

    public function execute(TaskTitle $title): Task
    {
        $task = new Task($this->repository->createTaskId(), $title);
        $this->repository->save($task);
        return $task;
    }
}
