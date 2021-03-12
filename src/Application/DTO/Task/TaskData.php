<?php


namespace App\Application\DTO\Task;


use App\Domain\Task\TaskInterface;

class TaskData
{
    private int $id;
    private string $title;

    public function __construct(TaskInterface $task)
    {
        $this->id = $task->id();
        $this->title = $task->title();
    }

    public function id(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

}