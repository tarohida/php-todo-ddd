<?php


namespace App\Application\Actions\Task;


use App\Application\DTO\Task\TaskData;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskServiceInterface;
use App\Exeption\Application\Actions\Task\SpecifiedTaskNotFoundException;

class ViewTaskAction implements ViewTaskActionInterface
{
    private TaskRepositoryInterface $task_repository;
    private TaskServiceInterface $task_service;

    /**
     * ViewTaskAction constructor.
     * @param TaskRepositoryInterface $task_repository
     * @param TaskServiceInterface $task_service
     */
    public function __construct(TaskRepositoryInterface $task_repository, TaskServiceInterface $task_service)
    {
        $this->task_repository = $task_repository;
        $this->task_service = $task_service;
    }

    public function action(int $id): TaskData
    {
        $task = $this->task_repository->find($id);
        if (is_null($task)) {
            $e = new SpecifiedTaskNotFoundException();
            $e->setTaskId($id);
            throw $e;
        }
        return new TaskData($task);
    }
}