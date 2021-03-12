<?php


namespace App\Application\Actions\Task;


use App\Application\Actions\ActionInterface;
use App\Application\DTO\Task\TaskData;
use App\Application\Exception\HttpNotFoundException;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskServiceInterface;

class ViewTaskAction implements ActionInterface
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
            throw new HttpNotFoundException("Specified Task Not Found." . PHP_EOL . "task id: ${id}");
        }
        return new TaskData($task);
    }
}