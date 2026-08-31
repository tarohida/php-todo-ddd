<?php
declare(strict_types=1);

namespace App\Domain\Task;

class Task
{
    public function __construct(
        private TaskId $id,
        private TaskTitle $title,
        ?TaskCompleted $completed = null,
    ) {
        $this->completed = $completed ?? new TaskCompleted(false);
    }

    private TaskCompleted $completed;

    public function id(): int
    {
        return $this->id->id();
    }

    public function title(): string
    {
        return $this->title->title();
    }

    public function completed(): bool
    {
        return $this->completed->completed();
    }

    public function withCompletion(TaskCompleted $completed): self
    {
        return new self($this->id, $this->title, $completed);
    }
}
