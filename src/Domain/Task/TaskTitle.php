<?php
declare(strict_types=1);


namespace App\Domain\Task;


use App\Domain\Task\Exception\TaskValidateFailedWithTitleException;

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
     * @throws TaskValidateFailedWithTitleException
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
     * @throws TaskValidateFailedWithTitleException
     */
    public static function validate(string $title) {
        if (empty($title)) {
            throw new TaskValidateFailedWithTitleException(reason: TaskValidateFailedWithTitleException::REASON_MUST_NOT_BE_EMPTY);
        }
    }
}