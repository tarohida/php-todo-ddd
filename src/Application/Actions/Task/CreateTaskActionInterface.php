<?php
declare(strict_types=1);


namespace App\Application\Actions\Task;


use App\Application\Actions\ActionInterface;
use App\Domain\Task\TaskInterface;

interface CreateTaskActionInterface extends ActionInterface
{
    public function action(TaskInterface $task): void;
}