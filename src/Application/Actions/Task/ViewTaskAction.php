<?php


namespace App\Application\Actions\Task;


use App\Application\Actions\ActionInterface;
use App\Application\DTO\Task\TaskData;
use App\Application\Exception\HttpNotFoundException;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskService;

class ViewTaskAction implements ActionInterface
{
    private TaskRepository $task_repository;
    private TaskService $task_service;

    /**
     * ViewTaskAction constructor.
     * @param $task_repository
     * @param TaskService $task_service
     */
    public function __construct(TaskRepository $task_repository, TaskService $task_service)
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