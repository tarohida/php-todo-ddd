<?php
declare(strict_types=1);

namespace App\Domain\Task;

class TaskList implements \Iterator
{
    private array $tasks;
    private int $position;

    public function __construct(array $tasks)
    {
        foreach ($tasks as $task) {
            if (!($task instanceof Task)) {
                throw new \TypeError('Not task element not allowed');
            }
        }
        $this->tasks = $tasks;
        $this->position = 0;
    }

    public function current(): Task
    {
        return $this->tasks[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->tasks[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}