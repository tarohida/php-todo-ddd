<?php
declare(strict_types=1);

namespace App\Domain\Task;

class Task
{
    public function __construct(
        private TaskId $id,
        private TaskTitle $title
    ) { }

    public function id(): int
    {
        return $this->id->id();
    }

    public function title(): string
    {
        return $this->title->title();
    }
}
