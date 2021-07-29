<?php
declare(strict_types=1);


namespace App\Domain\Task;


use App\Domain\Task\Exception\Validate\TaskIdMustBePositiveNumberException;
use App\Domain\Task\Exception\Validate\TaskTitleMustNotEmptyException;
use JetBrains\PhpStorm\Pure;

/**
 * Class Task
 * @package App\Domain\Task
 */
class Task implements TaskInterface
{
    private TaskId $id;
    private TaskTitle $title;

    /**
     * Task constructor.
     * @param int $id
     * @param string $title
     * @throws TaskIdMustBePositiveNumberException
     * @throws TaskTitleMustNotEmptyException
     */
    public function __construct(int $id, string $title) {
        $this->id = new TaskId($id);
        $this->title = new TaskTitle($title);
    }

    #[Pure] public function id(): int
    {
        return $this->id->getId();
    }

    #[Pure] public function title(): string
    {
        return $this->title->getTitle();
    }
}