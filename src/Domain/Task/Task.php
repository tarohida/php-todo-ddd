<?php


namespace App\Domain\Task;


class Task implements TaskInterface
{
    private string $title;
    private int $id;

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
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