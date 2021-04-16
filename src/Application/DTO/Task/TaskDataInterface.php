<?php
declare(strict_types=1);

namespace App\Application\DTO\Task;

interface TaskDataInterface
{
    public function id(): int;

    public function title(): string;
}