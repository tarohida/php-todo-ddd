<?php
declare(strict_types=1);


namespace App\Application\Command\Task;


/**
 * Class TaskCreateCommand
 * @package App\Application\Command\Task
 */
class TaskCreateCommand implements TaskCreateCommandInterface
{
    /**
     * TaskCreateCommand constructor.
     * @param string $title
     */
    public function __construct(
        private string $title
    ) {
    }

    public function title(): string
    {
        return $this->title;
    }
}