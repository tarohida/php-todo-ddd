<?php
declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\Exception\TaskValidateException;

class Task
{
    private int $id;
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
        return new self((int)$data['id'], $data['title']);
    }

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }
}