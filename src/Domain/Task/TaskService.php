<?php


namespace App\Domain\Task;


class TaskService implements TaskServiceInterface
{
    /**
     * @var TaskRepositoryInterface
     */
    private TaskRepositoryInterface $repository;

    public function __construct(TaskRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param TaskInterface $task
     * @return bool
     */
    public function exists(TaskInterface $task): bool
    {
        $task = $this->repository->find($task->id());
        return !is_null($task);
    }
}