<?php
declare(strict_types=1);

namespace App\Infrastructure\Task;

use RuntimeException;

class Task
{
    public static function createFromPdoDataSet(array $data)
    {
        if (!isset($data['id'], $data['title'])) {
            throw new RuntimeException();
        }
        if (!is_numeric($data['id'])) {
            throw new RuntimeException();
        }
        return new self((int)$data['id'], $data['title']);
    }

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}