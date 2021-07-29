<?php
declare(strict_types=1);


namespace App\Domain\Task;


/**
 * Interface DeleteTaskServiceInterface
 * @package App\Domain\Task
 */
interface DeleteTaskServiceInterface
{
    public function delete(int $task_id): void;
}