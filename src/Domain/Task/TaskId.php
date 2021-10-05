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
        if (!isset($rows[0]['nextval']) ||
            !is_numeric($rows[0]['nextval'])
        ) {
            throw new TaskIdValidateException();
        }
        return new self((int)$rows[0]['nextval']);
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