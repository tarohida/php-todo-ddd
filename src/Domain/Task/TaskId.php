<?php
declare(strict_types=1);


namespace App\Domain\Task;


use App\Domain\Task\Exception\Validate\TaskIdMustBePositiveNumberException;

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
     * @throws TaskIdMustBePositiveNumberException
     */
    public function __construct(int $id)
    {
        $this->validate($id);
        $this->id = $id;
    }

    /**
     * @throws TaskIdMustBePositiveNumberException
     */
    public static function validate(int $id): void
    {
        if ($id <= 0) {
            throw new TaskIdMustBePositiveNumberException();
        }
    }

    public function getId(): int
    {
        return $this->id;
    }
}