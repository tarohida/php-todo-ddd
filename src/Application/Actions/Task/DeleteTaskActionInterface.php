<?php
declare(strict_types=1);


namespace App\Application\Actions\Task;


use App\Domain\Task\TaskRepositoryInterface;

interface DeleteTaskActionInterface
{
    public function __construct(TaskRepositoryInterface $task_repository);

    public function action($task_id): void;
}