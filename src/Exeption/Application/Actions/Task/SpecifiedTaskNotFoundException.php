<?php
declare(strict_types=1);


namespace App\Exeption\Application\Actions\Task;


use App\Exeption\TodoappException;

class SpecifiedTaskNotFoundException extends TodoappException
{
    /**
     * @var int
     */
    private int $task_id;

    public function setTaskId(int $id)
    {
        $this->task_id = $id;
    }

    public function getTaskId(): ?int
    {
        return isset($this->task_id) ? $this->task_id : null;
    }
}