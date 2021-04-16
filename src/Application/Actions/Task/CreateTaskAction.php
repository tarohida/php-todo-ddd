<?php
declare(strict_types=1);


namespace App\Application\Actions\Task;


use App\Domain\Task\TaskInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskServiceInterface;

class CreateTaskAction implements CreateTaskActionInterface
{
    private TaskRepositoryInterface $task_repository;
    private TaskServiceInterface $task_service;

    /**
     * CreateTaskAction constructor.
     * @param TaskServiceInterface $task_service
     * @param TaskRepositoryInterface $task_repository
     */
    public function __construct(TaskRepositoryInterface $task_repository, TaskServiceInterface $task_service)
    {
        $this->task_repository = $task_repository;
        $this->task_service = $task_service;
    }

    public function action(TaskInterface $task): void
    {
        $this->task_repository->save($task);
    }
}