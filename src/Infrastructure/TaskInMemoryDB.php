<?php
declare(strict_types=1);

namespace Tarohida\PhpTodoDdd\Infrastructure;

use Tarohida\PhpTodoDdd\Domain\Exception\TaskNotFoundException;
use Tarohida\PhpTodoDdd\Domain\Task;
use Tarohida\PhpTodoDdd\Domain\TaskRepository;

class TaskInMemoryDB implements TaskRepository
{
    /**
     * @param array<Task> $tasks
     */
    public function __construct(
        private array $tasks = []
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function save(Task $task): void
    {
        $this->tasks[] = $task;
    }

    /**
     * @inheritDoc
     */
    public function delete(int $task_id): void
    {
        $task_key = $this->findTaskKeyBy($task_id);
        if (is_null($task_key)) {
            return;
        }
        unset($this->tasks[$task_key]);
    }

    /**
     * @param int $task_id
     * @return int|string|null
     */
    private function findTaskKeyBy(int $task_id): string|int|null
    {
        $task_key = null;
        foreach ($this->tasks as $key => $task) {
            if ($task->getId() === $task_id) {
                $task_key = $key;
            }
        }
        return $task_key;
    }

    /**
     * @inheritDoc
     */
    public function find(int $task_id): Task
    {
        $task_key = $this->findTaskKeyBy($task_id);
        if (is_null($task_key)) {
            throw new TaskNotFoundException();
        }
        return $this->tasks[$task_key];
    }

    /**
     * @inheritDoc
     */
    public function list(): array
    {
        return $this->tasks;
    }
}
