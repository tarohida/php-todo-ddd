<?php
declare(strict_types=1);

namespace App\Domain\Task;

interface TaskRepositoryInterface
{
    public function list(): TaskList;
    public function save(Task $task): void;
    public function createTaskId(): TaskId;
    public function delete(TaskId $id): void;
}