<?php


namespace App\Application\Actions\Task;


use App\Application\Actions\ActionInterface;
use App\Application\DTO\Task\TaskData;
use App\Application\DTO\Task\TaskDataList;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskService;

class ListTaskAction implements ActionInterface
{
    /**
     * @var TaskRepository
     */
    private TaskRepository $task_repository;
    /**
     * @var TaskService
     */
    private TaskService $task_service;

    /**
     * ViewListAction constructor.
     * @param $task_repository
     * @param TaskService $task_service
     */
    public function __construct(TaskRepository $task_repository, TaskService $task_service)
    {
        $this->task_repository = $task_repository;
        $this->task_service = $task_service;
    }

    public function action(): TaskDataList
    {
        $task_list = $this->task_repository->list();
        $task_data_list = [];
        foreach ($task_list as $task) {
            $task_data_list[] = new TaskData($task);
        }
        return new TaskDataList($task_data_list);
    }
}