<?php


namespace App\Application\Actions;


use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskServiceInterface;

interface ActionInterface
{
    public function __construct(TaskRepositoryInterface $task_repository, TaskServiceInterface $task_service);
}