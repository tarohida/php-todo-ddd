<?php
declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\Exception\TaskTitleValidateException;

class TaskTitle
{
    private string $title;

    /**
     * @throws TaskTitleValidateException
     */
    public static function createFromRowParam(mixed $param): self
    {
        if (!is_string($param)) {
            throw new TaskTitleValidateException();
        }
        return new TaskTitle($param);
    }

    /**
     * @throws TaskTitleValidateException
     */
    public function __construct(string $title)
    {
        if (empty($title)) {
            throw new TaskTitleValidateException('invalid title');
        }
        $this->title = $title;
    }

    public function title(): string
    {
        return $this->title;
    }
}