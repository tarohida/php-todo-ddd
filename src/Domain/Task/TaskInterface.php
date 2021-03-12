<?php
declare(strict_types=1);

namespace App\Domain\Task;

interface TaskInterface
{
    public function __construct(int $id, string $title);

    public function id(): int;

    public function title(): string;
}