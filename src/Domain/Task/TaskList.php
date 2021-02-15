<?php


namespace App\Domain\Task;


use Iterator;

class TaskList implements Iterator
{
    private int $position;
    private array $task_list;

    public function __construct(array $task_array)
    {
        $this->position = 0;
        foreach ($task_array as $task)
        {
            $this->add($task);
        }
    }

    private function add(Task $task)
    {
        $this->task_list[] = $task;
    }

    public function current(): Task
    {
        return $this->task_list[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->task_list[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }
}