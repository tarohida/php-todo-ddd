<?php
declare(strict_types=1);

namespace App\Domain\Task;

interface TaskRepositoryInterface
{
    public function list(): TaskList;

    /** @throws Exception\TaskAlreadyExistsException */
    public function save(Task $task): void;
    public function createTaskId(): TaskId;
    public function find(TaskId $id): Task;
    public function updateCompletion(TaskId $id, TaskCompleted $completed): Task;
    public function delete(TaskId $id): void;
}
