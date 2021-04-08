<?php
declare(strict_types=1);


namespace App\Application\Actions\Task;


use App\Domain\Task\TaskInterface;


interface CreateTaskActionInterface
{
    public function action(TaskInterface $task): void;
}