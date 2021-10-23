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
    public static function createFromPdoResultRows(array $rows): self
    {
        $next_val = $rows[0]['nextval'] ?? null;
        if (!is_numeric($next_val)) {
            throw new TaskIdValidateException();
        }
        return new self((int)$next_val);
    }

    /**
     * @throws TaskIdValidateException
     */
    public static function createFromRequestRawParam(mixed $param): self
    {
        if (!is_numeric($param)) {
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
        if ($task_id < 0) {
            throw new TaskIdValidateException();
        }
        $this->id = $task_id;
    }
}