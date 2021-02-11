<?php


namespace App\Application\Actions;


use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskService;

interface ActionInterface
{
    public function __construct(TaskRepository $task_repository, TaskService $task_service);
}