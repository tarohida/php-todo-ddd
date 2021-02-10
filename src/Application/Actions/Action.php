<?php


namespace App\Application\Actions;


use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskService;

class Action
{
    /**
     * @var TaskService
     */
    private TaskService $task_service;
    /**
     * @var TaskRepository
     */
    private TaskRepository $task_repository;

    /**
     * Action constructor.
     * @param TaskRepository $task_repository
     * @param TaskService $task_service
     */
    public function __construct(TaskRepository $task_repository, TaskService $task_service)
    {
        $this->task_repository = $task_repository;
        $this->task_service = $task_service;
    }
}