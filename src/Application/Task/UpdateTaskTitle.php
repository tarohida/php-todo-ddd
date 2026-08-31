<?php
declare(strict_types=1);

namespace App\Application\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskTitle;

class UpdateTaskTitle
{
    public function __construct(private TaskRepositoryInterface $repository) {}

    public function execute(TaskId $id, TaskTitle $title): Task
    {
        return $this->repository->updateTitle($id, $title);
    }
}
