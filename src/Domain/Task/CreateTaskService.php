<?php
declare(strict_types=1);

namespace App\Domain\Task;

class CreateTaskService
{
    public function serve(TaskTitle $title): Task
    {
        $task = new Task(
            $this->repository->createTaskId(),
            $title
        );
        $this->repository->save($task);
        return $task;
    }

    public function __construct(
        private TaskRepositoryInterface $repository
    ) { }
}