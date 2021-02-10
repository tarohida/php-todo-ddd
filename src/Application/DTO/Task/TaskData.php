<?php


namespace App\Application\DTO\Task;


use App\Domain\Task\Task;

class TaskData
{
    private int $id;
    private string $title;

    public function __construct(Task $task)
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