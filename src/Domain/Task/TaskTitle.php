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
    public static function createFromMixedTypeValue(mixed $param): self
    {
        if (!is_string($param)) {
            throw new TaskTitleValidateException();
        }
        return new self($param);
    }

    /**
     * @throws TaskTitleValidateException
     */
    public function __construct(string $title)
    {
        $characterCount = preg_match_all('/./us', $title);
        if (trim($title) === '' || $characterCount === false || $characterCount > 255) {
            throw new TaskTitleValidateException('invalid title');
        }
        $this->title = $title;
    }

    public function title(): string
    {
        return $this->title;
    }
}
