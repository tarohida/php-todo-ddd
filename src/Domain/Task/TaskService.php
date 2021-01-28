<?php


namespace App\Domain\Task;


class TaskService
{
    /**
     * @var TaskRepository
     */
    private TaskRepository $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function exists(Task $task): bool
    {
        $task = $this->repository->find($task->id());
        return !is_null($task);
    }
}