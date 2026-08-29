<?php
declare(strict_types=1);

namespace App\Application\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskCompleted;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;

class UpdateTaskCompletion
{
    public function __construct(private TaskRepositoryInterface $repository)
    {
    }

    public function execute(TaskId $id, TaskCompleted $completed): Task
    {
        return $this->repository->updateCompletion($id, $completed);
    }
}
