<?php
declare(strict_types=1);


namespace App\Domain\Task;


/**
 * Class TaskIterator
 * @package App\Domain\Task
 */
class TaskIterator implements TaskIteratorInterface
{
    private array $tasks;
    private int $position;

    public function __construct(array $task_array)
    {
        $this->position = 0;
        $this->tasks = [];
        foreach ($task_array as $task) {
            $this->add($task);
        }
    }

    /**
     * @param TaskInterface $task
     */
    private function add(TaskInterface $task)
    {
        $this->tasks[] = $task;
    }

    public function current(): TaskInterface
    {
        return $this->tasks[$this->position];
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
        return isset($this->tasks[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function getArray(): array
    {
        $array = [];
        foreach ($this as $task) {
            $array[] = ['id' => $task->id(), 'title' => $task->title()];
        }
        return $array;
    }
}