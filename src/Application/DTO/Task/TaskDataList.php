<?php


namespace App\Application\DTO\Task;


class TaskDataList implements TaskDataListInterface
{
    private array $task_data_array;
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

    private function add(TaskDataInterface $task_data)
    {
        $this->task_data_array[] = $task_data;
    }

    public function current(): TaskDataInterface
    {
        return $this->task_data_array[$this->position];
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
        return isset($this->task_data_array[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function isEmpty(): bool
    {
        return empty($this->task_data_array);
    }
}