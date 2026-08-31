<?php
declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\Exception\TaskCompletedValidateException;

final class TaskCompleted
{
    public function __construct(private bool $completed)
    {
    }

    public static function createFromMixedTypeValue(mixed $value): self
    {
        if (!is_bool($value)) {
            throw new TaskCompletedValidateException();
        }
        return new self($value);
    }

    public function completed(): bool
    {
        return $this->completed;
    }
}
