<?php
declare(strict_types=1);


namespace App\Domain\Task;


class Task implements TaskInterface
{
    /**
     * Task constructor.
     * @param int $id
     * @param string $title
     */
    public function __construct(
        private int $id,
        private string $title
    ) {}

    public function id(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }
}