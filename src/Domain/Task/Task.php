<?php
declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\Exception\TaskValidateException;
use JetBrains\PhpStorm\Pure;

class Task
{
    private TaskId $id;
    private string $title;

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
        return new self(new TaskId((int)$data['id']), $data['title']);
    }

    public function __construct(TaskId $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    #[Pure] public function id(): int
    {
        return $this->id->id();
    }

    public function title(): string
    {
        return $this->title;
    }
}