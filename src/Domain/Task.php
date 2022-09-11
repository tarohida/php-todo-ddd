<?php
declare(strict_types=1);

namespace Tarohida\PhpTodoDdd\Domain;

class Task
{
    public function __construct(
        private int $id,
        private string $title
    )
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
