<?php
declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\Exception\TaskValidateException;
use JetBrains\PhpStorm\Pure;

class Task
{
    /**
     * @throws TaskValidateException
     */
    public static function createFromPdoDataSet(array $data): Task
    {
        if (!isset($data['id'], $data['title'])) {
            throw new TaskValidateException();
        }
        if (!is_numeric($data['id'])) {
            throw new TaskValidateException();
        }
        return new self(new TaskId((int)$data['id']), new TaskTitle($data['title']));
    }

    public function __construct(
        private TaskId $id,
        private TaskTitle $title
    ) { }

    #[Pure] public function id(): int
    {
        return $this->id->id();
    }

    #[Pure] public function title(): string
    {
        return $this->title->title();
    }
}