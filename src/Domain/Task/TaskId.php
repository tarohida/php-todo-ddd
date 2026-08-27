<?php
declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\Exception\TaskIdValidateException;

class TaskId
{
    private int $id;

    /**
     * @throws TaskIdValidateException
     */
    public static function createFromMixedTypeValue(mixed $param): self
    {
        if (is_int($param)) {
            return new self($param);
        }
        if (!is_string($param) || preg_match('/^[1-9][0-9]*$/D', $param) !== 1) {
            throw new TaskIdValidateException();
        }

        $maximum = (string) PHP_INT_MAX;
        if (strlen($param) > strlen($maximum)
            || (strlen($param) === strlen($maximum) && strcmp($param, $maximum) > 0)
        ) {
            throw new TaskIdValidateException();
        }

        return new self((int)$param);
    }

    public function id(): int
    {
        return $this->id;
    }

    /**
     * @throws TaskIdValidateException
     */
    public function __construct(int $task_id)
    {
        if ($task_id <= 0) {
            throw new TaskIdValidateException();
        }
        $this->id = $task_id;
    }
}
