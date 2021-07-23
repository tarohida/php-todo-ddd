<?php
declare(strict_types=1);


namespace App\Domain\Task;


/**
 * Interface TaskServiceInterface
 * @package App\Domain\Task
 */
interface TaskServiceInterface
{
    public function taskExists(TaskId $id): bool;
}