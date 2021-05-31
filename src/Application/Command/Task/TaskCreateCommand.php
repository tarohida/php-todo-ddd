<?php
declare(strict_types=1);


namespace App\Application\Command\Task;


/**
 * Class TaskCreateCommand
 * @package App\Application\Command\Task
 */
class TaskCreateCommand implements TaskCreateCommandInterface
{
    private int $id;
    private string $title;

    /**
     * TaskCreateCommand constructor.
     * @param int $id
     * @param string $title
     */
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