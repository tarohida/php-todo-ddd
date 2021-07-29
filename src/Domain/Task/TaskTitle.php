<?php
declare(strict_types=1);


namespace App\Domain\Task;


use App\Domain\Task\Exception\Validate\TaskTitleMustNotEmptyException;

/**
 * テストはTaskTest経由で実施している
 *
 * Class TaskTitle
 * @package App\Domain\Task
 */
class TaskTitle
{
    private string $title;

    /**
     * @throws TaskTitleMustNotEmptyException
     */
    public function __construct(string $title)
    {
        self::validate($title);
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @throws \App\Domain\Task\Exception\Validate\TaskTitleMustNotEmptyException
     */
    public static function validate(string $title) {
        if (empty($title)) {
            throw new TaskTitleMustNotEmptyException();
        }
    }
}