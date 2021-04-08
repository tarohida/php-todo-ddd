<?php


namespace App\Application\Actions\Task;


use App\Application\DTO\Task\TaskData;
use App\Application\DTO\Task\TaskDataList;
use App\Application\DTO\Task\TaskDataListInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskServiceInterface;

class ListTasksAction implements ListTasksActionInterface
{
    /**
     * @var TaskRepositoryInterface
     */
    private TaskRepositoryInterface $task_repository;
    /**
     * @var TaskServiceInterface
     */
    private TaskServiceInterface $task_service;

    /**
     * ViewListAction constructor.
     * @param $task_repository
     * @param TaskServiceInterface $task_service
     */
    public function __construct(TaskRepositoryInterface $task_repository, TaskServiceInterface $task_service)
    {
        $this->task_repository = $task_repository;
        $this->task_service = $task_service;
    }

    public function action(): TaskDataListInterface
    {
        $task_list = $this->task_repository->list();
        $task_data_list = [];
        foreach ($task_list as $task) {
            $task_data_list[] = new TaskData($task);
        }
        return new TaskDataList($task_data_list);
    }
}