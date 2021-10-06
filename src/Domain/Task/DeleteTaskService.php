<?php
declare(strict_types=1);

namespace App\Domain\Task;

class DeleteTaskService
{
    public function serve(TaskId $task_id)
    {
        $this->repository->delete($task_id);
    }

    public function __construct(
        private TaskRepositoryInterface $repository
    ) { }
}