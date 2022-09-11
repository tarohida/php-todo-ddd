<?php
declare(strict_types=1);

namespace Tarohida\PhpTodoDdd\Domain;

use Tarohida\PhpTodoDdd\Domain\Exception\TaskNotFoundException;

interface TaskRepository
{
    public function save(Task $task): void;

    public function delete(int $task_id): void;

    /**
     * @param int $task_id
     * @return Task
     * @throws TaskNotFoundException
     */
    public function find(int $task_id): Task;

    /**
     * @return array<Task>
     */
    public function list(): array;
}
