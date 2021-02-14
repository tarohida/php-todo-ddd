<?php


namespace App\Application\DTO\Task;


use Iterator;

class TaskDataList implements Iterator
{
    private array $task_data;
    private int $position;

    /**
     * TaskDataList constructor.
     * @param array $task_data_list
     */
    public function __construct(array $task_data_list)
    {
        $this->position = 0;
        foreach ($task_data_list as $task_data)
        {
            $this->add($task_data);
        }
    }

    private function add(TaskData $task_data)
    {
        $this->task_data[] = $task_data;
    }

    public function current(): TaskData
    {
        return $this->task_data[$this->position];
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
        return isset($this->task_data[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }
}