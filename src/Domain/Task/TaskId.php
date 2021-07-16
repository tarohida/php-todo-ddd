<?php
declare(strict_types=1);


namespace App\Domain\Task;


use App\Domain\Task\Exception\TaskValidateFailedWithIdException;

/**
 * テストはTaskTest経由で実施している。
 *
 * Class TaskId
 * @package App\Domain\Task
 */
class TaskId
{
    private int $id;

    /**
     * @throws TaskValidateFailedWithIdException
     */
    public function __construct(int $id)
    {
        $this->validate($id);
        $this->id = $id;
    }

    /**
     * @throws TaskValidateFailedWithIdException
     */
    public static function validate(int $id): void
    {
        if ($id <= 0) {
            throw new TaskValidateFailedWithIdException(reason: TaskValidateFailedWithIdException::REASON_MUST_BE_POSITIVE_NUMBER);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }
}