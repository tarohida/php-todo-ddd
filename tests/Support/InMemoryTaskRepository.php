<?php
declare(strict_types=1);

namespace Tests\Support;

use App\Domain\Task\Exception\TaskNotFoundException;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskList;
use App\Domain\Task\TaskRepositoryInterface;

final class InMemoryTaskRepository implements TaskRepositoryInterface
{
    /** @var array<int, Task> */
    private array $tasks = [];
    private int $nextId = 1;

    public function __construct(Task ...$tasks)
    {
        foreach ($tasks as $task) {
            $this->tasks[$task->id()] = $task;
            $this->nextId = max($this->nextId, $task->id() + 1);
        }
    }

    public function list(): TaskList
    {
        return new TaskList(array_values($this->tasks));
    }

    public function save(Task $task): void
    {
        $this->tasks[$task->id()] = $task;
    }

    public function createTaskId(): TaskId
    {
        return new TaskId($this->nextId++);
    }

    public function delete(TaskId $id): void
    {
        if (!isset($this->tasks[$id->id()])) {
            throw new TaskNotFoundException();
        }
        unset($this->tasks[$id->id()]);
    }
}
